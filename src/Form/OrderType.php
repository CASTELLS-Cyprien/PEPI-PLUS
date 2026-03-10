<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On récupère l'objet Order en cours d'édition
        $order = $options['data'] ?? null;
        $currentStatus = $order ? $order->getStatus() : 'Réservation';

        // Définition des choix par défaut
        $choices = [
            'Réservation (En attente)' => 'Réservation',
            'Validé' => 'Validé',
            'Livrée (Terminée)' => 'Livrée',
            'Annulée' => 'Annulée',
        ];

        // LOGIQUE DE FILTRE :
        if ($currentStatus === 'Réservation') {
            // Si on est en Réservation, on ne peut pas passer directement à Livrée
            unset($choices['Livrée (Terminée)']);
        } elseif ($currentStatus === 'Validé') {
            // Si c'est Validé, on ne peut plus revenir en arrière vers Réservation
            unset($choices['Réservation (En attente)']);
        }

        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'État actuel',
                'choices' => $choices,
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
