<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un partenaire fournisseur (Partner).
 *
 * Un partenaire est une entreprise externe qui met à disposition de Pépi+
 * ses plants sous forme de stock virtuel. Ses plants sont intégrés dans
 * le stock global de l'application et peuvent être commandés par les collaborateurs.
 *
 * Relations clés :
 * - Un partenaire peut avoir plusieurs utilisateurs ({@see User}) associés (rôle PARTNER).
 * - Un partenaire peut avoir plusieurs stocks virtuels ({@see Stock}) déclarés.
 *
 * Un partenaire ne peut pas être supprimé tant que des stocks ou des utilisateurs
 * lui sont associés (contrainte d'intégrité référentielle).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner
{
    /**
     * Identifiant unique du partenaire (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Raison sociale de l'entreprise partenaire.
     *
     * Utilisée comme identifiant visuel dans toute l'application
     * (listes, badges, sélecteurs de formulaire).
     */
    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    /**
     * Coordonnées complètes du partenaire (format libre).
     *
     * Peut contenir l'adresse, le téléphone, l'email de contact, etc.
     * Stocké en TEXT pour permettre des contenus longs.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $contactDetails = null;

    /**
     * Stocks virtuels déclarés par ce partenaire.
     *
     * Chaque stock représente un plant disponible à la commande
     * depuis le stock global de Pépi+.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'partner')]
    private Collection $stocks;

    /**
     * Date d'enregistrement du partenaire dans le système.
     *
     * Définie automatiquement lors de la création dans {@see PartnerController::new()}.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * Utilisateurs (comptes PARTNER) associés à ce partenaire.
     *
     * Un partenaire peut avoir plusieurs comptes utilisateurs.
     * Chaque utilisateur avec le rôle PARTNER doit être rattaché à un partenaire.
     *
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'partner')]
    private Collection $users;

    /**
     * Initialise les collections stocks et users.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique du partenaire.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la raison sociale du partenaire.
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * Définit la raison sociale du partenaire.
     *
     * @param string $companyName nom de l'entreprise partenaire
     */
    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Retourne les coordonnées de contact du partenaire.
     */
    public function getContactDetails(): ?string
    {
        return $this->contactDetails;
    }

    /**
     * Définit les coordonnées de contact du partenaire.
     *
     * @param string $contactDetails Coordonnées en format libre (adresse, tél, email...).
     */
    public function setContactDetails(string $contactDetails): static
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    /**
     * Retourne la collection des stocks virtuels déclarés par ce partenaire.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Ajoute un stock virtuel à ce partenaire.
     *
     * Établit également la relation inverse sur le stock.
     *
     * @param Stock $stock le stock à associer
     */
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setPartner($this);
        }

        return $this;
    }

    /**
     * Retire un stock virtuel de ce partenaire.
     *
     * @param Stock $stock le stock à dissocier
     */
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // Nettoyage du côté propriétaire de la relation
            if ($stock->getPartner() === $this) {
                $stock->setPartner(null);
            }
        }

        return $this;
    }

    /**
     * Retourne la date d'enregistrement du partenaire.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Définit la date d'enregistrement du partenaire.
     *
     * @param \DateTimeImmutable $created_at date de création du partenaire dans le système
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Retourne la collection des utilisateurs associés à ce partenaire.
     *
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Ajoute un utilisateur à ce partenaire.
     *
     * Établit également la relation inverse sur l'utilisateur.
     *
     * @param User $user L'utilisateur à associer (doit avoir le rôle PARTNER)
     */
    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setPartner($this);
        }

        return $this;
    }

    /**
     * Retire un utilisateur de ce partenaire.
     *
     * @param User $user L'utilisateur à dissocier
     */
    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // Nettoyage du côté propriétaire de la relation
            if ($user->getPartner() === $this) {
                $user->setPartner(null);
            }
        }

        return $this;
    }
}
