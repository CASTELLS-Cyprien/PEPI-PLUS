<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Import corrigé
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderNumber', TextType::class, [
                'disabled' => true, // Empêche la modification mais reste visible
                'label' => 'Référence de la commande',
                'attr' => [
                    'class' => 'bg-gray-100 border-gray-300 text-gray-500 cursor-not-allowed'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'État actuel',
                'choices' => [
                    'Réservation (En attente)' => 'Réservation',
                    'Livrée (Terminée)' => 'Livrée',
                    'Annulée' => 'Annulée',
                ],
                'attr' => [
                    'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
