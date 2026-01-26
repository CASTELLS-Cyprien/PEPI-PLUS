<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class AppExportFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $entities = [];

        $entities['Season_1'] = new \App\Entity\Season();
        $entities['Season_1']->setYear(2023);
        $manager->persist($entities['Season_1']);

        $entities['Season_2'] = new \App\Entity\Season();
        $entities['Season_2']->setYear(2024);
        $manager->persist($entities['Season_2']);

        $entities['Season_3'] = new \App\Entity\Season();
        $entities['Season_3']->setYear(2025);
        $manager->persist($entities['Season_3']);

        $entities['Season_4'] = new \App\Entity\Season();
        $entities['Season_4']->setYear(2026);
        $manager->persist($entities['Season_4']);

        $entities['Season_5'] = new \App\Entity\Season();
        $entities['Season_5']->setYear(2020);
        $manager->persist($entities['Season_5']);

        $entities['Season_7'] = new \App\Entity\Season();
        $entities['Season_7']->setYear(2019);
        $manager->persist($entities['Season_7']);

        $entities['Plant_1'] = new \App\Entity\Plant();
        $entities['Plant_1']->setLatinName('Rosa');
        $entities['Plant_1']->setCommonName('Rose');
        $entities['Plant_1']->setType('Arbuste à fleurs');
        $manager->persist($entities['Plant_1']);

        $entities['Plant_2'] = new \App\Entity\Plant();
        $entities['Plant_2']->setLatinName('Lavandula angustifolia');
        $entities['Plant_2']->setCommonName('Lavande');
        $entities['Plant_2']->setType('Sous-arbrisseau');
        $manager->persist($entities['Plant_2']);

        $entities['Plant_3'] = new \App\Entity\Plant();
        $entities['Plant_3']->setLatinName('Helianthus annuus');
        $entities['Plant_3']->setCommonName('Tournesol');
        $entities['Plant_3']->setType('Fleur annuelle');
        $manager->persist($entities['Plant_3']);

        $entities['Plant_4'] = new \App\Entity\Plant();
        $entities['Plant_4']->setLatinName('Convallaria majalis');
        $entities['Plant_4']->setCommonName('Muguet');
        $entities['Plant_4']->setType('Vivace bulbeuse');
        $manager->persist($entities['Plant_4']);

        $entities['Plant_5'] = new \App\Entity\Plant();
        $entities['Plant_5']->setLatinName('Taraxacum officinale');
        $entities['Plant_5']->setCommonName('Pissenlit');
        $entities['Plant_5']->setType('Herbacée vivace');
        $manager->persist($entities['Plant_5']);

        $entities['Plant_6'] = new \App\Entity\Plant();
        $entities['Plant_6']->setLatinName('Tulipa');
        $entities['Plant_6']->setCommonName('Tulipe');
        $entities['Plant_6']->setType('Plante à bulbe');
        $manager->persist($entities['Plant_6']);

        $entities['Plant_7'] = new \App\Entity\Plant();
        $entities['Plant_7']->setLatinName('Ocimum basilicum');
        $entities['Plant_7']->setCommonName('Basilic');
        $entities['Plant_7']->setType('Herbe aromatique');
        $manager->persist($entities['Plant_7']);

        $entities['Plant_8'] = new \App\Entity\Plant();
        $entities['Plant_8']->setLatinName('Mentha x piperita');
        $entities['Plant_8']->setCommonName('Menthe poivrée');
        $entities['Plant_8']->setType('Herbe aromatique');
        $manager->persist($entities['Plant_8']);

        $entities['Plant_9'] = new \App\Entity\Plant();
        $entities['Plant_9']->setLatinName('Salvia rosmarinus');
        $entities['Plant_9']->setCommonName('Romarin');
        $entities['Plant_9']->setType('Arbuste aromatique');
        $manager->persist($entities['Plant_9']);

        $entities['Plant_10'] = new \App\Entity\Plant();
        $entities['Plant_10']->setLatinName('Papaver rhoeas');
        $entities['Plant_10']->setCommonName('Coquelicot');
        $entities['Plant_10']->setType('Fleur annuelle');
        $manager->persist($entities['Plant_10']);

        $entities['Plant_11'] = new \App\Entity\Plant();
        $entities['Plant_11']->setLatinName('Leucanthemum vulgare');
        $entities['Plant_11']->setCommonName('Marguerite');
        $entities['Plant_11']->setType('Fleur vivace');
        $manager->persist($entities['Plant_11']);

        $entities['Plant_12'] = new \App\Entity\Plant();
        $entities['Plant_12']->setLatinName('Quercus robur');
        $entities['Plant_12']->setCommonName('Chêne pédonculé');
        $entities['Plant_12']->setType('Arbre feuillu');
        $manager->persist($entities['Plant_12']);

        $entities['Plant_13'] = new \App\Entity\Plant();
        $entities['Plant_13']->setLatinName('Betula pendula');
        $entities['Plant_13']->setCommonName('Bouleau blanc');
        $entities['Plant_13']->setType('Arbre feuillu');
        $manager->persist($entities['Plant_13']);

        $entities['Plant_14'] = new \App\Entity\Plant();
        $entities['Plant_14']->setLatinName('Acer platanoides');
        $entities['Plant_14']->setCommonName('Érable plane');
        $entities['Plant_14']->setType('Arbre feuillu');
        $manager->persist($entities['Plant_14']);

        $entities['Plant_15'] = new \App\Entity\Plant();
        $entities['Plant_15']->setLatinName('Fagus sylvatica');
        $entities['Plant_15']->setCommonName('Hêtre commun');
        $entities['Plant_15']->setType('Arbre feuillu');
        $manager->persist($entities['Plant_15']);

        $entities['Plant_16'] = new \App\Entity\Plant();
        $entities['Plant_16']->setLatinName('Abies alba');
        $entities['Plant_16']->setCommonName('Sapin blanc');
        $entities['Plant_16']->setType('Conifère');
        $manager->persist($entities['Plant_16']);

        $entities['Plant_17'] = new \App\Entity\Plant();
        $entities['Plant_17']->setLatinName('Aloe barbadensis');
        $entities['Plant_17']->setCommonName('Aloe Vera');
        $entities['Plant_17']->setType('Plante succulente');
        $manager->persist($entities['Plant_17']);

        $entities['Plant_18'] = new \App\Entity\Plant();
        $entities['Plant_18']->setLatinName('Thymus vulgaris');
        $entities['Plant_18']->setCommonName('Thym');
        $entities['Plant_18']->setType('Herbe aromatique');
        $manager->persist($entities['Plant_18']);

        $entities['Plant_19'] = new \App\Entity\Plant();
        $entities['Plant_19']->setLatinName('Paeonia');
        $entities['Plant_19']->setCommonName('Pivoine');
        $entities['Plant_19']->setType('Fleur vivace');
        $manager->persist($entities['Plant_19']);

        $entities['Plant_20'] = new \App\Entity\Plant();
        $entities['Plant_20']->setLatinName('Iris germanica');
        $entities['Plant_20']->setCommonName('Iris');
        $entities['Plant_20']->setType('Plante à rhizome');
        $manager->persist($entities['Plant_20']);

        $entities['Plant_74'] = new \App\Entity\Plant();
        $entities['Plant_74']->setLatinName('Rosa rubiginosa');
        $entities['Plant_74']->setCommonName('Rosier');
        $entities['Plant_74']->setType('fleur');
        $manager->persist($entities['Plant_74']);

        $entities['Partner_1'] = new \App\Entity\Partner();
        $entities['Partner_1']->setCompanyName('Guillet');
        $entities['Partner_1']->setContactDetails('20, boulevard Bernier
48227 Traoredan');
        $manager->persist($entities['Partner_1']);

        $entities['Partner_2'] = new \App\Entity\Partner();
        $entities['Partner_2']->setCompanyName('Laroche');
        $entities['Partner_2']->setContactDetails('902, rue Christine Launay
28334 Rocher');
        $manager->persist($entities['Partner_2']);

        $entities['Partner_3'] = new \App\Entity\Partner();
        $entities['Partner_3']->setCompanyName('Riou S.A.R.L.');
        $entities['Partner_3']->setContactDetails('17, avenue Lebreton
98332 Cousin');
        $manager->persist($entities['Partner_3']);

        $entities['Partner_4'] = new \App\Entity\Partner();
        $entities['Partner_4']->setCompanyName('Lemaire');
        $entities['Partner_4']->setContactDetails('88, rue de Blanc
36045 JacquotBourg');
        $manager->persist($entities['Partner_4']);

        $entities['Partner_5'] = new \App\Entity\Partner();
        $entities['Partner_5']->setCompanyName('Delannoy');
        $entities['Partner_5']->setContactDetails('avenue Timothée Descamps
57745 Perrot');
        $manager->persist($entities['Partner_5']);

        $entities['Partner_6'] = new \App\Entity\Partner();
        $entities['Partner_6']->setCompanyName('Millet');
        $entities['Partner_6']->setContactDetails('23, rue Marion
85402 DufourBourg');
        $manager->persist($entities['Partner_6']);

        $entities['Partner_7'] = new \App\Entity\Partner();
        $entities['Partner_7']->setCompanyName('Pruvost Humbert SARL');
        $entities['Partner_7']->setContactDetails('86, rue de Robin
59973 Lebon-sur-Hubert');
        $manager->persist($entities['Partner_7']);

        $entities['Partner_8'] = new \App\Entity\Partner();
        $entities['Partner_8']->setCompanyName('Charrier');
        $entities['Partner_8']->setContactDetails('3, rue Torres
34027 Dupuy-sur-Guibert');
        $manager->persist($entities['Partner_8']);

        $entities['Partner_9'] = new \App\Entity\Partner();
        $entities['Partner_9']->setCompanyName('Delorme');
        $entities['Partner_9']->setContactDetails('70, rue Delmas
75107 Dumas');
        $manager->persist($entities['Partner_9']);

        $entities['Partner_10'] = new \App\Entity\Partner();
        $entities['Partner_10']->setCompanyName('Martineau S.A.S.');
        $entities['Partner_10']->setContactDetails('8, rue de Brunet
66564 Beguenec');
        $manager->persist($entities['Partner_10']);

        $entities['User_1'] = new \App\Entity\User();
        $entities['User_1']->setEmail('castellscyprien@gmail.com');
        $entities['User_1']->setRoles(array (
  1 => 'ROLE_ADMIN',
  2 => 'ROLE_USER',
));
        $entities['User_1']->setPassword('$2y$13$05x8uNui85x23QbX.5ZzUeN8.aHwBjg0MAoxL8tjM.HgzSZrGiEYu');
        $entities['User_1']->setLastName('CASTELLS');
        $entities['User_1']->setFirstName('Cyprien');
        $entities['User_1']->setPartner(NULL);
        $manager->persist($entities['User_1']);

        $entities['User_2'] = new \App\Entity\User();
        $entities['User_2']->setEmail('partner@gmail.com');
        $entities['User_2']->setRoles(array (
  1 => 'ROLE_PARTNER',
  2 => 'ROLE_USER',
));
        $entities['User_2']->setPassword('$2y$13$Sso1/.lRLcCXIXh.3Gw2dOEeCZHgDn1HoNEbFru8FtmHLbzHgMmAa');
        $entities['User_2']->setLastName('Roche');
        $entities['User_2']->setFirstName('Élisabeth');
        if (isset($entities['Partner_1'])) $entities['User_2']->setPartner($entities['Partner_1']);
        $manager->persist($entities['User_2']);

        $entities['User_3'] = new \App\Entity\User();
        $entities['User_3']->setEmail('collaborateur@gmail.com');
        $entities['User_3']->setRoles(array (
  0 => 'ROLE_COLLABORATOR',
  1 => 'ROLE_USER',
));
        $entities['User_3']->setPassword('$2y$13$Ga/LgPM1M18KLSo0cSXGzuPpBdI1nthU84JEHo2P9aYiNShOd03ZS');
        $entities['User_3']->setLastName('Moreau');
        $entities['User_3']->setFirstName('Nathan');
        $entities['User_3']->setPartner(NULL);
        $manager->persist($entities['User_3']);

        $entities['User_4'] = new \App\Entity\User();
        $entities['User_4']->setEmail('chloe.dubois@gmail.com');
        $entities['User_4']->setRoles(array (
  1 => 'ROLE_PARTNER',
  2 => 'ROLE_USER',
));
        $entities['User_4']->setPassword('$2y$13$qEgxARQQJhMTrTIRY.rVeOGf34Dnn86FAc/h6pSinDJa0Bn6/QDzy');
        $entities['User_4']->setLastName('Dubois');
        $entities['User_4']->setFirstName('Chloé');
        if (isset($entities['Partner_2'])) $entities['User_4']->setPartner($entities['Partner_2']);
        $manager->persist($entities['User_4']);

        $entities['User_5'] = new \App\Entity\User();
        $entities['User_5']->setEmail('hugo.durand@gmail.com');
        $entities['User_5']->setRoles(array (
  1 => 'ROLE_PARTNER',
  2 => 'ROLE_USER',
));
        $entities['User_5']->setPassword('$2y$13$rKbBx6fAgguL4ndMhfIQYuQQweHV.vfI.2nHgh6im1JApsM0gkEuC');
        $entities['User_5']->setLastName('Durand');
        $entities['User_5']->setFirstName('Hugo');
        if (isset($entities['Partner_3'])) $entities['User_5']->setPartner($entities['Partner_3']);
        $manager->persist($entities['User_5']);

        $entities['User_9'] = new \App\Entity\User();
        $entities['User_9']->setEmail('emma.richard@gmail.com');
        $entities['User_9']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_9']->setPassword('$2y$13$21PX7OQN90XkXOMe8FHToOiDtIymYhnDEJ2yqfGkn7K3nBU4mLs/u');
        $entities['User_9']->setLastName('Richard');
        $entities['User_9']->setFirstName('Emma');
        if (isset($entities['Partner_8'])) $entities['User_9']->setPartner($entities['Partner_8']);
        $manager->persist($entities['User_9']);

        $entities['User_10'] = new \App\Entity\User();
        $entities['User_10']->setEmail('lucas.robert@gmail.com');
        $entities['User_10']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_10']->setPassword('$2y$13$556R.wMWlz3ntmbYBgubC.bfEPymWdtmhcGExEcWWV1DRnkS4AVB6');
        $entities['User_10']->setLastName('Robert');
        $entities['User_10']->setFirstName('Lucas');
        if (isset($entities['Partner_5'])) $entities['User_10']->setPartner($entities['Partner_5']);
        $manager->persist($entities['User_10']);

        $entities['User_11'] = new \App\Entity\User();
        $entities['User_11']->setEmail('sophii.petit@gmail.com');
        $entities['User_11']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_11']->setPassword('$2y$13$51YrE7rzkrxCZcM1W/dwsuLzGIw3CkkwgpQOkHscNC.jOAwjc2FJC');
        $entities['User_11']->setLastName('Petit');
        $entities['User_11']->setFirstName('Sophie');
        if (isset($entities['Partner_9'])) $entities['User_11']->setPartner($entities['Partner_9']);
        $manager->persist($entities['User_11']);

        $entities['User_12'] = new \App\Entity\User();
        $entities['User_12']->setEmail('t.bernard@gmail.com');
        $entities['User_12']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_12']->setPassword('$2y$13$LrAJ9NvNBYDYJ5VySc3.eekuk81oZVtMhO0OSYStC/2McgeCJy63O');
        $entities['User_12']->setLastName('Bernard');
        $entities['User_12']->setFirstName('Thomas');
        if (isset($entities['Partner_4'])) $entities['User_12']->setPartner($entities['Partner_4']);
        $manager->persist($entities['User_12']);

        $entities['User_13'] = new \App\Entity\User();
        $entities['User_13']->setEmail('m.lefebvre@gmail.com');
        $entities['User_13']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_13']->setPassword('$2y$13$4Sz2UcCORwso7O0ehIPfvOw7NZFJDDg4rjHMZqKW9gZY0XqhmSNoG');
        $entities['User_13']->setLastName('Lefebvre');
        $entities['User_13']->setFirstName('Marie');
        if (isset($entities['Partner_10'])) $entities['User_13']->setPartner($entities['Partner_10']);
        $manager->persist($entities['User_13']);

        $entities['User_14'] = new \App\Entity\User();
        $entities['User_14']->setEmail('jean.dupont@gmail.com');
        $entities['User_14']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_14']->setPassword('$2y$13$1NWW/CLmg5oBOdvrZkEdEORJi0vaLh5ZcpmQhh/4i9IxvGhSTqb1i');
        $entities['User_14']->setLastName('Dupont');
        $entities['User_14']->setFirstName('Jean');
        if (isset($entities['Partner_6'])) $entities['User_14']->setPartner($entities['Partner_6']);
        $manager->persist($entities['User_14']);

        $entities['User_15'] = new \App\Entity\User();
        $entities['User_15']->setEmail('mannon.michel@gmail.com');
        $entities['User_15']->setRoles(array (
  0 => 'ROLE_PARTNER',
  1 => 'ROLE_USER',
));
        $entities['User_15']->setPassword('$2y$13$a8TsLk2/NRRDRUpPIEimaeKaNOQar7FZz58fQa19BX4jj6CV8mtIi');
        $entities['User_15']->setLastName('Michel');
        $entities['User_15']->setFirstName('Mannon');
        if (isset($entities['Partner_7'])) $entities['User_15']->setPartner($entities['Partner_7']);
        $manager->persist($entities['User_15']);

        $entities['Stock_1'] = new \App\Entity\Stock();
        $entities['Stock_1']->setQuantity(250);
        if (isset($entities['Plant_19'])) $entities['Stock_1']->setPlant($entities['Plant_19']);
        if (isset($entities['Packaging_4'])) $entities['Stock_1']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_1'])) $entities['Stock_1']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_5'])) $entities['Stock_1']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_1']);

        $entities['Stock_2'] = new \App\Entity\Stock();
        $entities['Stock_2']->setQuantity(401);
        if (isset($entities['Plant_4'])) $entities['Stock_2']->setPlant($entities['Plant_4']);
        if (isset($entities['Packaging_3'])) $entities['Stock_2']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_4'])) $entities['Stock_2']->setSeason($entities['Season_4']);
        $entities['Stock_2']->setPartner(NULL);
        $manager->persist($entities['Stock_2']);

        $entities['Stock_3'] = new \App\Entity\Stock();
        $entities['Stock_3']->setQuantity(81);
        if (isset($entities['Plant_12'])) $entities['Stock_3']->setPlant($entities['Plant_12']);
        if (isset($entities['Packaging_5'])) $entities['Stock_3']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_1'])) $entities['Stock_3']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_9'])) $entities['Stock_3']->setPartner($entities['Partner_9']);
        $manager->persist($entities['Stock_3']);

        $entities['Stock_4'] = new \App\Entity\Stock();
        $entities['Stock_4']->setQuantity(85);
        if (isset($entities['Plant_12'])) $entities['Stock_4']->setPlant($entities['Plant_12']);
        if (isset($entities['Packaging_1'])) $entities['Stock_4']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_2'])) $entities['Stock_4']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_2'])) $entities['Stock_4']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_4']);

        $entities['Stock_5'] = new \App\Entity\Stock();
        $entities['Stock_5']->setQuantity(185);
        if (isset($entities['Plant_13'])) $entities['Stock_5']->setPlant($entities['Plant_13']);
        if (isset($entities['Packaging_4'])) $entities['Stock_5']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_4'])) $entities['Stock_5']->setSeason($entities['Season_4']);
        $entities['Stock_5']->setPartner(NULL);
        $manager->persist($entities['Stock_5']);

        $entities['Stock_6'] = new \App\Entity\Stock();
        $entities['Stock_6']->setQuantity(194);
        if (isset($entities['Plant_14'])) $entities['Stock_6']->setPlant($entities['Plant_14']);
        if (isset($entities['Packaging_1'])) $entities['Stock_6']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_3'])) $entities['Stock_6']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_5'])) $entities['Stock_6']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_6']);

        $entities['Stock_7'] = new \App\Entity\Stock();
        $entities['Stock_7']->setQuantity(45);
        if (isset($entities['Plant_7'])) $entities['Stock_7']->setPlant($entities['Plant_7']);
        if (isset($entities['Packaging_2'])) $entities['Stock_7']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_3'])) $entities['Stock_7']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_5'])) $entities['Stock_7']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_7']);

        $entities['Stock_8'] = new \App\Entity\Stock();
        $entities['Stock_8']->setQuantity(25);
        if (isset($entities['Plant_8'])) $entities['Stock_8']->setPlant($entities['Plant_8']);
        if (isset($entities['Packaging_1'])) $entities['Stock_8']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_3'])) $entities['Stock_8']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_1'])) $entities['Stock_8']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_8']);

        $entities['Stock_9'] = new \App\Entity\Stock();
        $entities['Stock_9']->setQuantity(250);
        if (isset($entities['Plant_7'])) $entities['Stock_9']->setPlant($entities['Plant_7']);
        if (isset($entities['Packaging_3'])) $entities['Stock_9']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_1'])) $entities['Stock_9']->setSeason($entities['Season_1']);
        $entities['Stock_9']->setPartner(NULL);
        $manager->persist($entities['Stock_9']);

        $entities['Stock_10'] = new \App\Entity\Stock();
        $entities['Stock_10']->setQuantity(269);
        if (isset($entities['Plant_9'])) $entities['Stock_10']->setPlant($entities['Plant_9']);
        if (isset($entities['Packaging_3'])) $entities['Stock_10']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_1'])) $entities['Stock_10']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_2'])) $entities['Stock_10']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_10']);

        $entities['Stock_11'] = new \App\Entity\Stock();
        $entities['Stock_11']->setQuantity(344);
        if (isset($entities['Plant_15'])) $entities['Stock_11']->setPlant($entities['Plant_15']);
        if (isset($entities['Packaging_1'])) $entities['Stock_11']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_4'])) $entities['Stock_11']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_4'])) $entities['Stock_11']->setPartner($entities['Partner_4']);
        $manager->persist($entities['Stock_11']);

        $entities['Stock_12'] = new \App\Entity\Stock();
        $entities['Stock_12']->setQuantity(116);
        if (isset($entities['Plant_5'])) $entities['Stock_12']->setPlant($entities['Plant_5']);
        if (isset($entities['Packaging_4'])) $entities['Stock_12']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_2'])) $entities['Stock_12']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_9'])) $entities['Stock_12']->setPartner($entities['Partner_9']);
        $manager->persist($entities['Stock_12']);

        $entities['Stock_13'] = new \App\Entity\Stock();
        $entities['Stock_13']->setQuantity(325);
        if (isset($entities['Plant_20'])) $entities['Stock_13']->setPlant($entities['Plant_20']);
        if (isset($entities['Packaging_1'])) $entities['Stock_13']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_3'])) $entities['Stock_13']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_5'])) $entities['Stock_13']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_13']);

        $entities['Stock_14'] = new \App\Entity\Stock();
        $entities['Stock_14']->setQuantity(101);
        if (isset($entities['Plant_13'])) $entities['Stock_14']->setPlant($entities['Plant_13']);
        if (isset($entities['Packaging_4'])) $entities['Stock_14']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_3'])) $entities['Stock_14']->setSeason($entities['Season_3']);
        $entities['Stock_14']->setPartner(NULL);
        $manager->persist($entities['Stock_14']);

        $entities['Stock_15'] = new \App\Entity\Stock();
        $entities['Stock_15']->setQuantity(90);
        if (isset($entities['Plant_20'])) $entities['Stock_15']->setPlant($entities['Plant_20']);
        if (isset($entities['Packaging_5'])) $entities['Stock_15']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_1'])) $entities['Stock_15']->setSeason($entities['Season_1']);
        $entities['Stock_15']->setPartner(NULL);
        $manager->persist($entities['Stock_15']);

        $entities['Stock_16'] = new \App\Entity\Stock();
        $entities['Stock_16']->setQuantity(329);
        if (isset($entities['Plant_18'])) $entities['Stock_16']->setPlant($entities['Plant_18']);
        if (isset($entities['Packaging_5'])) $entities['Stock_16']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_4'])) $entities['Stock_16']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_4'])) $entities['Stock_16']->setPartner($entities['Partner_4']);
        $manager->persist($entities['Stock_16']);

        $entities['Stock_17'] = new \App\Entity\Stock();
        $entities['Stock_17']->setQuantity(80);
        if (isset($entities['Plant_17'])) $entities['Stock_17']->setPlant($entities['Plant_17']);
        if (isset($entities['Packaging_2'])) $entities['Stock_17']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_1'])) $entities['Stock_17']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_1'])) $entities['Stock_17']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_17']);

        $entities['Stock_18'] = new \App\Entity\Stock();
        $entities['Stock_18']->setQuantity(269);
        if (isset($entities['Plant_2'])) $entities['Stock_18']->setPlant($entities['Plant_2']);
        if (isset($entities['Packaging_2'])) $entities['Stock_18']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_18']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_6'])) $entities['Stock_18']->setPartner($entities['Partner_6']);
        $manager->persist($entities['Stock_18']);

        $entities['Stock_19'] = new \App\Entity\Stock();
        $entities['Stock_19']->setQuantity(104);
        if (isset($entities['Plant_10'])) $entities['Stock_19']->setPlant($entities['Plant_10']);
        if (isset($entities['Packaging_3'])) $entities['Stock_19']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_1'])) $entities['Stock_19']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_7'])) $entities['Stock_19']->setPartner($entities['Partner_7']);
        $manager->persist($entities['Stock_19']);

        $entities['Stock_20'] = new \App\Entity\Stock();
        $entities['Stock_20']->setQuantity(57);
        if (isset($entities['Plant_19'])) $entities['Stock_20']->setPlant($entities['Plant_19']);
        if (isset($entities['Packaging_1'])) $entities['Stock_20']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_3'])) $entities['Stock_20']->setSeason($entities['Season_3']);
        $entities['Stock_20']->setPartner(NULL);
        $manager->persist($entities['Stock_20']);

        $entities['Stock_21'] = new \App\Entity\Stock();
        $entities['Stock_21']->setQuantity(317);
        if (isset($entities['Plant_8'])) $entities['Stock_21']->setPlant($entities['Plant_8']);
        if (isset($entities['Packaging_4'])) $entities['Stock_21']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_4'])) $entities['Stock_21']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_7'])) $entities['Stock_21']->setPartner($entities['Partner_7']);
        $manager->persist($entities['Stock_21']);

        $entities['Stock_22'] = new \App\Entity\Stock();
        $entities['Stock_22']->setQuantity(177);
        if (isset($entities['Plant_10'])) $entities['Stock_22']->setPlant($entities['Plant_10']);
        if (isset($entities['Packaging_3'])) $entities['Stock_22']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_3'])) $entities['Stock_22']->setSeason($entities['Season_3']);
        $entities['Stock_22']->setPartner(NULL);
        $manager->persist($entities['Stock_22']);

        $entities['Stock_23'] = new \App\Entity\Stock();
        $entities['Stock_23']->setQuantity(40);
        if (isset($entities['Plant_14'])) $entities['Stock_23']->setPlant($entities['Plant_14']);
        if (isset($entities['Packaging_5'])) $entities['Stock_23']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_3'])) $entities['Stock_23']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_1'])) $entities['Stock_23']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_23']);

        $entities['Stock_24'] = new \App\Entity\Stock();
        $entities['Stock_24']->setQuantity(9);
        if (isset($entities['Plant_9'])) $entities['Stock_24']->setPlant($entities['Plant_9']);
        if (isset($entities['Packaging_3'])) $entities['Stock_24']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_2'])) $entities['Stock_24']->setSeason($entities['Season_2']);
        $entities['Stock_24']->setPartner(NULL);
        $manager->persist($entities['Stock_24']);

        $entities['Stock_25'] = new \App\Entity\Stock();
        $entities['Stock_25']->setQuantity(302);
        if (isset($entities['Plant_19'])) $entities['Stock_25']->setPlant($entities['Plant_19']);
        if (isset($entities['Packaging_1'])) $entities['Stock_25']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_1'])) $entities['Stock_25']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_1'])) $entities['Stock_25']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_25']);

        $entities['Stock_26'] = new \App\Entity\Stock();
        $entities['Stock_26']->setQuantity(147);
        if (isset($entities['Plant_15'])) $entities['Stock_26']->setPlant($entities['Plant_15']);
        if (isset($entities['Packaging_4'])) $entities['Stock_26']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_4'])) $entities['Stock_26']->setSeason($entities['Season_4']);
        $entities['Stock_26']->setPartner(NULL);
        $manager->persist($entities['Stock_26']);

        $entities['Stock_27'] = new \App\Entity\Stock();
        $entities['Stock_27']->setQuantity(314);
        if (isset($entities['Plant_19'])) $entities['Stock_27']->setPlant($entities['Plant_19']);
        if (isset($entities['Packaging_5'])) $entities['Stock_27']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_1'])) $entities['Stock_27']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_8'])) $entities['Stock_27']->setPartner($entities['Partner_8']);
        $manager->persist($entities['Stock_27']);

        $entities['Stock_28'] = new \App\Entity\Stock();
        $entities['Stock_28']->setQuantity(313);
        if (isset($entities['Plant_6'])) $entities['Stock_28']->setPlant($entities['Plant_6']);
        if (isset($entities['Packaging_3'])) $entities['Stock_28']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_1'])) $entities['Stock_28']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_3'])) $entities['Stock_28']->setPartner($entities['Partner_3']);
        $manager->persist($entities['Stock_28']);

        $entities['Stock_29'] = new \App\Entity\Stock();
        $entities['Stock_29']->setQuantity(22);
        if (isset($entities['Plant_20'])) $entities['Stock_29']->setPlant($entities['Plant_20']);
        if (isset($entities['Packaging_4'])) $entities['Stock_29']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_2'])) $entities['Stock_29']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_2'])) $entities['Stock_29']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_29']);

        $entities['Stock_30'] = new \App\Entity\Stock();
        $entities['Stock_30']->setQuantity(375);
        if (isset($entities['Plant_16'])) $entities['Stock_30']->setPlant($entities['Plant_16']);
        if (isset($entities['Packaging_4'])) $entities['Stock_30']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_1'])) $entities['Stock_30']->setSeason($entities['Season_1']);
        $entities['Stock_30']->setPartner(NULL);
        $manager->persist($entities['Stock_30']);

        $entities['Stock_31'] = new \App\Entity\Stock();
        $entities['Stock_31']->setQuantity(34);
        if (isset($entities['Plant_18'])) $entities['Stock_31']->setPlant($entities['Plant_18']);
        if (isset($entities['Packaging_4'])) $entities['Stock_31']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_2'])) $entities['Stock_31']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_4'])) $entities['Stock_31']->setPartner($entities['Partner_4']);
        $manager->persist($entities['Stock_31']);

        $entities['Stock_32'] = new \App\Entity\Stock();
        $entities['Stock_32']->setQuantity(222);
        if (isset($entities['Plant_9'])) $entities['Stock_32']->setPlant($entities['Plant_9']);
        if (isset($entities['Packaging_5'])) $entities['Stock_32']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_2'])) $entities['Stock_32']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_10'])) $entities['Stock_32']->setPartner($entities['Partner_10']);
        $manager->persist($entities['Stock_32']);

        $entities['Stock_33'] = new \App\Entity\Stock();
        $entities['Stock_33']->setQuantity(121);
        if (isset($entities['Plant_17'])) $entities['Stock_33']->setPlant($entities['Plant_17']);
        if (isset($entities['Packaging_5'])) $entities['Stock_33']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_4'])) $entities['Stock_33']->setSeason($entities['Season_4']);
        $entities['Stock_33']->setPartner(NULL);
        $manager->persist($entities['Stock_33']);

        $entities['Stock_34'] = new \App\Entity\Stock();
        $entities['Stock_34']->setQuantity(76);
        if (isset($entities['Plant_13'])) $entities['Stock_34']->setPlant($entities['Plant_13']);
        if (isset($entities['Packaging_1'])) $entities['Stock_34']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_4'])) $entities['Stock_34']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_10'])) $entities['Stock_34']->setPartner($entities['Partner_10']);
        $manager->persist($entities['Stock_34']);

        $entities['Stock_35'] = new \App\Entity\Stock();
        $entities['Stock_35']->setQuantity(270);
        if (isset($entities['Plant_12'])) $entities['Stock_35']->setPlant($entities['Plant_12']);
        if (isset($entities['Packaging_2'])) $entities['Stock_35']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_35']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_7'])) $entities['Stock_35']->setPartner($entities['Partner_7']);
        $manager->persist($entities['Stock_35']);

        $entities['Stock_36'] = new \App\Entity\Stock();
        $entities['Stock_36']->setQuantity(77);
        if (isset($entities['Plant_13'])) $entities['Stock_36']->setPlant($entities['Plant_13']);
        if (isset($entities['Packaging_3'])) $entities['Stock_36']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_4'])) $entities['Stock_36']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_9'])) $entities['Stock_36']->setPartner($entities['Partner_9']);
        $manager->persist($entities['Stock_36']);

        $entities['Stock_37'] = new \App\Entity\Stock();
        $entities['Stock_37']->setQuantity(310);
        if (isset($entities['Plant_5'])) $entities['Stock_37']->setPlant($entities['Plant_5']);
        if (isset($entities['Packaging_1'])) $entities['Stock_37']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_3'])) $entities['Stock_37']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_10'])) $entities['Stock_37']->setPartner($entities['Partner_10']);
        $manager->persist($entities['Stock_37']);

        $entities['Stock_38'] = new \App\Entity\Stock();
        $entities['Stock_38']->setQuantity(67);
        if (isset($entities['Plant_20'])) $entities['Stock_38']->setPlant($entities['Plant_20']);
        if (isset($entities['Packaging_2'])) $entities['Stock_38']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_38']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_2'])) $entities['Stock_38']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_38']);

        $entities['Stock_39'] = new \App\Entity\Stock();
        $entities['Stock_39']->setQuantity(28);
        if (isset($entities['Plant_3'])) $entities['Stock_39']->setPlant($entities['Plant_3']);
        if (isset($entities['Packaging_2'])) $entities['Stock_39']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_39']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_1'])) $entities['Stock_39']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_39']);

        $entities['Stock_40'] = new \App\Entity\Stock();
        $entities['Stock_40']->setQuantity(131);
        if (isset($entities['Plant_8'])) $entities['Stock_40']->setPlant($entities['Plant_8']);
        if (isset($entities['Packaging_3'])) $entities['Stock_40']->setPackaging($entities['Packaging_3']);
        if (isset($entities['Season_3'])) $entities['Stock_40']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_4'])) $entities['Stock_40']->setPartner($entities['Partner_4']);
        $manager->persist($entities['Stock_40']);

        $entities['Stock_41'] = new \App\Entity\Stock();
        $entities['Stock_41']->setQuantity(23);
        if (isset($entities['Plant_11'])) $entities['Stock_41']->setPlant($entities['Plant_11']);
        if (isset($entities['Packaging_4'])) $entities['Stock_41']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_1'])) $entities['Stock_41']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_5'])) $entities['Stock_41']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_41']);

        $entities['Stock_42'] = new \App\Entity\Stock();
        $entities['Stock_42']->setQuantity(340);
        if (isset($entities['Plant_14'])) $entities['Stock_42']->setPlant($entities['Plant_14']);
        if (isset($entities['Packaging_2'])) $entities['Stock_42']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_3'])) $entities['Stock_42']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_2'])) $entities['Stock_42']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_42']);

        $entities['Stock_43'] = new \App\Entity\Stock();
        $entities['Stock_43']->setQuantity(122);
        if (isset($entities['Plant_6'])) $entities['Stock_43']->setPlant($entities['Plant_6']);
        if (isset($entities['Packaging_2'])) $entities['Stock_43']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_1'])) $entities['Stock_43']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_7'])) $entities['Stock_43']->setPartner($entities['Partner_7']);
        $manager->persist($entities['Stock_43']);

        $entities['Stock_45'] = new \App\Entity\Stock();
        $entities['Stock_45']->setQuantity(340);
        if (isset($entities['Plant_14'])) $entities['Stock_45']->setPlant($entities['Plant_14']);
        if (isset($entities['Packaging_5'])) $entities['Stock_45']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_4'])) $entities['Stock_45']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_5'])) $entities['Stock_45']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_45']);

        $entities['Stock_46'] = new \App\Entity\Stock();
        $entities['Stock_46']->setQuantity(227);
        if (isset($entities['Plant_4'])) $entities['Stock_46']->setPlant($entities['Plant_4']);
        if (isset($entities['Packaging_2'])) $entities['Stock_46']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_46']->setSeason($entities['Season_4']);
        if (isset($entities['Partner_2'])) $entities['Stock_46']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_46']);

        $entities['Stock_47'] = new \App\Entity\Stock();
        $entities['Stock_47']->setQuantity(340);
        if (isset($entities['Plant_1'])) $entities['Stock_47']->setPlant($entities['Plant_1']);
        if (isset($entities['Packaging_4'])) $entities['Stock_47']->setPackaging($entities['Packaging_4']);
        if (isset($entities['Season_3'])) $entities['Stock_47']->setSeason($entities['Season_3']);
        if (isset($entities['Partner_5'])) $entities['Stock_47']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_47']);

        $entities['Stock_48'] = new \App\Entity\Stock();
        $entities['Stock_48']->setQuantity(241);
        if (isset($entities['Plant_8'])) $entities['Stock_48']->setPlant($entities['Plant_8']);
        if (isset($entities['Packaging_2'])) $entities['Stock_48']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_1'])) $entities['Stock_48']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_1'])) $entities['Stock_48']->setPartner($entities['Partner_1']);
        $manager->persist($entities['Stock_48']);

        $entities['Stock_49'] = new \App\Entity\Stock();
        $entities['Stock_49']->setQuantity(243);
        if (isset($entities['Plant_4'])) $entities['Stock_49']->setPlant($entities['Plant_4']);
        if (isset($entities['Packaging_1'])) $entities['Stock_49']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_2'])) $entities['Stock_49']->setSeason($entities['Season_2']);
        if (isset($entities['Partner_5'])) $entities['Stock_49']->setPartner($entities['Partner_5']);
        $manager->persist($entities['Stock_49']);

        $entities['Stock_50'] = new \App\Entity\Stock();
        $entities['Stock_50']->setQuantity(286);
        if (isset($entities['Plant_7'])) $entities['Stock_50']->setPlant($entities['Plant_7']);
        if (isset($entities['Packaging_2'])) $entities['Stock_50']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_1'])) $entities['Stock_50']->setSeason($entities['Season_1']);
        if (isset($entities['Partner_2'])) $entities['Stock_50']->setPartner($entities['Partner_2']);
        $manager->persist($entities['Stock_50']);

        $entities['Stock_52'] = new \App\Entity\Stock();
        $entities['Stock_52']->setQuantity(50);
        if (isset($entities['Plant_19'])) $entities['Stock_52']->setPlant($entities['Plant_19']);
        if (isset($entities['Packaging_1'])) $entities['Stock_52']->setPackaging($entities['Packaging_1']);
        if (isset($entities['Season_1'])) $entities['Stock_52']->setSeason($entities['Season_1']);
        $entities['Stock_52']->setPartner(NULL);
        $manager->persist($entities['Stock_52']);

        $entities['Stock_53'] = new \App\Entity\Stock();
        $entities['Stock_53']->setQuantity(20);
        if (isset($entities['Plant_2'])) $entities['Stock_53']->setPlant($entities['Plant_2']);
        if (isset($entities['Packaging_2'])) $entities['Stock_53']->setPackaging($entities['Packaging_2']);
        if (isset($entities['Season_4'])) $entities['Stock_53']->setSeason($entities['Season_4']);
        $entities['Stock_53']->setPartner(NULL);
        $manager->persist($entities['Stock_53']);

        $entities['Stock_54'] = new \App\Entity\Stock();
        $entities['Stock_54']->setQuantity(15);
        if (isset($entities['Plant_18'])) $entities['Stock_54']->setPlant($entities['Plant_18']);
        if (isset($entities['Packaging_5'])) $entities['Stock_54']->setPackaging($entities['Packaging_5']);
        if (isset($entities['Season_4'])) $entities['Stock_54']->setSeason($entities['Season_4']);
        $entities['Stock_54']->setPartner(NULL);
        $manager->persist($entities['Stock_54']);

        $entities['Order_19'] = new \App\Entity\Order();
        $entities['Order_19']->setOrderNumber('CMD-A5D8DD40');
        $entities['Order_19']->setStatus('Livrée');
        $entities['Order_19']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:29:18'));
        if (isset($entities['User_1'])) $entities['Order_19']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_19']);

        $entities['Order_20'] = new \App\Entity\Order();
        $entities['Order_20']->setOrderNumber('CMD-E1716062');
        $entities['Order_20']->setStatus('Annulée');
        $entities['Order_20']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:29:28'));
        if (isset($entities['User_1'])) $entities['Order_20']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_20']);

        $entities['Order_21'] = new \App\Entity\Order();
        $entities['Order_21']->setOrderNumber('CMD-E9C76D67');
        $entities['Order_21']->setStatus('Livrée');
        $entities['Order_21']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:29:37'));
        if (isset($entities['User_1'])) $entities['Order_21']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_21']);

        $entities['Order_22'] = new \App\Entity\Order();
        $entities['Order_22']->setOrderNumber('CMD-2A01B697');
        $entities['Order_22']->setStatus('Réservation');
        $entities['Order_22']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:29:45'));
        if (isset($entities['User_1'])) $entities['Order_22']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_22']);

        $entities['Order_23'] = new \App\Entity\Order();
        $entities['Order_23']->setOrderNumber('CMD-903AF58F');
        $entities['Order_23']->setStatus('Réservation');
        $entities['Order_23']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:29:56'));
        if (isset($entities['User_1'])) $entities['Order_23']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_23']);

        $entities['Order_24'] = new \App\Entity\Order();
        $entities['Order_24']->setOrderNumber('CMD-799187E8');
        $entities['Order_24']->setStatus('Réservation');
        $entities['Order_24']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:30:05'));
        if (isset($entities['User_1'])) $entities['Order_24']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_24']);

        $entities['Order_25'] = new \App\Entity\Order();
        $entities['Order_25']->setOrderNumber('CMD-DB7FB83D');
        $entities['Order_25']->setStatus('Annulée');
        $entities['Order_25']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:30:13'));
        if (isset($entities['User_1'])) $entities['Order_25']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_25']);

        $entities['Order_26'] = new \App\Entity\Order();
        $entities['Order_26']->setOrderNumber('CMD-41E34563');
        $entities['Order_26']->setStatus('Livrée');
        $entities['Order_26']->setCreatedAt(new \DateTimeImmutable('2026-01-15 11:30:20'));
        if (isset($entities['User_1'])) $entities['Order_26']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_26']);

        $entities['Order_27'] = new \App\Entity\Order();
        $entities['Order_27']->setOrderNumber('CMD-BA7C40A0');
        $entities['Order_27']->setStatus('Réservation');
        $entities['Order_27']->setCreatedAt(new \DateTimeImmutable('2026-01-16 10:56:06'));
        if (isset($entities['User_3'])) $entities['Order_27']->setCollaborator($entities['User_3']);
        $manager->persist($entities['Order_27']);

        $entities['Order_28'] = new \App\Entity\Order();
        $entities['Order_28']->setOrderNumber('CMD-BC97D781');
        $entities['Order_28']->setStatus('Livrée');
        $entities['Order_28']->setCreatedAt(new \DateTimeImmutable('2026-01-19 09:16:36'));
        if (isset($entities['User_1'])) $entities['Order_28']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_28']);

        $entities['Order_29'] = new \App\Entity\Order();
        $entities['Order_29']->setOrderNumber('CMD-E5EC138C');
        $entities['Order_29']->setStatus('Réservation');
        $entities['Order_29']->setCreatedAt(new \DateTimeImmutable('2026-01-19 09:27:38'));
        if (isset($entities['User_1'])) $entities['Order_29']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_29']);

        $entities['Order_30'] = new \App\Entity\Order();
        $entities['Order_30']->setOrderNumber('CMD-11BCA3BE');
        $entities['Order_30']->setStatus('Livrée');
        $entities['Order_30']->setCreatedAt(new \DateTimeImmutable('2026-01-19 09:40:35'));
        if (isset($entities['User_1'])) $entities['Order_30']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_30']);

        $entities['Order_31'] = new \App\Entity\Order();
        $entities['Order_31']->setOrderNumber('CMD-B7894EF7');
        $entities['Order_31']->setStatus('Annulée');
        $entities['Order_31']->setCreatedAt(new \DateTimeImmutable('2026-01-23 15:11:51'));
        if (isset($entities['User_1'])) $entities['Order_31']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_31']);

        $entities['Order_32'] = new \App\Entity\Order();
        $entities['Order_32']->setOrderNumber('CMD-1A584D6B');
        $entities['Order_32']->setStatus('Annulée');
        $entities['Order_32']->setCreatedAt(new \DateTimeImmutable('2026-01-23 15:17:44'));
        if (isset($entities['User_1'])) $entities['Order_32']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_32']);

        $entities['Order_33'] = new \App\Entity\Order();
        $entities['Order_33']->setOrderNumber('CMD-ED7987B4');
        $entities['Order_33']->setStatus('Réservation');
        $entities['Order_33']->setCreatedAt(new \DateTimeImmutable('2026-01-26 08:34:33'));
        if (isset($entities['User_1'])) $entities['Order_33']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_33']);

        $entities['Order_34'] = new \App\Entity\Order();
        $entities['Order_34']->setOrderNumber('CMD-3E72CE55');
        $entities['Order_34']->setStatus('Livrée');
        $entities['Order_34']->setCreatedAt(new \DateTimeImmutable('2026-01-26 08:45:08'));
        if (isset($entities['User_1'])) $entities['Order_34']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_34']);

        $entities['Order_35'] = new \App\Entity\Order();
        $entities['Order_35']->setOrderNumber('CMD-B148FFAC');
        $entities['Order_35']->setStatus('Annulée');
        $entities['Order_35']->setCreatedAt(new \DateTimeImmutable('2026-01-26 09:01:10'));
        if (isset($entities['User_1'])) $entities['Order_35']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_35']);

        $entities['Order_36'] = new \App\Entity\Order();
        $entities['Order_36']->setOrderNumber('CMD-AE7177F2');
        $entities['Order_36']->setStatus('Annulée');
        $entities['Order_36']->setCreatedAt(new \DateTimeImmutable('2026-01-26 09:15:26'));
        if (isset($entities['User_1'])) $entities['Order_36']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_36']);

        $entities['Order_37'] = new \App\Entity\Order();
        $entities['Order_37']->setOrderNumber('CMD-B885A6AF');
        $entities['Order_37']->setStatus('Annulée');
        $entities['Order_37']->setCreatedAt(new \DateTimeImmutable('2026-01-26 09:16:12'));
        if (isset($entities['User_1'])) $entities['Order_37']->setCollaborator($entities['User_1']);
        $manager->persist($entities['Order_37']);

        $entities['OrderLine_42'] = new \App\Entity\OrderLine();
        $entities['OrderLine_42']->setQuantity(50);
        if (isset($entities['Stock_47'])) $entities['OrderLine_42']->setStock($entities['Stock_47']);
        if (isset($entities['Order_19'])) $entities['OrderLine_42']->setPurchaseOrder($entities['Order_19']);
        $manager->persist($entities['OrderLine_42']);

        $entities['OrderLine_43'] = new \App\Entity\OrderLine();
        $entities['OrderLine_43']->setQuantity(88);
        if (isset($entities['Stock_45'])) $entities['OrderLine_43']->setStock($entities['Stock_45']);
        if (isset($entities['Order_20'])) $entities['OrderLine_43']->setPurchaseOrder($entities['Order_20']);
        $manager->persist($entities['OrderLine_43']);

        $entities['OrderLine_44'] = new \App\Entity\OrderLine();
        $entities['OrderLine_44']->setQuantity(70);
        if (isset($entities['Stock_47'])) $entities['OrderLine_44']->setStock($entities['Stock_47']);
        if (isset($entities['Order_21'])) $entities['OrderLine_44']->setPurchaseOrder($entities['Order_21']);
        $manager->persist($entities['OrderLine_44']);

        $entities['OrderLine_45'] = new \App\Entity\OrderLine();
        $entities['OrderLine_45']->setQuantity(66);
        if (isset($entities['Stock_13'])) $entities['OrderLine_45']->setStock($entities['Stock_13']);
        if (isset($entities['Order_22'])) $entities['OrderLine_45']->setPurchaseOrder($entities['Order_22']);
        $manager->persist($entities['OrderLine_45']);

        $entities['OrderLine_46'] = new \App\Entity\OrderLine();
        $entities['OrderLine_46']->setQuantity(55);
        if (isset($entities['Stock_28'])) $entities['OrderLine_46']->setStock($entities['Stock_28']);
        if (isset($entities['Order_23'])) $entities['OrderLine_46']->setPurchaseOrder($entities['Order_23']);
        $manager->persist($entities['OrderLine_46']);

        $entities['OrderLine_47'] = new \App\Entity\OrderLine();
        $entities['OrderLine_47']->setQuantity(33);
        if (isset($entities['Stock_35'])) $entities['OrderLine_47']->setStock($entities['Stock_35']);
        if (isset($entities['Order_24'])) $entities['OrderLine_47']->setPurchaseOrder($entities['Order_24']);
        $manager->persist($entities['OrderLine_47']);

        $entities['OrderLine_48'] = new \App\Entity\OrderLine();
        $entities['OrderLine_48']->setQuantity(12);
        if (isset($entities['Stock_48'])) $entities['OrderLine_48']->setStock($entities['Stock_48']);
        if (isset($entities['Order_25'])) $entities['OrderLine_48']->setPurchaseOrder($entities['Order_25']);
        $manager->persist($entities['OrderLine_48']);

        $entities['OrderLine_49'] = new \App\Entity\OrderLine();
        $entities['OrderLine_49']->setQuantity(23);
        if (isset($entities['Stock_49'])) $entities['OrderLine_49']->setStock($entities['Stock_49']);
        if (isset($entities['Order_26'])) $entities['OrderLine_49']->setPurchaseOrder($entities['Order_26']);
        $manager->persist($entities['OrderLine_49']);

        $entities['OrderLine_50'] = new \App\Entity\OrderLine();
        $entities['OrderLine_50']->setQuantity(15);
        if (isset($entities['Stock_18'])) $entities['OrderLine_50']->setStock($entities['Stock_18']);
        if (isset($entities['Order_27'])) $entities['OrderLine_50']->setPurchaseOrder($entities['Order_27']);
        $manager->persist($entities['OrderLine_50']);

        $entities['OrderLine_51'] = new \App\Entity\OrderLine();
        $entities['OrderLine_51']->setQuantity(50);
        if (isset($entities['Stock_25'])) $entities['OrderLine_51']->setStock($entities['Stock_25']);
        if (isset($entities['Order_28'])) $entities['OrderLine_51']->setPurchaseOrder($entities['Order_28']);
        $manager->persist($entities['OrderLine_51']);

        $entities['OrderLine_52'] = new \App\Entity\OrderLine();
        $entities['OrderLine_52']->setQuantity(50);
        if (isset($entities['Stock_25'])) $entities['OrderLine_52']->setStock($entities['Stock_25']);
        if (isset($entities['Order_29'])) $entities['OrderLine_52']->setPurchaseOrder($entities['Order_29']);
        $manager->persist($entities['OrderLine_52']);

        $entities['OrderLine_53'] = new \App\Entity\OrderLine();
        $entities['OrderLine_53']->setQuantity(20);
        if (isset($entities['Stock_18'])) $entities['OrderLine_53']->setStock($entities['Stock_18']);
        if (isset($entities['Order_30'])) $entities['OrderLine_53']->setPurchaseOrder($entities['Order_30']);
        $manager->persist($entities['OrderLine_53']);

        $entities['OrderLine_54'] = new \App\Entity\OrderLine();
        $entities['OrderLine_54']->setQuantity(150);
        if (isset($entities['Stock_50'])) $entities['OrderLine_54']->setStock($entities['Stock_50']);
        if (isset($entities['Order_31'])) $entities['OrderLine_54']->setPurchaseOrder($entities['Order_31']);
        $manager->persist($entities['OrderLine_54']);

        $entities['OrderLine_55'] = new \App\Entity\OrderLine();
        $entities['OrderLine_55']->setQuantity(150);
        if (isset($entities['Stock_18'])) $entities['OrderLine_55']->setStock($entities['Stock_18']);
        if (isset($entities['Order_32'])) $entities['OrderLine_55']->setPurchaseOrder($entities['Order_32']);
        $manager->persist($entities['OrderLine_55']);

        $entities['OrderLine_56'] = new \App\Entity\OrderLine();
        $entities['OrderLine_56']->setQuantity(20);
        if (isset($entities['Stock_42'])) $entities['OrderLine_56']->setStock($entities['Stock_42']);
        if (isset($entities['Order_33'])) $entities['OrderLine_56']->setPurchaseOrder($entities['Order_33']);
        $manager->persist($entities['OrderLine_56']);

        $entities['OrderLine_57'] = new \App\Entity\OrderLine();
        $entities['OrderLine_57']->setQuantity(10);
        if (isset($entities['Stock_45'])) $entities['OrderLine_57']->setStock($entities['Stock_45']);
        if (isset($entities['Order_33'])) $entities['OrderLine_57']->setPurchaseOrder($entities['Order_33']);
        $manager->persist($entities['OrderLine_57']);

        $entities['OrderLine_58'] = new \App\Entity\OrderLine();
        $entities['OrderLine_58']->setQuantity(15);
        if (isset($entities['Stock_16'])) $entities['OrderLine_58']->setStock($entities['Stock_16']);
        if (isset($entities['Order_34'])) $entities['OrderLine_58']->setPurchaseOrder($entities['Order_34']);
        $manager->persist($entities['OrderLine_58']);

        $entities['OrderLine_59'] = new \App\Entity\OrderLine();
        $entities['OrderLine_59']->setQuantity(14);
        if (isset($entities['Stock_42'])) $entities['OrderLine_59']->setStock($entities['Stock_42']);
        if (isset($entities['Order_35'])) $entities['OrderLine_59']->setPurchaseOrder($entities['Order_35']);
        $manager->persist($entities['OrderLine_59']);

        $entities['OrderLine_60'] = new \App\Entity\OrderLine();
        $entities['OrderLine_60']->setQuantity(6);
        if (isset($entities['Stock_45'])) $entities['OrderLine_60']->setStock($entities['Stock_45']);
        if (isset($entities['Order_36'])) $entities['OrderLine_60']->setPurchaseOrder($entities['Order_36']);
        $manager->persist($entities['OrderLine_60']);

        $entities['OrderLine_61'] = new \App\Entity\OrderLine();
        $entities['OrderLine_61']->setQuantity(6);
        if (isset($entities['Stock_47'])) $entities['OrderLine_61']->setStock($entities['Stock_47']);
        if (isset($entities['Order_37'])) $entities['OrderLine_61']->setPurchaseOrder($entities['Order_37']);
        $manager->persist($entities['OrderLine_61']);

        $manager->flush();
    }
}