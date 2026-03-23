<?php

namespace App\Form;

use App\Entity\Packaging;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création et modification d'un conditionnement (Packaging).
 *
 * Formulaire minimaliste avec un seul champ `label`.
 * Utilisé dans {@see PackagingController::new()} et {@see PackagingController::edit()}.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PackagingType extends AbstractType
{
    /**
     * Construit le formulaire avec le champ libellé du conditionnement.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Libellé du conditionnement (ex : "GF400", "Racine nue", "Pot 1L")
            ->add('label');
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Packaging::class,
        ]);
    }
}
