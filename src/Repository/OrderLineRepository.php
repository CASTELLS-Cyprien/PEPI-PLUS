<?php

namespace App\Repository;

use App\Entity\OrderLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Partner;
/**
 * @extends ServiceEntityRepository<OrderLine>
 */
class OrderLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLine::class);
    }

    public function searchReservations(Partner $partner, ?string $term): array
    {
        $qb = $this->createQueryBuilder('ol')
            ->join('ol.stock', 's')
            ->join('ol.PurchaseOrder', 'po')
            ->leftJoin('s.plant', 'p')
            ->leftJoin('s.packaging', 'pack')
            ->leftJoin('s.season', 'sea')
            ->where('s.partner = :partner')
            ->setParameter('partner', $partner);

        if ($term) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'p.latinName LIKE :term',
                    'p.commonName LIKE :term',
                    'po.orderNumber LIKE :term',
                    'pack.label LIKE :term',
                    'sea.year LIKE :term'
                )
            )->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('po.createdAt', 'DESC')->getQuery()->getResult();
    }

    //    /**
    //     * @return OrderLine[] Returns an array of OrderLine objects
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

    //    public function findOneBySomeField($value): ?OrderLine
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
