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

/**
 * Contrôleur de gestion des lignes de commande (OrderLine).
 *
 * Permet la consultation, la modification, la suppression et la mise à jour
 * des quantités des lignes de commande, avec restitution automatique du stock
 * lors de toute réduction ou suppression.
 *
 * Toute modification est bloquée si la commande parente est au statut
 * "Livrée" ou "Annulée" (commande verrouillée).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/order/line')]
final class OrderLineController extends AbstractController
{
    /**
     * Affiche la liste de toutes les lignes de commande.
     *
     * @param OrderLineRepository $orderLineRepository repository des lignes de commande
     *
     * @return Response la vue Twig avec toutes les lignes de commande
     */
    #[Route(name: 'app_order_line_index', methods: ['GET'])]
    public function index(OrderLineRepository $orderLineRepository): Response
    {
        return $this->render('order_line/index.html.twig', [
            'order_lines' => $orderLineRepository->findAll(),
        ]);
    }

    /**
     * Affiche le détail d'une ligne de commande.
     *
     * @param OrderLine $orderLine la ligne de commande à afficher
     *
     * @return Response la vue Twig de détail
     */
    #[Route('/{id}', name: 'app_order_line_show', methods: ['GET'])]
    public function show(OrderLine $orderLine): Response
    {
        return $this->render('order_line/show.html.twig', [
            'order_line' => $orderLine,
        ]);
    }

    /**
     * Modifie une ligne de commande via formulaire.
     *
     * @param Request                $request       requête HTTP
     * @param OrderLine              $orderLine     la ligne à modifier
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire ou redirection après modification
     */
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
                $this->addFlash('error', 'Impossible de mettre à jour la ligne de commande : '.$e->getMessage());
            }
        }

        return $this->render('order_line/edit.html.twig', [
            'order_line' => $orderLine,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une ligne de commande et restitue la quantité au stock d'origine.
     *
     * La suppression est interdite si la commande parente est au statut
     * "Livrée" ou "Annulée". En cas de suppression valide, la quantité
     * est restituée au stock associé avant la suppression de la ligne.
     *
     * @param Request                $request   requête HTTP contenant le token CSRF
     * @param OrderLine              $orderLine la ligne à supprimer
     * @param EntityManagerInterface $em        gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la page d'édition de la commande parente
     */
    #[Route('/{id}', name: 'app_order_line_delete', methods: ['POST'])]
    public function deleteLine(Request $request, OrderLine $orderLine, EntityManagerInterface $em): Response
    {
        $order = $orderLine->getPurchaseOrder();

        // Vérification : modification interdite sur une commande terminée ou annulée
        if ('Livrée' === $order->getStatus() || 'Annulée' === $order->getStatus()) {
            $this->addFlash('danger', 'Impossible de modifier le contenu d\'une commande terminée ou annulée.');

            return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$orderLine->getId(), $request->request->get('_token'))) {
            // Restitution de la quantité au stock source avant suppression de la ligne
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

    /**
     * Met à jour la quantité d'une ligne de commande existante.
     *
     * Le stock maximum autorisé est calculé comme la somme du stock disponible
     * et de la quantité déjà réservée par cette ligne.
     * Si la quantité est 0, la ligne est supprimée via redirection.
     * Si elle dépasse le maximum, elle est automatiquement ajustée.
     *
     * Modification interdite si la commande est "Livrée" ou "Annulée".
     *
     * @param OrderLine              $orderLine la ligne à mettre à jour
     * @param Request                $request   requête HTTP contenant `quantity`
     * @param EntityManagerInterface $em        gestionnaire d'entités Doctrine
     *
     * @return Response redirection vers la page d'édition de la commande parente
     */
    #[Route('/update-quantity/{id}', name: 'app_order_line_update_qty', methods: ['POST'])]
    public function updateQuantity(OrderLine $orderLine, Request $request, EntityManagerInterface $em): Response
    {
        $order = $orderLine->getPurchaseOrder();

        // Sécurité : modification impossible sur une commande verrouillée
        if (in_array($order->getStatus(), ['Livrée', 'Annulée'])) {
            $this->addFlash('danger', 'Modification impossible sur une commande terminée.');

            return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()]);
        }

        $quantity = (int) $request->request->get('quantity');
        $stock = $orderLine->getStock();

        // Stock maximum disponible = stock en rayon + quantité déjà réservée par cette ligne
        $maxAvailable = $stock->getQuantity() + $orderLine->getQuantity();

        if ($quantity <= 0) {
            // Quantité nulle : redirection vers la suppression de la ligne
            return $this->redirectToRoute('app_order_line_delete', ['id' => $orderLine->getId()], Response::HTTP_TEMPORARY_REDIRECT);
        }

        // Plafonnement au stock réellement disponible
        $finalQty = min($quantity, $maxAvailable);

        // Calcul de la différence pour ajuster le stock global en conséquence
        $diff = $finalQty - $orderLine->getQuantity();

        // Mise à jour de la quantité sur la ligne et ajustement du stock
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
