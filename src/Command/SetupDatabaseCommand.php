<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Commande d'initialisation complète de la base de données.
 *
 * Cette commande automatise l'ensemble du processus d'installation de la base
 * de données en exécutant les étapes suivantes dans l'ordre :
 *   1. Création de l'utilisateur MySQL `pepi_user` avec les droits nécessaires
 *      (nécessite une connexion root à MySQL).
 *   2. Création de la base de données via Doctrine.
 *   3. Exécution des migrations Doctrine.
 *   4. Chargement des fixtures (données initiales).
 *
 * Chaque commande Doctrine est lancée dans un sous-processus PHP indépendant
 * afin d'éviter les conflits d'état de connexion entre les commandes.
 *
 * Usage :
 * ```bash
 * php bin/console app:setup-database
 * ```
 *
 * @author CASTELLS Cyprien
 * @version 1.1
 */
#[AsCommand(
    name: 'app:setup-database',
    description: 'Initialise la base de données : crée l\'utilisateur MySQL, la BDD, exécute les migrations et charge les fixtures.',
)]
class SetupDatabaseCommand extends Command
{
    /**
     * Hôte MySQL utilisé pour créer l'utilisateur.
     *
     * @var string
     */
    private const DB_HOST = '127.0.0.1';

    /**
     * Nom de l'utilisateur MySQL à créer pour l'application.
     *
     * @var string
     */
    private const DB_USER = 'pepi_user';

    /**
     * Mot de passe de l'utilisateur MySQL à créer.
     *
     * @var string
     */
    private const DB_PASSWORD = 'Password123';

    /**
     * Nom de la base de données accordée à l'utilisateur.
     *
     * @var string
     */
    private const DB_NAME = 'pepi';

    /**
     * Point d'entrée de la commande.
     *
     * Orchestre les différentes étapes d'initialisation et affiche
     * un retour visuel pour chaque étape réalisée.
     *
     * @param InputInterface  $input  Interface d'entrée Symfony.
     * @param OutputInterface $output Interface de sortie Symfony.
     *
     * @return int Code de retour : Command::SUCCESS ou Command::FAILURE.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Initialisation de la base de données Pépi+');

        // Étape 1 : Création de l'utilisateur MySQL
        if (!$this->createMysqlUser($io)) {
            return Command::FAILURE;
        }

        // Étape 2 : Création de la base de données
        if (!$this->runSubProcess(['doctrine:database:create'], 'Étape 2/4 : Création de la base de données', $io, $output)) {
            return Command::FAILURE;
        }

        // Étape 3 : Migrations
        if (!$this->runSubProcess(['doctrine:migrations:migrate', '--no-interaction'], 'Étape 3/4 : Exécution des migrations', $io, $output)) {
            return Command::FAILURE;
        }

        // Étape 4 : Fixtures
        if (!$this->runSubProcess(['doctrine:fixtures:load', '--no-interaction'], 'Étape 4/4 : Chargement des fixtures', $io, $output)) {
            return Command::FAILURE;
        }

        $io->success('Base de données initialisée avec succès !');

        return Command::SUCCESS;
    }

    /**
     * Crée l'utilisateur MySQL `pepi_user` via une connexion root.
     *
     * Demande les identifiants root MySQL à l'utilisateur, puis exécute
     * les instructions SQL suivantes :
     *   - CREATE USER IF NOT EXISTS
     *   - GRANT ALL PRIVILEGES sur la base `pepi`
     *   - FLUSH PRIVILEGES
     *
     * @param SymfonyStyle $io Interface de style pour l'affichage console.
     *
     * @return bool `true` si la création a réussi, `false` en cas d'erreur.
     */
    private function createMysqlUser(SymfonyStyle $io): bool
    {
        $io->section('Étape 1/4 : Création de l\'utilisateur MySQL');

        $dsn = sprintf('mysql:host=%s;charset=utf8mb4', self::DB_HOST);

        $pdoOptions = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];

