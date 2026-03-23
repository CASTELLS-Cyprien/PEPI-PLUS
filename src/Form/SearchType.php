<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType as SymfonySearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de recherche textuelle générique.
 *
 * Utilisé dans tous les contrôleurs disposant d'une barre de recherche :
 * {@see PlantController}, {@see PartnerController}, {@see PackagingController},
 * {@see SeasonController}, {@see UserController}, {@see StockController}, etc.
 *
 * Caractéristiques :
 * - Champ unique `query` de type SearchType (input HTML5 type="search")
 * - Méthode GET pour permettre le partage d'URL avec le terme de recherche
 * - Sans protection CSRF (formulaire public, pas d'action de modification)
 * - `getBlockPrefix()` retourne une chaîne vide pour éviter le préfixe
 *   dans les paramètres GET (les paramètres sont directement `?query=...`)
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class SearchType extends AbstractType
{
    /**
     * Construit le formulaire avec le champ de recherche.
     *
     * @param FormBuilderInterface $builder constructeur de formulaire Symfony
     * @param array<string, mixed> $options options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', SymfonySearchType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une plante...',
                    // Classes TailwindCSS pour le style de la barre de recherche
                    'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm',
                ],
                'required' => false,
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * Méthode GET sans CSRF pour les recherches publiques.
     *
     * @param OptionsResolver $resolver résolveur d'options Symfony
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    /**
     * Retourne un préfixe de bloc vide.
     *
     * Supprime le préfixe du formulaire dans les paramètres GET pour que
     * le terme de recherche soit accessible directement via `?query=...`
     * au lieu de `?search[query]=...`.
     *
     * @return string chaîne vide pour supprimer le préfixe
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
