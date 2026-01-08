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
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Partenaire' => 'ROLE_PARTNER',
                    'Collaborateur' => 'ROLE_COLLAB',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
            ])
            // Champ mot de passe manuel (non lié à l'entité directement pour l'edit)
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['placeholder' => 'Laisser vide pour ne pas modifier']
            ])
            // La case à cocher pour la réinitialisation
            ->add('resetPassword', CheckboxType::class, [
                'label' => 'Réinitialiser le mot de passe',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}