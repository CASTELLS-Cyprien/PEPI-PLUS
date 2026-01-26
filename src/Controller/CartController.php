<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Service\CartService;
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
    public function validate(CartService $cartService, EntityManagerInterface $em): Response
    {
        $items = $cartService->getFullCart();
        if (empty($items)) {
            return $this->redirectToRoute('app_cart_index');
        }

        $order = new Order();
        $order->setOrderNumber('CMD-' . strtoupper(bin2hex(random_bytes(4))));
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

            // Décrémentation immédiate du stock
            $stock->setQuantity($stock->getQuantity() - $qty);
            $stock->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($line);
        }

        // Création de l'historique
        $history = new OrderStatusHistory();
        $history->setStatus('Réservation');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());

        $order->addOrderStatusHistory($history);

        $em->persist($order);
        $em->flush();

        $cartService->clear();

        $this->addFlash('success', 'Commande créée avec succès.');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
}
