<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Partner;
use App\Model\StockFilterData;

/**
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * Pour le Dashboard Admin et collaborateur : Tous les stocks bas
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
     * Pour le Dashboard Partenaire : Ses stocks bas uniquement
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

    public function findWithFilters(StockFilterData $filters)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.plant', 'p')
            ->leftJoin('s.partner', 'partner')
            ->leftJoin('s.season', 'season')
            ->leftJoin('s.packaging', 'packaging')
            ->addSelect('p', 'partner', 'season', 'packaging');

        if ($filters->query) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'p.commonName LIKE :q',
                    'p.latinName LIKE :q',
                    'partner.companyName LIKE :q',
                    'season.year LIKE :q',
                    'packaging.label LIKE :q'
                )
            )
                ->setParameter('q', '%' . $filters->query . '%');
        }

        if ($filters->minQuantity !== null) {
            $qb->andWhere('s.quantity >= :minQty')
                ->setParameter('minQty', $filters->minQuantity);
        }

        if ($filters->maxQuantity !== null) {
            $qb->andWhere('s.quantity <= :maxQty')
                ->setParameter('maxQty', $filters->maxQuantity);
        }

        return $qb->orderBy('s.quantity', 'DESC');
    }
    /**
     * Retourne les stocks internes (sans partenaire) filtrés
     * en fonction de la quantité minimum et de la recherche
     * sur le nom commun et le nom latin de la plante
     *
     * @param StockFilterData $filters
     * @return Query
     */
    public function findInternalStocksWithFilters(StockFilterData $filters)
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.plant', 'p')
            ->addSelect('p')
            // CONDITION CRUCIALE : Uniquement les stocks sans partenaire
            ->andWhere('s.partner IS NULL');

        if ($filters->query) {
            $qb->andWhere('p.commonName LIKE :q OR p.latinName LIKE :q')
                ->setParameter('q', '%' . $filters->query . '%');
        }

        if ($filters->minQuantity !== null) {
            $qb->andWhere('s.quantity >= :minQty')
                ->setParameter('minQty', $filters->minQuantity);
        }
        
        if ($filters->maxQuantity !== null) {
            $qb->andWhere('s.quantity <= :maxQty')
                ->setParameter('maxQty', $filters->maxQuantity);
        }
        return $qb->orderBy('s.quantity', 'DESC');
    }
    //    /**
    //     * @return Stock[] Returns an array of Stock objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stock
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
