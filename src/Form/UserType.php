<?php

namespace App\Form;

use App\Entity\Partner;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

/**
 * Formulaire de gestion d'un utilisateur (User).
 *
 * Ce formulaire est utilisé dans deux contextes distincts contrôlés par
 * l'option `is_profile` :
 *
 * **Mode Administration** (`is_profile = false`, par défaut) :
 * Utilisé dans {@see UserController::new()} et {@see UserController::edit()}.
 * Expose tous les champs, y compris les champs réservés à l'admin :
 * rôles, statut actif, mot de passe, réinitialisation forcée, association partenaire.
 *
 * **Mode Profil personnel** (`is_profile = true`) :
 * Utilisé dans {@see UserController::profile()}.
 * Expose uniquement les champs d'identité (prénom, nom).
 * L'email est affiché en lecture seule (RG-16).
 * Les champs d'administration sont masqués.
 *
 * Champs spéciaux (non mappés sur l'entité) :
 * - `password` : lu et haché manuellement dans le contrôleur
 * - `resetPassword` : case à cocher déclenchant la réinitialisation forcée (RG-15)
 *
 * Le sélecteur de partenaire utilise Preline UI avec recherche intégrée
 * et option "vider la sélection" (isClearable) pour le personnel interne.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class UserType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs adaptés au contexte (admin ou profil).
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire (contient `is_profile`)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Email de connexion — en lecture seule en mode profil (RG-16)
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    // readonly activé en mode profil : l'utilisateur voit mais ne peut pas modifier
                    'readonly' => $options['is_profile'],
                    'placeholder' => 'Ex: jean.dupont@example.com',
                    // Classes CSS grises pour indiquer visuellement la lecture seule
                    'class' => $options['is_profile'] ? 'bg-gray-100 cursor-not-allowed' : '',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Ex: Jean'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Ex: Dupont'],
            ]);

        // Champs réservés à l'administration — masqués en mode profil personnel
        if (!$options['is_profile']) {
            $builder
                // Sélection multiple des rôles avec checkboxes
                ->add('roles', ChoiceType::class, [
                    'label' => 'Rôles / Permissions',
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Partenaire' => 'ROLE_PARTNER',
                        'Collaborateur' => 'ROLE_COLLABORATOR',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ])
                // Case à cocher pour activer/désactiver le compte sans le supprimer
                ->add('isActive', CheckboxType::class, [
                    'label' => 'Compte actif',
                    'required' => false,
                ])
                // Saisie manuelle d'un nouveau mot de passe — non mappé, haché dans le contrôleur
                ->add('password', PasswordType::class, [
                    'label' => 'Définir un mot de passe',
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['placeholder' => 'Laisser vide pour ne pas modifier'],
                    'constraints' => [
                        new Length([
                            'min' => 10,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                            'max' => 4096,
                        ]),
                        new PasswordStrength([
                            'minScore' => PasswordStrength::STRENGTH_MEDIUM,
                            'message' => 'Le mot de passe est trop facile à deviner. Mélangez lettres, chiffres et symboles.',
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'Ce mot de passe a été trouvé dans une fuite de données, veuillez en choisir un autre.',
                        ]),
                    ],
                ])
                // Case à cocher déclenchant la réinitialisation forcée (RG-15)
                // Définit le mot de passe à "Password123!" et active mustChangePassword
                ->add('resetPassword', CheckboxType::class, [
                    'label' => 'Forcer la réinitialisation (Password123!)',
                    'required' => false,
                    'mapped' => false,
                ])
                // Association à un partenaire — utilise Preline UI avec recherche et option vider
                ->add('partner', EntityType::class, [
                    'class' => Partner::class,
                    'choice_label' => 'companyName',
                    'label' => 'Entreprise associée',
                    'placeholder' => 'Aucune entreprise (Personnel interne)',
                    'required' => false,
                    'attr' => [
                        // Masqué par défaut, Preline génère son propre rendu visuel
                        'class' => 'hidden',
                        // Configuration JSON du composant Preline UI pour le select partenaire
                        'data-hs-select' => json_encode([
                            'hasSearch' => true,
                            // Permet de vider la sélection (retour à "Personnel interne")
                            'isClearable' => true,
                            'placeholder' => 'Aucune entreprise (Personnel interne)',
                            'allowEmptyOption' => true,
                            'allowPlaceholderSelection' => true,
                            'searchPlaceholder' => 'Rechercher une entreprise...',
                            'searchNoResultText' => 'Aucun résultat',
                            'searchClasses' => 'block w-full text-sm border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 py-2 px-3',
                            'toggleTag' => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
                            'toggleClasses' => 'hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-green-500',
                            'dropdownClasses' => 'mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto',
                            'optionClasses' => 'py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100',
                            'optionTemplate' => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-green-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>',
                        ]),
                    ],
                ]);
        }
    }

    /**
     * Configure les options du formulaire.
     *
     * Déclare l'option personnalisée `is_profile` pour adapter le formulaire
     * au contexte (administration ou profil personnel).
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // Par défaut : mode administration (tous les champs visibles)
            // Passer is_profile = true pour le mode profil personnel (champs admin masqués)
            'is_profile' => false,
        ]);
    }
}
