-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 jan. 2026 à 10:54
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
('DoctrineMigrations\\Version20260109133917', '2026-01-09 13:39:38', 111),
('DoctrineMigrations\\Version20260109145425', '2026-01-09 15:03:45', 12),
('DoctrineMigrations\\Version20260114101934', '2026-01-14 10:19:52', 115),
('DoctrineMigrations\\Version20260115102638', '2026-01-15 10:26:58', 131),
('DoctrineMigrations\\Version20260116150855', '2026-01-16 15:09:00', 133),
('DoctrineMigrations\\Version20260119090757', '2026-01-19 09:08:00', 227);

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
(19, 1, 'CMD-A5D8DD40', 'Livrée', '2026-01-15 11:29:18', 1, '2026-01-15 11:30:34'),
(20, 1, 'CMD-E1716062', 'Annulée', '2026-01-15 11:29:28', 1, '2026-01-19 09:40:21'),
(21, 1, 'CMD-E9C76D67', 'Livrée', '2026-01-15 11:29:37', 1, '2026-01-15 11:30:41'),
(22, 1, 'CMD-2A01B697', 'Réservation', '2026-01-15 11:29:45', 1, '2026-01-15 11:29:45'),
(23, 1, 'CMD-903AF58F', 'Réservation', '2026-01-15 11:29:56', 1, '2026-01-15 11:29:56'),
(24, 1, 'CMD-799187E8', 'Réservation', '2026-01-15 11:30:05', 1, '2026-01-15 11:30:05'),
(25, 1, 'CMD-DB7FB83D', 'Annulée', '2026-01-15 11:30:13', 1, '2026-01-15 11:30:47'),
(26, 1, 'CMD-41E34563', 'Livrée', '2026-01-15 11:30:20', 1, '2026-01-15 11:30:25'),
(27, 3, 'CMD-BA7C40A0', 'Réservation', '2026-01-16 10:56:06', 3, '2026-01-16 10:56:06'),
(28, 1, 'CMD-BC97D781', 'Livrée', '2026-01-19 09:16:36', 1, '2026-01-19 09:26:31'),
(29, 1, 'CMD-E5EC138C', 'Réservation', '2026-01-19 09:27:38', 1, '2026-01-19 09:27:38'),
(30, 1, 'CMD-11BCA3BE', 'Livrée', '2026-01-19 09:40:35', 1, '2026-01-19 09:40:53');

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
(42, 47, 19, 50),
(43, 45, 20, 88),
(44, 47, 21, 70),
(45, 13, 22, 66),
(46, 28, 23, 55),
(47, 35, 24, 33),
(48, 48, 25, 12),
(49, 49, 26, 23),
(50, 18, 27, 15),
(51, 25, 28, 50),
(52, 25, 29, 50),
(53, 18, 30, 20);

-- --------------------------------------------------------

--
-- Structure de la table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `changed_by_id` int(11) DEFAULT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `changed_by_id`, `purchase_order_id`, `status`, `created_at`) VALUES
(1, 1, 28, 'Livrée', '2026-01-19 09:26:31'),
(2, 1, 20, 'Annulée', '2026-01-19 09:40:21'),
(3, 1, 30, 'Réservation', '2026-01-19 09:40:35'),
(4, 1, 30, 'Livrée', '2026-01-19 09:40:53');

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
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partner`
--

INSERT INTO `partner` (`id`, `company_name`, `contact_details`, `created_at`) VALUES
(1, 'Guillet', '20, boulevard Bernier\n48227 Traoredan', '2025-12-15 04:00:04'),
(2, 'Laroche', '902, rue Christine Launay\n28334 Rocher', '2025-09-16 03:56:54'),
(3, 'Riou S.A.R.L.', '17, avenue Lebreton\n98332 Cousin', '2025-04-29 13:45:56'),
(4, 'Lemaire', '88, rue de Blanc\n36045 JacquotBourg', '2025-04-03 13:12:40'),
(5, 'Delannoy', 'avenue Timothée Descamps\n57745 Perrot', '2025-11-30 22:04:22'),
(6, 'Millet', '23, rue Marion\n85402 DufourBourg', '2025-02-08 03:49:11'),
(7, 'Pruvost Humbert SARL', '86, rue de Robin\n59973 Lebon-sur-Hubert', '2025-12-03 17:29:00'),
(8, 'Charrier', '3, rue Torres\n34027 Dupuy-sur-Guibert', '2025-09-27 00:03:46'),
(9, 'Delorme', '70, rue Delmas\n75107 Dumas', '2025-04-23 21:59:45'),
(10, 'Martineau S.A.S.', '8, rue de Brunet\n66564 Beguenec', '2025-12-27 01:58:01');

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
(1, 'Rosa', 'Rose', 'Arbuste à fleurs'),
(2, 'Lavandula angustifolia', 'Lavande', 'Sous-arbrisseau'),
(3, 'Helianthus annuus', 'Tournesol', 'Fleur annuelle'),
(4, 'Convallaria majalis', 'Muguet', 'Vivace bulbeuse'),
(5, 'Taraxacum officinale', 'Pissenlit', 'Herbacée vivace'),
(6, 'Tulipa', 'Tulipe', 'Plante à bulbe'),
(7, 'Ocimum basilicum', 'Basilic', 'Herbe aromatique'),
(8, 'Mentha x piperita', 'Menthe poivrée', 'Herbe aromatique'),
(9, 'Salvia rosmarinus', 'Romarin', 'Arbuste aromatique'),
(10, 'Papaver rhoeas', 'Coquelicot', 'Fleur annuelle'),
(11, 'Leucanthemum vulgare', 'Marguerite', 'Fleur vivace'),
(12, 'Quercus robur', 'Chêne pédonculé', 'Arbre feuillu'),
(13, 'Betula pendula', 'Bouleau blanc', 'Arbre feuillu'),
(14, 'Acer platanoides', 'Érable plane', 'Arbre feuillu'),
(15, 'Fagus sylvatica', 'Hêtre commun', 'Arbre feuillu'),
(16, 'Abies alba', 'Sapin blanc', 'Conifère'),
(17, 'Aloe barbadensis', 'Aloe Vera', 'Plante succulente'),
(18, 'Thymus vulgaris', 'Thym', 'Herbe aromatique'),
(19, 'Paeonia', 'Pivoine', 'Fleur vivace'),
(20, 'Iris germanica', 'Iris', 'Plante à rhizome');

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

CREATE TABLE `reset_password_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reset_password_request`
--

INSERT INTO `reset_password_request` (`id`, `user_id`, `selector`, `hashed_token`, `requested_at`, `expires_at`) VALUES
(11, 1, '76T0n1LgO9wHG03y6Fm8', 'KDxYj30f6jQv36Ui8yldjBqO0JdkCB5G6LO0Ud5fo1U=', '2026-01-19 08:21:58', '2026-01-19 09:21:58');

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
(4, 2026),
(5, 2020),
(7, 2019);

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
  `quantity` int(11) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `plant_id`, `packaging_id`, `season_id`, `partner_id`, `updated_by_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 19, 4, 1, 5, 4, 250, '2026-01-09 16:03:45', '2026-01-13 10:25:04'),
