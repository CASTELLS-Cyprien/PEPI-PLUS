<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Partner;

/**
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }
    public function searchByTerm(?string $term): array
    {
        // On utilise 's' pour Stock (plus explicite que 'p')
        $qb = $this->createQueryBuilder('s');

        // Jointures nécessaires pour accéder aux champs des entités liées
        $qb->leftJoin('s.plant', 'plant')
            ->leftJoin('s.partner', 'partner')
            ->leftJoin('s.season', 'season')
            ->leftJoin('s.packaging', 'packaging')
            // On sélectionne les entités jointes pour éviter des requêtes SQL supplémentaires (optimisation)
            ->addSelect('plant', 'partner', 'season', 'packaging');

        if ($term) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'partner.companyName LIKE :term',
                    'plant.latinName LIKE :term',
                    'plant.commonName LIKE :term',
                    'season.year LIKE :term',
                    'packaging.label LIKE :term',
                    's.quantity LIKE :term'
                )
            )
                ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('s.quantity', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Pour le Dashboard Admin : Tous les stocks bas
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
