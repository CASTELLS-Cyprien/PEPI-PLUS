<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\OrderStatusHistory;
use App\Entity\Stock;
use App\Form\OrderFilterType;
use App\Form\OrderType;
use App\Model\OrderFilterData;
use App\Repository\OrderRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des commandes (Order).
 *
 * Gère le cycle de vie complet d'une commande :
 * - Consultation de la liste avec filtres avancés (statut, dates)
 * - Affichage du détail avec historique des statuts
 * - Modification du statut avec validation du flux métier
 * - Ajout direct de lignes sur une commande existante
 * - Livraison : mise à jour du statut uniquement (stock déjà décrémenté à la réservation)
 * - Annulation : restitution automatique des stocks réservés
 *
 * Flux de statut autorisé : Réservation → Validé → Livrée ou Annulée
 * Une commande Livrée ne peut plus être modifiée.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/order')]
final class OrderController extends AbstractController
{
    /**
     * Affiche la liste paginée des commandes avec filtres avancés.
     *
     * Utilise un DTO {@see OrderFilterData} et le formulaire {@see OrderFilterType}
     * pour filtrer par statut, numéro de commande et plages de dates.
     * Résultats paginés à 10 par page.
     *
     * @param Request            $request         requête HTTP (paramètres GET de filtre)
     * @param OrderRepository    $orderRepository repository des commandes
     * @param PaginatorInterface $paginator       service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de filtres
     */
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        // Initialisation du DTO de filtrage
        $filterData = new OrderFilterData();
        $form = $this->createForm(OrderFilterType::class, $filterData);
        $form->handleRequest($request);

