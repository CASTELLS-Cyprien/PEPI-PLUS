<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Stock;
use App\Entity\User;
use App\Form\PartnerType;
use App\Form\SearchType;
use App\Form\StockType;
use App\Repository\OrderRepository;
use App\Repository\PartnerRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des partenaires fournisseurs et de l'espace partenaire.
 *
 * Ce contrôleur regroupe deux ensembles de fonctionnalités distincts :
 *
 * **1. Gestion des partenaires (Admin/Collaborateur)**
 * - CRUD des entreprises partenaires fournisseurs
 * - Consultation de la liste avec recherche et pagination
 *
 * **2. Espace partenaire personnel (Partner)**
 * - Gestion du stock virtuel personnel (CRUD avec pagination)
 * - Consultation des réservations portant sur ses stocks
 *
 * Sécurité :
 * - Un partenaire ne peut accéder qu'à son propre stock (RG-10).
 * - La suppression d'un stock est bloquée si le stock n'appartient pas
 *   au partenaire connecté.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/partner')]
final class PartnerController extends AbstractController
{
    /**
     * Affiche la liste paginée des partenaires avec recherche par nom de société.
     *
     * Résultats paginés à 8 par page, triés par nom de société alphabétiquement.
     *
     * @param Request            $request           requête HTTP (paramètre GET `query`)
     * @param PartnerRepository  $partnerRepository repository des partenaires
     * @param PaginatorInterface $paginator         service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de recherche
     */
    #[Route(name: 'app_partner_index', methods: ['GET'])]
    public function index(Request $request, PartnerRepository $partnerRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class, null);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        $allPartners = $partnerRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allPartners,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('partner/index.html.twig', [
            'searchForm' => $form->createView(),
            'partners' => $pagination,
        ]);
    }

    /**
     * Crée un nouveau partenaire fournisseur.
     *
     * Initialise automatiquement la date de création avant la persistance.
     * Après création, redirige vers la liste des partenaires.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/gestion/new', name: 'app_partner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partner = new Partner();
        // Initialisation automatique de la date de création (traçabilité)
        $partner->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($partner);
                $entityManager->flush();

                $this->addFlash('success', 'Partenaire ajouté avec succès !');

                return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter le partenaire : '.$e->getMessage());
            }

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    /**
     * Affiche la fiche détaillée d'un partenaire.
     *
     * @param Partner $partner le partenaire à afficher
     *
     * @return Response la vue Twig de détail (société, contacts, utilisateurs liés, stocks)
     */
    #[Route('/show/{id}', name: 'app_partner_show', methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    /**
     * Modifie les informations d'un partenaire fournisseur.
     *
     * @param Request                $request       requête HTTP
     * @param Partner                $partner       le partenaire à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/edit/{id}', name: 'app_partner_edit', methods: ['GET', 'POST'])]
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
                    $this->addFlash('error', 'Impossible de mettre à jour le partenaire : '.$e->getMessage());
                }
            }

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // ESPACE PARTENAIRE : Gestion du stock virtuel personnel
    // ──────────────────────────────────────────────────────────────

    /**
     * Affiche la liste paginée du stock virtuel personnel du partenaire connecté.
     *
     * Filtre automatiquement les stocks sur le partenaire de l'utilisateur connecté.
     * La recherche s'effectue sur le nom latin et le nom commun des plants.
     * Résultats paginés à 8 par page.
     *
     * @param Request            $request         requête HTTP (paramètre GET `query`)
     * @param StockRepository    $stockRepository repository des stocks
     * @param PaginatorInterface $paginator       service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste des stocks du partenaire
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *                                                                          Si l'utilisateur connecté n'a pas de profil Partner associé
     */
    #[Route('/my-stock/liste', name: 'app_partner_myStock', methods: ['GET'])]
    public function MyStockIndex(Request $request, StockRepository $stockRepository, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $partner = $user->getPartner();

        // Vérification que l'utilisateur a bien un profil Partner associé
        if (!$partner) {
            throw $this->createAccessDeniedException('Aucun profil partenaire associé à ce compte.');
        }

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');

        // Construction du QueryBuilder filtré sur le partenaire connecté
        $queryBuilder = $stockRepository->createQueryBuilder('s')
            ->where('s.partner = :partner')
            ->setParameter('partner', $partner);

        // Ajout du filtre de recherche textuelle sur le plant si un terme est saisi
        if ($searchTerm) {
            $queryBuilder->leftJoin('s.plant', 'p')
                ->andWhere('p.latinName LIKE :term OR p.commonName LIKE :term')
                ->setParameter('term', '%'.$searchTerm.'%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('partner/myStock.html.twig', [
            'searchForm' => $form->createView(),
            'stocks' => $pagination,
        ]);
    }

    /**
     * Crée une nouvelle entrée de stock virtuel pour le partenaire connecté.
     *
     * Les informations de traçabilité (partner, created_by, updated_by, dates)
     * sont renseignées automatiquement à partir de la session utilisateur.
     * Le partenaire n'a qu'à saisir le plant, le conditionnement, la saison
     * et la quantité disponible.
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste des stocks
     */
    #[Route('/my-stock/new', name: 'app_partner_newMyStock', methods: ['GET', 'POST'])]
    public function newMyStock(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        /** @var User $user */
        $user = $this->getUser();
        $now = new \DateTimeImmutable();

        // Pré-remplissage automatique des informations de traçabilité
        $stock->setPartner($user->getPartner());
        $stock->setCreatedAt($now);
        $stock->setUpdatedAt($now);
        $stock->setCreatedBy($user);
        $stock->setUpdatedBy($user);

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($stock);
                $entityManager->flush();

                $this->addFlash('success', 'Nouveau stock ajouté avec succès !');

                return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter le stock : '.$e->getMessage());
            }
        }

        return $this->render('partner/newMyStock.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'un stock virtuel du partenaire.
     *
     * Vérifie que le stock appartient au partenaire connecté avant l'affichage.
     * Redirige vers la liste des stocks si le partenaire tente d'accéder
     * à un stock qui ne lui appartient pas (RG-10).
     *
     * @param Stock $stock le stock à afficher
     *
     * @return Response la vue Twig de détail ou redirection
     */
    #[Route('/my-stock/show/{id}', name: 'app_partner_showMyStock', methods: ['GET'])]
    public function showMyStock(Stock $stock): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Contrôle d'accès : un partenaire ne peut voir que son propre stock (RG-10)
        if (
            $this->isGranted('ROLE_ADMIN', 'ROLE_COLLABORATOR')
            && $stock->getPartner() !== $user->getPartner()
        ) {
            return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/showMyStock.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * Modifie un stock virtuel existant du partenaire.
     *
     * @param Request                $request       requête HTTP
     * @param Stock                  $stock         le stock à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/my-stock/edit/{id}', name: 'app_partner_editMyStock', methods: ['GET', 'POST'])]
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
                $this->addFlash('error', 'Impossible de mettre à jour le stock : '.$e->getMessage());
            }
        }

        return $this->render('partner/editMyStock.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les réservations (commandes) portant sur les stocks du partenaire connecté.
     *
     * Utilise {@see OrderRepository::searchOrdersByPartner()} pour récupérer
     * les commandes regroupées et dédupliquées, avec recherche textuelle optionnelle
     * sur le plant, le numéro de commande, le conditionnement ou la saison.
     *
     * @param Request            $request           requête HTTP (paramètre GET `query`)
     * @param OrderRepository    $orderRepo         repository des commandes
     * @param PartnerRepository  $partnerRepository repository des partenaires
     * @param PaginatorInterface $paginator         service de pagination
     *
     * @return Response la vue Twig des réservations du partenaire
     */
    #[Route('/my-reservations/liste', name: 'app_partner_reservations', methods: ['GET'])]
    public function reservations(Request $request, OrderRepository $orderRepo, PartnerRepository $partnerRepository, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $partner = $user->getPartner();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        $allPartners = $partnerRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allPartners,
            $request->query->getInt('page', 1),
            8
        );

        // Récupération des commandes liées aux stocks de ce partenaire (dédupliquées)
        $orders = $orderRepo->searchOrdersByPartner($partner, $searchTerm);

        return $this->render('partner/myReservation.html.twig', [
            'orders' => $orders,
            'searchForm' => $form->createView(),
            'stocks' => $pagination,
        ]);
    }

    /**
     * Supprime un stock virtuel appartenant au partenaire connecté.
     *
     * Vérifie que le stock appartient bien au partenaire avant la suppression.
     * Si le stock appartient à un autre partenaire, l'accès est refusé (RG-10).
     * Utilise une validation CSRF pour sécuriser la requête POST.
     *
     * @param Request                $request       requête HTTP contenant le token CSRF
     * @param Stock                  $stock         le stock à supprimer
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la liste des stocks du partenaire
     */
    #[Route('/my-stock/delete/{id}', name: 'app_partner_deleteMyStock', methods: ['POST'])]
    public function deleteMyStock(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Vérification que le stock appartient bien au partenaire connecté (RG-10)
        if ($stock->getPartner() !== $user->getPartner()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce stock.');

            return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$stock->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $entityManager->remove($stock);
                $entityManager->flush();
                $this->addFlash('success', 'Stock supprimé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le stock : '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
    }
}
