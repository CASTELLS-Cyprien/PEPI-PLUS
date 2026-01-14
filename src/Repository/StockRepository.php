<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
                    'packaging.label LIKE :term'
                )
            )
                ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('s.quantity', 'DESC')
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