        // Requête filtrée transmise au paginator
        $query = $orderRepository->findWithFilters($filterData);
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('order/index.html.twig', [
            'orders' => $pagination,
            // filterForm active le bouton icône de filtres avancés dans l'include search
            'filterForm' => $form->createView(),
        ]);
    }

    /**
     * Crée une nouvelle commande via le formulaire d'administration.
     *
     * Note : la création normale passe par le panier (CartController::validate).
     * Cette route est réservée à un usage administratif direct.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($order);
                $order->setCreatedAt(new \DateTimeImmutable());
                $entityManager->flush();

                $this->addFlash('success', 'Commande enregistrée avec succès !');

                return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'enregistrer la commande : '.$e->getMessage());
            }

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    /**
     * Affiche le détail d'une commande avec ses lignes et son historique de statuts.
     *
     * @param Order $order la commande à afficher (résolution automatique par ParamConverter)
     *
     * @return Response la vue Twig de détail de la commande
     */
    #[Route('/show/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Modifie le statut d'une commande et permet l'ajout direct de lignes.
     *
     * Logique métier appliquée :
     * - Sauvegarde le statut initial avant soumission du formulaire.
     * - Bloque le passage à "Livrée" si le statut précédent n'est pas "Validé".
     * - En cas de passage à "Annulée", restitue les quantités aux stocks d'origine.
     * - Enregistre un historique (OrderStatusHistory) à chaque changement de statut.
     * - Permet la recherche de stocks pour ajout direct (uniquement en "Réservation").
     *
     * @param Request                $request         requête HTTP
     * @param Order                  $order           la commande à modifier
     * @param EntityManagerInterface $entityManager   gestionnaire d'entités Doctrine
     * @param StockRepository        $stockRepository repository pour la recherche de stocks
     *
     * @return Response la vue d'édition ou redirection après mise à jour
     */
    #[Route('/edit/{id}', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager, StockRepository $stockRepository): Response
    {
        // Sauvegarde du statut avant modification pour détecter les changements
        $oldStatus = $order->getStatus();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newStatus = $order->getStatus();

            // Sécurité flux de statut : "Livrée" uniquement depuis "Validé" (RG-05b)
            if ('Livrée' === $newStatus && 'Validé' !== $oldStatus) {
                $this->addFlash('danger', 'Action impossible : une commande doit être "Validée" avant d\'être marquée comme "Livrée".');

                return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
            }

            // Restitution des stocks si la commande passe à "Annulée"
            if ('Annulée' === $newStatus && 'Annulée' !== $oldStatus) {
                foreach ($order->getOrderLines() as $line) {
                    $stock = $line->getStock();
                    if ($stock) {
                        $stock->setQuantity($stock->getQuantity() + $line->getQuantity());
                        $stock->setUpdatedAt(new \DateTimeImmutable());
                    }
                }
            }

            // Enregistrement de l'historique uniquement si le statut a réellement changé
            if ($oldStatus !== $newStatus) {
                $history = new OrderStatusHistory();
                $history->setStatus($newStatus);
                $history->setChangedBy($this->getUser());
                $history->setCreatedAt(new \DateTimeImmutable());
                $history->setPurchaseOrder($order);

                $entityManager->persist($history);
                $order->setUpdatedAt(new \DateTimeImmutable());
                $order->setUpdatedBy($this->getUser());
            }

            $entityManager->flush();
            $this->addFlash('success', 'La commande a été mise à jour.');

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // Recherche de stocks pour ajout direct de ligne (disponible en "Réservation" uniquement)
        $searchQuery = $request->query->get('q');
        $stocksFound = [];

        if ('Réservation' === $order->getStatus() && $searchQuery) {
            $stocksFound = $stockRepository->searchByTerm($searchQuery);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
            'stocksFound' => $stocksFound,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Passe une commande au statut "Livrée".
     *
     * Le stock a déjà été décrémenté lors de la réservation initiale.
     * Cette action met uniquement à jour le statut et enregistre l'historique.
     * Action impossible si la commande est déjà "Livrée" ou "Annulée".
     *
     * @param Order                  $order la commande à livrer
     * @param EntityManagerInterface $em    gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la page de détail de la commande
     */
    #[Route('/{id}/deliver', name: 'app_order_deliver', methods: ['POST'])]
    public function deliver(Order $order, EntityManagerInterface $em): Response
    {
        // Sécurité : impossible de modifier une commande déjà terminée
        if (in_array($order->getStatus(), ['Livrée', 'Annulée'])) {
            $this->addFlash('warning', 'Le statut de cette commande ne peut plus être modifié.');

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // Mise à jour du statut uniquement (stock déjà déduit à la réservation)
        $order->setStatus('Livrée');
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        // Enregistrement dans l'historique des statuts (traçabilité)
        $history = new OrderStatusHistory();
        $history->setStatus('Livrée');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());
        $history->setPurchaseOrder($order);

        $em->persist($history);
        $em->flush();

        $this->addFlash('success', 'La commande est désormais marquée comme livrée au client.');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    /**
     * Annule une commande et restitue les stocks réservés.
     *
     * Pour chaque ligne de commande, la quantité est restituée au stock d'origine.
     * Action impossible si la commande est déjà "Livrée" (stock déjà intégré en interne).
     *
     * @param Order                  $order la commande à annuler
     * @param EntityManagerInterface $em    gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la page de détail de la commande
     */
    #[Route('/{id}/cancel', name: 'app_order_cancel', methods: ['POST'])]
    public function cancel(Order $order, EntityManagerInterface $em): Response
    {
        // Vérification : commande déjà annulée
        if ('Annulée' === $order->getStatus()) {
            $this->addFlash('warning', 'Cette commande est déjà annulée.');

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // Vérification : annulation impossible sur une commande livrée (RG-05b)
        if ('Livrée' === $order->getStatus()) {
            $this->addFlash('danger', 'Impossible d\'annuler une commande déjà livrée (le stock est déjà en interne).');

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // Restitution de chaque quantité au stock d'origine
        foreach ($order->getOrderLines() as $line) {
            $stock = $line->getStock();
            // On rajoute la quantité de la ligne au stock d'origine
            $stock->setQuantity($stock->getQuantity() + $line->getQuantity());
            $stock->setUpdatedAt(new \DateTimeImmutable());
        }

        // Mise à jour du statut de la commande
        $order->setStatus('Annulée');
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        // Enregistrement dans l'historique des statuts (traçabilité)
        $history = new OrderStatusHistory();
        $history->setStatus('Annulée');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());
        $order->addOrderStatusHistory($history);

        $em->flush();

        $this->addFlash('success', 'La commande a été annulée.');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    /**
     * Ajoute directement une ligne de commande à une commande existante.
     *
     * Vérifie que le stock demandé existe et que la quantité est disponible
     * avant de créer la ligne. Décrémente immédiatement le stock concerné.
     * Utilisé depuis la page d'édition via recherche de stock (paramètre ?q=...).
     *
     * @param Order                  $order     la commande cible
     * @param Request                $request   requête HTTP contenant `stockId` et `qty`
     * @param StockRepository        $stockRepo repository pour retrouver le stock par ID
     * @param EntityManagerInterface $em        gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la page d'édition de la commande
     */
    #[Route('/{id}/add-line-direct', name: 'app_order_add_line_direct', methods: ['POST'])]
    public function addLineDirect(Order $order, Request $request, StockRepository $stockRepo, EntityManagerInterface $em): Response
    {
        $stockId = $request->request->get('stockId');
        $qty = (int) $request->request->get('qty');
        $stock = $stockRepo->find($stockId);

        // Vérification que le stock existe et que la quantité demandée est disponible
        if ($stock && $qty > 0 && $stock->getQuantity() >= $qty) {
            $line = new OrderLine();
            $line->setPurchaseOrder($order);
            $line->setStock($stock);
            $line->setQuantity($qty);

            // Décrémentation immédiate du stock lors de l'ajout à la commande
            $stock->setQuantity($stock->getQuantity() - $qty);
            $em->persist($line);
            $em->flush();

            $this->addFlash('success', 'Article ajouté à la commande '.$order->getOrderNumber());
        }

        return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
    }
}
