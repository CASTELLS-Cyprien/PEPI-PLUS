<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Stock;
use App\Entity\User;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des stocks (Stock).
 *
 * Gère deux types de vues :
 * - **Stock global** (`/stock/global`) : tous les stocks réels et virtuels confondus.
 * - **Stock interne** (`/stock/gestion`) : uniquement les stocks Pépi+ (partner IS NULL).
 *
 * Fonctionnalités clés :
 * - Création avec cumul automatique si un stock identique existe déjà (RG-14).
 * - Mode "ajout direct à commande" activé via le paramètre GET `editOrderId`.
 * - Suppression avec redirection dynamique selon le type de stock.
 * - Modification interdite sur les stocks appartenant à un partenaire.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/stock')]
final class StockController extends AbstractController
{
    /**
     * Affiche le stock global (réel + virtuel) avec filtres avancés.
     *
     * Supporte un paramètre GET `editOrderId` pour activer le mode
     * "ajout direct à une commande" depuis la vue stock global.
     * Dans ce mode, chaque ligne affiche un formulaire d'ajout direct
     * à la commande en cours au lieu du bouton panier.
     *
     * @param Request                $request         requête HTTP (paramètres GET de filtres)
     * @param StockRepository        $stockRepository repository des stocks
     * @param PaginatorInterface     $paginator       service de pagination (7 éléments par page)
     * @param EntityManagerInterface $em              gestionnaire pour récupérer la commande en mode édition
     *
     * @return Response la vue Twig du stock global paginé
     */
    #[Route('/global', name: 'app_stock_index', methods: ['GET'])]
    public function index(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $filterData = new \App\Model\StockFilterData();
        $form = $this->createForm(\App\Form\StockFilterType::class, $filterData);
        $form->handleRequest($request);

        $query = $stockRepository->findWithFilters($filterData);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        // Récupération de la commande en cours d'édition si le paramètre est présent dans l'URL
        $editOrderId = $request->query->get('editOrderId');
        $editOrder = $editOrderId ? $em->getRepository(Order::class)->find($editOrderId) : null;

        return $this->render('stock/indexGlobal.html.twig', [
            'stocks' => $pagination,
            'filterForm' => $form->createView(),
            'editOrderId' => $request->query->get('editOrderId'),
            'editOrder' => $editOrder,
        ]);
    }

    /**
     * Affiche uniquement le stock interne Pépi+ (partner IS NULL) avec filtres.
     *
     * Utilise {@see StockRepository::findInternalStocksWithFilters()} pour filtrer
     * exclusivement les stocks sans partenaire (RG-02b).
     *
     * @param Request            $request         requête HTTP
     * @param StockRepository    $stockRepository repository des stocks
     * @param PaginatorInterface $paginator       service de pagination (7 éléments par page)
     *
     * @return Response la vue Twig de l'inventaire interne
     */
    #[Route('/gestion', name: 'app_stock_gestion_index', methods: ['GET'])]
    public function Gestionindex(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        // Initialisation du DTO et du formulaire de filtres
        $filterData = new \App\Model\StockFilterData();
        $form = $this->createForm(\App\Form\StockFilterType::class, $filterData);
        $form->handleRequest($request);

        // Appel de la méthode spécifique filtrant sur partner IS NULL
        $query = $stockRepository->findInternalStocksWithFilters($filterData);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('stock/indexGestion.html.twig', [
            'stocks' => $pagination,
            'filterForm' => $form->createView(),
        ]);
    }

