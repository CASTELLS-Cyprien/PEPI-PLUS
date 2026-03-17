<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Model\OrderFilterData;
use App\Entity\OrderLine;
use App\Entity\Stock;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\OrderStatusHistory;
use App\Form\OrderFilterType;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $filterData = new OrderFilterData(); // Votre DTO
        $form = $this->createForm(OrderFilterType::class, $filterData);
        $form->handleRequest($request);

        // On utilise la méthode de recherche globale qui prend le DTO
        $query = $orderRepository->findWithFilters($filterData);

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('order/index.html.twig', [
            'orders' => $pagination,
            'filterForm' => $form->createView(), // On envoie filterForm pour activer le bouton icône
        ]);
    }

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
                $this->addFlash('error', 'Impossible d\'enregistrer la commande : ' . $e->getMessage());
            }
            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
    #[Route('/edit/{id}', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager, StockRepository $stockRepository): Response
    {
        // 1. Sauvegarde de l'état initial
        $oldStatus = $order->getStatus();

        // 2. Création et gestion du formulaire de statut
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newStatus = $order->getStatus();

            // --- VÉRIFICATION : Sécurité du flux de statut ---
            // Une commande ne peut être "Livrée" que si elle était "Validé" au préalable
            if ($newStatus === 'Livrée' && $oldStatus !== 'Validé') {
                $this->addFlash('danger', 'Action impossible : une commande doit être "Validée" avant d\'être marquée comme "Livrée".');
                return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
            }

            // --- GESTION DE L'ANNULATION ---
            if ($newStatus === 'Annulée' && $oldStatus !== 'Annulée') {
                foreach ($order->getOrderLines() as $line) {
                    $stock = $line->getStock();
                    if ($stock) {
                        $stock->setQuantity($stock->getQuantity() + $line->getQuantity());
                        $stock->setUpdatedAt(new \DateTimeImmutable());
                    }
                }
            }

            // --- ENREGISTREMENT DE L'HISTORIQUE ---
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

        // 3. LOGIQUE DE RECHERCHE DE STOCK (Ajout direct sans JS)
        $searchQuery = $request->query->get('q');
        $stocksFound = [];

        // On ne permet la recherche d'articles que si la commande est en "Réservation"
        if ($order->getStatus() === 'Réservation' && $searchQuery) {
            $stocksFound = $stockRepository->searchByTerm($searchQuery);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
            'stocksFound' => $stocksFound,
            'searchQuery' => $searchQuery
        ]);
    }

    #[Route('/{id}/deliver', name: 'app_order_deliver', methods: ['POST'])]
    public function deliver(Order $order, EntityManagerInterface $em): Response
    {
        // Sécurité : On ne livre pas une commande déjà terminée ou annulée
        if (in_array($order->getStatus(), ['Livrée', 'Annulée'])) {
            $this->addFlash('warning', 'Le statut de cette commande ne peut plus être modifié.');
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // MISE À JOUR DU STATUT UNIQUEMENT
        // Le stock a déjà été déduit lors de la validation/réservation.
        $order->setStatus('Livrée');
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

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

    #[Route('/{id}/cancel', name: 'app_order_cancel', methods: ['POST'])]
    public function cancel(Order $order, EntityManagerInterface $em): Response
    {
        // 1. Vérification de sécurité
        if ($order->getStatus() === 'Annulée') {
            $this->addFlash('warning', 'Cette commande est déjà annulée.');
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        if ($order->getStatus() === 'Livrée') {
            $this->addFlash('danger', 'Impossible d\'annuler une commande déjà livrée (le stock est déjà en interne).');
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        // 2. Restitution du Stock
        foreach ($order->getOrderLines() as $line) {
            $stock = $line->getStock();
            // On rajoute la quantité de la ligne au stock d'origine
            $stock->setQuantity($stock->getQuantity() + $line->getQuantity());
            $stock->setUpdatedAt(new \DateTimeImmutable());
        }

        // 3. Mise à jour du statut
        $order->setStatus('Annulée');
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        // 4. Création de l'Historique (Traçabilité)
        $history = new OrderStatusHistory();
        $history->setStatus('Annulée');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());

        $order->addOrderStatusHistory($history);

        $em->flush();

        $this->addFlash('success', 'La commande a été annuléee.');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    #[Route('/{id}/add-line-direct', name: 'app_order_add_line_direct', methods: ['POST'])]
    public function addLineDirect(Order $order, Request $request, StockRepository $stockRepo, EntityManagerInterface $em): Response
    {
        $stockId = $request->request->get('stockId');
        $qty = (int) $request->request->get('qty');
        $stock = $stockRepo->find($stockId);

        if ($stock && $qty > 0 && $stock->getQuantity() >= $qty) {
            $line = new OrderLine();
            $line->setPurchaseOrder($order);
            $line->setStock($stock);
            $line->setQuantity($qty);

            $stock->setQuantity($stock->getQuantity() - $qty);
            $em->persist($line);
            $em->flush();

            $this->addFlash('success', 'Article ajouté à la commande ' . $order->getOrderNumber());
        }

        return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
    }
}
