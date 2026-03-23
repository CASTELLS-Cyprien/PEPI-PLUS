<?php

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des partenaires fournisseurs (Partner).
 *
 * Fournit une méthode de recherche textuelle sur la raison sociale,
 * utilisée dans la barre de recherche de {@see PartnerController::index()}
 * et dans {@see PartnerController::reservations()}.
 *
 * @extends ServiceEntityRepository<Partner>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PartnerRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    /**
     * Recherche des partenaires par nom de société.
     *
     * @param string|null $term terme de recherche (peut être null pour tout retourner)
     *
     * @return Partner[] tableau des partenaires triés par nom de société alphabétiquement
     */
    public function searchByTerm(?string $term): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($term) {
            $qb->andWhere('p.companyName LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        return $qb->orderBy('p.companyName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
