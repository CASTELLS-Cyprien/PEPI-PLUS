<?php

namespace App\Model;

/**
 * DTO (Data Transfer Object) de filtrage des stocks.
 *
 * Objet de transfert de données utilisé par {@see \App\Form\StockFilterType}
 * et les méthodes de filtrage du {@see \App\Repository\StockRepository}
 * pour transporter les critères depuis le formulaire vers la couche de données.
 *
 * Champs de filtrage disponibles :
 * - Texte libre (nom commun/latin du plant, partenaire, saison, conditionnement)
 * - Quantité minimum (filtre >= sur la quantité)
 * - Quantité maximum (filtre <= sur la quantité)
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class StockFilterData
{
    /**
     * Terme de recherche textuelle.
     *
     * La recherche s'effectue selon le contexte :
     * - Stock global : nom commun/latin, partenaire, saison, conditionnement
     * - Stock interne : nom commun et latin du plant uniquement
     */
    public ?string $query = null;

    /**
     * Quantité minimum pour filtrer les stocks (borne inférieure incluse).
     *
     * Filtre : `quantity >= minQuantity`
     */
    public ?int $minQuantity = null;

    /**
     * Quantité maximum pour filtrer les stocks (borne supérieure incluse).
     *
     * Filtre : `quantity <= maxQuantity`
     */
    public ?int $maxQuantity = null;
}
