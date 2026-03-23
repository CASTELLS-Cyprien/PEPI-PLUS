<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une saison de production (Season).
 *
 * Une saison correspond à l'année de production d'un plant.
 * Elle est indispensable pour assurer la traçabilité sanitaire réglementaire :
 * chaque plant vendu doit pouvoir être tracé par sa saison de production
 * afin de connaître son âge au moment de la vente (RG-12).
 *
 * Une saison ne peut pas être supprimée si elle est liée à des stocks,
 * afin de préserver l'intégrité des données de traçabilité.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    /**
     * Identifiant unique de la saison (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Année de production correspondant à cette saison.
     *
     * Ex : 2023, 2024, 2025. Stocké en entier pour faciliter le tri
     * et les comparaisons chronologiques.
     */
    #[ORM\Column]
    private ?int $year = null;

    /**
     * Stocks produits durant cette saison.
     *
     * Un plant peut être présent dans plusieurs stocks de différentes saisons.
     * La suppression de la saison est bloquée si cette collection n'est pas vide.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'season')]
    private Collection $stocks;

    /**
     * Initialise la collection des stocks associés.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique de la saison.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'année de production de la saison.
     *
     * @return int|null ex : 2024, 2025
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Définit l'année de production de la saison.
     *
     * @param int $year année de production (ex : 2025)
     */
    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Retourne la collection des stocks produits durant cette saison.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Ajoute un stock à cette saison.
     *
     * Établit également la relation inverse sur le stock.
     *
     * @param Stock $stock le stock à associer
     */
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setSeason($this);
        }

        return $this;
    }

    /**
     * Retire un stock de cette saison.
     *
     * @param Stock $stock le stock à dissocier
     */
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // Nettoyage du côté propriétaire de la relation
            if ($stock->getSeason() === $this) {
                $stock->setSeason(null);
            }
        }

        return $this;
    }
}
