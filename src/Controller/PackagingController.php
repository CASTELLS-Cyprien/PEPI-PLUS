<?php

namespace App\Controller;

use App\Entity\Packaging;
use App\Form\PackagingType;
use App\Form\SearchType;
use App\Repository\PackagingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des conditionnements (Packaging).
 *
 * Un conditionnement représente le mode de présentation d'un plant
 * (ex : GF400, racine nue, pot normalisé).
 *
 * Ce contrôleur gère le CRUD complet avec pagination et recherche textuelle.
 * La suppression est protégée : un conditionnement lié à des stocks
 * ne peut pas être supprimé afin de préserver l'intégrité des données.
 *
 * La redirection après création varie selon le rôle de l'utilisateur :
 * - Partenaire → page de création de son stock virtuel
 * - Admin/Collaborateur → liste des conditionnements
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/packaging')]
final class PackagingController extends AbstractController
{
    /**
     * Affiche la liste paginée des conditionnements avec recherche textuelle.
     *
     * La recherche s'effectue sur le champ `label` du conditionnement.
     * Résultats paginés à 10 par page.
     *
     * @param Request             $request             requête HTTP (paramètre GET `query`)
     * @param PackagingRepository $packagingRepository repository des conditionnements
     * @param PaginatorInterface  $paginator           service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de recherche
     */
    #[Route(name: 'app_packaging_index', methods: ['GET'])]
    public function index(Request $request, PackagingRepository $packagingRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Récupération du terme de recherche directement depuis l'URL via le paramètre 'query'
        $searchTerm = $request->query->get('query');
        $allPackagings = $packagingRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allPackagings,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('packaging/index.html.twig', [
            'searchForm' => $form->createView(),
            'packagings' => $pagination,
        ]);
    }

    /**
     * Crée un nouveau conditionnement.
     *
     * Comportement de redirection après création selon le rôle :
     * - **Partenaire** : redirige vers la création de son stock virtuel.
     * - **Admin/Collaborateur** : redirige vers la liste des conditionnements.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_packaging_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        // Redirection spécifique vers le stock partenaire après création
        if ($this->isGranted('ROLE_PARTNER')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($packaging);
                    $entityManager->flush();

                    $this->addFlash('success', 'Conditionnement ajoutée avec succès !');

                    return $this->redirectToRoute('app_partner_newMyStock', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le conditionnement : '.$e->getMessage());
                }
            }
        } // Redirection vers la liste des conditionnements pour les collaborateurs/admins
        elseif ($this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entityManager->persist($packaging);
                    $entityManager->flush();

                    $this->addFlash('success', 'Conditionnement ajoutée avec succès !');

                    return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter le conditionnement : '.$e->getMessage());
                }
            }
        }

        return $this->render('packaging/new.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'un conditionnement.
     *
     * @param Packaging $packaging le conditionnement à afficher
     *
     * @return Response la vue Twig de détail
     */
    #[Route('/show/{id}', name: 'app_packaging_show', methods: ['GET'])]
    public function show(Packaging $packaging): Response
    {
        return $this->render('packaging/show.html.twig', [
            'packaging' => $packaging,
        ]);
    }

    /**
     * Modifie un conditionnement existant.
     *
     * @param Request                $request       requête HTTP
     * @param Packaging              $packaging     le conditionnement à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection après modification
     */
    #[Route('/edit/{id}', name: 'app_packaging_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Packaging $packaging, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($packaging);
                $entityManager->flush();

                $this->addFlash('success', 'Conditionnement mis à jour avec succès !');

                return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour le conditionnement : '.$e->getMessage());
            }
        }

        return $this->render('packaging/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un conditionnement.
     *
     * La suppression est bloquée si le conditionnement est lié à au moins
     * un stock, afin de préserver l'intégrité des données de traçabilité.
     * Un message d'erreur indique le nombre de stocks concernés.
     *
     * @param Request                $request       requête HTTP contenant le token CSRF
     * @param Packaging              $packaging     le conditionnement à supprimer
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la liste des conditionnements
     */
    #[Route('/{id}', name: 'app_packaging_delete', methods: ['POST'])]
    public function delete(Request $request, Packaging $packaging, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$packaging->getId(), $request->getPayload()->getString('_token'))) {
            // Vérification : suppression bloquée si le conditionnement est lié à des stocks
            if (!$packaging->getStocks()->isEmpty()) {
                $this->addFlash('error', 'Impossible de supprimer ce conditionnement : il est lié à '.$packaging->getStocks()->count().' stock(s).');

                return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
            }

            try {
                $entityManager->remove($packaging);
                $entityManager->flush();
                $this->addFlash('success', 'Conditionnement supprimé avec succès !');

                return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le conditionnement : '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
    }
}
