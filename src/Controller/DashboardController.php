<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\PartnerRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur du tableau de bord principal de l'application Pépi+.
 *
 * Affiche une vue adaptée selon le rôle de l'utilisateur connecté :
 * - **Partenaire** : ses stocks, ses alertes de stock bas et ses commandes récentes.
 * - **Admin / Collaborateur** : statistiques globales (commandes, partenaires, stocks bas).
 *
 * Le seuil d'alerte de stock bas est fixé à 10 unités (RG-02).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
final class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord adapté au rôle de l'utilisateur connecté.
     *
     * @param OrderRepository   $orderRepository   repository des commandes
     * @param StockRepository   $stockRepository   repository des stocks pour les alertes
     * @param PartnerRepository $partnerRepository repository des partenaires
     *
     * @return Response la vue Twig rendue avec les données adaptées au rôle
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(OrderRepository $orderRepository, StockRepository $stockRepository, PartnerRepository $partnerRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vue spécifique partenaire : filtrée sur son entité Partner uniquement
        if ($this->isGranted('ROLE_PARTNER') && $user->getPartner()) {
            $partner = $user->getPartner();

            return $this->render('dash_board/partner_index.html.twig', [
                'partner' => $partner,
                // Uniquement les stocks déclarés par ce partenaire
                'my_stocks' => $stockRepository->findBy(['partner' => $partner]),
                // Stocks critiques (quantité < 10) de ce partenaire uniquement
                'low_stocks' => $stockRepository->findLowStockAlertByPartner($partner, 10),
                // 5 dernières commandes contenant ses produits
                'recent_orders' => $orderRepository->findRecentOrdersByPartner($partner, 5),
            ]);
        }

        // Vue Admin / Collaborateur : statistiques globales de l'application
        return $this->render('dash_board/index.html.twig', [
            'total_orders' => $orderRepository->count([]),
            'total_partners' => $partnerRepository->count([]),
            // Tous les stocks dont la quantité est inférieure au seuil d'alerte (10)
            'low_stocks' => $stockRepository->findLowStockAlert(10),
            // 5 dernières commandes créées ou modifiées
            'recent_orders' => $orderRepository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }
}
