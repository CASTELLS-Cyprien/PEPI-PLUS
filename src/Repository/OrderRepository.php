<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use App\Model\OrderFilterData;


/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function searchByTerm(?string $term): array
    {
        $qd = $this->createQueryBuilder('p');

        if ($term) {
            $qd
                ->andWhere('p.orderNumber LIKE :term OR p.status LIKE :term')
                ->setParameter('term', "%$term%");
        }

        return $qd->orderBy('p.orderNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentOrdersByPartner($partner, int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.orderLines', 'ol')
            ->innerJoin('ol.stock', 's')
            ->andWhere('s.partner = :partner')
            ->setParameter('partner', $partner)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    public function findWithFilters(OrderFilterData $filters): Query
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC');

        // Filtre de recherche textuelle
        if ($filters->query) {
            $qb->andWhere('o.orderNumber LIKE :q')
               ->setParameter('q', "%{$filters->query}%");
        }

        // Filtre par Statut
        if ($filters->status) {
            $qb->andWhere('o.status = :status')
               ->setParameter('status', $filters->status);
        }

        // Filtre par plage de dates updated_at
        if ($filters->updatedAtStart && $filters->updatedAtEnd) {
            $qb->andWhere('o.updated_at BETWEEN :updatedStart AND :updatedEnd')
               ->setParameter('updatedStart', $filters->updatedAtStart->format('Y-m-d 00:00:00'))
               ->setParameter('updatedEnd', $filters->updatedAtEnd->format('Y-m-d 23:59:59'));
        } elseif ($filters->updatedAtStart) {
            $qb->andWhere('o.updated_at >= :updatedStart')
               ->setParameter('updatedStart', $filters->updatedAtStart->format('Y-m-d 00:00:00'));
        } elseif ($filters->updatedAtEnd) {
            $qb->andWhere('o.updated_at <= :updatedEnd')
               ->setParameter('updatedEnd', $filters->updatedAtEnd->format('Y-m-d 23:59:59'));
        }

        // Filtre par plage de dates createdAt
        if ($filters->createdAtStart && $filters->createdAtEnd) {
            $qb->andWhere('o.createdAt BETWEEN :createdStart AND :createdEnd')
               ->setParameter('createdStart', $filters->createdAtStart->format('Y-m-d 00:00:00'))
               ->setParameter('createdEnd', $filters->createdAtEnd->format('Y-m-d 23:59:59'));
        } elseif ($filters->createdAtStart) {
            $qb->andWhere('o.createdAt >= :createdStart')
               ->setParameter('createdStart', $filters->createdAtStart->format('Y-m-d 00:00:00'));
        } elseif ($filters->createdAtEnd) {
            $qb->andWhere('o.createdAt <= :createdEnd')
               ->setParameter('createdEnd', $filters->createdAtEnd->format('Y-m-d 23:59:59'));
        }
        
        return $qb->getQuery();
    }
}
    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
