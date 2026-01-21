<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Partner;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'readonly' => $options['is_profile'], // L'utilisateur peut voir mais pas modifier
                    'placeholder' => 'Ex: jean.dupont@example.com',
                    'class' => $options['is_profile'] ? 'bg-gray-100 cursor-not-allowed' : ''
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Ex: Jean']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Ex: Dupont']
            ]);

        // On affiche ces champs UNIQUEMENT si on n'est PAS sur le profil personnel
        if (!$options['is_profile']) {
            $builder
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
                ->add('isActive', CheckboxType::class, [
                    'label' => 'Compte actif',
                    'required' => false,
                ])
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
                            'message' => 'Le mot de passe est trop facile à deviner. Mélangez lettres, chiffres et symboles.'
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'Ce mot de passe a été trouvé dans une fuite de données, veuillez en choisir un autre.'
                        ]),
                    ],
                ])
                ->add('resetPassword', CheckboxType::class, [
                    'label' => 'Forcer la réinitialisation (Password123!)',
                    'required' => false,
                    'mapped' => false,
                ])
                ->add('partner', EntityType::class, [
                    'class' => Partner::class,
                    'choice_label' => 'companyName',
                    'label' => 'Entreprise associée',
                    'placeholder' => 'Aucune entreprise (Personnel interne)',
                    'required' => false,
                    'attr' => [
                        'class' => 'hidden',
                        'data-hs-select' => json_encode([
                            "hasSearch" => true,
                            "isClearable" => true,
                            "placeholder" => "Aucune entreprise (Personnel interne)",
                            "allowEmptyOption" => true,
                            "allowPlaceholderSelection" => true,
                            "searchPlaceholder" => "Rechercher une entreprise...",
                            "searchNoResultText" => "Aucun résultat",
                            "searchClasses" => "block w-full text-sm border-gray-200 rounded-lg focus:border-green-500 focus:ring-green-500 py-2 px-3",
                            "toggleTag" => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
                            "toggleClasses" => "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-green-500",
                            "dropdownClasses" => "mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto",
                            "optionClasses" => "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100",
                            "optionTemplate" => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-green-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>'
                        ]),
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_profile' => false, // Par défaut, on considère que c'est pour l'admin
        ]);
    }
}
