<?php

namespace App\Form;

use App\Model\OrderFilterData;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'Recherche',
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut',
                'choices' => [
                    'Réservation' => 'Réservation',
                    'Livrée' => 'Livrée',
                    'Annulée' => 'Annulée',
                ],
                'placeholder' => 'Tous les statuts',
            ])
            // Champs visibles pour le range picker
            ->add('updatedAtRange', TextType::class, [
                'required' => false,
                'label' => 'Mis à jour',
                'mapped' => false,
                'attr' => [
                    'class' => 'flatpickr-range',
                    'readonly' => true,
                    'placeholder' => 'Sélectionner une période...'
                ]
            ])
            ->add('createdAtRange', TextType::class, [
                'required' => false,
                'label' => 'Créé',
                'mapped' => false,
                'attr' => [
                    'class' => 'flatpickr-range',
                    'readonly' => true,
                    'placeholder' => 'Sélectionner une période...'
                ]
            ])
            // Champs cachés pour les dates start/end
            ->add('updatedAtStart', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('updatedAtEnd', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('createdAtStart', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('createdAtEnd', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}