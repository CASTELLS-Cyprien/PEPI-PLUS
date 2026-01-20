<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:export-fixtures', description: 'Exporte la DB PEPI-PLUS en fixtures PHP')]
class ExportFixturesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // L'ordre doit respecter les dépendances de votre MLD [cite: 210, 251, 235]
        $entities = [
            'Season'    => \App\Entity\Season::class,   // [cite: 276]
            'Plant'     => \App\Entity\Plant::class,    // [cite: 210]
            'Partner'   => \App\Entity\Partner::class,  // [cite: 306]
            'User'      => \App\Entity\User::class,     // [cite: 258]
            'Stock'     => \App\Entity\Stock::class,    // [cite: 251]
            'Order'     => \App\Entity\Order::class,    // [cite: 235]
            'OrderLine' => \App\Entity\OrderLine::class // [cite: 216]
        ];

        $phpCode = "<?php\n\nnamespace App\DataFixtures;\n\nuse Doctrine\Bundle\FixturesBundle\Fixture;\nuse Doctrine\Persistence\ObjectManager;\n";
        $phpCode .= "class AppExportFixtures extends Fixture\n{\n    public function load(ObjectManager \$manager): void\n    {\n";
        $phpCode .= "        \$entities = [];\n\n"; // Tableau pour éviter les variables undefined

        foreach ($entities as $shortName => $class) {
            $items = $this->entityManager->getRepository($class)->findAll();
            $io->writeln("Exportation de : $shortName (" . count($items) . " lignes)");

            foreach ($items as $item) {
                $id = $item->getId();
                $refKey = "{$shortName}_{$id}";
                $phpCode .= "        \$entities['{$refKey}'] = new \\{$class}();\n";

                $reflect = new \ReflectionClass($item);
                foreach ($reflect->getProperties() as $prop) {
                    $name = ucfirst($prop->getName());
                    if ($name === 'Id') continue;

                    $getter = "get$name";
                    $setter = "set$name";

                    if (method_exists($item, $getter) && method_exists($item, $setter)) {
                        $val = $item->$getter();

                        if ($val instanceof \DateTimeInterface) {
                            // Détection du type (DateTime ou DateTimeImmutable)
                            $setterParam = new \ReflectionParameter([$item, $setter], 0);
                            $type = $setterParam->getType()?->getName();
                            $dateClass = ($type === 'DateTimeImmutable') ? '\DateTimeImmutable' : '\DateTime';

                            // On utilise la date réelle, sinon 'now' si la donnée est corrompue/nulle
                            $dateStr = $val ? $val->format('Y-m-d H:i:s') : 'now';
                            $phpCode .= "        \$entities['{$refKey}']->{$setter}(new {$dateClass}('{$dateStr}'));\n";
                        } elseif (is_object($val) && method_exists($val, 'getId')) {
                            // Correction Undefined : on vérifie si l'objet parent est déjà dans notre tableau
                            $targetShortName = (new \ReflectionClass($val))->getShortName();
                            $targetKey = "{$targetShortName}_{$val->getId()}";
                            $phpCode .= "        if (isset(\$entities['{$targetKey}'])) \$entities['{$refKey}']->{$setter}(\$entities['{$targetKey}']);\n";
                        } else {
                            $phpCode .= "        \$entities['{$refKey}']->{$setter}(" . var_export($val, true) . ");\n";
                        }
                    }
                }
                $phpCode .= "        \$manager->persist(\$entities['{$refKey}']);\n\n";
            }
        }

        $phpCode .= "        \$manager->flush();\n    }\n}";

        file_put_contents('src/DataFixtures/AppExportFixtures.php', $phpCode);
        $io->success('Fichier généré sans erreurs !');

        return Command::SUCCESS;
    }
}
