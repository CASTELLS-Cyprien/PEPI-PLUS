<?php

namespace App\Form;

use App\Entity\Packaging;
use App\Entity\Partner;
use App\Entity\Plant;
use App\Entity\Season;
use App\Entity\Stock;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity')
            ->add('plant', EntityType::class, [
                'class' => Plant::class,
                'choice_label' => 'id',
            ])
            ->add('packaging', EntityType::class, [
                'class' => Packaging::class,
                'choice_label' => 'id',
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'id',
            ])
            ->add('partner', EntityType::class, [
                'class' => Partner::class,
                'choice_label' => 'id',
            ])
            ->add('updated_by', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
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
