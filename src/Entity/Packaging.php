<?php

namespace App\Entity;

use App\Repository\PackagingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un conditionnement de plant (Packaging).
 *
 * Un conditionnement définit le mode de présentation d'un plant au moment
 * de sa vente (ex : GF400, racine nue, pot 1L, pot normalisé).
 * Il fait partie du référentiel commun partagé entre Pépi+ et ses partenaires.
 *
 * Un conditionnement ne peut pas être supprimé s'il est lié à des stocks
 * afin de préserver l'intégrité des données de traçabilité.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: PackagingRepository::class)]
class Packaging
{
    /**
     * Identifiant unique du conditionnement (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Libellé du conditionnement (ex : "GF400", "Racine nue", "Pot 1L").
     *
     * Champ obligatoire, affiché dans les formulaires de création de stock
     * et dans les tableaux de l'application.
     */
    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * Stocks utilisant ce conditionnement.
     *
     * Un conditionnement peut être associé à plusieurs stocks.
     * La suppression est bloquée si cette collection n'est pas vide.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'packaging')]
    private Collection $stocks;

    /**
     * Initialise la collection des stocks associés.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique du conditionnement.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le libellé du conditionnement.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Définit le libellé du conditionnement.
     *
     * @param string $label ex : "GF400", "Racine nue", "Pot 1L"
     */
    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Retourne la collection des stocks associés à ce conditionnement.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Ajoute un stock à ce conditionnement.
     *
     * Établit également la relation inverse sur le stock.
     *
     * @param Stock $stock le stock à associer
     */
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setPackaging($this);
        }

        return $this;
    }

    /**
     * Retire un stock de ce conditionnement.
     *
     * @param Stock $stock le stock à dissocier
     */
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // Nettoyage du côté propriétaire de la relation
            if ($stock->getPackaging() === $this) {
                $stock->setPackaging(null);
            }
        }

        return $this;
    }
}
