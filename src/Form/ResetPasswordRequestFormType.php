<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire de demande de réinitialisation de mot de passe.
 *
 * Utilisé dans {@see ResetPasswordController::request()}.
 * Expose un seul champ `email` pour identifier l'utilisateur souhaitant
 * réinitialiser son mot de passe.
 *
 * Le formulaire ne révèle jamais si l'email est enregistré ou non
 * dans le système (protection anti-énumération) : la redirection est
 * identique qu'un compte existe ou non.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class ResetPasswordRequestFormType extends AbstractType
{
    /**
     * Construit le formulaire avec le champ email.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                // Autocomplétion activée pour faciliter la saisie sur mobile
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
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
        $resolver->setDefaults([]);
    }
}
