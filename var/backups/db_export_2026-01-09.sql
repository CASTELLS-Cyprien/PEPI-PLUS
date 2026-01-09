-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 09 jan. 2026 à 14:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pepi`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260106100215', '2026-01-09 10:04:16', 722),
('DoctrineMigrations\\Version20260109094622', '2026-01-09 10:04:16', 144),
('DoctrineMigrations\\Version20260109133917', '2026-01-09 13:39:38', 111);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `collaborator_id` int(11) DEFAULT NULL,
  `order_number` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_by_id` int(11) NOT NULL,
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `collaborator_id`, `order_number`, `status`, `created_at`, `updated_by_id`, `updated_at`) VALUES
(1, 3, 'CMD-92378', 'Réservation', '2025-07-20 03:07:14', 4, '1979-08-22 10:47:30'),
(2, 5, 'CMD-41097', 'Réservation', '2025-12-02 19:43:40', 1, '1991-04-02 03:11:46'),
(3, 4, 'CMD-9539', 'Livrée', '2025-11-11 12:03:14', 5, '2009-02-17 02:31:45'),
(4, 5, 'CMD-10835', 'Réservation', '2025-10-05 03:20:32', 3, '1980-04-27 13:28:38'),
(5, 5, 'CMD-53146', 'Annulée', '2025-07-27 10:32:34', 2, '2024-01-03 17:04:51'),
(6, 1, 'CMD-31189', 'Validée', '2025-08-22 11:20:23', 2, '2024-12-27 15:49:02'),
(7, 1, 'CMD-9704', 'Validée', '2025-12-04 03:44:06', 3, '1977-04-13 22:27:32'),
(8, 4, 'CMD-84074', 'Validée', '2025-11-17 20:27:14', 2, '2009-03-23 16:49:50'),
(9, 3, 'CMD-57569', 'Réservation', '2026-01-03 18:22:08', 3, '1988-09-19 09:59:56'),
(10, 4, 'CMD-30511', 'Livrée', '2025-08-04 01:09:43', 2, '2025-04-29 11:18:57'),
(11, 4, 'CMD-64049', 'Livrée', '2025-11-14 09:58:14', 3, '1994-04-29 18:07:47'),
(12, 1, 'CMD-30607', 'Annulée', '2025-09-26 05:48:21', 1, '1994-12-13 03:10:12'),
(13, 3, 'CMD-67321', 'Réservation', '2025-08-06 11:23:44', 1, '1977-10-13 06:35:03'),
(14, 5, 'CMD-4556', 'Validée', '2025-10-23 21:14:22', 5, '1982-05-22 02:07:22'),
(15, 2, 'CMD-12217', 'Annulée', '2025-12-07 18:40:18', 1, '1981-12-31 02:43:28');

-- --------------------------------------------------------

--
-- Structure de la table `order_line`
--

