<?php

namespace App\Controller;

use App\Entity\Packaging;
use App\Form\PackagingType;
use App\Repository\PackagingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\SearchType;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/packaging')]
final class PackagingController extends AbstractController
{
    #[Route(name: 'app_packaging_index', methods: ['GET'])]
    public function index(Request $request, PackagingRepository $packagingRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // On récupère le terme directement depuis l'URL via 'query'
        $searchTerm = $request->query->get('query');
        $allStocks = $packagingRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('packaging/index.html.twig', [
            'packagings' => $packagingRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
            'packagings'     => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_packaging_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        //Si l'utilisateur est un partenaire, on le redirige vers la page de création de son stock 
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($packaging);
                    $entityManager->flush();

                    $this->addFlash('success', 'Conditionnement ajoutée avec succès !');

                    return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le conditionnement : ' . $e->getMessage());
                }
            }
        } //sinon si c'est un collaborateur, on le redirige vers la page de gestion des plantes
        else if ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($packaging);
                    $entityManager->flush();

                    $this->addFlash('success', 'Conditionnement ajoutée avec succès !');

                    return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le conditionnement : ' . $e->getMessage());
                }
            }
        }

        return $this->render('packaging/new.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_packaging_show', methods: ['GET'])]
    public function show(Packaging $packaging): Response
    {
        return $this->render('packaging/show.html.twig', [
            'packaging' => $packaging,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_packaging_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Packaging $packaging, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($packaging);
                $entityManager->flush();

                $this->addFlash('success', 'Conditionnement ajoutée avec succès !');

                return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter le conditionnement : ' . $e->getMessage());
            }
        }

        return $this->render('packaging/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_packaging_delete', methods: ['POST'])]
    public function delete(Request $request, Packaging $packaging, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $packaging->getId(), $request->getPayload()->getString('_token'))) {

            try {
                $entityManager->remove($packaging);
                $entityManager->flush();

                $this->addFlash('success', 'Conditionnement supprimée avec succès !');

                return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le conditionnement : ' . $e->getMessage());
            }
        }


        return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
    }
}
