<?php

namespace App\Repository;

use App\Entity\Plant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository du référentiel botanique (Plant).
 *
 * Fournit une méthode de recherche textuelle multi-champs sur les plants,
 * utilisée dans {@see PlantController::index()} et dans les sélecteurs
 * de formulaire de création de stock.
 *
 * @extends ServiceEntityRepository<Plant>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class PlantRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plant::class);
    }

    /**
     * Recherche des plants par nom latin, nom commun ou type.
     *
     * La recherche s'effectue simultanément sur les trois champs botaniques
     * via des conditions OR pour maximiser les résultats pertinents.
     *
     * @param string|null $term terme de recherche (peut être null pour tout retourner)
     *
     * @return Plant[] tableau des plants triés par identifiant croissant
     */
    public function searchByTerm(?string $term): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($term) {
            // Recherche simultanée sur nom latin, nom commun et type du plant
            $qb->andWhere('p.latinName LIKE :term OR p.commonName LIKE :term OR p.type LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        return $qb->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
