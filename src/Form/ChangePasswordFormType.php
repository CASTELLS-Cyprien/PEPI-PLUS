<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

/**
 * Formulaire de changement de mot de passe utilisé lors de la réinitialisation
 * via le lien email (flux SymfonyCasts ResetPassword).
 *
 * Contraintes appliquées :
 * - Champ obligatoire (NotBlank)
 * - Minimum 12 caractères (Length)
 * - Force du mot de passe vérifiée (PasswordStrength)
 * - Mot de passe non compromis vérifié contre les fuites connues (NotCompromisedPassword)
 *
 * Le champ `plainPassword` n'est pas mappé directement sur l'entité :
 * il est lu et haché manuellement dans {@see ResetPasswordController::reset()}.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class ChangePasswordFormType extends AbstractType
{
    /**
     * Construit le formulaire avec un champ mot de passe répété (confirmation).
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        // Désactive l'autocomplétion pour forcer la saisie d'un nouveau mot de passe
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // Limite maximale imposée par Symfony pour des raisons de sécurité
                            'max' => 4096,
                        ]),
                        // Vérifie la complexité du mot de passe (entropie suffisante)
                        new PasswordStrength(),
                        // Vérifie que le mot de passe n'est pas dans une base de fuites connues
                        new NotCompromisedPassword(),
                    ],
                    'label' => 'New password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'The password fields must match.',
                // Non mappé : lu et haché manuellement dans le contrôleur
                'mapped' => false,
            ]);
    }

    /**
     * Configure les options par défaut du formulaire.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
