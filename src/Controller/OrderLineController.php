<?php

namespace App\Controller;

use App\Entity\OrderLine;
use App\Form\OrderLineType;
use App\Repository\OrderLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order/line')]
final class OrderLineController extends AbstractController
{
    #[Route(name: 'app_order_line_index', methods: ['GET'])]
    public function index(OrderLineRepository $orderLineRepository): Response
    {
        return $this->render('order_line/index.html.twig', [
            'order_lines' => $orderLineRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_line_show', methods: ['GET'])]
    public function show(OrderLine $orderLine): Response
    {
        return $this->render('order_line/show.html.twig', [
            'order_line' => $orderLine,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_order_line_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrderLine $orderLine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderLineType::class, $orderLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($orderLine);
                $entityManager->flush();

                $this->addFlash('success', 'Ligne de commande mise à jour avec succès !');

                return $this->redirectToRoute('app_order_line_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible de mettre à jour la ligne de commande : ' . $e->getMessage());
            }
        }

        return $this->render('order_line/edit.html.twig', [
            'order_line' => $orderLine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_line_delete', methods: ['POST'])]
    public function delete(Request $request, OrderLine $orderLine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $orderLine->getId(), $request->getPayload()->getString('_token'))) {
        }
        try {
            $entityManager->remove($orderLine);
            $entityManager->flush();

            $this->addFlash('success', 'Ligne de commande supprimée avec succès !');

            return $this->redirectToRoute('app_order_line_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de supprimer la ligne de commande : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_order_line_index', [], Response::HTTP_SEE_OTHER);
    }
}
