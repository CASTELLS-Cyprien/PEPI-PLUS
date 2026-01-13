<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/season')]
final class SeasonController extends AbstractController
{
    #[Route(name: 'app_season_index', methods: ['GET'])]
    public function index(SeasonRepository $seasonRepository): Response
    {
        return $this->render('season/index.html.twig', [
            'seasons' => $seasonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_season_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        //Si l'utilisateur est un partenaire, on le redirige vers la page de création de son stock 
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($season);
                $entityManager->flush();

                return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
            }
        } //sinon si c'est un collaborateur, on le redirige vers la page de gestion des plantes
        else if ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($season);
                $entityManager->flush();

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        
        return $this->render('season/new.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/my-stock/new', name: 'app_season_newPartner', methods: ['GET', 'POST'])]
    public function newPartner(Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($season);
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_newMyStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('season/newPartner.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_season_show', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->render('season/show.html.twig', [
            'season' => $season,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_season_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $season->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($season);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
    }
}
