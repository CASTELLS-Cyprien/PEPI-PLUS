<?php

namespace App\Form;

use App\Model\StockFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de filtrage des stocks (Stock).
 *
 * Lié au DTO {@see StockFilterData} et utilisé dans {@see StockController::index()}
 * et {@see StockController::Gestionindex()} pour filtrer les vues de stock.
 *
 * Permet de filtrer par :
 * - Texte libre : nom latin/commun du plant, partenaire, saison, conditionnement
 * - Quantité minimum (filtre >= sur la quantité)
 * - Quantité maximum (filtre <= sur la quantité)
 *
 * Configuré en méthode GET sans CSRF pour la compatibilité avec la pagination
 * et le partage d'URL avec les filtres actifs.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class StockFilterType extends AbstractType
{
    /**
     * Construit le formulaire avec les champs de filtrage du stock.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Recherche textuelle : plant, partenaire, saison, conditionnement
            ->add('query', TextType::class, [
                'required' => false,
                // Pas de label affiché (géré par le placeholder dans la vue)
                'label' => false,
            ])
            // Filtre quantité minimum (inclus dans les résultats)
            ->add('minQuantity', IntegerType::class, [
                'required' => false,
                'label' => 'Quantité minimum',
            ])
            // Filtre quantité maximum (inclus dans les résultats)
            ->add('maxQuantity', IntegerType::class, [
                'required' => false,
                'label' => 'Quantité maximum',
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * Lié à {@see StockFilterData}, méthode GET, sans CSRF
     * pour la compatibilité avec la pagination via URL.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
