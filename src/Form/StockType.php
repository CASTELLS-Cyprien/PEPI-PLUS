<?php

namespace App\Form;

use App\Entity\Packaging;
use App\Entity\Plant;
use App\Entity\Season;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plant', EntityType::class, [
                'class' => Plant::class,
                'choice_label' => 'commonName', // Affiche le nom commun
                'label' => 'Plants',
            ])
            ->add('packaging', EntityType::class, [
                'class' => Packaging::class,
                'choice_label' => 'label', // Affiche le label (ex: Sachet 50g)
                'label' => 'Conditionnement',
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'year', // Affiche l'année
                'label' => 'Saison',
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}