CREATE TABLE `order_line` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_line`
--

INSERT INTO `order_line` (`id`, `stock_id`, `purchase_order_id`, `quantity`) VALUES
(1, 35, 1, 13),
(2, 22, 1, 15),
(3, 2, 1, 16),
(4, 47, 2, 20),
(5, 45, 2, 20),
(6, 49, 2, 15),
(7, 32, 2, 9),
(8, 26, 3, 7),
(9, 37, 3, 3),
(10, 7, 3, 16),
(11, 29, 3, 16),
(12, 39, 4, 3),
(13, 26, 4, 13),
(14, 3, 5, 2),
(15, 35, 6, 1),
(16, 16, 7, 10),
(17, 12, 7, 15),
(18, 22, 8, 1),
(19, 18, 8, 19),
(20, 12, 8, 10),
(21, 25, 8, 16),
(22, 44, 9, 9),
(23, 48, 9, 9),
(24, 32, 10, 4),
(25, 7, 10, 1),
(26, 5, 10, 2),
(27, 21, 10, 8),
(28, 2, 11, 6),
(29, 28, 12, 8),
(30, 7, 12, 16),
(31, 9, 12, 11),
(32, 30, 12, 13),
(33, 12, 13, 7),
(34, 28, 13, 17),
(35, 27, 14, 5),
(36, 42, 15, 20),
(37, 18, 15, 2),
(38, 19, 15, 10);

-- --------------------------------------------------------

--
-- Structure de la table `packaging`
--

CREATE TABLE `packaging` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `packaging`
--

INSERT INTO `packaging` (`id`, `label`) VALUES
(1, 'Pot 1L'),
(2, 'Pot 3L'),
(3, 'Barquette x6'),
(4, 'Godet'),
(5, 'Sac 50L');

-- --------------------------------------------------------

--
-- Structure de la table `partner`
--

CREATE TABLE `partner` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_details` longtext NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partner`
--

INSERT INTO `partner` (`id`, `company_name`, `contact_details`, `user_id`, `created_at`) VALUES
(1, 'Guillet', '20, boulevard Bernier\n48227 Traoredan', 2, '2025-12-15 04:00:04'),
(2, 'Laroche', '902, rue Christine Launay\n28334 Rocher', 4, '2025-09-16 03:56:54'),
(3, 'Riou S.A.R.L.', '17, avenue Lebreton\n98332 Cousin', 5, '2025-04-29 13:45:56'),
(4, 'Lemaire', '88, rue de Blanc\n36045 JacquotBourg', NULL, '2025-04-03 13:12:40'),
(5, 'Delannoy', 'avenue Timothée Descamps\n57745 Perrot', NULL, '2025-11-30 22:04:22'),
(6, 'Millet', '23, rue Marion\n85402 DufourBourg', NULL, '2025-02-08 03:49:11'),
(7, 'Pruvost Humbert SARL', '86, rue de Robin\n59973 Lebon-sur-Hubert', NULL, '2025-12-03 17:29:00'),
(8, 'Charrier', '3, rue Torres\n34027 Dupuy-sur-Guibert', NULL, '2025-09-27 00:03:46'),
(9, 'Delorme', '70, rue Delmas\n75107 Dumas', NULL, '2025-04-23 21:59:45'),
(10, 'Martineau S.A.S.', '8, rue de Brunet\n66564 Beguenec', NULL, '2025-12-27 01:58:01');

-- --------------------------------------------------------

--
-- Structure de la table `plant`
--

CREATE TABLE `plant` (
  `id` int(11) NOT NULL,
  `latin_name` varchar(255) NOT NULL,
  `common_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plant`
--

INSERT INTO `plant` (`id`, `latin_name`, `common_name`, `type`) VALUES
(1, 'Rosa canina', 'Églantier', 'Arbuste'),
(2, 'Quercus robur', 'Chêne pédonculé', 'Arbre'),
(3, 'Lavandula angustifolia', 'Lavande vraie', 'Plante annuelle / aromatique'),
(4, 'Solanum lycopersicum', 'Tomate', 'Plante annuelle'),
(5, 'Ocimum basilicum', 'Basilic', 'Plante annuelle / aromatique'),
(6, 'Mentha spicata', 'Menthe verte', 'Plante vivace / aromatique'),
(7, 'Helianthus annuus', 'Tournesol', 'Plante annuelle'),
(8, 'Ficus carica', 'Figuier', 'Arbre fruitier'),
(9, 'Cucurbita pepo', 'Courgette', 'Plante annuelle'),
(10, 'Aloe vera', 'Aloe vera', 'Plante succulente'),
(11, 'voluptatem ut', 'distinctio', 'Vivace'),
(12, 'voluptatibus a', 'in', 'Annuelle'),
(13, 'provident ullam', 'cumque', 'Arbre'),
(14, 'ipsa itaque', 'sit', 'Vivace'),
(15, 'facere omnis', 'quam', 'Arbre'),
(16, 'perspiciatis ipsa', 'ipsum', 'Arbre'),
(17, 'dignissimos itaque', 'fugit', 'Arbre'),
(18, 'illo deserunt', 'aut', 'Annuelle'),
(19, 'sed nihil', 'enim', 'Annuelle'),
(20, 'error iusto', 'sint', 'Arbustre');

-- --------------------------------------------------------

--
-- Structure de la table `season`
--

CREATE TABLE `season` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `season`
--

INSERT INTO `season` (`id`, `year`) VALUES
(1, 2023),
(2, 2024),
(3, 2025),
(4, 2026);

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `packaging_id` int(11) DEFAULT NULL,
  `season_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `updated_by_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `plant_id`, `packaging_id`, `season_id`, `partner_id`, `updated_by_id`, `quantity`) VALUES
(1, 19, 4, 1, 5, 4, 287),
(2, 4, 3, 4, NULL, 1, 385),
(3, 12, 5, 1, 9, 3, 81),
(4, 12, 1, 2, 2, 1, 85),
(5, 13, 4, 4, NULL, 1, 185),
(6, 14, 1, 3, 5, 4, 194),
(7, 7, 2, 3, 5, 4, 45),
(8, 8, 1, 3, 1, 1, 20),
(9, 7, 3, 1, NULL, 3, 250),
(10, 9, 3, 1, 2, 4, 269),
(11, 15, 1, 4, 4, 5, 344),
(12, 5, 4, 2, 9, 3, 116),
(13, 20, 1, 3, 5, 2, 391),
(14, 13, 4, 3, NULL, 3, 101),
(15, 20, 5, 1, NULL, 1, 90),
(16, 18, 5, 4, 4, 5, 344),
(17, 17, 2, 1, 1, 4, 80),
(18, 2, 2, 4, 6, 3, 454),
(19, 10, 3, 1, 7, 3, 104),
(20, 19, 1, 3, NULL, 1, 57),
(21, 8, 4, 4, 7, 2, 317),
(22, 10, 3, 3, NULL, 1, 162),
(23, 14, 5, 3, 1, 2, 40),
(24, 9, 3, 2, NULL, 3, 37),
(25, 19, 1, 1, 1, 4, 402),
(26, 15, 4, 4, NULL, 3, 147),
(27, 19, 5, 1, 8, 3, 314),
(28, 6, 3, 1, 3, 5, 368),
(29, 20, 4, 2, 2, 4, 22),
(30, 16, 4, 1, NULL, 1, 375),
(31, 18, 4, 2, 4, 4, 34),
(32, 9, 5, 2, 10, 5, 222),
(33, 17, 5, 4, NULL, 1, 121),
(34, 13, 1, 4, 10, 5, 76),
(35, 12, 2, 4, 7, 4, 303),
(36, 13, 3, 4, 9, 4, 77),
(37, 5, 1, 3, 10, 5, 310),
(38, 20, 2, 4, 2, 5, 67),
(39, 3, 2, 4, 1, 3, 28),
(40, 8, 3, 3, 4, 5, 131),
(41, 11, 4, 1, 5, 5, 23),
(42, 14, 2, 3, 2, 5, 374),
(43, 6, 2, 1, 7, 4, 122),
(44, 12, 3, 4, 3, 1, 21),
(45, 14, 5, 4, 5, 5, 494),
(46, 4, 2, 4, 2, 4, 227),
(47, 1, 4, 3, 5, 4, 466),
(48, 8, 2, 1, 1, 5, 253),
(49, 4, 1, 2, 5, 4, 266),
(50, 7, 2, 1, 2, 1, 436);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL,
  `partner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `last_name`, `first_name`, `is_active`, `must_change_password`, `partner_id`) VALUES
