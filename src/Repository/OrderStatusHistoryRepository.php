<?php

namespace App\Repository;

use App\Entity\OrderStatusHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository de l'historique des statuts de commande (OrderStatusHistory).
 *
 * Ce repository ne fournit pas de méthodes personnalisées car l'historique
 * est toujours accédé via la relation `Order::getOrderStatusHistories()`
 * directement depuis l'entité commande.
 *
 * Il hérite de toutes les méthodes standard de {@see ServiceEntityRepository} :
 * find(), findAll(), findBy(), findOneBy(), count()...
 *
 * @extends ServiceEntityRepository<OrderStatusHistory>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderStatusHistoryRepository extends ServiceEntityRepository
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatusHistory::class);
    }
}
