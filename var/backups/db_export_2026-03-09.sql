-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 09 mars 2026 à 09:15
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
('DoctrineMigrations\\Version20260106100215', '2026-03-09 09:14:57', 544),
('DoctrineMigrations\\Version20260109094622', '2026-03-09 09:14:57', 110),
('DoctrineMigrations\\Version20260109133917', '2026-03-09 09:14:58', 56),
('DoctrineMigrations\\Version20260109145425', '2026-03-09 09:14:58', 5),
('DoctrineMigrations\\Version20260114101934', '2026-03-09 09:14:58', 26),
('DoctrineMigrations\\Version20260115102638', '2026-03-09 09:14:58', 41),
('DoctrineMigrations\\Version20260116150855', '2026-03-09 09:14:58', 55),
('DoctrineMigrations\\Version20260119090757', '2026-03-09 09:14:58', 133),
('DoctrineMigrations\\Version20260304101507', '2026-03-09 09:14:58', 65);

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
(1, 1, 'CMD-A5D8DD40', 'Livrée', '2026-01-15 11:29:18', 1, '2026-02-24 10:28:40'),
(2, 1, 'CMD-E1716062', 'Annulée', '2026-01-15 11:29:28', 1, '2026-02-24 10:28:40'),
(3, 1, 'CMD-E9C76D67', 'Livrée', '2026-01-15 11:29:37', 1, '2026-02-24 10:28:40'),
(4, 1, 'CMD-2A01B697', 'Réservation', '2026-01-15 11:29:45', 1, '2026-02-24 10:28:40'),
(5, 1, 'CMD-903AF58F', 'Réservation', '2026-01-15 11:29:56', 1, '2026-02-24 10:28:40'),
(6, 1, 'CMD-799187E8', 'Réservation', '2026-01-15 11:30:05', 1, '2026-02-24 10:28:40'),
(7, 1, 'CMD-DB7FB83D', 'Annulée', '2026-01-15 11:30:13', 1, '2026-02-24 10:28:40'),
(8, 1, 'CMD-41E34563', 'Livrée', '2026-01-15 11:30:20', 1, '2026-02-24 10:28:40'),
(9, 3, 'CMD-BA7C40A0', 'Réservation', '2026-01-16 10:56:06', 1, '2026-02-24 10:28:40'),
(10, 1, 'CMD-BC97D781', 'Livrée', '2026-01-19 09:16:36', 1, '2026-02-24 10:28:40'),
(11, 1, 'CMD-E5EC138C', 'Réservation', '2026-01-19 09:27:38', 1, '2026-02-24 10:28:40'),
(12, 1, 'CMD-11BCA3BE', 'Livrée', '2026-01-19 09:40:35', 1, '2026-02-24 10:28:40'),
(13, 1, 'CMD-B7894EF7', 'Annulée', '2026-01-23 15:11:51', 1, '2026-02-24 10:28:40'),
(14, 1, 'CMD-1A584D6B', 'Annulée', '2026-01-23 15:17:44', 1, '2026-02-24 10:28:40'),
(15, 1, 'CMD-ED7987B4', 'Réservation', '2026-01-26 08:34:33', 1, '2026-02-24 10:28:40'),
(16, 1, 'CMD-3E72CE55', 'Livrée', '2026-01-26 08:45:08', 1, '2026-02-24 10:28:40'),
(17, 1, 'CMD-B148FFAC', 'Annulée', '2026-01-26 09:01:10', 1, '2026-02-24 10:28:40'),
(18, 1, 'CMD-AE7177F2', 'Annulée', '2026-01-26 09:15:26', 1, '2026-02-24 10:28:40'),
(19, 1, 'CMD-B885A6AF', 'Annulée', '2026-01-26 09:16:12', 1, '2026-02-24 10:28:40'),
(20, 1, 'CMD-0CE5E425', 'Réservation', '2026-02-24 11:05:54', 1, '2026-02-24 11:05:54'),
(21, 1, 'CMD-A3BB8282', 'Réservation', '2026-02-27 09:22:31', 1, '2026-02-27 09:22:31'),
(22, 1, 'CMD-004700CA', 'Annulée', '2026-03-03 08:46:40', 1, '2026-03-03 10:44:24');

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
(1, 46, 1, 50),
(2, 44, 2, 88),
(3, 46, 3, 70),
(4, 13, 4, 66),
(5, 28, 5, 55),
(6, 35, 6, 33),
(7, 47, 7, 12),
(8, 48, 8, 23),
(9, 18, 9, 15),
(10, 25, 10, 50),
(11, 25, 11, 50),
(12, 18, 12, 20),
(13, 49, 13, 150),
(14, 18, 14, 150),
(15, 42, 15, 20),
(16, 44, 15, 10),
(17, 16, 16, 15),
(18, 42, 17, 14),
(19, 44, 18, 6),
(20, 46, 19, 6),
(21, 42, 20, 10),
(22, 46, 20, 10),
(23, 42, 21, 10),
(24, 46, 21, 10),
(25, 11, 22, 10),
(26, 13, 22, 10);

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
(1, 1, 20, 'Réservation', '2026-02-24 11:05:54'),
(2, 1, 21, 'Réservation', '2026-02-27 09:22:31'),
(3, 1, 22, 'Réservation', '2026-03-03 08:46:40'),
(4, 1, 22, 'Annulée', '2026-03-03 10:44:24');

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
(1, 'Godet (9x9x9)'),
(2, 'Pot 1L'),
(3, 'Pot 3L'),
(4, 'Pot 10L'),
(5, 'Racine nue'),
(6, 'Motte');

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
(1, 'Guillet', '20, boulevard Bernier\r\n48227 Traoredan', '2026-02-24 10:28:40'),
(2, 'Laroche', '902, rue Christine Launay\r\n28334 Rocher', '2026-02-24 10:28:40'),
(3, 'Riou S.A.R.L.', '17, avenue Lebreton\r\n98332 Cousin', '2026-02-24 10:28:40'),
(4, 'Lemaire', '88, rue de Blanc\r\n36045 JacquotBourg', '2026-02-24 10:28:40'),
(5, 'Delannoy', 'avenue Timothée Descamps\r\n57745 Perrot', '2026-02-24 10:28:40'),
(6, 'Millet', '23, rue Marion\r\n85402 DufourBourg', '2026-02-24 10:28:40'),
(7, 'Pruvost Humbert SARL', '86, rue de Robin\r\n59973 Lebon-sur-Hubert', '2026-02-24 10:28:40'),
(8, 'Charrier', '3, rue Torres\r\n34027 Dupuy-sur-Guibert', '2026-02-24 10:28:40'),
(9, 'Delorme', '70, rue Delmas\r\n75107 Dumas', '2026-02-24 10:28:40'),
(10, 'Martineau S.A.S.', '8, rue de Brunet\r\n66564 Beguenec', '2026-02-24 10:28:40');

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
(1, 'Sorbus torminalis', 'Alisier torminal', 'Feuillu'),
(2, 'Prunus dulcis', 'Amandier', 'Fruitier'),
(3, 'Hippophae rhamnoides', 'Argousier', 'Arbuste'),
(4, 'Crataegus monogyna', 'Aubépine monogyne', 'Arbuste'),
(5, 'Betula pendula', 'Bouleau verruqueux', 'Feuillu'),
(6, 'Buxus sempervirens', 'Buis toujours vert', 'Arbuste'),
(7, 'Catalpa bignonioides', 'Catalpa commun', 'Feuillu'),
(8, 'Cedrus atlantica', 'Cèdre de l\'Atlas', 'Résineux'),
(9, 'Cedrus libani', 'Cèdre du Liban', 'Résineux'),
(10, 'Prunus avium', 'Merisier', 'Feuillu'),
(11, 'Prunus cerasus', 'Cerisier acide', 'Fruitier'),
(12, 'Castanea sativa', 'Châtaignier commun', 'Feuillu'),
(13, 'Quercus robur', 'Chêne pédonculé', 'Feuillu'),
(14, 'Quercus petraea', 'Chêne sessile', 'Feuillu'),
(15, 'Quercus pubescens', 'Chêne pubescent', 'Feuillu'),
(16, 'Cornus mas', 'Cornouiller mâle', 'Arbuste'),
(17, 'Cornus sanguinea', 'Cornouiller sanguin', 'Arbuste'),
(18, 'Corylus avellana', 'Noisetier commun', 'Arbuste'),
(19, 'Cotinus coggygria', 'Arbre à perruques', 'Arbuste'),
(20, 'Cupressus sempervirens', 'Cyprès toujours vert', 'Résineux'),
(21, 'Cytisus scoparius', 'Genêt à balais', 'Arbuste'),
(22, 'Pyrus communis', 'Poirier commun', 'Fruitier'),
(23, 'Malus domestica', 'Pommier commun', 'Fruitier'),
(24, 'Prunus spinosa', 'Prunellier', 'Arbuste'),
(25, 'Rosa canina', 'Églantier', 'Arbuste'),
(26, 'Sambucus nigra', 'Sureau noir', 'Arbuste'),
(27, 'Sorbus aucuparia', 'Sorbier des oiseleurs', 'Feuillu'),
(28, 'Taxus baccata', 'If commun', 'Résineux'),
(29, 'Tilia cordata', 'Tilleul à petites feuilles', 'Feuillu'),
(30, 'Ulmus minor', 'Orme champêtre', 'Feuillu'),
(31, 'Viburnum lantana', 'Viorne lantane', 'Arbuste'),
(32, 'Viburnum opulus', 'Viorne obier', 'Arbuste');

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
(6, 2019);

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
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `created_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `plant_id`, `packaging_id`, `season_id`, `partner_id`, `updated_by_id`, `quantity`, `created_at`, `updated_at`, `created_by_id`) VALUES
(1, 19, 1, 1, 5, 5, 250, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(2, 4, 1, 4, NULL, 1, 401, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(3, 12, 1, 1, 9, 9, 81, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 9),
(4, 12, 1, 2, 2, 2, 85, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(5, 13, 1, 4, NULL, 1, 185, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(6, 14, 1, 3, 5, 5, 194, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(7, 7, 1, 3, 5, 5, 45, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(8, 8, 1, 3, 1, 1, 25, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(9, 7, 1, 1, NULL, 1, 250, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(10, 9, 1, 1, 2, 2, 269, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(11, 15, 1, 4, 4, 4, 334, '2026-02-24 10:28:40', '2026-03-03 08:46:40', 4),
(12, 5, 1, 2, 9, 9, 116, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 9),
(13, 20, 1, 3, 5, 5, 315, '2026-02-24 10:28:40', '2026-03-03 08:46:40', 5),
(14, 13, 1, 3, NULL, 1, 101, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(15, 20, 1, 1, NULL, 1, 90, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(16, 18, 1, 4, 4, 4, 329, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 4),
(17, 17, 1, 1, 1, 1, 80, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(18, 2, 1, 4, 6, 6, 269, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 6),
(19, 10, 1, 1, 7, 7, 104, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 7),
(20, 19, 1, 3, NULL, 1, 57, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(21, 8, 1, 4, 7, 7, 317, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 7),
(22, 10, 1, 3, NULL, 1, 177, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(23, 14, 1, 3, 1, 1, 40, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(24, 9, 1, 2, NULL, 1, 9, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(25, 19, 1, 1, 1, 1, 302, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(26, 15, 1, 4, NULL, 1, 147, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(27, 19, 1, 1, 8, 8, 314, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 8),
(28, 6, 1, 1, 3, 3, 313, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 3),
(29, 20, 1, 2, 2, 2, 22, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(30, 16, 1, 1, NULL, 1, 375, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(31, 18, 1, 2, 4, 4, 34, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 4),
(32, 9, 1, 2, 10, 10, 222, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 10),
(33, 17, 1, 4, NULL, 1, 121, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(34, 13, 1, 4, 10, 10, 76, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 10),
(35, 12, 1, 4, 7, 7, 270, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 7),
(36, 13, 1, 4, 9, 9, 77, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 9),
(37, 5, 1, 3, 10, 10, 310, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 10),
(38, 20, 1, 4, 2, 2, 67, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(39, 3, 1, 4, 1, 1, 28, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(40, 8, 1, 3, 4, 4, 131, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 4),
(41, 11, 1, 1, 5, 5, 23, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(42, 14, 1, 3, 2, 2, 320, '2026-02-24 10:28:40', '2026-02-27 09:22:31', 2),
(43, 6, 1, 1, 7, 7, 122, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 7),
(44, 14, 1, 4, 5, 5, 340, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(45, 4, 1, 4, 2, 2, 227, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(46, 1, 1, 3, 5, 5, 320, '2026-02-24 10:28:40', '2026-02-27 09:22:31', 5),
(47, 8, 1, 1, 1, 1, 241, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(48, 4, 1, 2, 5, 5, 243, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 5),
(49, 7, 1, 1, 2, 2, 286, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 2),
(50, 19, 1, 1, NULL, 1, 50, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(51, 2, 1, 4, NULL, 1, 20, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(52, 18, 1, 4, NULL, 1, 15, '2026-02-24 10:28:40', '2026-02-24 10:28:40', 1),
(53, 1, 1, 1, NULL, 1, 40, '2026-03-04 13:09:03', '2026-03-04 13:09:03', 1);

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
(1, 'castellscyprien@gmail.com', '{\"1\":\"ROLE_ADMIN\",\"2\":\"ROLE_USER\"}', '$2y$13$05x8uNui85x23QbX.5ZzUeN8.aHwBjg0MAoxL8tjM.HgzSZrGiEYu', 'CASTELLS', 'Cyprien', 1, 0, NULL),
(2, 'partner@gmail.com', '{\"1\":\"ROLE_PARTNER\",\"2\":\"ROLE_USER\"}', '$2y$13$Sso1/.lRLcCXIXh.3Gw2dOEeCZHgDn1HoNEbFru8FtmHLbzHgMmAa', 'Roche', 'Élisabeth', 1, 0, 1),
(3, 'collaborateur@gmail.com', '[\"ROLE_COLLABORATOR\",\"ROLE_USER\"]', '$2y$13$Ga/LgPM1M18KLSo0cSXGzuPpBdI1nthU84JEHo2P9aYiNShOd03ZS', 'Moreau', 'Nathan', 1, 0, NULL),
(4, 'chloe.dubois@gmail.com', '{\"1\":\"ROLE_PARTNER\",\"2\":\"ROLE_USER\"}', '$2y$13$qEgxARQQJhMTrTIRY.rVeOGf34Dnn86FAc/h6pSinDJa0Bn6/QDzy', 'Dubois', 'Chloé', 1, 0, 2),
(5, 'hugo.durand@gmail.com', '{\"1\":\"ROLE_PARTNER\",\"2\":\"ROLE_USER\"}', '$2y$13$rKbBx6fAgguL4ndMhfIQYuQQweHV.vfI.2nHgh6im1JApsM0gkEuC', 'Durand', 'Hugo', 1, 0, 3),
(6, 'emma.richard@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$21PX7OQN90XkXOMe8FHToOiDtIymYhnDEJ2yqfGkn7K3nBU4mLs/u', 'Richard', 'Emma', 1, 0, 8),
(7, 'lucas.robert@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$556R.wMWlz3ntmbYBgubC.bfEPymWdtmhcGExEcWWV1DRnkS4AVB6', 'Robert', 'Lucas', 1, 0, 5),
(8, 'sophii.petit@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$51YrE7rzkrxCZcM1W/dwsuLzGIw3CkkwgpQOkHscNC.jOAwjc2FJC', 'Petit', 'Sophie', 1, 0, 9),
(9, 't.bernard@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$LrAJ9NvNBYDYJ5VySc3.eekuk81oZVtMhO0OSYStC/2McgeCJy63O', 'Bernard', 'Thomas', 1, 0, 4),
(10, 'm.lefebvre@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$4Sz2UcCORwso7O0ehIPfvOw7NZFJDDg4rjHMZqKW9gZY0XqhmSNoG', 'Lefebvre', 'Marie', 1, 0, 10),
(11, 'jean.dupont@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$1NWW/CLmg5oBOdvrZkEdEORJi0vaLh5ZcpmQhh/4i9IxvGhSTqb1i', 'Dupont', 'Jean', 1, 0, 6),
(12, 'mannon.michel@gmail.com', '[\"ROLE_PARTNER\",\"ROLE_USER\"]', '$2y$13$a8TsLk2/NRRDRUpPIEimaeKaNOQar7FZz58fQa19BX4jj6CV8mtIi', 'Michel', 'Mannon', 1, 0, 7);

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
  ADD KEY `IDX_4B365660896DBBDE` (`updated_by_id`),
  ADD KEY `IDX_4B365660B03A8386` (`created_by_id`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `order_line`
--
ALTER TABLE `order_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `plant`
--
ALTER TABLE `plant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `season`
--
ALTER TABLE `season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  ADD CONSTRAINT `FK_4B3656609393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`),
  ADD CONSTRAINT `FK_4B365660B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6499393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
