<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\OrderStatusHistory;
use App\Entity\Packaging;
use App\Entity\Partner;
use App\Entity\Plant;
use App\Entity\Season;
use App\Entity\Stock;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe de chargement des données initiales de l'application.
 *
 * Cette classe orchestre l'importation de données complexes depuis un fichier JSON.
 * Elle gère l'ordre des dépendances entre les entités (parents avant enfants) et 
 * utilise le système de références de Doctrine pour lier les objets entre eux.
 *
 * @author CASTELLS Cyprien
 * @version 1.2
 */
class AppFixtures extends Fixture
{
    /**
     * Stockage temporaire des données brutes extraites du JSON.
     * * @var array $tables Tableau associatif où chaque clé est le nom d'une table.
     */
    private array $tables = [];

    /**
     * Méthode principale de chargement des fixtures.
     *
     * Charge le fichier JSON, décode les données et appelle les méthodes d'importation
     * spécifiques dans un ordre chronologique pour respecter l'intégrité référentielle.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités de Doctrine.
     *
     * @return void
     *
     * @author CASTELLS Cyprien
     * @version 1.2
     */
    public function load(ObjectManager $manager): void
    {
        // 1. Chargement et décodage du JSON
        $json = file_get_contents(__DIR__.'/data/pepi.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            if (isset($item['type']) && 'table' === $item['type']) {
                $this->tables[$item['name']] = $item['data'];
            }
        }

        // 2. Importation par ordre de dépendance
        $this->importPackagings($manager);
        $this->importSeasons($manager);
        $this->importPlants($manager);
        $this->importPartners($manager);
        $this->importUsers($manager);

        // Flush pour obtenir les références des parents
        $manager->flush();

        $this->importStocks($manager);
        $this->importOrders($manager);

        $manager->flush();

        $this->importOrderLines($manager);
        $this->importOrderStatusHistory($manager);

        $manager->flush();
    }

    /**
     * Importation des types de conditionnement.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importPackagings(ObjectManager $manager): void
    {
        foreach ($this->tables['packaging'] ?? [] as $row) {
            $entity = new Packaging();
            $entity->setLabel($row['label']);
            $manager->persist($entity);
            $this->addReference('packaging_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des saisons de production.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importSeasons(ObjectManager $manager): void
    {
        foreach ($this->tables['season'] ?? [] as $row) {
            $entity = new Season();
            $entity->setYear($row['year']);
            $manager->persist($entity);
            $this->addReference('season_'.$row['id'], $entity);
        }
    }

    /**
     * Importation du catalogue de plantes.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importPlants(ObjectManager $manager): void
    {
        foreach ($this->tables['plant'] ?? [] as $row) {
            $entity = new Plant();
            $entity->setLatinName($row['latin_name']);
            $entity->setCommonName($row['common_name']);
            $entity->setType($row['type']);
            $manager->persist($entity);
            $this->addReference('plant_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des partenaires (pépinières, fournisseurs, etc.).
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importPartners(ObjectManager $manager): void
    {
        foreach ($this->tables['partner'] ?? [] as $row) {
            $entity = new Partner();
            $entity->setCompanyName($row['company_name']);
            $entity->setContactDetails($row['contact_details']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $manager->persist($entity);
            $this->addReference('partner_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des utilisateurs du système.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importUsers(ObjectManager $manager): void
    {
        foreach ($this->tables['user'] ?? [] as $row) {
            $entity = new User();
            $entity->setEmail($row['email']);
            $entity->setRoles(json_decode($row['roles'], true));
            $entity->setPassword($row['password']);
            $entity->setLastName($row['last_name']);
            $entity->setFirstName($row['first_name']);
            $entity->setIsActive((bool) $row['is_active']);

            if (!empty($row['partner_id'])) {
                $entity->setPartner($this->getReference('partner_'.$row['partner_id'], Partner::class));
            }

            $manager->persist($entity);
            $this->addReference('user_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des stocks de plantes.
     * * Lie les stocks aux plantes, packagings, saisons et partenaires 
     * ainsi qu'aux utilisateurs ayant créé ou modifié l'entrée.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importStocks(ObjectManager $manager): void
    {
        foreach ($this->tables['stock'] ?? [] as $row) {
            $entity = new Stock();
            $entity->setQuantity((int) $row['quantity']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $entity->setUpdatedAt(new \DateTimeImmutable($row['updated_at']));

            $entity->setPlant($this->getReference('plant_'.$row['plant_id'], Plant::class));
            $entity->setPackaging($this->getReference('packaging_'.$row['packaging_id'], Packaging::class));
            $entity->setSeason($this->getReference('season_'.$row['season_id'], Season::class));

            if (!empty($row['partner_id'])) {
                $entity->setPartner($this->getReference('partner_'.$row['partner_id'], Partner::class));
            }

            if (!empty($row['created_by_id'])) {
                $entity->setCreatedBy($this->getReference('user_'.$row['created_by_id'], User::class));
            }
            if (!empty($row['updated_by_id'])) {
                $entity->setUpdatedBy($this->getReference('user_'.$row['updated_by_id'], User::class));
            }

            $manager->persist($entity);
            $this->addReference('stock_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des commandes (en-têtes).
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importOrders(ObjectManager $manager): void
    {
        foreach ($this->tables['order'] ?? [] as $row) {
            $entity = new Order();
            $entity->setOrderNumber($row['order_number']);
            $entity->setStatus($row['status']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $entity->setUpdatedAt(new \DateTimeImmutable($row['updated_at']));

            $entity->setCollaborator($this->getReference('user_'.$row['collaborator_id'], User::class));

            if (!empty($row['updated_by_id'])) {
                $entity->setUpdatedBy($this->getReference('user_'.$row['updated_by_id'], User::class));
            }

            $manager->persist($entity);
            $this->addReference('order_'.$row['id'], $entity);
        }
    }

    /**
     * Importation des lignes de commande détaillées.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importOrderLines(ObjectManager $manager): void
    {
        foreach ($this->tables['order_line'] ?? [] as $row) {
            $entity = new OrderLine();
            $entity->setQuantity((int) $row['quantity']);
            $entity->setStock($this->getReference('stock_'.$row['stock_id'], Stock::class));
            $entity->setPurchaseOrder($this->getReference('order_'.$row['purchase_order_id'], Order::class));

            $manager->persist($entity);
        }
    }

    /**
     * Importation de l'historique des statuts de commande.
     *
     * @param ObjectManager $manager Le gestionnaire d'entités.
     * @return void
     * * @author CASTELLS Cyprien
     * @version 1.2
     */
    private function importOrderStatusHistory(ObjectManager $manager): void
    {
        foreach ($this->tables['order_status_history'] ?? [] as $row) {
            $entity = new OrderStatusHistory();
            $entity->setStatus($row['status']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));

            $entity->setPurchaseOrder($this->getReference('order_'.$row['purchase_order_id'], Order::class));
            $entity->setChangedBy($this->getReference('user_'.$row['changed_by_id'], User::class));

            $manager->persist($entity);
        }
    }
}