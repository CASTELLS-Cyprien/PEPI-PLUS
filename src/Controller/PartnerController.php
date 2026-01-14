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

#[Route('/partner')]
final class PartnerController extends AbstractController
{
    #[Route(name: 'app_partner_index', methods: ['GET'])]
    public function index(Request $request, PartnerRepository $partnerRepository): Response
    {
        $form = $this->createForm(SearchType::class, null);
        $form->handleRequest($request);

        $searchTerm = $request->query->get('query');
        return $this->render('partner/index.html.twig', [
            'partners' => $partnerRepository->searchByTerm($searchTerm),
            'searchForm' => $form->createView(),
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
            $entityManager->persist($partner); // On persiste le nouveau partenaire
            $entityManager->flush();

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/gestion/{id}/show', name: 'app_partner_show', methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    #[Route('/gestion/{id}/edit', name: 'app_partner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }
    #[Route('/my-stock/liste', name: 'app_partner_myStock', methods: ['GET'])]
    public function MyStockindex(StockRepository $stockRepository): Response
    {
        if ($this->isGranted(['ROLE_ADMIN', 'ROLE_COLLABORATOR'])) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $partner = $user->getPartner();

        if (!$partner) {
            throw $this->createAccessDeniedException('Aucun profil partenaire associé à ce compte.');
        }

        return $this->render('partner/myStock.html.twig', [
            'stocks' => $stockRepository->findBy(['partner' => $partner]),
        ]);
    }

    #[Route('/my-stock/new', name: 'app_partner_newMyStock', methods: ['GET', 'POST'])]
    public function newMyStock(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
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
            $this->isGranted('ROLE_PARTNER') &&
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
            $entityManager->flush();

            return $this->redirectToRoute('app_partner_myStock', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/editMyStock.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/delete', name: 'app_partner_delete', methods: ['POST'])]
    public function delete(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partner->getId(), $request->request->get('_token'))) {
            $user = $partner->getUsers();



            $entityManager->remove($partner);
            $entityManager->flush();
            $this->addFlash('success', 'Partenaire supprimé et compte utilisateur désactivé.');
        }

        return $this->redirectToRoute('app_partner_index');
    }

    #[Route('/my-reservations/liste', name: 'app_partner_reservations', methods: ['GET'])]
    public function reservations(EntityManagerInterface $entityManager): Response
    {
        // On récupère l'utilisateur connecté (celui qui a le ROLE_PARTNER)
        $user = $this->getUser();

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $partner = $user->getPartner(); // On récupère l'entreprise de l'utilisateur

        if (!$partner) {
            $this->addFlash('error', 'Vous n\'êtes rattaché à aucune entreprise.');
            return $this->redirectToRoute('app_dashboard');
        }

        // On récupère les lignes de commande via un QueryBuilder directement
        // (Puisque vous n'avez rien dans le Repository OrderLine)
        $orderLines = $entityManager->getRepository(OrderLine::class)
            ->createQueryBuilder('ol')
            ->join('ol.stock', 's')
            ->where('s.partner = :partner')
            ->setParameter('partner', $partner)
            ->getQuery()
            ->getResult();

        return $this->render('partner/myReservation.html.twig', [
            'orderLines' => $orderLines,
        ]);
    }
}
