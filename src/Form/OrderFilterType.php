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
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut',
                'choices' => [
                    'Réservation' => 'Réservation',
                    'Livrée' => 'Livrée',
                ],
                'placeholder' => 'Tous les statuts',
                'attr' => ['class' => 'form-select']
            ])
            ->add('updatedAt', DateType::class, [
                'required' => false,
                'label' => 'Mis à jour le',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('createdAt', DateType::class, [
                'required' => false,
                'label' => 'Créé le',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
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
