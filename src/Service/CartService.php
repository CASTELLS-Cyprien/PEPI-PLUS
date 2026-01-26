<?php
namespace App\Service;

use App\Repository\StockRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
        private StockRepository $stockRepository
    ) {}

    public function add(int $id, int $quantity): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $cartData = [];

        foreach ($cart as $id => $quantity) {
            $stock = $this->stockRepository->find($id);
            if ($stock) {
                $cartData[] = [
                    'stock' => $stock,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartData;
    }

    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) unset($cart[$id]);
        $session->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }
}