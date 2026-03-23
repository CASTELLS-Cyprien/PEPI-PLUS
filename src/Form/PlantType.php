<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création et modification d'un plant végétal (Plant).
 *
 * Expose les trois champs du référentiel botanique :
 * - `latinName` : nom scientifique (latin) — obligatoire (RG-11)
 * - `commonName` : nom vernaculaire (commun) — obligatoire (RG-11)
 * - `type` : catégorie du plant (arbre, arbuste, vivace...)
 *
 * Utilisé dans {@see PlantController::new()} et {@see PlantController::edit()}.
 * Les types de champ sont inférés automatiquement par Symfony (null = auto-détection
 * basée sur les annotations Doctrine de l'entité).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PlantType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs botaniques du plant.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Nom scientifique (latin) — obligatoire (RG-11)
            ->add('latinName', null, [
                'label' => 'Nom Latin',
            ])
            // Nom vernaculaire (commun) — obligatoire (RG-11)
            ->add('commonName', null, [
                'label' => 'Nom commun',
            ])
            // Catégorie du plant (arbre, arbuste, vivace, etc.)
            ->add('type', null, [
                'label' => 'Type',
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
