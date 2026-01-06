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

#[Route('/packaging')]
final class PackagingController extends AbstractController
{
    #[Route(name: 'app_packaging_index', methods: ['GET'])]
    public function index(PackagingRepository $packagingRepository): Response
    {
        return $this->render('packaging/index.html.twig', [
            'packagings' => $packagingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_packaging_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $packaging = new Packaging();
        $form = $this->createForm(PackagingType::class, $packaging);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($packaging);
            $entityManager->flush();

            return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
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
            $entityManager->flush();

            return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('packaging/edit.html.twig', [
            'packaging' => $packaging,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_packaging_delete', methods: ['POST'])]
    public function delete(Request $request, Packaging $packaging, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$packaging->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($packaging);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_packaging_index', [], Response::HTTP_SEE_OTHER);
    }
}
