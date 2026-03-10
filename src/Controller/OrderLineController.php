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
    public function deleteLine(Request $request, OrderLine $orderLine, EntityManagerInterface $em): Response
    {
        $order = $orderLine->getPurchaseOrder();

        if ($order->getStatus() === 'Livrée' || $order->getStatus() === 'Annulée') {
            $this->addFlash('danger', 'Impossible de modifier le contenu d\'une commande terminée ou annulée.');
            return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
        }

        if ($this->isCsrfTokenValid('delete' . $orderLine->getId(), $request->request->get('_token'))) {
            $stock = $orderLine->getStock();
            if ($stock) {
                $stock->setQuantity($stock->getQuantity() + $orderLine->getQuantity());
            }
            $em->remove($orderLine);
            $em->flush();
            $this->addFlash('success', 'Article retiré et stock rendu.');
        }

        return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
    }
    #[Route('/update-quantity/{id}', name: 'app_order_line_update_qty', methods: ['POST'])]
    public function updateQuantity(OrderLine $orderLine, Request $request, EntityManagerInterface $em): Response
    {
        $order = $orderLine->getPurchaseOrder();

        // Sécurité : Vérifier que la commande n'est pas verrouillée
        if (in_array($order->getStatus(), ['Livrée', 'Annulée'])) {
            $this->addFlash('danger', 'Modification impossible sur une commande terminée.');
            return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
        }

        $quantity = (int) $request->request->get('quantity');
        $stock = $orderLine->getStock();

        // Le stock total disponible est le stock en rayon + ce qui est déjà pris par cette ligne
        $maxAvailable = $stock->getQuantity() + $orderLine->getQuantity();

        if ($quantity <= 0) {
            // Redirection vers la suppression si quantité 0 (comme le panier)
            return $this->redirectToRoute('app_order_line_delete', ['id' => $orderLine->getId()], Response::HTTP_TEMPORARY_REDIRECT);
        }

        // On s'assure de ne pas dépasser le stock réel
        $finalQty = min($quantity, $maxAvailable);

        // Calcul de la différence pour ajuster le stock global
        $diff = $finalQty - $orderLine->getQuantity();

        // Mise à jour
        $orderLine->setQuantity($finalQty);
        $stock->setQuantity($stock->getQuantity() - $diff);

        $order->setUpdatedAt(new \DateTimeImmutable());
        $order->setUpdatedBy($this->getUser());

        $em->flush();

        if ($quantity > $maxAvailable) {
            $this->addFlash('warning', 'Quantité ajustée au maximum disponible.');
        }

        return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
    }
}