(2, 4, 3, 4, NULL, 1, 401, '2026-01-09 16:03:45', '2026-01-13 10:50:28'),
(3, 12, 5, 1, 9, 3, 81, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(4, 12, 1, 2, 2, 1, 85, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(5, 13, 4, 4, NULL, 1, 185, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(6, 14, 1, 3, 5, 4, 194, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(7, 7, 2, 3, 5, 4, 45, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(8, 8, 1, 3, 1, 1, 25, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(9, 7, 3, 1, NULL, 3, 250, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(10, 9, 3, 1, 2, 4, 269, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(11, 15, 1, 4, 4, 5, 344, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(12, 5, 4, 2, 9, 3, 116, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(13, 20, 1, 3, 5, 2, 325, '2026-01-09 16:03:45', '2026-01-15 11:29:45'),
(14, 13, 4, 3, NULL, 3, 101, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(15, 20, 5, 1, NULL, 1, 90, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(16, 18, 5, 4, 4, 5, 344, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(17, 17, 2, 1, 1, 4, 80, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(18, 2, 2, 4, 6, 3, 419, '2026-01-09 16:03:45', '2026-01-19 09:40:35'),
(19, 10, 3, 1, 7, 3, 104, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(20, 19, 1, 3, NULL, 1, 57, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(21, 8, 4, 4, 7, 2, 317, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(22, 10, 3, 3, NULL, 1, 177, '2026-01-09 16:03:45', '2026-01-13 10:50:28'),
(23, 14, 5, 3, 1, 2, 40, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(24, 9, 3, 2, NULL, 3, 9, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(25, 19, 1, 1, 1, 4, 302, '2026-01-09 16:03:45', '2026-01-19 09:27:38'),
(26, 15, 4, 4, NULL, 3, 147, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(27, 19, 5, 1, 8, 3, 314, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(28, 6, 3, 1, 3, 5, 313, '2026-01-09 16:03:45', '2026-01-15 11:29:56'),
(29, 20, 4, 2, 2, 4, 22, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(30, 16, 4, 1, NULL, 1, 375, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(31, 18, 4, 2, 4, 4, 34, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(32, 9, 5, 2, 10, 5, 222, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(33, 17, 5, 4, NULL, 1, 121, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(34, 13, 1, 4, 10, 5, 76, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(35, 12, 2, 4, 7, 4, 270, '2026-01-09 16:03:45', '2026-01-15 11:30:05'),
(36, 13, 3, 4, 9, 4, 77, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(37, 5, 1, 3, 10, 5, 310, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(38, 20, 2, 4, 2, 5, 67, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(39, 3, 2, 4, 1, 3, 28, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(40, 8, 3, 3, 4, 5, 131, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(41, 11, 4, 1, 5, 5, 23, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(42, 14, 2, 3, 2, 5, 374, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(43, 6, 2, 1, 7, 4, 122, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(45, 14, 5, 4, 5, 5, 356, '2026-01-09 16:03:45', '2026-01-15 11:29:28'),
(46, 4, 2, 4, 2, 4, 227, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(47, 1, 4, 3, 5, 4, 346, '2026-01-09 16:03:45', '2026-01-15 11:29:37'),
(48, 8, 2, 1, 1, 5, 241, '2026-01-09 16:03:45', '2026-01-15 11:30:13'),
(49, 4, 1, 2, 5, 4, 243, '2026-01-09 16:03:45', '2026-01-15 11:30:20'),
(50, 7, 2, 1, 2, 1, 436, '2026-01-09 16:03:45', '2026-01-09 16:03:45'),
(52, 19, 1, 1, NULL, 1, 50, '2026-01-19 09:26:31', '2026-01-19 09:26:31'),
(53, 2, 2, 4, NULL, 1, 20, '2026-01-19 09:40:53', '2026-01-19 09:40:53');

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
(1, 'castellscyprien@gmail.com', '{\"1\":\"ROLE_ADMIN\"}', '$2y$13$05x8uNui85x23QbX.5ZzUeN8.aHwBjg0MAoxL8tjM.HgzSZrGiEYu', 'CASTELLS', 'Cyprien', 1, 0, NULL),
(2, 'partner@gmail.com', '{\"1\":\"ROLE_PARTNER\"}', '$2y$13$Sso1/.lRLcCXIXh.3Gw2dOEeCZHgDn1HoNEbFru8FtmHLbzHgMmAa', 'Roche', 'Élisabeth', 1, 0, 1),
(3, 'collaborateur@gmail.com', '[\"ROLE_COLLABORATOR\"]', '$2y$13$Ga/LgPM1M18KLSo0cSXGzuPpBdI1nthU84JEHo2P9aYiNShOd03ZS', 'Moreau', 'Nathan', 1, 0, NULL),
(4, 'chloe.dubois@gmail.com', '{\"1\":\"ROLE_PARTNER\"}', '$2y$13$qEgxARQQJhMTrTIRY.rVeOGf34Dnn86FAc/h6pSinDJa0Bn6/QDzy', 'Dubois', 'Chloé', 1, 0, 2),
(5, 'hugo.durand@gmail.com', '{\"1\":\"ROLE_PARTNER\"}', '$2y$13$rKbBx6fAgguL4ndMhfIQYuQQweHV.vfI.2nHgh6im1JApsM0gkEuC', 'Durand', 'Hugo', 1, 0, 3),
(9, 'emma.richard@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$21PX7OQN90XkXOMe8FHToOiDtIymYhnDEJ2yqfGkn7K3nBU4mLs/u', 'Richard', 'Emma', 1, 0, 8),
(10, 'lucas.robert@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$556R.wMWlz3ntmbYBgubC.bfEPymWdtmhcGExEcWWV1DRnkS4AVB6', 'Robert', 'Lucas', 1, 0, 5),
(11, 'sophii.petit@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$51YrE7rzkrxCZcM1W/dwsuLzGIw3CkkwgpQOkHscNC.jOAwjc2FJC', 'Petit', 'Sophie', 1, 0, 9),
(12, 't.bernard@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$LrAJ9NvNBYDYJ5VySc3.eekuk81oZVtMhO0OSYStC/2McgeCJy63O', 'Bernard', 'Thomas', 1, 0, 4),
(13, 'm.lefebvre@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$4Sz2UcCORwso7O0ehIPfvOw7NZFJDDg4rjHMZqKW9gZY0XqhmSNoG', 'Lefebvre', 'Marie', 1, 0, 10),
(14, 'jean.dupont@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$1NWW/CLmg5oBOdvrZkEdEORJi0vaLh5ZcpmQhh/4i9IxvGhSTqb1i', 'Dupont', 'Jean', 1, 0, 6),
(15, 'mannon.michel@gmail.com', '[\"ROLE_PARTNER\"]', '$2y$13$a8TsLk2/NRRDRUpPIEimaeKaNOQar7FZz58fQa19BX4jj6CV8mtIi', 'Michel', 'Mannon', 1, 0, 7);

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
-- Index pour la table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_471AD77E828AD0A0` (`changed_by_id`),
  ADD KEY `IDX_471AD77EA45D7E6A` (`purchase_order_id`);

--
-- Index pour la table `packaging`
--
ALTER TABLE `packaging`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plant`
--
ALTER TABLE `plant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CE748AA76ED395` (`user_id`);

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
  ADD KEY `IDX_8D93D6499393F8FE` (`partner_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `order_line`
--
ALTER TABLE `order_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `packaging`
--
ALTER TABLE `packaging`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `partner`
--
ALTER TABLE `partner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `plant`
--
ALTER TABLE `plant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `season`
--
ALTER TABLE `season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  ADD CONSTRAINT `FK_9CE58EE1DCD6110` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `FK_471AD77E828AD0A0` FOREIGN KEY (`changed_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_471AD77EA45D7E6A` FOREIGN KEY (`purchase_order_id`) REFERENCES `order` (`id`);

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

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
