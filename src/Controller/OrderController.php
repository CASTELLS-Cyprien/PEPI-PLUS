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
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // On mémorise l'ancien statut pour comparer
        $oldStatus = $order->getStatus();

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $order->setUpdatedAt(new \DateTimeImmutable());
                $order->setUpdatedBy($this->getUser());

                // Si le statut a été modifié dans le formulaire
                if ($oldStatus !== $order->getStatus()) {
                    $history = new OrderStatusHistory();
                    $history->setStatus($order->getStatus());
                    $history->setChangedBy($this->getUser());
                    $history->setCreatedAt(new \DateTimeImmutable());
                    $order->addOrderStatusHistory($history);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Commande mise à jour avec succès !');
                return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour la commande : ' . $e->getMessage());
            }
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/deliver', name: 'app_order_deliver', methods: ['POST'])]
    public function deliver(Order $order, EntityManagerInterface $em, StockRepository $stockRepo): Response
    {
        if ($order->getStatus() === 'Livrée') {
            $this->addFlash('warning', 'Cette commande est déjà livrée.');
            return $this->redirectToRoute('app_order_index');
        }

        foreach ($order->getOrderLines() as $line) {
            $originalStock = $line->getStock();

            // Chercher le stock interne de Pépi+ (Partner NULL ou spécifique à l'entreprise)
            // On se base sur le Plant, le Packaging et le Season pour identifier un produit identique
            $internalStock = $em->getRepository(Stock::class)->findOneBy([
                'plant' => $originalStock->getPlant(),
                'packaging' => $originalStock->getPackaging(),
                'season' => $originalStock->getSeason(),
                'partner' => null // Représente le stock propre à Pépi+
            ]);

            if ($internalStock) {
                // Mise à jour du stock existant
                $internalStock->setQuantity($internalStock->getQuantity() + $line->getQuantity());
                $internalStock->setUpdatedAt(new \DateTimeImmutable());
            } else {
                // Création d'une nouvelle entrée de stock interne
                $newStock = new Stock();
                $newStock->setPlant($originalStock->getPlant());
                $newStock->setPackaging($originalStock->getPackaging());
                $newStock->setSeason($originalStock->getSeason());
                $newStock->setQuantity($line->getQuantity());
                $newStock->setPartner(null); // Stock Pépi+
                $newStock->setCreatedAt(new \DateTimeImmutable());
                $newStock->setUpdatedAt(new \DateTimeImmutable());
                $newStock->setUpdatedBy($this->getUser());
                $em->persist($newStock);
            }
        }

        $order->setStatus('Livrée');
        $order->setUpdatedAt(new \DateTimeImmutable());

        $history = new OrderStatusHistory();
        $history->setStatus($order->getStatus());
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());

        $order->addOrderStatusHistory($history);

        $em->flush();

        $this->addFlash('success', 'Commande livrée : le stock a été basculé en interne.');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
}