        // Tentative silencieuse avec root sans mot de passe
        $pdo = null;
        try {
            $pdo = new \PDO($dsn, 'root', '', $pdoOptions);
            $io->text('<info>[OK]</info> Connexion MySQL root établie sans mot de passe.');
        } catch (\PDOException) {
            $io->text('<comment>[INFO]</comment> Connexion root sans mot de passe échouée, veuillez saisir vos identifiants.');
        }

        // Si la connexion silencieuse a échoué, demander les identifiants
        if ($pdo === null) {
            $rootUser = $io->ask('Utilisateur MySQL root', 'root');
            $rootPassword = $io->askHidden('Mot de passe MySQL root');

            try {
                $pdo = new \PDO($dsn, $rootUser, $rootPassword ?? '', $pdoOptions);
            } catch (\PDOException $e) {
                $io->error(sprintf('Impossible de se connecter à MySQL : %s', $e->getMessage()));

                return false;
            }
        }

        try {

            $host = self::DB_HOST === '127.0.0.1' ? 'localhost' : self::DB_HOST;

            $pdo->exec(sprintf(
                "CREATE USER IF NOT EXISTS '%s'@'%s' IDENTIFIED BY '%s'",
                self::DB_USER,
                $host,
                self::DB_PASSWORD
            ));

            // Accorder également depuis 127.0.0.1 pour les connexions TCP
            $pdo->exec(sprintf(
                "CREATE USER IF NOT EXISTS '%s'@'127.0.0.1' IDENTIFIED BY '%s'",
                self::DB_USER,
                self::DB_PASSWORD
            ));

            $pdo->exec(sprintf(
                "GRANT ALL PRIVILEGES ON `%s`.* TO '%s'@'%s'",
                self::DB_NAME,
                self::DB_USER,
                $host
            ));

            $pdo->exec(sprintf(
                "GRANT ALL PRIVILEGES ON `%s`.* TO '%s'@'127.0.0.1'",
                self::DB_NAME,
                self::DB_USER
            ));

            $pdo->exec('FLUSH PRIVILEGES');

            $io->text(sprintf(
                '<info>[OK]</info> Utilisateur "%s" créé avec les droits sur la base "%s".',
                self::DB_USER,
                self::DB_NAME
            ));

            return true;
        } catch (\PDOException $e) {
            $io->error(sprintf('Impossible de créer l\'utilisateur MySQL : %s', $e->getMessage()));

            return false;
        }
    }

    /**
     * Exécute une commande Symfony Console dans un sous-processus PHP indépendant.
     *
     * Chaque commande est lancée via un nouveau processus PHP afin d'éviter les
     * conflits d'état de connexion Doctrine entre les commandes (notamment les
     * savepoints de transactions laissés ouverts après les migrations).
     *
     * @param string[]        $args    Arguments à passer à `bin/console` (commande + options).
     * @param string          $label   Libellé de section affiché avant l'exécution.
     * @param SymfonyStyle    $io      Interface de style pour les messages de section.
     * @param OutputInterface $output  Interface de sortie pour l'affichage en temps réel.
     *
     * @return bool `true` si le processus s'est terminé avec le code 0, `false` sinon.
     */
    private function runSubProcess(array $args, string $label, SymfonyStyle $io, OutputInterface $output): bool
    {
        $io->section($label);

        $phpBinary = (new PhpExecutableFinder())->find();

        if ($phpBinary === false) {
            $io->error('Impossible de localiser l\'exécutable PHP.');

            return false;
        }

        $process = new Process(
            array_merge([$phpBinary, 'bin/console'], $args),
            null,
            null,
            null,
            300
        );

        $process->run(function (string $_type, string $buffer) use ($output): void {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $io->error(sprintf('La commande "%s" a échoué (code %d).', implode(' ', $args), $process->getExitCode()));

            return false;
        }

        $io->text(sprintf('<info>[OK]</info> %s terminée.', $args[0]));

        return true;
    }
}
