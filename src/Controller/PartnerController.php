<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Form\StockType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\OrderLine;
use App\Repository\StockRepository;
use App\Entity\Stock;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\OrderLineRepository;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/partner')]
final class PartnerController extends AbstractController
{
    #[Route(name: 'app_partner_index', methods: ['GET'])]
    public function index(Request $request, PartnerRepository $partnerRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class, null);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        $allStocks = $partnerRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('partner/index.html.twig', [
            'partners' => $partnerRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
            'partners'     => $pagination,
        ]);
    }

    #[Route('/gestion/new', name: 'app_partner_new', methods: ['GET', 'POST'])] // Ajout de POST
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partner = new Partner(); // On crée l'objet ici
        $partner->setCreatedAt(new \DateTimeImmutable()); // Initialise la date

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $entityManager->persist($partner);
                $entityManager->flush();

                $this->addFlash('success', 'Partenaire ajouté avec succès !');

                return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter le partenaire : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_partner_show', methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_partner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->flush();
                    $this->addFlash('success', 'Partenaire mis à jour avec succès !');

                    return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible de mettre à jour le partenaire : ' . $e->getMessage());
                }
            }
            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }
    #[Route('/my-stock/liste', name: 'app_partner_myStock', methods: ['GET'])]
    public function MyStockIndex(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        if ($this->isGranted(['ROLE_ADMIN', 'ROLE_COLLABORATOR'])) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $partner = $user->getPartner();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // On récupère le terme directement depuis l'URL via 'query'
        $searchTerm = $request->query->get('query');
        $allStocks = $stockRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            8
        );

        if (!$partner) {
            throw $this->createAccessDeniedException('Aucun profil partenaire associé à ce compte.');
        }

        return $this->render('partner/myStock.html.twig', [
            'stocks' => $stockRepository->findBy(['partner' => $partner]),
            'stocks' => $stockRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
            'stocks'     => $pagination,
        ]);
    }

    #[Route('/my-stock/new', name: 'app_partner_newMyStock', methods: ['GET', 'POST'])]
    public function newMyStock(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($stock);
                $entityManager->flush();

                $this->addFlash('success', 'Nouveau stock ajouté avec succès !');

                return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter le stock : ' . $e->getMessage());
            }
        }

        return $this->render('partner/newMyStock.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/my-stock/{id}/show', name: 'app_partner_showMyStock', methods: ['GET'])]
    public function showMyStock(Stock $stock): Response
    {
        //Limiter l'accès aux partenaires seulement si ce n'est pas leur stock

        /** @var User $user */
        $user = $this->getUser();

        if (
            $this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR') &&
            $stock->getPartner() !== $user->getPartner()
        ) {
            return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/showMyStock.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/my-stock/{id}/edit', name: 'app_partner_editMyStock', methods: ['GET', 'POST'])]
    public function editMyStock(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($stock);
                $entityManager->flush();

                $this->addFlash('success', 'Stock mis à jour avec succès !');

                return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour le stock : ' . $e->getMessage());
            }
        }

        return $this->render('partner/editMyStock.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }
    #[Route('/my-reservations/liste', name: 'app_partner_reservations', methods: ['GET'])]
    public function reservations(Request $request, OrderLineRepository $orderLineRepo , PartnerRepository $partnerRepository, PaginatorInterface $paginator): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $partner = $user->getPartner();

        if (!$partner) {
            $this->addFlash('error', 'Aucune entreprise rattachée.');
            return $this->redirectToRoute('app_dashboard');
        }

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        $allStocks = $partnerRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            8
        );

        $orderLines = $orderLineRepo->searchReservations($partner, $searchTerm);

        return $this->render('partner/myReservation.html.twig', [
            'orderLines' => $orderLines,
            'searchForm' => $form->createView(),
            'stocks'     => $pagination,
        ]);
    }
}
