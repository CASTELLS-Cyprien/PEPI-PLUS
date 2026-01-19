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
            ->orderBy('o.updated_at', 'DESC');

        // Filtre de recherche textuelle (issu de votre barre search)
        if ($filters->query) {
            $qb->andWhere('o.orderNumber LIKE :q')
               ->setParameter('q', "%{$filters->query}%");
        }

        // Filtre par Statut
        if ($filters->status) {
            $qb->andWhere('o.status = :status')
               ->setParameter('status', $filters->status);
        }

        // Filtre par Date
        if ($filters->updatedAt) {
            $dateStart = $filters->updatedAt->format('Y-m-d 00:00:00');
            $dateEnd = $filters->updatedAt->format('Y-m-d 23:59:59');
            
            $qb->andWhere('o.updated_at BETWEEN :start AND :end')
               ->setParameter('start', $dateStart)
               ->setParameter('end', $dateEnd);
        }

         // Filtre par Date
        if ($filters->createdAt) {
            $dateStart = $filters->createdAt->format('Y-m-d 00:00:00');
            $dateEnd = $filters->createdAt->format('Y-m-d 23:59:59');
            
            $qb->andWhere('o.updated_at BETWEEN :start AND :end')
               ->setParameter('start', $dateStart)
               ->setParameter('end', $dateEnd);
        }
        
        return $qb->getQuery();
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
}
