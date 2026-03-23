<?php

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des saisons de production (Season).
 *
 * Fournit une méthode de recherche textuelle sur l'année de la saison,
 * utilisée dans la barre de recherche de {@see SeasonController::index()}.
 *
 * @extends ServiceEntityRepository<Season>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class SeasonRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * Recherche des saisons par année.
     *
     * La recherche LIKE sur l'année permet de trouver partiellement
     * (ex : "202" retourne 2023, 2024, 2025...).
     *
     * @param string|null $term terme de recherche sur l'année (peut être null pour tout retourner)
     *
     * @return Season[] tableau des saisons triées par année croissante
     */
    public function searchByTerm(?string $term): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($term) {
            $qb->andWhere('p.year LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        return $qb->orderBy('p.year', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
