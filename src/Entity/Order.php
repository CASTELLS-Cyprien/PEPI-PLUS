<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une commande de plants (Order).
 *
 * Une commande est créée lors de la validation du panier et passe par
 * différents statuts tout au long de son cycle de vie :
 * Réservation → Validé → Livrée ou Annulée.
 *
 * Chaque changement de statut est tracé dans {@see OrderStatusHistory}.
 * Chaque article commandé est représenté par une {@see OrderLine}.
 *
 * Le numéro de commande est généré au format CMD-YYYY-NNN.
 * Le stock est décrémenté dès la création (statut "Réservation")
 * et restitué en cas d'annulation.
 *
 * La table est nommée `order` (entre backticks car mot réservé SQL).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    /**
     * Identifiant unique de la commande (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Numéro de commande unique au format CMD-YYYY-NNN.
     *
     * Généré automatiquement lors de la validation du panier
     * dans {@see CartController::validate()}.
     */
    #[ORM\Column(length: 255)]
    private ?string $orderNumber = null;

    /**
     * Statut actuel de la commande.
     *
     * Valeurs possibles : "Réservation", "Validé", "Livrée", "Annulée".
     * Le flux autorisé est : Réservation → Validé → Livrée ou Annulée.
     * Une commande "Livrée" ne peut plus être modifiée.
     */
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * Date et heure de création de la commande.
     *
     * Définie automatiquement lors de la validation du panier.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Collaborateur Pépi+ ayant créé la commande.
     *
     * Nullable : la commande subsiste si l'utilisateur est supprimé.
     * La suppression de l'utilisateur ne doit pas effacer l'historique des commandes.
     */
    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $collaborator = null;

    /**
     * Lignes de commande associées à cette commande.
     *
     * Chaque ligne représente un plant réservé avec sa quantité.
     *
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'PurchaseOrder')]
    private Collection $orderLines;

    /**
     * Date et heure de la dernière mise à jour de la commande.
     *
     * Mise à jour à chaque changement de statut ou modification de ligne.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * Utilisateur ayant effectué la dernière modification.
     *
     * Non nullable : toute modification doit être attribuée à un utilisateur
     * pour assurer la traçabilité complète des actions.
     */
    #[ORM\ManyToOne(inversedBy: 'userOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $updated_by = null;

    /**
     * Historique chronologique des changements de statut de la commande.
     *
     * Chaque entrée enregistre le nouveau statut, l'auteur du changement
     * et la date/heure de l'opération. Cascade persist pour enregistrement automatique.
     *
     * @var Collection<int, OrderStatusHistory>
     */
    #[ORM\OneToMany(targetEntity: OrderStatusHistory::class, mappedBy: 'PurchaseOrder', cascade: ['persist'])]
    private Collection $orderStatusHistories;

    /**
     * Initialise les collections orderLines et orderStatusHistories.
     */
    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
        $this->orderStatusHistories = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique de la commande.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le numéro de commande au format CMD-YYYY-NNN.
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * Définit le numéro de commande.
     *
     * @param string $orderNumber numéro au format CMD-YYYY-NNN
     */
    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Retourne le statut actuel de la commande.
     *
     * @return string|null valeur parmi : "Réservation", "Validé", "Livrée", "Annulée"
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Définit le statut de la commande.
     *
     * @param string $status nouveau statut de la commande
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne la date de création de la commande.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création de la commande.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Retourne le collaborateur ayant créé la commande.
     *
     * @return User|null null si l'utilisateur a été supprimé
     */
    public function getCollaborator(): ?User
    {
        return $this->collaborator;
    }

    /**
     * Définit le collaborateur ayant créé la commande.
     */
    public function setCollaborator(?User $collaborator): static
    {
        $this->collaborator = $collaborator;

        return $this;
    }

    /**
     * Retourne la collection des lignes de commande.
     *
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    /**
     * Ajoute une ligne de commande à la commande.
     *
     * Établit également la relation inverse (PurchaseOrder) sur la ligne
     * pour maintenir la cohérence des deux côtés de la relation.
     *
     * @param OrderLine $orderLine la ligne à ajouter
     */
    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setPurchaseOrder($this);
        }

        return $this;
    }

    /**
     * Retire une ligne de commande de la commande.
     *
     * Remet la relation PurchaseOrder à null sur la ligne si elle pointait
     * encore vers cette commande (nettoyage du côté propriétaire).
     *
     * @param OrderLine $orderLine la ligne à retirer
     */
    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // Nettoyage du côté propriétaire de la relation
            if ($orderLine->getPurchaseOrder() === $this) {
                $orderLine->setPurchaseOrder(null);
            }
        }

        return $this;
    }

    /**
     * Retourne la date de dernière mise à jour.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Définit la date de dernière mise à jour.
     */
    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Retourne l'utilisateur ayant effectué la dernière modification.
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updated_by;
    }

    /**
     * Définit l'utilisateur ayant effectué la dernière modification.
     */
    public function setUpdatedBy(?User $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * Retourne la collection de l'historique des statuts.
     *
     * @return Collection<int, OrderStatusHistory>
     */
    public function getOrderStatusHistories(): Collection
    {
        return $this->orderStatusHistories;
    }

    /**
     * Ajoute une entrée dans l'historique des statuts.
     *
     * Établit également la relation inverse (PurchaseOrder) sur l'historique
     * pour maintenir la cohérence des deux côtés de la relation.
     *
     * @param OrderStatusHistory $statusHistory L'entrée d'historique à ajouter
     */
    public function addOrderStatusHistory(OrderStatusHistory $statusHistory): static
    {
        if (!$this->orderStatusHistories->contains($statusHistory)) {
            $this->orderStatusHistories->add($statusHistory);
            $statusHistory->setPurchaseOrder($this);
        }

        return $this;
    }

    /**
     * Retire une entrée de l'historique des statuts.
     *
     * @param OrderStatusHistory $statusHistory L'entrée à retirer
     */
    public function removeStatusHistory(OrderStatusHistory $statusHistory): static
    {
        if ($this->orderStatusHistories->removeElement($statusHistory)) {
            // Nettoyage du côté propriétaire de la relation
            if ($statusHistory->getPurchaseOrder() === $this) {
                $statusHistory->setPurchaseOrder(null);
            }
        }

        return $this;
    }
}
