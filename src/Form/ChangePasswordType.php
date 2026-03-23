<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire de changement de mot de passe pour l'utilisateur connecté.
 *
 * Utilisé dans {@see UserController::changePassword()} lorsqu'un utilisateur
 * souhaite modifier son propre mot de passe depuis son profil, ou lorsqu'il
 * est contraint de le faire après une réinitialisation admin (flag mustChangePassword).
 *
 * Contraintes appliquées :
 * - Champ obligatoire (NotBlank)
 * - Minimum 8 caractères (Length) — contrainte allégée par rapport à ChangePasswordFormType
 *
 * Le champ `plainPassword` n'est pas mappé sur l'entité : il est lu et haché
 * manuellement dans {@see UserController::changePassword()}.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Construit le formulaire avec un champ mot de passe répété (saisie + confirmation).
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmez le nouveau mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                // Non mappé : lu et haché manuellement dans le contrôleur
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length(
                        min: 8,
                        minMessage: 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        // Limite maximale imposée par Symfony pour des raisons de sécurité
                        max: 4096,
                    ),
                ],
            ]);
    }
}