(1, 'castellscyprien@gmail.com', '[\"ROLE_USER\",\"ROLE_ADMIN\"]', '$2y$13$04xUgfZ8U3gMJWRNajLy5ON0mehE4sgoOjnNhIaFOjRnxSmNnT7Ti', 'CASTELLS', 'Cyprien', 1, 0, NULL),
(2, 'partner@gmail.com', '[\"ROLE_USER\",\"ROLE_PARTNER\"]', '$2y$13$Sso1/.lRLcCXIXh.3Gw2dOEeCZHgDn1HoNEbFru8FtmHLbzHgMmAa', 'Roche', 'Élisabeth', 1, 0, NULL),
(3, 'collaborateur@gmail.com', '[\"ROLE_USER\",\"ROLE_COLLABORATOR\"]', '$2y$13$Ga/LgPM1M18KLSo0cSXGzuPpBdI1nthU84JEHo2P9aYiNShOd03ZS', 'Nicolas', 'Françoise', 1, 0, NULL),
(4, 'pauline63@hotmail.fr', '[\"ROLE_USER\",\"ROLE_PARTNER\"]', '$2y$13$r4TM63hcucPTiWvq0uabtu.ODoqEmr01jAgC6rbrAQGVJZrpq69Gq', 'Renaud', 'Benoît', 1, 0, NULL),
(5, 'acolas@sfr.fr', '[\"ROLE_USER\",\"ROLE_PARTNER\"]', '$2y$13$rKbBx6fAgguL4ndMhfIQYuQQweHV.vfI.2nHgh6im1JApsM0gkEuC', 'Jacquot', 'Bertrand', 1, 0, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F529939830098C8C` (`collaborator_id`),
  ADD KEY `IDX_F5299398896DBBDE` (`updated_by_id`);

--
-- Index pour la table `order_line`
--
ALTER TABLE `order_line`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9CE58EE1DCD6110` (`stock_id`),
  ADD KEY `IDX_9CE58EE1A45D7E6A` (`purchase_order_id`);

--
-- Index pour la table `packaging`
--
ALTER TABLE `packaging`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_312B3E16A76ED395` (`user_id`);

--
-- Index pour la table `plant`
--
ALTER TABLE `plant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `season`
--
ALTER TABLE `season`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4B3656601D935652` (`plant_id`),
  ADD KEY `IDX_4B3656604E7B3801` (`packaging_id`),
  ADD KEY `IDX_4B3656604EC001D1` (`season_id`),
  ADD KEY `IDX_4B3656609393F8FE` (`partner_id`),
  ADD KEY `IDX_4B365660896DBBDE` (`updated_by_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD UNIQUE KEY `UNIQ_8D93D6499393F8FE` (`partner_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `order_line`
--
ALTER TABLE `order_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `packaging`
--
ALTER TABLE `packaging`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `partner`
--
ALTER TABLE `partner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `plant`
--
ALTER TABLE `plant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `season`
--
ALTER TABLE `season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F529939830098C8C` FOREIGN KEY (`collaborator_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F5299398896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `order_line`
--
ALTER TABLE `order_line`
  ADD CONSTRAINT `FK_9CE58EE1A45D7E6A` FOREIGN KEY (`purchase_order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `FK_9CE58EE1DCD6110` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`);

--
-- Contraintes pour la table `partner`
--
ALTER TABLE `partner`
  ADD CONSTRAINT `FK_312B3E16A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `FK_4B3656601D935652` FOREIGN KEY (`plant_id`) REFERENCES `plant` (`id`),
  ADD CONSTRAINT `FK_4B3656604E7B3801` FOREIGN KEY (`packaging_id`) REFERENCES `packaging` (`id`),
  ADD CONSTRAINT `FK_4B3656604EC001D1` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`),
  ADD CONSTRAINT `FK_4B365660896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_4B3656609393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6499393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