    /**
     * Crée une nouvelle entrée de stock interne avec cumul automatique des doublons.
     *
     * Avant de persister, recherche un stock identique (même plant + conditionnement
     * + saison + partner NULL). Si trouvé, la quantité est cumulée au lieu de créer
     * un doublon (RG-14). Sinon, un nouveau stock est créé avec les informations
     * de traçabilité complètes.
     *
     * @param Request                $request         requête HTTP
     * @param EntityManagerInterface $entityManager   gestionnaire d'entités Doctrine
     * @param StockRepository        $stockRepository repository pour la détection des doublons
     *
     * @return Response la vue du formulaire ou redirection vers l'inventaire interne
     */
    #[Route('/gestion/new', name: 'app_stock_gestion_new', methods: ['GET', 'POST'])]
    public function newGestion(Request $request, EntityManagerInterface $entityManager, StockRepository $stockRepository): Response
    {
        $stock = new Stock();
        $now = new \DateTimeImmutable();

        // Initialisation par défaut : stock interne sans partenaire
        $stock->setCreatedAt($now);
        $stock->setUpdatedAt($now);
        $stock->setPartner(null);

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            // Recherche d'un stock identique existant (plant + conditionnement + saison + interne)
            $existingStock = $stockRepository->findOneBy([
                'plant' => $stock->getPlant(),
                'packaging' => $stock->getPackaging(),
                'season' => $stock->getSeason(),
                'partner' => null,
            ]);

            if ($existingStock) {
                // Stock existant : cumul de la quantité pour éviter les doublons (RG-14)
                $existingStock->setQuantity($existingStock->getQuantity() + $stock->getQuantity());
                $existingStock->setUpdatedAt($now);
                $existingStock->setUpdatedBy($user);
                $this->addFlash('success', 'Quantité ajoutée au stock existant.');
            } else {
                // Nouveau stock : définition des informations de traçabilité
                $stock->setCreatedBy($user);
                $stock->setUpdatedBy($user);
                $entityManager->persist($stock);
                $this->addFlash('success', 'Nouveau stock créé avec succès.');
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->render('stock/newGestion.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    /**
     * Affiche le détail d'un stock (vue globale).
     *
     * @param Stock $stock le stock à afficher
     *
     * @return Response la vue Twig de détail
     */
    #[Route('/show/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * Affiche le détail d'un stock depuis l'espace partenaire.
     *
     * Redirige vers la liste des stocks si un partenaire tente d'accéder
     * à un stock qui ne lui appartient pas (contrôle d'accès par entité).
     *
     * @param Stock $stock le stock à afficher
     *
     * @return Response la vue Twig de détail ou redirection
     */
    #[Route('/my-stock/{id}/show', name: 'app_stock_showPartner', methods: ['GET'])]
    public function showPartner(Stock $stock): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Restriction : un partenaire ne peut voir que les stocks qui lui appartiennent
        if (
            $this->isGranted('ROLE_PARTNER')
            && $stock->getPartner() !== $user->getPartner()
        ) {
            return $this->redirectToRoute('app_stock_myStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/showPartner.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * Affiche le détail d'un stock interne (vue gestion).
     *
     * @param Stock $stock le stock interne à afficher
     *
     * @return Response la vue Twig de détail de l'inventaire interne
     */
    #[Route('/gestion/show/{id}', name: 'app_stock_gestion_show', methods: ['GET'])]
    public function gestionShow(Stock $stock): Response
    {
        return $this->render('stock/showGestion.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * Modifie un stock interne existant.
     *
     * La modification est interdite si le stock appartient à un partenaire
     * (redirige vers la vue globale dans ce cas).
     * Met à jour les champs de traçabilité (updatedAt, updatedBy) à chaque modification.
     *
     * @param Request                $request       requête HTTP
     * @param Stock                  $stock         le stock à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers l'inventaire interne
     */
    #[Route('/edit/{id}', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : modification interdite sur un stock appartenant à un partenaire
        if (null !== $stock->getPartner()) {
            return $this->redirectToRoute('app_stock_index');
        }

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour des informations de traçabilité
            $stock->setUpdatedAt(new \DateTimeImmutable());
            $stock->setUpdatedBy($this->getUser());

            $entityManager->flush();

            $this->addFlash('success', 'Stock mis à jour.');

            // Redirection vers l'inventaire interne (pas le stock global)
            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un stock et redirige dynamiquement selon son type.
     *
     * Détermine le type de stock avant la suppression pour rediriger
     * vers la bonne vue après l'opération :
     * - Stock interne (partner NULL) → inventaire interne
     * - Stock partenaire → liste des stocks du partenaire
     *
     * @param Request                $request       requête HTTP contenant le token CSRF
     * @param Stock                  $stock         le stock à supprimer
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la liste appropriée
     */
    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        // Sauvegarde avant suppression pour déterminer la redirection
        $isInternal = (null === $stock->getPartner());

        if ($this->isCsrfTokenValid('delete'.$stock->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stock);
            $entityManager->flush();
            $this->addFlash('success', 'L\'élément a été supprimé.');
        }

        // Redirection dynamique : inventaire interne ou liste partenaire
        if ($isInternal) {
            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->redirectToRoute('app_partner_myStock');
    }
}
