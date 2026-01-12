<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('latinName', null, [
                'label'=> 'Nom Latin',
            ])
            ->add('commonName', null, [
                'label'=> 'Nom commun',
            ])
            ->add('type', null, [
                'label'=> 'Type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plant::class,
        ]);
    }
}
