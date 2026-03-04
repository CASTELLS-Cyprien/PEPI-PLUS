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
use Knp\Component\Pager\PaginatorInterface;

#[Route('/stock')]
final class StockController extends AbstractController
{

    #[Route('/global', name: 'app_stock_index', methods: ['GET'])]
    public function index(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        $filterData = new \App\Model\StockFilterData();
        $form = $this->createForm(\App\Form\StockFilterType::class, $filterData);
        $form->handleRequest($request);

        $query = $stockRepository->findWithFilters($filterData);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('stock/indexGlobal.html.twig', [
            'stocks' => $pagination,
            'filterForm' => $form->createView(),
        ]);
    }

    #[Route('/gestion', name: 'app_stock_gestion_index', methods: ['GET'])]
    public function Gestionindex(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        // 1. Initialisation du DTO et du Formulaire
        $filterData = new \App\Model\StockFilterData();
        $form = $this->createForm(\App\Form\StockFilterType::class, $filterData);
        $form->handleRequest($request);

        // 2. Appel de la méthode spécifique "Internal" (partner IS NULL)
        $query = $stockRepository->findInternalStocksWithFilters($filterData);

        // 3. Pagination
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('stock/indexGestion.html.twig', [
            'stocks' => $pagination,
            'filterForm' => $form->createView(),
        ]);
    }

    #[Route('/gestion/new', name: 'app_stock_gestion_new', methods: ['GET', 'POST'])]
    public function newGestion(Request $request, EntityManagerInterface $entityManager, StockRepository $stockRepository): Response
    {
        $stock = new Stock();
        $now = new \DateTimeImmutable();

        // Initialisation par défaut
        $stock->setCreatedAt($now);
        $stock->setUpdatedAt($now);
        $stock->setPartner(null); // Stock interne

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            // Recherche d'un stock identique existant
            $existingStock = $stockRepository->findOneBy([
                'plant' => $stock->getPlant(),
                'packaging' => $stock->getPackaging(),
                'season' => $stock->getSeason(),
                'partner' => null
            ]);

            if ($existingStock) {
                // Si existe, on cumule
                $existingStock->setQuantity($existingStock->getQuantity() + $stock->getQuantity());
                $existingStock->setUpdatedAt($now);
                $existingStock->setUpdatedBy($user);
                $this->addFlash('success', 'Quantité ajoutée au stock existant.');
            } else {
                // Si nouveau, on définit le créateur
                $stock->setCreatedBy($user);
                $stock->setUpdatedBy($user);
                $entityManager->persist($stock);
                $this->addFlash('success', 'Nouveau stock créé avec succès.');
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->render('stock/newGestion.html.twig', [
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
        // Sécurité : Si le stock appartient à un partenaire, on redirige vers l'index global
        if ($stock->getPartner() !== null) {
            return $this->redirectToRoute('app_stock_index');
        }

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock->setUpdatedAt(new \DateTimeImmutable());
            $stock->setUpdatedBy($this->getUser());

            $entityManager->flush();

            $this->addFlash('success', 'Stock mis à jour.');
            // On redirige vers l'index de GESTION et non global
            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        // On stocke l'information avant la suppression pour savoir où rediriger
        $isInternal = ($stock->getPartner() === null);

        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stock);
            $entityManager->flush();
            $this->addFlash('success', 'L\'élément a été supprimé.');
        }

        // Redirection dynamique
        if ($isInternal) {
            return $this->redirectToRoute('app_stock_gestion_index');
        }

        return $this->redirectToRoute('app_partner_myStock');
    }
}
