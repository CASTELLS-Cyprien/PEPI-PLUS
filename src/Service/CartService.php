<?php

namespace App\Service;

use App\Repository\StockRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service de gestion du panier de réservation en session.
 *
 * Le panier est stocké en session PHP sous la clé `cart` sous la forme
 * d'un tableau associatif `[stockId => quantity]`.
 *
 * Ce service est utilisé par {@see \App\Controller\CartController} pour :
 * - Ajouter des stocks depuis la vue globale
 * - Mettre à jour les quantités depuis la vue panier
 * - Retirer des articles
 * - Récupérer le contenu complet avec les objets Stock hydratés
 * - Vider le panier après validation de la commande
 *
 * Sécurité des quantités :
 * La validation de la quantité maximale (plafonnée au stock disponible)
 * est effectuée dans {@see \App\Controller\CartController::update()},
 * pas dans ce service.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class CartService
{
    /**
     * Initialise le service avec les dépendances nécessaires.
     *
     * @param RequestStack    $requestStack    pile de requêtes pour accéder à la session
     * @param StockRepository $stockRepository repository pour hydrater les stocks depuis les IDs en session
     */
    public function __construct(
        private RequestStack $requestStack,
        private StockRepository $stockRepository,
    ) {
    }

    /**
     * Ajoute ou met à jour un article dans le panier.
     *
     * Deux modes de fonctionnement selon le paramètre `$replace` :
     * - **Mode addition** (`replace = false`, défaut) : additionne la quantité
     *   à une éventuelle quantité existante. Utilisé depuis la page stock global.
     * - **Mode remplacement** (`replace = true`) : remplace directement la quantité.
     *   Utilisé depuis la vue panier lors de la modification d'une quantité.
     *
     * @param int  $id       identifiant du stock à ajouter
     * @param int  $quantity quantité souhaitée (doit être > 0)
     * @param bool $replace  `true` pour remplacer, `false` pour additionner (défaut)
     */
    public function add(int $id, int $quantity, bool $replace = false): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if ($replace) {
            // Mode remplacement : utilisé dans la vue panier pour modifier la quantité
            $cart[$id] = $quantity;
        } else {
            // Mode addition : utilisé sur la page stock global pour ajouter un article
            if (!empty($cart[$id])) {
                $cart[$id] += $quantity;
            } else {
                $cart[$id] = $quantity;
            }
        }

        $session->set('cart', $cart);
    }

    /**
     * Retourne le contenu complet du panier avec les objets Stock hydratés.
     *
     * Pour chaque entrée de la session, charge l'entité Stock correspondante
     * depuis le repository. Les stocks introuvables en base sont ignorés
     * (cas de suppression d'un stock entre temps).
     *
     * @return array<int, array{stock: \App\Entity\Stock, quantity: int}>
     *                                                                    Tableau indexé numériquement, chaque entrée contient :
     *                                                                    - `stock` : l'objet Stock hydraté
     *                                                                    - `quantity` : la quantité souhaitée dans le panier
     */
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
                    'quantity' => $quantity,
                ];
            }
            // Les stocks supprimés entre temps sont silencieusement ignorés
        }

        return $cartData;
    }

    /**
     * Supprime un article du panier par l'identifiant du stock.
     *
     * Si l'identifiant n'est pas présent dans le panier, l'opération
     * est silencieuse (pas d'exception).
     *
     * @param int $id identifiant du stock à retirer du panier
     */
    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
    }

    /**
     * Vide complètement le panier en supprimant la clé de session.
     *
     * Appelé dans {@see \App\Controller\CartController::validate()} après
     * la création réussie de la commande.
     */
    public function clear(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }
}
