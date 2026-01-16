<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\PlantType;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\SearchType;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/plant')]
final class PlantController extends AbstractController
{
    #[Route(name: 'app_plant_index', methods: ['GET'])]
    public function index(Request $request, PlantRepository $plantRepository, PaginatorInterface $paginator): Response
    {
        // On crée le formulaire sans protection CSRF car c'est une recherche GET publique
        $form = $this->createForm(SearchType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        // On récupère la valeur du champ 'query'
        $searchTerm = $form->get('query')->getData();
        $allStocks = $plantRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allStocks,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('plant/index.html.twig', [
            'plants' => $plantRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
            'plants'     => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_plant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        //Si l'utilisateur est un partenaire, on le redirige vers la page de création de son stock 
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($plant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Plant ajoutée avec succès !');

                    return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le plant : ' . $e->getMessage());
                }

                return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
            }
        } //sinon si c'est un collaborateur, on le redirige vers la page de gestion des plantes
        else if ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($plant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Plant ajoutée avec succès !');

                    return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le plant : ' . $e->getMessage());
                }
            }
        }

        return $this->render('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_plant_show', methods: ['GET'])]
    public function show(Plant $plant): Response
    {
        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_plant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plant $plant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($plant);
                $entityManager->flush();

                $this->addFlash('success', 'Plant mis à jour avec succès !');

                return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour le plant : ' . $e->getMessage());
            }
        }
        return $this->render('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plant_delete', methods: ['POST'])]
    public function delete(Request $request, Plant $plant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $plant->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($plant);
                $entityManager->flush();
                $this->addFlash('success', 'Plant supprimée avec succès !');

                return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le plant : ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
    }
}
