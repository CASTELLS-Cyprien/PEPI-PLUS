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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'readonly' => $options['is_profile'], // L'utilisateur peut voir mais pas modifier
                    'class' => $options['is_profile'] ? 'bg-gray-100 cursor-not-allowed' : '',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ]);

        // On affiche ces champs UNIQUEMENT si on n'est PAS sur le profil personnel
        if (!$options['is_profile']) {
            $builder
                ->add('roles', ChoiceType::class, [
                    'label' => 'Rôles / Permissions',
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Partenaire' => 'ROLE_PARTNER',
                        'Collaborateur' => 'ROLE_COLLAB',
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
                    'attr' => ['placeholder' => 'Laisser vide pour ne pas modifier']
                ])
                ->add('resetPassword', CheckboxType::class, [
                    'label' => 'Forcer la réinitialisation (Password123!)',
                    'required' => false,
                    'mapped' => false,
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