<?php

namespace App\Form;

use App\Entity\Partner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création et modification d'un partenaire fournisseur (Partner).
 *
 * Expose deux champs :
 * - `companyName` : raison sociale de l'entreprise partenaire
 * - `contactDetails` : coordonnées complètes en format libre (textarea)
 *
 * Utilisé dans {@see PartnerController::new()} et {@see PartnerController::edit()}.
 * La relation avec les utilisateurs (comptes PARTNER) est gérée séparément
 * via {@see UserType} lors de la création ou modification d'un utilisateur.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PartnerType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs du partenaire.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Raison sociale de l'entreprise partenaire
            ->add('companyName', TextType::class, [
                'label' => 'Nom de la société',
                'attr' => ['placeholder' => 'Ex: Pépinière Durand'],
            ])
            // Coordonnées de contact en format libre (adresse, tél, email, etc.)
            ->add('contactDetails', TextareaType::class, [
                'label' => 'Coordonnées de contact',
                'attr' => ['rows' => 3],
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
            'data_class' => Partner::class,
        ]);
    }
}
