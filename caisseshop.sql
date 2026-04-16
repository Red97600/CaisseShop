-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 09 avr. 2026 à 07:55
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
-- Base de données : `caisseshop`
--

-- --------------------------------------------------------

--
-- Structure de la table `details_vente`
--

CREATE TABLE `details_vente` (
  `Id` int(11) NOT NULL,
  `Vente_id` int(11) NOT NULL,
  `Produit_id` int(10) NOT NULL,
  `Quantite` int(11) NOT NULL,
  `Prix_debut` decimal(10,0) NOT NULL,
  `Total_ligne` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `Id` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `Prix` decimal(10,0) NOT NULL,
  `Stock` int(11) NOT NULL,
  `Code_bare` varchar(50) NOT NULL,
  `Date_creation` date NOT NULL,
  `Date_ajout` date NOT NULL,
  `categorie` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`Id`, `Nom`, `Description`, `Prix`, `Stock`, `Code_bare`, `Date_creation`, `Date_ajout`, `categorie`) VALUES
(1, 'Baguette Tradition', 'Pain artisanal croustillant', 2, 100, '200001', '2026-04-09', '2026-04-09', 'Boulangerie'),
(2, 'Croissant Beurre', 'Viennoiserie pur beurre', 1, 80, '200002', '2026-04-09', '2026-04-09', 'Boulangerie'),
(3, 'Pain au Chocolat', 'Viennoiserie avec chocolat', 1, 20, '200003', '2026-04-09', '2026-04-09', 'Boulangerie'),
(4, 'Lait Demi-écrémé 1L', 'Lait frais pasteurisé', 2, 60, '300001', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(5, 'Yaourt Nature x4', 'Yaourts nature en pack', 2, 50, '300002', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(6, 'Fromage Emmental', 'Fromage râpé 200g', 4, 40, '300003', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(7, 'Banane', 'Banane fraîche', 2, 120, '400001', '2026-04-09', '2026-04-09', 'Fruits'),
(8, 'Pomme Rouge', 'Pomme sucrée et croquante', 3, 90, '400002', '2026-04-09', '2026-04-09', 'Fruits'),
(9, 'Orange', 'Orange juteuse', 2, 85, '400003', '2026-04-09', '2026-04-09', 'Fruits'),
(10, 'Riz 1kg', 'Riz blanc longue grain', 3, 70, '500001', '2026-04-09', '2026-04-09', 'Épicerie'),
(11, 'Pâtes Spaghetti', 'Pâtes de blé dur 500g', 2, 100, '500002', '2026-04-09', '2026-04-09', 'Épicerie'),
(12, 'Huile d’Olive 1L', 'Huile vierge extra', 9, 30, '500003', '2026-04-09', '2026-04-09', 'Épicerie'),
(13, 'Eau Minérale 1.5L', 'Eau plate', 1, 150, '600001', '2026-04-09', '2026-04-09', 'Boissons'),
(14, 'Jus d’Orange 1L', 'Jus pur fruit', 3, 60, '600002', '2026-04-09', '2026-04-09', 'Boissons'),
(15, 'Soda Cola 1.5L', 'Boisson gazeuse sucrée', 2, 75, '600003', '2026-04-09', '2026-04-09', 'Boissons'),
(16, 'Lait Entier 1L', 'Lait entier frais pasteurisé', 2, 50, '300010', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(17, 'Lait Demi-écrémé 1L', 'Lait demi-écrémé riche en calcium', 2, 60, '300011', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(18, 'Yaourt Nature x4', 'Yaourts nature en pack de 4', 2, 40, '300012', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(19, 'Beurre Doux 250g', 'Beurre doux de qualité supérieure', 3, 35, '300013', '2026-04-09', '2026-04-09', 'Produits Laitiers'),
(20, 'Fromage Camembert', 'Fromage à pâte molle au lait de vache', 4, 25, '300014', '2026-04-09', '2026-04-09', 'Produits Laitiers');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `Id` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mot_de_passe` varchar(150) NOT NULL,
  `Date_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `Id` int(11) NOT NULL,
  `Utilisateur_id` int(11) NOT NULL,
  `Total` decimal(10,0) NOT NULL,
  `Date_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`Id`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
