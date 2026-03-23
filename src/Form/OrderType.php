<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de modification du statut d'une commande.
 *
 * Utilisé dans {@see OrderController::edit()} pour permettre aux collaborateurs
 * et administrateurs de faire progresser une commande dans son cycle de vie.
 *
 * Logique métier intégrée dans le formulaire :
 * Les choix de statut disponibles sont filtrés dynamiquement selon le statut actuel,
 * conformément aux règles de gestion RG-05b :
 * - Depuis "Réservation" : ne peut pas passer directement à "Livrée"
 *   (doit passer par "Validé" d'abord)
 * - Depuis "Validé" : ne peut pas revenir en arrière à "Réservation"
 *
 * Ce filtrage côté formulaire est doublé d'une vérification côté contrôleur
 * dans {@see OrderController::edit()} pour une sécurité renforcée.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderType extends AbstractType
{
    /**
     * Construit le formulaire avec les choix de statut filtrés selon l'état actuel.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire (contient l'objet Order via 'data')
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupération de la commande en cours d'édition pour lire son statut actuel
        $order = $options['data'] ?? null;
        $currentStatus = $order ? $order->getStatus() : 'Réservation';

        // Définition de l'ensemble complet des choix de statut disponibles
        $choices = [
            'Réservation (En attente)' => 'Réservation',
            'Validé' => 'Validé',
            'Livrée (Terminée)' => 'Livrée',
            'Annulée' => 'Annulée',
        ];

        // Filtrage des choix selon le statut actuel (RG-05b)
        if ('Réservation' === $currentStatus) {
            // Depuis "Réservation", le passage direct à "Livrée" est interdit
            // (la commande doit d'abord être "Validée")
            unset($choices['Livrée (Terminée)']);
        } elseif ('Validé' === $currentStatus) {
            // Depuis "Validé", le retour en arrière à "Réservation" est interdit
            unset($choices['Réservation (En attente)']);
        }

        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'État actuel',
                'choices' => $choices,
                'attr' => [
                    'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm',
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
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
