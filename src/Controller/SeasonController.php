<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SearchType;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des saisons de production (Season).
 *
 * Une saison correspond à l'année de production d'un plant.
 * Elle est indispensable pour assurer la traçabilité sanitaire réglementaire :
 * chaque plant vendu doit être tracé par sa pépinière de provenance
 * et sa saison de production (RG-12).
 *
 * Ce contrôleur gère le CRUD complet avec pagination, recherche et
 * protection de suppression (une saison liée à des stocks ne peut pas
 * être supprimée).
 *
 * La redirection après création varie selon le rôle :
 * - Partenaire → page de création de son stock virtuel
 * - Admin/Collaborateur → liste des saisons
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/season')]
final class SeasonController extends AbstractController
{
    /**
     * Affiche la liste paginée des saisons avec recherche sur l'année.
     *
     * Résultats paginés à 10 par page, triés par année croissante.
     *
     * @param Request            $request          requête HTTP (paramètre GET `query`)
     * @param SeasonRepository   $seasonRepository repository des saisons
     * @param PaginatorInterface $paginator        service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de recherche
     */
    #[Route(name: 'app_season_index', methods: ['GET'])]
    public function index(Request $request, SeasonRepository $seasonRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Récupération du terme de recherche depuis l'URL via le paramètre 'query'
        $searchTerm = $request->query->get('query');
        $allSeasons = $seasonRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allSeasons,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('season/index.html.twig', [
            'searchForm' => $form->createView(),
            'seasons' => $pagination,
        ]);
    }

    /**
     * Crée une nouvelle saison de production.
     *
     * Comportement de redirection après création selon le rôle :
     * - **Partenaire** : redirige vers la création de son stock virtuel.
     * - **Admin/Collaborateur** : redirige vers la liste des saisons.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_season_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        // Redirection spécifique pour les partenaires vers la création de leur stock
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($season);
                    $entityManager->flush();

                    $this->addFlash('success', 'Saison ajoutée avec succès !');

                    return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter la saison : '.$e->getMessage());
                }

                return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
            }
        } // Redirection vers la liste des saisons pour les collaborateurs/admins
        elseif ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($season);
                    $entityManager->flush();

                    $this->addFlash('success', 'Saison ajoutée avec succès !');

                    return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter la saison : '.$e->getMessage());
                }
            }
        }

        return $this->render('season/new.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    /**
     * Crée une saison depuis l'espace partenaire.
     *
     * Route dédiée aux partenaires dans le flux de création de leur stock virtuel.
     * Redirige vers la création de stock après enregistrement.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la création de stock
     */
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

    /**
     * Affiche les détails d'une saison.
     *
     * @param Season $season la saison à afficher
     *
     * @return Response la vue Twig de détail
     */
    #[Route('/show/{id}', name: 'app_season_show', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->render('season/show.html.twig', [
            'season' => $season,
        ]);
    }

    /**
     * Modifie une saison existante.
     *
     * @param Request                $request       requête HTTP
     * @param Season                 $season        la saison à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/edit/{id}', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                $this->addFlash('success', 'Saison mise à jour avec succès !');

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour la saison : '.$e->getMessage());
            }
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une saison de production.
     *
     * La suppression est bloquée si la saison est liée à au moins un stock,
     * afin de préserver l'intégrité de la traçabilité sanitaire réglementaire.
     * Un message d'erreur indique le nombre de stocks concernés.
     *
     * @param Request                $request       requête HTTP contenant le token CSRF
     * @param Season                 $season        la saison à supprimer
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la liste des saisons
     */
    #[Route('/{id}', name: 'app_season_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->getPayload()->getString('_token'))) {
            // Suppression bloquée si la saison est liée à des stocks (traçabilité)
            if (!$season->getStocks()->isEmpty()) {
                $this->addFlash('error', 'Impossible de supprimer cette saison : elle est liée à '.$season->getStocks()->count().' stock(s).');

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            }

            try {
                $entityManager->remove($season);
                $entityManager->flush();
                $this->addFlash('success', 'Saison supprimée avec succès !');

                return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer la saison : '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
    }
}
