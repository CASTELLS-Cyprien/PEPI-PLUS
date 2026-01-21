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
                'attr' => [
                    'data-hs-select' => json_encode([
                        "hasSearch" => true,
                        "searchPlaceholder" => "Rechercher...",
                        "searchClasses" => "block w-full text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-[1] dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-2 px-3",
                        "toggleTag" => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
                        "toggleClasses" => "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400",
                        "dropdownClasses" => "mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto dark:bg-neutral-900 dark:border-neutral-700",
                        "optionClasses" => "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                        "optionTemplate" => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>'
                    ]),
                    'class' => 'hidden',
                ],
            ])
            ->add('packaging', EntityType::class, [
                'class' => Packaging::class,
                'choice_label' => 'label', // Affiche le label (ex: Sachet 50g)
                'label' => 'Conditionnement',
                'attr' => [
                    'data-hs-select' => json_encode([
                        "hasSearch" => true,
                        "searchPlaceholder" => "Rechercher...",
                        "searchClasses" => "block w-full text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-[1] dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-2 px-3",
                        "toggleTag" => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
                        "toggleClasses" => "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400",
                        "dropdownClasses" => "mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto dark:bg-neutral-900 dark:border-neutral-700",
                        "optionClasses" => "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                        "optionTemplate" => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>'
                    ]),
                    'class' => 'hidden',
                ],
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'year', // Affiche l'année
                'label' => 'Saison',
                'attr' => [
                    'data-hs-select' => json_encode([
                        "hasSearch" => true,
                        "searchPlaceholder" => "Rechercher...",
                        "searchClasses" => "block w-full text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 before:absolute before:inset-0 before:z-[1] dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 py-2 px-3",
                        "toggleTag" => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
                        "toggleClasses" => "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400",
                        "dropdownClasses" => "mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto dark:bg-neutral-900 dark:border-neutral-700",
                        "optionClasses" => "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-neutral-900 dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                        "optionTemplate" => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600 dark:text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>'
                    ]),
                    'class' => 'hidden',
                ],
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
