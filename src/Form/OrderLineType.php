<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire d'édition d'une ligne de commande (OrderLine).
 *
 * Utilisé dans {@see OrderLineController::edit()} pour la modification
 * administrative directe d'une ligne de commande.
 * Expose les champs quantity, stock et PurchaseOrder avec sélection par ID.
 *
 * Note : dans le flux normal de l'application, les lignes sont créées via
 * le panier ({@see CartController}) et non via ce formulaire.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderLineType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs de la ligne de commande.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Quantité réservée dans cette ligne
            ->add('quantity')
            // Stock source (affiché par ID pour usage administratif)
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'id',
            ])
            // Commande parente (affichée par ID pour usage administratif)
            ->add('PurchaseOrder', EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'id',
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
            'data_class' => OrderLine::class,
        ]);
    }
}
