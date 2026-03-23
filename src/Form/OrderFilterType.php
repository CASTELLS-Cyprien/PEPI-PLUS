<?php

namespace App\Form;

use App\Model\OrderFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de filtrage avancé des commandes.
 *
 * Lié au DTO {@see OrderFilterData} et utilisé dans {@see OrderController::index()}.
 * Permet de filtrer la liste des commandes par :
 * - Texte libre (numéro de commande)
 * - Statut (Réservation, Livrée, Annulée)
 * - Plage de dates de mise à jour (via flatpickr range picker)
 * - Plage de dates de création (via flatpickr range picker)
 *
 * Architecture du date range picker :
 * - Les champs `updatedAtRange` et `createdAtRange` sont des champs texte visibles
 *   affichés avec la classe CSS `flatpickr-range` pour l'initialisation du composant JS.
 * - Les champs `updatedAtStart`, `updatedAtEnd`, `createdAtStart`, `createdAtEnd`
 *   sont des champs date cachés remplis automatiquement par flatpickr lors de la sélection.
 *
 * Le formulaire est configuré en méthode GET sans protection CSRF
 * pour être utilisable via l'URL et compatible avec la pagination.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderFilterType extends AbstractType
{
    /**
     * Construit le formulaire avec tous les champs de filtrage.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ de recherche textuelle sur le numéro de commande
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'Recherche',
            ])
            // Filtre par statut exact de la commande
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut',
                'choices' => [
                    'Réservation' => 'Réservation',
                    'Livrée' => 'Livrée',
                    'Annulée' => 'Annulée',
                ],
                'placeholder' => 'Tous les statuts',
            ])
            // Champ visible pour le range picker de date de mise à jour (non mappé en base)
            ->add('updatedAtRange', TextType::class, [
                'required' => false,
                'label' => 'Mis à jour',
                'mapped' => false,
                'attr' => [
                    'class' => 'flatpickr-range',
                    'readonly' => true,
                    'placeholder' => 'Sélectionner une période...',
                ],
            ])
            // Champ visible pour le range picker de date de création (non mappé en base)
            ->add('createdAtRange', TextType::class, [
                'required' => false,
                'label' => 'Créé',
                'mapped' => false,
                'attr' => [
                    'class' => 'flatpickr-range',
                    'readonly' => true,
                    'placeholder' => 'Sélectionner une période...',
                ],
            ])
            // Champs cachés pour les bornes start/end de la plage de mise à jour
            ->add('updatedAtStart', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('updatedAtEnd', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            // Champs cachés pour les bornes start/end de la plage de création
            ->add('createdAtStart', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('createdAtEnd', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * Lié à {@see OrderFilterData}, méthode GET, sans CSRF
     * pour la compatibilité avec la pagination via URL.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
