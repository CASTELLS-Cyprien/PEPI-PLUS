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

/**
 * Formulaire de création et modification d'un stock de plants (Stock).
 *
 * Utilisé dans {@see StockController::newGestion()}, {@see StockController::edit()},
 * {@see PartnerController::newMyStock()} et {@see PartnerController::editMyStock()}.
 *
 * Les champs de sélection (plant, packaging, season) utilisent le composant
 * Preline UI pour un rendu de select amélioré avec recherche intégrée.
 * La configuration `data-hs-select` passe les options JSON au composant JS Preline.
 *
 * Le champ `partner` n'est pas exposé dans ce formulaire :
 * il est défini automatiquement dans les contrôleurs selon le contexte
 * (null pour stock interne, partenaire connecté pour stock virtuel).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class StockType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs plant, packaging, season et quantity.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Configuration Preline UI pour les selects avec recherche (thème clair uniquement)
        // Cette configuration est partagée entre les trois champs EntityType
        $prelineConfig = [
            'hasSearch' => true,
            'searchPlaceholder' => 'Rechercher...',
            'searchNoResultText' => 'Aucun résultat',
            'searchNoResultClasses' => 'py-2 px-4 text-sm text-black',
            'searchClasses' => 'block w-full text-sm border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 py-2 px-3 text-black',
            // Template du bouton de toggle du dropdown
            'toggleTag' => '<button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-select-dropdown" aria-label="Select"></button>',
            'toggleClasses' => 'hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative py-3 px-4 pe-9 flex text-nowrap w-full cursor-pointer bg-white border border-gray-200 rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-blue-500 text-black',
            'dropdownClasses' => 'mt-2 max-h-72 pb-1 z-20 w-full hidden bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto',
            'optionClasses' => 'py-2 px-4 w-full text-sm text-black cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100',
            // Template d'une option avec icône de sélection
            'optionTemplate' => '<div class="flex justify-between items-center w-full"><span data-title></span><span class="hidden hs-selected:block"><svg class="shrink-0 size-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>',
        ];

        $builder
            // Sélection du plant — affiché par nom commun, rendu Preline UI
            ->add('plant', EntityType::class, [
                'class' => Plant::class,
                'choice_label' => 'commonName',
                'label' => 'Plants',
                'attr' => [
                    // Configuration JSON transmise au composant JS Preline
                    'data-hs-select' => json_encode($prelineConfig),
                    // Masqué par défaut, Preline génère son propre rendu visuel
                    'class' => 'hidden',
                ],
            ])
            // Sélection du conditionnement — affiché par libellé, rendu Preline UI
            ->add('packaging', EntityType::class, [
                'class' => Packaging::class,
                'choice_label' => 'label',
                'label' => 'Conditionnement',
                'attr' => [
                    'data-hs-select' => json_encode($prelineConfig),
                    'class' => 'hidden',
                ],
            ])
            // Sélection de la saison — affichée par année, rendu Preline UI
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'choice_label' => 'year',
                'label' => 'Saison',
                'attr' => [
                    'data-hs-select' => json_encode($prelineConfig),
                    'class' => 'hidden',
                ],
            ])
            // Quantité disponible dans ce stock
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500',
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
            'data_class' => Stock::class,
        ]);
    }
}
