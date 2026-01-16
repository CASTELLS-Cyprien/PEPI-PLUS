<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\StockRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(OrderRepository $orderRepository, StockRepository $stockRepository, PartnerRepository $partnerRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Si c'est un partenaire, on filtre par son entité Partner
        if ($this->isGranted('ROLE_PARTNER') && $user->getPartner()) {
            $partner = $user->getPartner();

            return $this->render('dash_board/partner_index.html.twig', [
                'partner' => $partner,
                // On récupère uniquement ses stocks
                'my_stocks' => $stockRepository->findBy(['partner' => $partner]),
                // Stocks critiques chez lui uniquement
                'low_stocks' => $stockRepository->findLowStockAlertByPartner($partner, 10),
                // On récupère les commandes qui contiennent ses produits
                'recent_orders' => $orderRepository->findRecentOrdersByPartner($partner, 5),
            ]);
        }

        return $this->render('dash_board/index.html.twig', [
            'total_orders' => $orderRepository->count([]),
            'total_partners' => $partnerRepository->count([]),
            'low_stocks' => $stockRepository->findLowStockAlert(10),
            'recent_orders' => $orderRepository->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }
}
