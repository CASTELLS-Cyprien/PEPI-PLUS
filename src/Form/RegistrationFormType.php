<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire d'inscription d'un nouvel utilisateur.
 *
 * Utilisé dans {@see RegistrationController::register()}.
 * Expose trois champs :
 * - `email` : adresse email de connexion (mappé sur l'entité)
 * - `agreeTerms` : case à cocher CGU (non mappé, validation seule)
 * - `plainPassword` : mot de passe en clair (non mappé, haché dans le contrôleur)
 *
 * Contraintes sur le mot de passe :
 * - Obligatoire (NotBlank)
 * - Minimum 6 caractères (Length)
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Construit le formulaire d'inscription.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Email de connexion (mappé sur l'entité User)
            ->add('email')
            // Case à cocher CGU — non mappée, validation uniquement
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            // Mot de passe en clair — non mappé, haché manuellement dans le contrôleur
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // Limite maximale imposée par Symfony pour des raisons de sécurité
                        'max' => 4096,
                    ]),
                ],
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
            'data_class' => User::class,
        ]);
    }
}
