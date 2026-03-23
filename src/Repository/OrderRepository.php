<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Partner;
use App\Model\OrderFilterData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository des commandes (Order).
 *
 * Fournit des méthodes de recherche et de filtrage avancées :
 * - Recherche textuelle par numéro de commande ou statut
 * - Filtrage multi-critères via DTO {@see OrderFilterData} (statut, plages de dates)
 * - Requêtes spécialisées pour les tableaux de bord partenaire
 * - Recherche groupée et dédupliquée des commandes d'un partenaire
 *
 * @extends ServiceEntityRepository<Order>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Recherche des commandes par numéro de commande ou statut.
     *
     * @param string|null $term terme de recherche (peut être null pour retourner toutes les commandes)
     *
     * @return Order[] tableau des commandes triées par numéro de commande croissant
     */
    public function searchByTerm(?string $term): array
    {
        $qd = $this->createQueryBuilder('p');

        if ($term) {
            $qd->andWhere('p.orderNumber LIKE :term OR p.status LIKE :term')
                ->setParameter('term', "%$term%");
        }

        return $qd->orderBy('p.orderNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les commandes récentes contenant des stocks d'un partenaire donné.
     *
     * Utilisé pour le widget "Dernières activités" du tableau de bord partenaire.
     * Filtre via jointure sur les lignes de commande et les stocks du partenaire.
     *
     * @param Partner $partner le partenaire dont on cherche les commandes récentes
     * @param int     $limit   nombre maximum de commandes à retourner (défaut : 5)
     *
     * @return Order[] tableau des commandes triées par date de création décroissante
     */
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

    /**
     * Retourne une requête filtrée de commandes selon un DTO de filtres avancés.
     *
     * Supporte le filtrage par :
     * - Texte libre sur le numéro de commande
     * - Statut exact de la commande
     * - Plage de dates de mise à jour (updatedAtStart / updatedAtEnd)
     * - Plage de dates de création (createdAtStart / createdAtEnd)
     *
     * Chaque plage de dates gère trois cas : les deux bornes, borne gauche seule,
     * borne droite seule.
     *
     * Retourne un objet {@see Query} (et non un tableau) pour la compatibilité
     * avec KnpPaginator via {@see OrderController::index()}.
     *
     * @param OrderFilterData $filters DTO contenant les critères de filtrage
     *
     * @return Query requête Doctrine prête pour la pagination KnpPaginator
     */
    public function findWithFilters(OrderFilterData $filters): Query
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC');

        // Filtre de recherche textuelle sur le numéro de commande
        if ($filters->query) {
            $qb->andWhere('o.orderNumber LIKE :q')
                ->setParameter('q', "%{$filters->query}%");
        }

        // Filtre par statut exact
        if ($filters->status) {
            $qb->andWhere('o.status = :status')
                ->setParameter('status', $filters->status);
        }

        // Filtre par plage de dates de mise à jour
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

        // Filtre par plage de dates de création
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

    /**
     * Recherche les commandes portant sur les stocks d'un partenaire donné.
     *
     * Résultats dédupliqués (DISTINCT) pour éviter les doublons dus aux jointures
     * multi-lignes. Utilisé dans {@see PartnerController::reservations()} pour
     * l'espace "Mes réservations" du partenaire.
     *
     * Supporte une recherche textuelle sur : nom latin/commun du plant,
     * numéro de commande, conditionnement ou année de saison.
     *
     * @param Partner     $partner le partenaire dont on cherche les commandes
     * @param string|null $term    terme de recherche optionnel
     *
     * @return Order[] tableau des commandes dédupliquées, triées par date décroissante
     */
    public function searchOrdersByPartner(Partner $partner, ?string $term): array
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.orderLines', 'ol')
            ->innerJoin('ol.stock', 's')
            ->leftJoin('s.plant', 'p')
            ->leftJoin('s.packaging', 'pack')
            ->leftJoin('s.season', 'sea')
            ->where('s.partner = :partner')
            ->setParameter('partner', $partner)
            ->distinct();

        if ($term) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'p.latinName LIKE :term',
                    'p.commonName LIKE :term',
                    'o.orderNumber LIKE :term',
                    'pack.label LIKE :term',
                    'sea.year LIKE :term'
                )
            )->setParameter('term', '%'.$term.'%');
        }

        return $qb->orderBy('o.createdAt', 'DESC')->getQuery()->getResult();
    }
}
