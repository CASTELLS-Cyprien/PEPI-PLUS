<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Service\CartService;
use App\Service\OrderNotificationService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\OrderStatusHistory;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart()
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, CartService $cartService): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->add($id, $quantity);

        $this->addFlash('success', 'Article ajouté au panier.');
        return $this->redirectToRoute('app_stock_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(CartService $cartService, EntityManagerInterface $em, OrderNotificationService $notificationService): Response {
        $items = $cartService->getFullCart();
        if (empty($items)) {
            return $this->redirectToRoute('app_cart_index');
        }

        $order = new Order();
        $year = (new \DateTime())->format('Y');
        $lastOrder = $em->getRepository(Order::class)->findOneBy([], ['id' => 'DESC']);
        $nextNumber = $lastOrder ? (int) substr($lastOrder->getOrderNumber(), -3) + 1 : 1;

        $order->setOrderNumber('CMD-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT));
        $order->setStatus('Réservation');
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setCollaborator($this->getUser());
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        foreach ($items as $item) {
            $stock = $item['stock'];
            $qty = $item['quantity'];

            $line = new OrderLine();
            $line->setStock($stock);
            $line->setQuantity($qty);
            $line->setPurchaseOrder($order);
            $order->addOrderLine($line);

            // Décrémentation immédiate de la SOURCE (Réel ou Virtuel)
            $stock->setQuantity($stock->getQuantity() - $qty);
            $stock->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($line);
        }

        $history = new OrderStatusHistory();
        $history->setStatus('Réservation');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());
        $order->addOrderStatusHistory($history);

        $em->persist($order);
        $em->flush();

        // Notification : Le service doit filtrer pour n'envoyer qu'aux partenaires concernés
        // try {
        //     $notificationService->notifyPartnersForOrder($order);
        // } catch (\Exception $e) {
        //     $this->addFlash('warning', 'Commande créée, mais erreur lors de l\'envoi des emails.');
        // }

        $cartService->clear();

        $this->addFlash('success', 'Commande client enregistrée avec succès.');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
    
    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $id, Request $request, CartService $cartService, \App\Repository\StockRepository $stockRepository): Response
    {
        $quantity = (int) $request->request->get('quantity');
        $stock = $stockRepository->find($id);

        if (!$stock) {
            return $this->redirectToRoute('app_cart_index');
        }

        $maxAvailable = $stock->getQuantity(); // Stock réel en base

        if ($quantity <= 0) {
            $cartService->remove($id);
        } else {
            // On s'assure que la quantité demandée ne dépasse jamais le stock réel
            $finalQty = min($quantity, $maxAvailable);
            $cartService->add($id, $finalQty, true);

            if ($quantity > $maxAvailable) {
                $this->addFlash('warning', 'Quantité ajustée au maximum disponible.');
            }
        }

        return $this->redirectToRoute('app_cart_index');
    }
}
