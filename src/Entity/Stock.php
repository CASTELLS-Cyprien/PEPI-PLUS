<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité centrale représentant un stock de plants (Stock).
 *
 * Le champ `partner` est la clé architecturale qui distingue deux types de stocks :
 * - **Stock réel interne** (Pépi+) : `partner = NULL`
 * - **Stock virtuel partenaire** : `partner` renseigné avec l'entité partenaire
 *
 * Un stock est identifié de manière unique par la combinaison :
 * plant + conditionnement + saison + partner.
 * Si un doublon est détecté lors d'une création interne, les quantités sont
 * automatiquement cumulées (RG-14).
 *
 * La traçabilité complète est assurée par :
 * - `created_by` : utilisateur ayant créé l'entrée (non modifié ensuite)
 * - `updated_by` : dernier utilisateur ayant modifié l'entrée
 * - `created_at` / `updated_at` : horodatages correspondants
 *
 * La quantité est décrémentée à la réservation et restituée à l'annulation.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    /**
     * Identifiant unique du stock (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Quantité actuellement disponible dans ce stock.
     *
     * Décrémentée automatiquement lors d'une réservation (création d'OrderLine)
     * et restituée lors d'une annulation ou d'une suppression de ligne.
     * Un stock peut atteindre 0 mais ne peut pas être négatif.
     */
    #[ORM\Column]
    private ?int $quantity = null;

    /**
     * Plant associé à ce stock.
     *
     * Référence obligatoire pour la traçabilité sanitaire.
     * Identifie quel végétal est stocké (nom latin + commun).
     */
    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Plant $plant = null;

    /**
     * Lignes de commande utilisant ce stock.
     *
     * Configuré avec `onDelete: SET NULL` dans OrderLine pour préserver
     * l'historique des commandes si ce stock est supprimé.
     *
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'stock')]
    private Collection $orderLines;

    /**
     * Conditionnement associé à ce stock.
     *
     * Définit le mode de présentation du plant (ex : GF400, racine nue).
     */
    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Packaging $packaging = null;

    /**
     * Saison de production associée à ce stock.
     *
     * Indispensable pour la traçabilité sanitaire réglementaire.
     * Permet de connaître l'âge du plant au moment de la vente (RG-12).
     */
    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Season $season = null;

    /**
     * Partenaire fournisseur associé à ce stock.
     *
     * C'est le champ discriminant de l'architecture :
     * - `NULL` = stock réel interne Pépi+ (production propre)
     * - Renseigné = stock virtuel d'un partenaire fournisseur
     */
    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Partner $partner = null;

    /**
     * Dernier utilisateur ayant modifié ce stock.
     *
     * Mis à jour à chaque modification de la quantité ou des métadonnées.
     */
    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?User $updated_by = null;

    /**
     * Date et heure de création de l'entrée de stock.
     *
     * Définie automatiquement lors de la création et jamais modifiée ensuite.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * Date et heure de la dernière mise à jour du stock.
     *
     * Mise à jour à chaque décrémentation, restitution ou modification manuelle.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * Utilisateur ayant créé cette entrée de stock.
     *
     * Renseigné à la création uniquement, non modifié ensuite.
     * Permet de tracer l'origine de chaque entrée de stock.
     */
    #[ORM\ManyToOne]
    private ?User $created_by = null;

    /**
     * Initialise la collection des lignes de commande.
     */
    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique du stock.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la quantité disponible dans ce stock.
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Définit la quantité disponible dans ce stock.
     *
     * @param int $quantity nouvelle quantité (doit être >= 0)
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Retourne le plant associé à ce stock.
     */
    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    /**
     * Définit le plant associé à ce stock.
     *
     * @param Plant|null $plant le plant à associer
     */
    public function setPlant(?Plant $plant): static
    {
        $this->plant = $plant;

        return $this;
    }

    /**
     * Retourne la collection des lignes de commande utilisant ce stock.
     *
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    /**
     * Ajoute une ligne de commande à ce stock.
     *
     * Établit également la relation inverse sur la ligne.
     *
     * @param OrderLine $orderLine la ligne à associer
     */
    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setStock($this);
        }

        return $this;
    }

    /**
     * Retire une ligne de commande de ce stock.
     *
     * @param OrderLine $orderLine la ligne à dissocier
     */
    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // Nettoyage du côté propriétaire de la relation
            if ($orderLine->getStock() === $this) {
                $orderLine->setStock(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le conditionnement associé à ce stock.
     */
    public function getPackaging(): ?Packaging
    {
        return $this->packaging;
    }

    /**
     * Définit le conditionnement associé à ce stock.
     *
     * @param Packaging|null $packaging le conditionnement à associer
     */
    public function setPackaging(?Packaging $packaging): static
    {
        $this->packaging = $packaging;

        return $this;
    }

    /**
     * Retourne la saison de production associée à ce stock.
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * Définit la saison de production associée à ce stock.
     *
     * @param Season|null $season la saison à associer
     */
    public function setSeason(?Season $season): static
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Retourne le partenaire associé à ce stock.
     *
     * @return Partner|null null si c'est un stock interne Pépi+
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * Définit le partenaire associé à ce stock.
     *
     * Passer `null` pour un stock interne Pépi+.
     * Passer une entité Partner pour un stock virtuel partenaire.
     *
     * @param Partner|null $partner le partenaire, ou null pour stock interne
     */
    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Retourne le dernier utilisateur ayant modifié ce stock.
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updated_by;
    }

    /**
     * Définit le dernier utilisateur ayant modifié ce stock.
     *
     * @param User|null $updated_by L'utilisateur responsable de la modification
     */
    public function setUpdatedBy(?User $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * Retourne la date de création de l'entrée de stock.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Définit la date de création de l'entrée de stock.
     *
     * @param \DateTimeImmutable $created_at horodatage de création
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Retourne la date de dernière mise à jour du stock.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Définit la date de dernière mise à jour du stock.
     *
     * @param \DateTimeImmutable $updated_at horodatage de la dernière modification
     */
    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Retourne l'utilisateur ayant créé cette entrée de stock.
     */
    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    /**
     * Définit l'utilisateur ayant créé cette entrée de stock.
     *
     * Renseigné uniquement à la création, ne doit pas être modifié ensuite.
     *
     * @param User|null $created_by L'utilisateur créateur
     */
    public function setCreatedBy(?User $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }
}
