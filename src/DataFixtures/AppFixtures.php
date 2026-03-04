<?php

namespace App\DataFixtures;

use App\Entity\Plant;
use App\Entity\Partner;
use App\Entity\Season;
use App\Entity\Packaging;
use App\Entity\User;
use App\Entity\Stock;
use App\Entity\Order;
use App\Entity\OrderLine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private array $tables = [];

    public function load(ObjectManager $manager): void
    {
        // 1. Chargement et décodage du JSON
        $json = file_get_contents(__DIR__ . '/data/pepi.json');
        $data = json_decode($json, true);

        // On indexe les tables par leur nom pour un accès facile
        foreach ($data as $item) {
            if (isset($item['type']) && $item['type'] === 'table') {
                $this->tables[$item['name']] = $item['data'];
            }
        }

        // 2. Importation dans l'ordre des dépendances
        $this->importPackagings($manager);
        $this->importSeasons($manager);
        $this->importPlants($manager);
        $this->importPartners($manager);
        $this->importUsers($manager);

        // On flush une première fois pour avoir les IDs des parents
        $manager->flush();

        $this->importStocks($manager);
        $this->importOrders($manager);

        $manager->flush();

        $this->importOrderLines($manager);

        $manager->flush();
    }

    private function importPackagings(ObjectManager $manager): void
    {
        foreach ($this->tables['packaging'] ?? [] as $row) {
            $entity = new Packaging();
            $entity->setLabel($row['label']);
            $manager->persist($entity);
            $this->addReference('packaging_' . $row['id'], $entity);
        }
    }

    private function importSeasons(ObjectManager $manager): void
    {
        foreach ($this->tables['season'] ?? [] as $row) {
            $entity = new Season();
            $entity->setYear($row['year']);
            $manager->persist($entity);
            $this->addReference('season_' . $row['id'], $entity);
        }
    }

    private function importPlants(ObjectManager $manager): void
    {
        foreach ($this->tables['plant'] ?? [] as $row) {
            $entity = new Plant();
            $entity->setLatinName($row['latin_name']);
            $entity->setCommonName($row['common_name']);
            $entity->setType($row['type']);
            $manager->persist($entity);
            $this->addReference('plant_' . $row['id'], $entity);
        }
    }

    private function importPartners(ObjectManager $manager): void
    {
        foreach ($this->tables['partner'] ?? [] as $row) {
            $entity = new Partner();
            $entity->setCompanyName($row['company_name']);
            $entity->setContactDetails($row['contact_details']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $manager->persist($entity);
            $this->addReference('partner_' . $row['id'], $entity);
        }
    }

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

            if ($row['partner_id']) {
                $entity->setPartner($this->getReference('partner_' . $row['partner_id'], Partner::class));
            }

            $manager->persist($entity);
            $this->addReference('user_' . $row['id'], $entity);
        }
    }

    private function importStocks(ObjectManager $manager): void
    {
        foreach ($this->tables['stock'] ?? [] as $row) {
            $entity = new Stock();
            $entity->setQuantity((int) $row['quantity']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $entity->setUpdatedAt(new \DateTimeImmutable($row['updated_at']));

            // Relations
            $entity->setPlant($this->getReference('plant_' . $row['plant_id'], Plant::class));
            $entity->setPackaging($this->getReference('packaging_' . $row['packaging_id'], Packaging::class));
            $entity->setSeason($this->getReference('season_' . $row['season_id'], Season::class ));

            if ($row['partner_id']) {
                $entity->setPartner($this->getReference('partner_' . $row['partner_id'], Partner::class));
            }
            if ($row['updated_by_id']) {
                $entity->setUpdatedBy($this->getReference('user_' . $row['updated_by_id'], User::class));
            }

            $manager->persist($entity);
            $this->addReference('stock_' . $row['id'], $entity);
        }
    }

    private function importOrders(ObjectManager $manager): void
    {
        foreach ($this->tables['order'] ?? [] as $row) {
            $entity = new Order();
            $entity->setOrderNumber($row['order_number']);
            $entity->setStatus($row['status']);
            $entity->setCreatedAt(new \DateTimeImmutable($row['created_at']));
            $entity->setUpdatedAt(new \DateTimeImmutable($row['updated_at']));

            $entity->setCollaborator($this->getReference('user_' . $row['collaborator_id'], User::class));
            $entity->setUpdatedBy($this->getReference('user_' . $row['updated_by_id'], User::class));

            $manager->persist($entity);
            $this->addReference('order_' . $row['id'], $entity);
        }
    }

    private function importOrderLines(ObjectManager $manager): void
    {
        foreach ($this->tables['order_line'] ?? [] as $row) {
            $entity = new OrderLine();
            $entity->setQuantity((int) $row['quantity']);

            $entity->setStock($this->getReference('stock_' . $row['stock_id'], Stock::class));
            $entity->setPurchaseOrder($this->getReference('order_' . $row['purchase_order_id'], Order::class));

            $manager->persist($entity);
        }
    }
}