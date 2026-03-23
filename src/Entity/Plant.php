<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un plant végétal du référentiel botanique (Plant).
 *
 * Un plant est un végétal cultivé identifié obligatoirement par :
 * - Son nom latin (scientifique) : ex. "Sorbus torminalis"
 * - Son nom commun (vernaculaire) : ex. "Alisier torminal"
 * - Son type : ex. "arbre", "arbuste", "vivace"
 *
 * Les deux champs `latinName` et `commonName` sont obligatoires (NOT NULL)
 * conformément à la règle de gestion RG-11.
 *
 * Un plant ne peut pas être supprimé s'il est lié à des stocks afin de
 * préserver l'intégrité des données de traçabilité sanitaire.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: PlantRepository::class)]
class Plant
{
    /**
     * Identifiant unique du plant (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom scientifique (latin) du plant.
     *
     * Obligatoire (NOT NULL). Ex : "Quercus petraea", "Sorbus torminalis".
     * Utilisé pour l'identification précise du végétal (RG-11).
     */
    #[ORM\Column(length: 255)]
    private ?string $latinName = null;

    /**
     * Nom vernaculaire (commun) du plant.
     *
     * Obligatoire (NOT NULL). Ex : "Chêne sessile", "Alisier torminal".
     * Affiché dans les listes et les commandes pour une lecture rapide (RG-11).
     */
    #[ORM\Column(length: 255)]
    private ?string $commonName = null;

    /**
     * Type ou catégorie du plant.
     *
     * Ex : "arbre", "arbuste", "vivace", "graminée", etc.
     * Permet de filtrer et classer les plants par famille botanique.
     */
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * Stocks associés à ce plant.
     *
     * Un plant peut être présent dans plusieurs stocks
     * (différentes saisons, conditionnements ou partenaires).
     * La suppression est bloquée si cette collection n'est pas vide.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'plant')]
    private Collection $stocks;

    /**
     * Initialise la collection des stocks associés.
     */
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique du plant.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom scientifique (latin) du plant.
     */
    public function getLatinName(): ?string
    {
        return $this->latinName;
    }

    /**
     * Définit le nom scientifique (latin) du plant.
     *
     * @param string $latinName nom latin obligatoire (RG-11)
     */
    public function setLatinName(string $latinName): static
    {
        $this->latinName = $latinName;

        return $this;
    }

    /**
     * Retourne le nom vernaculaire (commun) du plant.
     */
    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    /**
     * Définit le nom vernaculaire (commun) du plant.
     *
     * @param string $commonName nom commun obligatoire (RG-11)
     */
    public function setCommonName(string $commonName): static
    {
        $this->commonName = $commonName;

        return $this;
    }

    /**
     * Retourne le type ou la catégorie du plant.
     *
     * @return string|null ex : "arbre", "arbuste", "vivace"
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Définit le type ou la catégorie du plant.
     *
     * @param string $type catégorie botanique du plant
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Retourne la collection des stocks associés à ce plant.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Ajoute un stock à ce plant.
     *
     * Établit également la relation inverse sur le stock.
     *
     * @param Stock $stock le stock à associer
     */
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setPlant($this);
        }

        return $this;
    }

    /**
     * Retire un stock de ce plant.
     *
     * @param Stock $stock le stock à dissocier
     */
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // Nettoyage du côté propriétaire de la relation
            if ($stock->getPlant() === $this) {
                $stock->setPlant(null);
            }
        }

        return $this;
    }
}
