<?php

namespace App\Model;

/**
 * DTO (Data Transfer Object) de filtrage des commandes.
 *
 * Objet de transfert de données utilisé par {@see \App\Form\OrderFilterType}
 * et {@see \App\Repository\OrderRepository::findWithFilters()} pour transporter
 * les critères de filtrage depuis le formulaire vers la couche de données.
 *
 * Champs de filtrage disponibles :
 * - Texte libre sur le numéro de commande
 * - Statut exact de la commande
 * - Plage de dates de mise à jour (via flatpickr range picker)
 * - Plage de dates de création (via flatpickr range picker)
 *
 * Les champs `updatedAtRange` et `createdAtRange` sont des champs texte
 * affichés dans l'interface (non mappés en base). Les bornes réelles sont
 * portées par `updatedAtStart`/`updatedAtEnd` et `createdAtStart`/`createdAtEnd`.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderFilterData
{
    /**
     * Terme de recherche textuelle sur le numéro de commande.
     */
    public ?string $query = null;

    /**
     * Filtre par statut exact de la commande.
     *
     * Valeurs possibles : "Réservation", "Livrée", "Annulée".
     */
    public ?string $status = null;

    /**
     * Valeur affichée dans le champ range picker de date de mise à jour.
     *
     * Champ non mappé en base, utilisé uniquement pour l'affichage
     * et l'initialisation du composant flatpickr côté interface.
     */
    public ?string $updatedAtRange = null;

    /**
     * Borne inférieure de la plage de dates de mise à jour (incluse).
     */
    public ?\DateTimeInterface $updatedAtStart = null;

    /**
     * Borne supérieure de la plage de dates de mise à jour (incluse).
     */
    public ?\DateTimeInterface $updatedAtEnd = null;

    /**
     * Valeur affichée dans le champ range picker de date de création.
     *
     * Champ non mappé en base, utilisé uniquement pour l'affichage
     * et l'initialisation du composant flatpickr côté interface.
     */
    public ?string $createdAtRange = null;

    /**
     * Borne inférieure de la plage de dates de création (incluse).
     */
    public ?\DateTimeInterface $createdAtStart = null;

    /**
     * Borne supérieure de la plage de dates de création (incluse).
     */
    public ?\DateTimeInterface $createdAtEnd = null;
}
