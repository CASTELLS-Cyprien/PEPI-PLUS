<?php

namespace App\Repository;

use App\Entity\Partner;
use App\Entity\Stock;
use App\Model\StockFilterData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des stocks de plants (Stock).
 *
 * Fournit des méthodes de recherche et de filtrage avancées pour les stocks,
 * incluant :
 * - Détection des alertes de stock bas (seuil configurable)
 * - Filtrage du stock global (réel + virtuel) via DTO
 * - Filtrage du stock interne uniquement (partner IS NULL)
 *
 * La distinction réel/virtuel repose sur le champ `partner` :
 * - `partner IS NULL` = stock réel interne Pépi+ (RG-02b)
 * - `partner IS NOT NULL` = stock virtuel partenaire
 *
 * @extends ServiceEntityRepository<Stock>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class StockRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * Retourne tous les stocks dont la quantité est inférieure au seuil d'alerte.
     *
     * Utilisé pour le tableau de bord Admin/Collaborateur (vue globale). (RG-02)
     * Couvre tous les stocks, qu'ils soient internes ou partenaires.
     *
     * @param int $threshold seuil d'alerte en dessous duquel un stock est considéré critique (défaut : 10)
     *
     * @return Stock[] tableau des stocks critiques triés par quantité croissante (le plus bas en premier)
     */
    public function findLowStockAlert(int $threshold = 10): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.quantity < :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('s.quantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les stocks d'un partenaire dont la quantité est inférieure au seuil.
     *
     * Utilisé pour le tableau de bord Partenaire (vue filtrée sur ses propres stocks). (RG-02)
     *
     * @param Partner $partner   le partenaire dont on cherche les stocks critiques
     * @param int     $threshold seuil d'alerte (défaut : 10)
     *
     * @return Stock[] tableau des stocks critiques du partenaire triés par quantité croissante
     */
    public function findLowStockAlertByPartner(Partner $partner, int $threshold = 10): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.partner = :partner')
            ->andWhere('s.quantity < :threshold')
            ->setParameter('partner', $partner)
            ->setParameter('threshold', $threshold)
            ->orderBy('s.quantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne un QueryBuilder filtré pour le stock global (réel + virtuel).
     *
     * Joint les entités Plant, Partner, Season et Packaging avec addSelect
     * pour optimiser les requêtes (éviter le N+1 problem).
     *
     * Supporte le filtrage par :
     * - Texte (nom commun/latin du plant, nom du partenaire, saison, conditionnement)
     * - Quantité minimum (>= minQuantity)
     * - Quantité maximum (<= maxQuantity)
     *
     * Retourne un QueryBuilder (et non un tableau) pour la compatibilité
     * avec KnpPaginator via {@see StockController::index()}.
     *
     * @param StockFilterData $filters DTO contenant les critères de filtrage
     *
     * @return \Doctrine\ORM\QueryBuilder queryBuilder prêt pour la pagination, trié par quantité décroissante
     */
    public function findWithFilters(StockFilterData $filters)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.plant', 'p')
            ->leftJoin('s.partner', 'partner')
            ->leftJoin('s.season', 'season')
            ->leftJoin('s.packaging', 'packaging')
            // Chargement eager des entités jointes pour éviter le N+1 problem
            ->addSelect('p', 'partner', 'season', 'packaging');

        if ($filters->query) {
            // Recherche multi-champs : plant, partenaire, saison, conditionnement
            $qb->andWhere(
                $qb->expr()->orX(
                    'p.commonName LIKE :q',
                    'p.latinName LIKE :q',
                    'partner.companyName LIKE :q',
                    'season.year LIKE :q',
                    'packaging.label LIKE :q'
                )
            )->setParameter('q', '%'.$filters->query.'%');
        }

        if (null !== $filters->minQuantity) {
            $qb->andWhere('s.quantity >= :minQty')
                ->setParameter('minQty', $filters->minQuantity);
        }

        if (null !== $filters->maxQuantity) {
            $qb->andWhere('s.quantity <= :maxQty')
                ->setParameter('maxQty', $filters->maxQuantity);
        }

        return $qb->orderBy('s.quantity', 'DESC');
    }

    /**
     * Retourne un QueryBuilder filtré pour les stocks internes uniquement (partner IS NULL).
     *
     * Condition cruciale : `s.partner IS NULL` restreint les résultats aux seuls
     * stocks produits en interne par Pépi+ (stocks réels), excluant tous les stocks
     * virtuels des partenaires fournisseurs (RG-02b).
     *
     * Utilisé pour la vue "Inventaire Interne" (`/stock/gestion`) via
     * {@see StockController::Gestionindex()}.
     *
     * @param StockFilterData $filters DTO contenant les critères de filtrage
     *
     * @return \Doctrine\ORM\QueryBuilder queryBuilder prêt pour la pagination, trié par quantité décroissante
     */
    public function findInternalStocksWithFilters(StockFilterData $filters)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.plant', 'p')
            ->addSelect('p')
            // CONDITION CRUCIALE : Uniquement les stocks sans partenaire (internes Pépi+)
            ->andWhere('s.partner IS NULL');

        if ($filters->query) {
            // Recherche sur le nom commun et le nom latin du plant uniquement
            $qb->andWhere('p.commonName LIKE :q OR p.latinName LIKE :q')
                ->setParameter('q', '%'.$filters->query.'%');
        }

        if (null !== $filters->minQuantity) {
            $qb->andWhere('s.quantity >= :minQty')
                ->setParameter('minQty', $filters->minQuantity);
        }

        if (null !== $filters->maxQuantity) {
            $qb->andWhere('s.quantity <= :maxQty')
                ->setParameter('maxQty', $filters->maxQuantity);
        }

        return $qb->orderBy('s.quantity', 'DESC');
    }
}
