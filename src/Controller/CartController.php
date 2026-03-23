<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\OrderStatusHistory;
use App\Service\CartService;
use App\Service\OrderNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion du panier de réservation.
 *
 * Gère le cycle de vie du panier stocké en session :
 * ajout de plants depuis le stock global, modification des quantités,
 * suppression d'articles et validation finale qui crée la commande (Order).
 *
 * À la validation :
 * - Une commande est créée avec le statut "Réservation"
 * - Un numéro au format CMD-YYYY-NNN est généré automatiquement
 * - Les stocks sont décrémentés immédiatement (réels et virtuels)
 * - Un historique de statut initial est enregistré
 * - Le panier est vidé
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
final class CartController extends AbstractController
{
    /**
     * Affiche le contenu actuel du panier.
     *
     * Récupère tous les articles en session via le CartService
     * et les transmet à la vue pour affichage.
     *
     * @param CartService $cartService service de gestion du panier en session
     *
     * @return Response la vue Twig du panier avec la liste des articles
     */
    #[Route('/cart', name: 'app_cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
        ]);
    }

    /**
     * Ajoute un stock au panier par son identifiant.
     *
     * La quantité est lue depuis le corps de la requête POST.
     * Si elle est absente, la valeur par défaut est 1.
     *
     * @param int         $id          identifiant du stock à ajouter (issu de l'URL)
     * @param Request     $request     requête HTTP contenant le champ `quantity`
     * @param CartService $cartService service de gestion du panier
     *
     * @return Response redirection vers la vue du stock global
     */
    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request, CartService $cartService): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cartService->add($id, $quantity);

        $this->addFlash('success', 'Article ajouté au panier.');

        return $this->redirectToRoute('app_stock_index');
    }

    /**
     * Supprime un article du panier par l'identifiant du stock.
     *
     * @param int         $id          identifiant du stock à retirer
     * @param CartService $cartService service de gestion du panier
     *
     * @return Response redirection vers la vue du panier
     */
    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Valide le panier et crée une nouvelle commande en base de données.
     *
     * Processus de validation :
     * 1. Vérifie que le panier n'est pas vide.
     * 2. Génère un numéro de commande unique (CMD-YYYY-NNN).
     * 3. Crée les OrderLine pour chaque article du panier.
     * 4. Décrémente immédiatement chaque stock source (réel ou virtuel).
     * 5. Enregistre une entrée initiale dans l'historique des statuts.
     * 6. Vide le panier après persistance.
     *
     * @param CartService              $cartService         service du panier
     * @param EntityManagerInterface   $em                  gestionnaire d'entités Doctrine
     * @param OrderNotificationService $notificationService service d'emails partenaires (désactivé en v1)
     *
     * @return Response redirection vers le détail de la commande créée,
     *                  ou vers le panier si celui-ci est vide
     */
    #[Route('/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(CartService $cartService, EntityManagerInterface $em, OrderNotificationService $notificationService): Response
    {
        $items = $cartService->getFullCart();

        // Redirection si le panier est vide : aucune commande ne peut être créée
        if (empty($items)) {
            return $this->redirectToRoute('app_cart_index');
        }

        $order = new Order();
        $year = (new \DateTime())->format('Y');

        // Récupération du dernier numéro pour incrémenter la séquence
        $lastOrder = $em->getRepository(Order::class)->findOneBy([], ['id' => 'DESC']);
        $nextNumber = $lastOrder ? (int) substr($lastOrder->getOrderNumber(), -3) + 1 : 1;

        // Génération du numéro unique au format CMD-YYYY-NNN
        $order->setOrderNumber('CMD-'.$year.'-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT));
        $order->setStatus('Réservation');
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setCollaborator($this->getUser());
        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        foreach ($items as $item) {
            $stock = $item['stock'];
            $qty = $item['quantity'];

            // Création d'une ligne de commande pour chaque article du panier
            $line = new OrderLine();
            $line->setStock($stock);
            $line->setQuantity($qty);
            $line->setPurchaseOrder($order);
            $order->addOrderLine($line);

            // Décrémentation immédiate de la SOURCE (stock réel Pépi+ ou stock virtuel partenaire)
            $stock->setQuantity($stock->getQuantity() - $qty);
            $stock->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($line);
        }

        // Création de l'entrée initiale dans l'historique des statuts (traçabilité)
        $history = new OrderStatusHistory();
        $history->setStatus('Réservation');
        $history->setChangedBy($this->getUser());
        $history->setCreatedAt(new \DateTimeImmutable());
        $order->addOrderStatusHistory($history);

        $em->persist($order);
        $em->flush();

        // Notification email aux partenaires (désactivée en v1 - décommenter pour activer)
        // try {
        //     $notificationService->notifyPartnersForOrder($order);
        // } catch (\Exception $e) {
        //     $this->addFlash('warning', 'Commande créée, mais erreur lors de l\'envoi des emails.');
        // }

        // Vidage du panier après la création de la commande
        $cartService->clear();

        $this->addFlash('success', 'Commande client enregistrée avec succès.');

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    /**
     * Met à jour la quantité d'un article dans le panier.
     *
     * La quantité finale est plafonnée au stock réellement disponible en base
     * pour éviter toute sur-réservation. Si la quantité demandée est 0 ou
     * négative, l'article est retiré du panier.
     *
     * @param int                             $id              identifiant du stock à mettre à jour
     * @param Request                         $request         requête HTTP contenant le champ `quantity`
     * @param CartService                     $cartService     service de gestion du panier
     * @param \App\Repository\StockRepository $stockRepository repository pour vérifier le stock disponible
     *
     * @return Response redirection vers la vue du panier
     */
    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $id, Request $request, CartService $cartService, \App\Repository\StockRepository $stockRepository): Response
    {
        $quantity = (int) $request->request->get('quantity');
        $stock = $stockRepository->find($id);

        // Si le stock n'existe plus en base, retour au panier sans modification
        if (!$stock) {
            return $this->redirectToRoute('app_cart_index');
        }

        // Stock réel actuellement disponible en base de données
        $maxAvailable = $stock->getQuantity();

        if ($quantity <= 0) {
            // Quantité nulle ou négative : suppression de l'article du panier
            $cartService->remove($id);
        } else {
            // Plafonnement au stock maximum réel pour éviter la sur-réservation
            $finalQty = min($quantity, $maxAvailable);
            $cartService->add($id, $finalQty, true);

            if ($quantity > $maxAvailable) {
                $this->addFlash('warning', 'Quantité ajustée au maximum disponible.');
            }
        }

        return $this->redirectToRoute('app_cart_index');
    }
}
