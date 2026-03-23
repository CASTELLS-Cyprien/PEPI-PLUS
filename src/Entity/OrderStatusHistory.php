<?php

namespace App\Entity;

use App\Repository\OrderStatusHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une entrée dans l'historique des statuts d'une commande.
 *
 * Chaque changement de statut d'une commande ({@see Order}) génère une entrée
 * dans cet historique, permettant de tracer :
 * - Quel statut a été appliqué
 * - Qui a effectué le changement
 * - À quelle date et heure
 *
 * Cet historique est immuable une fois créé : il ne doit jamais être modifié,
 * uniquement consulté. Il constitue l'audit trail complet du cycle de vie
 * de chaque commande, essentiel pour la traçabilité sanitaire réglementaire.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: OrderStatusHistoryRepository::class)]
class OrderStatusHistory
{
    /**
     * Identifiant unique de l'entrée d'historique (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Statut de la commande au moment de ce changement.
     *
     * Valeurs possibles : "Réservation", "Validé", "Livrée", "Annulée".
     */
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * Date et heure exactes du changement de statut.
     *
     * Définie automatiquement lors de la création de l'entrée d'historique.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Utilisateur ayant effectué ce changement de statut.
     *
     * Nullable : l'historique est conservé même si l'utilisateur est supprimé.
     */
    #[ORM\ManyToOne(inversedBy: 'orderStatusHistories')]
    private ?User $changedBy = null;

    /**
     * Commande à laquelle appartient cette entrée d'historique.
     */
    #[ORM\ManyToOne(inversedBy: 'orderStatusHistories')]
    private ?Order $PurchaseOrder = null;

    /**
     * Retourne l'identifiant unique de l'entrée d'historique.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le statut enregistré dans cette entrée d'historique.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut de cette entrée d'historique.
     *
     * @param string $status statut au moment du changement
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne la date et l'heure du changement de statut.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit la date et l'heure du changement de statut.
     *
     * @param \DateTimeImmutable $createdAt horodatage du changement
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Retourne l'utilisateur ayant effectué ce changement de statut.
     *
     * @return User|null null si l'utilisateur a été supprimé
     */
    public function getChangedBy(): ?User
    {
        return $this->changedBy;
    }

    /**
     * Définit l'utilisateur ayant effectué ce changement de statut.
     *
     * @param User|null $changedBy L'utilisateur responsable du changement
     */
    public function setChangedBy(?User $changedBy): static
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    /**
     * Retourne la commande associée à cette entrée d'historique.
     */
    public function getPurchaseOrder(): ?Order
    {
        return $this->PurchaseOrder;
    }

    /**
     * Définit la commande associée à cette entrée d'historique.
     *
     * @param Order|null $PurchaseOrder la commande parente
     */
    public function setPurchaseOrder(?Order $PurchaseOrder): static
    {
        $this->PurchaseOrder = $PurchaseOrder;

        return $this;
    }
}
