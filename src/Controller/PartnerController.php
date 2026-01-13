<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\OrderLine;

#[Route('/partner')]
final class PartnerController extends AbstractController
{
    #[Route(name: 'app_partner_index', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository): Response
    {
        return $this->render('partner/index.html.twig', [
            'partners' => $partnerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_partner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partner = new Partner();
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partner);
            $entityManager->flush();

            return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partner_show', methods: ['GET'])]
    public function show(Partner $partner): Response
    {
        return $this->render('partner/show.html.twig', [
            'partner' => $partner,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_partner_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_partner_delete', methods: ['POST'])]
    public function delete(Request $request, Partner $partner, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partner->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($partner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_partner_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/my-reservations/liste', name: 'app_partner_reservations', methods: ['GET'])]
    public function reservations(EntityManagerInterface $entityManager): Response
    {
        // On récupère l'utilisateur connecté (celui qui a le ROLE_PARTNER)
        $user = $this->getUser();

        // On cherche le partenaire lié à cet utilisateur
        // Note : Votre entité Partner a une propriété $user
        $partner = $entityManager->getRepository(Partner::class)->findOneBy(['user' => $user]);

        if (!$partner) {
            throw $this->createNotFoundException('Aucun profil partenaire trouvé.');
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

        return $this->render('stock/indexMyReservation.html.twig', [
            'orderLines' => $orderLines,
        ]);
    }
}
