<?php

namespace App\Form;

use App\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création et modification d'une saison de production (Season).
 *
 * Formulaire minimaliste avec un seul champ `year` (année entière).
 * Utilisé dans {@see SeasonController::new()} et {@see SeasonController::edit()}.
 *
 * Le type du champ est inféré automatiquement par Symfony via les métadonnées
 * Doctrine de l'entité (INTEGER → IntegerType).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class SeasonType extends AbstractType
{
    /**
     * Construit le formulaire avec le champ année de la saison.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Année de production (ex : 2024, 2025) — type INTEGER inféré automatiquement
            ->add('year');
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
