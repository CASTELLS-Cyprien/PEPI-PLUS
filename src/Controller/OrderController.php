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
use App\Form\SearchType;
use App\Entity\OrderLine;
use App\Entity\Stock;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // On récupère le terme directement depuis l'URL via 'query'
        $searchTerm = $request->query->get('query');
        $allStocks = $orderRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
            'orders'     => $pagination,
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

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $order->setUpdatedBy($this->getUser());
                $order->setUpdatedAt(new \DateTimeImmutable());
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
        $em->flush();

        $this->addFlash('success', 'Commande livrée : le stock a été basculé en interne.');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }


    #[Route('/order/reserve/{id}', name: 'app_order_reserve', methods: ['POST'])]
    public function reserve(Stock $stock, Request $request, EntityManagerInterface $em): Response
    {
        $quantity = (int) $request->request->get('quantity');

        if ($quantity <= 0 || $quantity > $stock->getQuantity()) {
            $this->addFlash('danger', 'Quantité invalide ou stock insuffisant.');
            return $this->redirectToRoute('app_stock_index');
        }

        $order = new Order();
        $order->setOrderNumber('CMD-' . strtoupper(bin2hex(random_bytes(4))));
        $order->setStatus('Réservation');
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setCollaborator($this->getUser());
        $order->setUpdatedBy($this->getUser());
        $order->setUpdatedAt(new \DateTimeImmutable());

        $line = new OrderLine();
        $line->setStock($stock);
        $line->setQuantity($quantity);
        $line->setPurchaseOrder($order);

        // Décrémentation immédiate du stock virtuel
        $stock->setQuantity($stock->getQuantity() - $quantity);
        $stock->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($order);
        $em->persist($line);
        $em->flush();

        $this->addFlash('success', "Réservation de $quantity unités effectuée.");
        return $this->redirectToRoute('app_order_index');
    }
}
