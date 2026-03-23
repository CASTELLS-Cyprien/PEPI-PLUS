<?php

namespace App\Repository;

use App\Entity\Packaging;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des conditionnements (Packaging).
 *
 * Fournit une méthode de recherche textuelle sur le libellé du conditionnement,
 * utilisée dans la barre de recherche de {@see PackagingController::index()}.
 *
 * @extends ServiceEntityRepository<Packaging>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PackagingRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Packaging::class);
    }

    /**
     * Recherche des conditionnements par libellé.
     *
     * @param string|null $term terme de recherche (peut être null pour tout retourner)
     *
     * @return Packaging[] tableau des conditionnements triés par libellé alphabétiquement
     */
    public function searchByTerm(?string $term): array
    {
        $qd = $this->createQueryBuilder('p');

        if ($term) {
            $qd->andWhere('p.label LIKE :term')
                ->setParameter('term', "%$term%");
        }

        return $qd->orderBy('p.label', 'ASC')->getQuery()->getResult();
    }
}
