<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\PlantType;
use App\Form\SearchType;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion du catalogue de plants (Plant).
 *
 * Un plant est un végétal cultivé identifié obligatoirement par son nom latin,
 * son nom commun et son type (RG-11). Ce contrôleur gère le CRUD complet
 * avec pagination et recherche textuelle.
 *
 * La suppression est protégée : un plant lié à des stocks ne peut pas
 * être supprimé pour préserver l'intégrité des données de traçabilité.
 *
 * La redirection après création varie selon le rôle :
 * - Partenaire → page de création de son stock virtuel
 * - Admin/Collaborateur → liste des plants
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/plant')]
final class PlantController extends AbstractController
{
    /**
     * Affiche la liste paginée des plants avec recherche textuelle.
     *
     * Le formulaire de recherche est configuré sans protection CSRF
     * (méthode GET publique). La recherche s'effectue sur le nom latin,
     * le nom commun et le type du plant.
     * Résultats paginés à 9 par page.
     *
     * @param Request            $request         requête HTTP (paramètre GET `query`)
     * @param PlantRepository    $plantRepository repository des plants
     * @param PaginatorInterface $paginator       service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de recherche
     */
    #[Route(name: 'app_plant_index', methods: ['GET'])]
    public function index(Request $request, PlantRepository $plantRepository, PaginatorInterface $paginator): Response
    {
        // Formulaire sans CSRF car c'est une recherche publique en méthode GET
        $form = $this->createForm(SearchType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        // Récupération de la valeur du champ 'query' depuis le formulaire
        $searchTerm = $form->get('query')->getData();
        $allPlants = $plantRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allPlants,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('plant/index.html.twig', [
            'searchForm' => $form->createView(),
            'plants' => $pagination,
        ]);
    }

    /**
     * Crée un nouveau plant dans le référentiel botanique.
     *
     * Comportement de redirection après création selon le rôle :
     * - **Partenaire** : redirige vers la création de son stock virtuel.
     * - **Admin/Collaborateur** : redirige vers la liste des plants.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_plant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        // Redirection spécifique pour les partenaires vers la création de leur stock
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($plant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Plant ajouté avec succès !');

                    return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le plant : '.$e->getMessage());
                }

                return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
            }
        } // Redirection vers la liste des plants pour les collaborateurs/admins
        elseif ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($plant);
                    $entityManager->flush();

                    $this->addFlash('success', 'Plant ajouté avec succès !');

                    return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le plant : '.$e->getMessage());
                }
            }
        }

        return $this->render('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails botaniques d'un plant.
     *
     * @param Plant $plant le plant à afficher
     *
     * @return Response la vue Twig de détail (nom latin, nom commun, type)
     */
    #[Route('/show/{id}', name: 'app_plant_show', methods: ['GET'])]
    public function show(Plant $plant): Response
    {
        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
        ]);
    }

    /**
     * Modifie les informations d'un plant existant.
     *
     * @param Request                $request       requête HTTP
     * @param Plant                  $plant         le plant à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
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
                $this->addFlash('error', 'Impossible de mettre à jour le plant : '.$e->getMessage());
            }
        }

        return $this->render('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un plant du référentiel botanique.
     *
     * La suppression est bloquée si le plant est lié à au moins un stock,
     * afin de préserver l'intégrité des données de traçabilité sanitaire.
     * Un message d'erreur indique le nombre de stocks concernés.
     *
     * @param Request                $request       requête HTTP contenant le token CSRF
     * @param Plant                  $plant         le plant à supprimer
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la liste des plants
     */
    #[Route('/{id}', name: 'app_plant_delete', methods: ['POST'])]
    public function delete(Request $request, Plant $plant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plant->getId(), $request->getPayload()->getString('_token'))) {
            // Suppression bloquée si le plant est lié à des stocks actifs
            if (!$plant->getStocks()->isEmpty()) {
                $this->addFlash('error', 'Impossible de supprimer ce plant : il est lié à '.$plant->getStocks()->count().' stock(s).');

                return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
            }

            try {
                $entityManager->remove($plant);
                $entityManager->flush();
                $this->addFlash('success', 'Plant supprimé avec succès !');

                return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le plant : '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_plant_index', [], Response::HTTP_SEE_OTHER);
    }
}
