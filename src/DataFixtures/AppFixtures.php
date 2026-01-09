<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Packaging;
use App\Entity\Partner;
use App\Entity\Plant;
use App\Entity\Season;
use App\Entity\Stock;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // --- 1. CRÉATION DES UTILISATEURS (Collaborateurs) ---
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setIsActive(true)
                ->setMustChangePassword(false)
                ->setRoles(['ROLE_USER', 'ROLE_COLLABORATOR', 'ROLE_PARTNER', 'ROLE_ADMIN']);



            $password = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);
            $users[] = $user;
        }

        // --- 2. CRÉATION DES SAISONS ---
        $seasons = [];
        for ($year = 2023; $year <= 2026; $year++) {
            $season = new Season();
            $season->setYear($year);
            $manager->persist($season);
            $seasons[] = $season;
        }

        // --- 3. CRÉATION DES PACKAGINGS ---
        $packagings = [];
        $labels = ['Pot 1L', 'Pot 3L', 'Barquette x6', 'Godet', 'Sac 50L'];
        foreach ($labels as $label) {
            $packaging = new Packaging();
            $packaging->setLabel($label);
            $manager->persist($packaging);
            $packagings[] = $packaging;
        }

        // --- 4. CRÉATION DES PLANTES ---
        $plants = [];
        for ($i = 0; $i < 20; $i++) {
            $plant = new Plant();
            $plant->setCommonName($faker->word)
                ->setLatinName($faker->words(2, true))
                ->setType($faker->randomElement(['Vivace', 'Annuelle', 'Arbustre', 'Arbre']));
            $manager->persist($plant);
            $plants[] = $plant;
        }

        // --- 5. CRÉATION DES PARTENAIRES ---
        $partners = [];
        for ($i = 0; $i < 10; $i++) {
            $partner = new Partner();
            $partner->setCompanyName($faker->company)
                ->setContactDetails($faker->address)
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year')));

            // On peut lier un utilisateur optionnellement si besoin
            $manager->persist($partner);
            $partners[] = $partner;
        }

        // --- 6. CRÉATION DU STOCK ---
        $stocks = [];
        for ($i = 0; $i < 50; $i++) {
            $stock = new Stock();
            $stock->setQuantity($faker->numberBetween(0, 500))
                ->setPlant($faker->randomElement($plants))
                ->setPackaging($faker->randomElement($packagings))
                ->setSeason($faker->randomElement($seasons))
                ->setPartner($faker->randomElement($partners))
                ->setUpdatedBy($faker->randomElement($users));

            $manager->persist($stock);
            $stocks[] = $stock;
        }

        // --- 7. CRÉATION DES COMMANDES (Orders & OrderLines) ---
        for ($i = 0; $i < 15; $i++) {
            $order = new Order();
            $order->setOrderNumber('CMD-' . $faker->unique()->randomNumber(5))
                ->setStatus($faker->randomElement(['Réservation', 'Validée', 'Livrée', 'Annulée']))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime('-1 month')))
                ->setCollaborator($faker->randomElement($users))
                ->setUpdatedBy($faker->randomElement($users));

            $manager->persist($order);

            // Créer 1 à 5 lignes par commande
            for ($j = 0; $j < rand(1, 5); $j++) {
                $line = new OrderLine();
                $line->setQuantity($faker->numberBetween(1, 20))
                    ->setStock($faker->randomElement($stocks))
                    ->setPurchaseOrder($order);
                $manager->persist($line);
            }
        }

        $manager->flush();
    }
}