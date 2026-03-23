<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une ligne de commande (OrderLine).
 *
 * Une ligne de commande est le lien entre une commande ({@see Order})
 * et un stock ({@see Stock}). Elle enregistre la quantité réservée
 * pour un plant donné au moment de la validation du panier.
 *
 * Comportement clé :
 * - La relation vers Stock est configurée avec `onDelete: SET NULL`.
 *   Si un stock est supprimé, la ligne est conservée avec `stock = null`
 *   pour préserver l'historique des commandes (intégrité des données).
 * - La quantité est décrémentée du stock à la création de la ligne
 *   et restituée si la ligne ou la commande est annulée.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    /**
     * Identifiant unique de la ligne de commande (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Quantité de plants réservée dans cette ligne de commande.
     *
     * Décrémentée du stock à la création et restituée en cas d'annulation
     * ou de suppression de la ligne.
     */
    #[ORM\Column]
    private ?int $quantity = null;

    /**
     * Stock source de cette ligne de commande.
     *
     * Peut être null si le stock a été supprimé (onDelete: SET NULL).
     * Cette configuration permet de conserver l'historique des commandes
     * même après la suppression d'un stock du référentiel.
     */
    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Stock $stock = null;

    /**
     * Commande parente à laquelle appartient cette ligne.
     */
    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    private ?Order $PurchaseOrder = null;

    /**
     * Retourne l'identifiant unique de la ligne de commande.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la quantité réservée dans cette ligne.
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Définit la quantité réservée dans cette ligne.
     *
     * @param int $quantity quantité à réserver (doit être > 0)
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Retourne le stock source de cette ligne de commande.
     *
     * @return Stock|null null si le stock a été supprimé (onDelete: SET NULL)
     */
    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    /**
     * Définit le stock source de cette ligne de commande.
     *
     * @param Stock|null $stock le stock à associer, ou null pour dissocier
     */
    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Retourne la commande parente de cette ligne.
     */
    public function getPurchaseOrder(): ?Order
    {
        return $this->PurchaseOrder;
    }

    /**
     * Définit la commande parente de cette ligne.
     *
     * @param Order|null $PurchaseOrder la commande à associer, ou null pour dissocier
     */
    public function setPurchaseOrder(?Order $PurchaseOrder): static
    {
        $this->PurchaseOrder = $PurchaseOrder;

        return $this;
    }
}
