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
        // Configuration unique pour Preline (Thème Clair uniquement)
        $prelineConfig = [
            "hasSearch" => true,
            "searchPlaceholder" => "Rechercher...",
            "searchNoResultText" => "Aucun résultat",
            "searchNoResultClasses" => "py-2 px-4 text-sm text-black",
            "searchClasses" => "block w-full text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 py-2 px-3 text-black",
            "toggleTag" => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
            "toggleClasses" => "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-blue-500 text-black",
            "dropdownClasses" => "mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto",
            "optionClasses" => "py-2 px-4 w-full text-sm text-black cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100",
            "optionTemplate" => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>'
        ];

        $builder
            ->add('plant', EntityType::class, [
                'class' => Plant::class,
                'choice_label' => 'commonName',
                'label' => 'Plants',
                'attr' => [
                    'data-hs-select' => json_encode($prelineConfig),
                    'class' => 'hidden',
                ],
            ])
            ->add('packaging', EntityType::class, [
                'class' => Packaging::class,
                'choice_label' => 'label',
                'label' => 'Conditionnement',
                'attr' => [
                    'data-hs-select' => json_encode($prelineConfig),
                    'class' => 'hidden',
                ],
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'year',
                'label' => 'Saison',
                'attr' => [
                    'data-hs-select' => json_encode($prelineConfig),
                    'class' => 'hidden',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
