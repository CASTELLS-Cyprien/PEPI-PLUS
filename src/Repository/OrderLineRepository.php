<?php

namespace App\Repository;

use App\Entity\OrderLine;
use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des lignes de commande (OrderLine).
 *
 * Fournit des méthodes de recherche spécialisées pour retrouver
 * les lignes de commande liées aux stocks d'un partenaire donné.
 * Utilisé notamment pour la vue "Mes réservations" de l'espace partenaire.
 *
 * @extends ServiceEntityRepository<OrderLine>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderLineRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLine::class);
    }

    /**
     * Recherche les lignes de commande liées aux stocks d'un partenaire donné.
     *
     * Effectue des jointures sur stock, commande, plant, conditionnement et saison
     * pour permettre une recherche textuelle multi-critères.
     * Résultats triés par date de création de la commande décroissante.
     *
     * @param Partner     $partner le partenaire dont on cherche les lignes de réservation
     * @param string|null $term    terme de recherche optionnel (nom latin/commun, numéro commande,
     *                             conditionnement ou année de saison)
     *
     * @return OrderLine[] tableau des lignes de commande triées par date de commande décroissante
     */
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
            )->setParameter('term', '%'.$term.'%');
        }

        return $qb->orderBy('po.createdAt', 'DESC')->getQuery()->getResult();
    }
}
