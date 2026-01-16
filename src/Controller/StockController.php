<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\SearchType;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/stock')]
final class StockController extends AbstractController
{

    #[Route('/global', name: 'app_stock_index', methods: ['GET'])]
    public function index(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        $allStocks = $stockRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            7
        );

        // 5. Rendu de la vue
        return $this->render('stock/indexGlobal.html.twig', [
            'searchForm' => $form->createView(),
            'stocks'     => $pagination,
        ]);
    }

    #[Route('/gestion', name: 'app_stock_gestion_index', methods: ['GET'])]
    public function Gestionindex(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Récupération du terme de recherche
        $searchTerm = $request->query->get('query');

        // On récupère le QueryBuilder préparé par le repository
        $queryBuilder = $stockRepository->getGestionQueryBuilder($searchTerm);

        // On passe ce QueryBuilder au paginateur
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('stock/indexGestion.html.twig', [
            'searchForm' => $form->createView(),
            'stocks'     => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/my-stock/{id}/show', name: 'app_stock_showPartner', methods: ['GET'])]
    public function showPartner(Stock $stock): Response
    {
        //Limiter l'accès aux partenaires seulement si ce n'est pas leur stock

        /** @var User $user */
        $user = $this->getUser();

        if (
            $this->isGranted('ROLE_PARTNER') &&
            $stock->getPartner() !== $user->getPartner()
        ) {
            return $this->redirectToRoute('app_stock_myStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/showPartner.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/gestion/show/{id}', name: 'app_stock_gestion_show', methods: ['GET'])]
    public function gestionShow(Stock $stock): Response
    {
        return $this->render('stock/showGestion.html.twig', [
            'stock' => $stock,
        ]);
    }


    #[Route('/edit/{id}', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {

        //Si le stock appartient à un partenaire, on interdit l'édition
        if ($stock->getPartner() !== null) {
            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
    }
}
