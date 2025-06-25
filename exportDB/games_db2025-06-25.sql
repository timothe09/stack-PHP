-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : mer. 25 juin 2025 à 08:25
-- Version du serveur : 8.0.42
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `games_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `nom_du_jeux` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `games`
--

INSERT INTO `games` (`id`, `nom_du_jeux`, `description`, `path`, `created_at`) VALUES
(1, 'Tower Defense', 'Un jeu de stratgie o vous devez dfendre votre base contre des vagues d\'ennemis.', 'games/TowerDefense/TowerDefense.html', '2025-06-18 13:30:32'),
(5, 'Tic Tac Toe', 'est un morpion', 'games/TicTacToe/tictactoe.html', '2025-06-24 15:03:52'),
(7, 'Carré surfers', 'inspiré du Subway surfers', 'games/carre-surfers/carre-surfers.html', '2025-06-25 07:41:59'),
(8, 'Pc clickers', 'tout est dans le nom', 'games/pc-clickers/pc-clickers.html', '2025-06-25 08:23:55');

-- --------------------------------------------------------

--
-- Structure de la table `high_scores`
--

CREATE TABLE `high_scores` (
  `id` int NOT NULL,
  `game_id` int DEFAULT NULL,
  `player_name` varchar(50) NOT NULL,
  `score` int NOT NULL,
  `achieved_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `high_scores`
--

INSERT INTO `high_scores` (`id`, `game_id`, `player_name`, `score`, `achieved_at`) VALUES
(16, 1, 'youhou', 1520, '2025-06-20 08:55:26'),
(17, 1, 'biloute', 240, '2025-06-20 09:46:58'),
(18, 1, 'tcho', 130, '2025-06-20 09:48:41'),
(19, 1, 'truc', 960, '2025-06-20 09:51:09'),
(20, 1, 'tut', 260, '2025-06-20 09:54:35');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `high_scores`
--
ALTER TABLE `high_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `high_scores`
--
ALTER TABLE `high_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `high_scores`
--
ALTER TABLE `high_scores`
  ADD CONSTRAINT `high_scores_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
