-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql
-- Généré le : ven. 27 juin 2025 à 13:00
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
-- Structure de la table `connexion_games`
--

CREATE TABLE `connexion_games` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `connexion_games`
--

INSERT INTO `connexion_games` (`id`, `nom`, `email`, `mot_de_passe`, `date_naissance`, `date_inscription`) VALUES
(4, 'Timothé', 'timothe.parisot09@gmail.com', '$2y$10$i/fulZKTza5PPPox0OOr8uKo7HOXzkXNr/HBGesMpnLyMf8LesbNi', '2009-10-26', '2025-06-26 12:32:56'),
(5, 'viomaurer', 'violaine.helary@gmail.com', '$2y$10$onKMGXOV9vYeIEHFJZ0IfesazliYKxXkuMbLE3/BnKANc8FhzVrve', '1978-12-08', '2025-06-26 19:05:06');

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
(1, 'Tower Defense', 'Un jeu de stratégie o vous devez défendre votre base contre des vagues d\'ennemis.', 'games/TowerDefense/TowerDefense.php', '2025-06-18 13:30:32'),
(5, 'Tic Tac Toe', 'c\'est un morpion', 'games/TicTacToe/tictactoe.php', '2025-06-24 15:03:52'),
(7, 'Carré surfers', 'inspiré du Subway surfers', 'games/carre-surfers/carre-surfers.php', '2025-06-25 07:41:59'),
(8, 'Pc clickers', 'tout est dans le nom', 'games/pc-clickers/pc-clickers.php', '2025-06-25 08:23:55'),
(9, 'Tirage Loto', 'loto', 'games/loto/loto.php', '2025-06-25 14:40:52'),
(10, 'OSU !', 'jeux de précision', 'games/osu/osu.php', '2025-06-26 13:48:31'),
(11, 'Casse brique', 'casse brique', 'games/casse_brique/casse_brique.php', '2025-06-27 12:28:33');

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
(22, 1, 'Timothe', 103420, '2025-06-26 08:57:17'),
(23, 1, 'viomaurer', 78500, '2025-06-26 19:30:50');

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
-- Index pour la table `connexion_games`
--
ALTER TABLE `connexion_games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT pour la table `connexion_games`
--
ALTER TABLE `connexion_games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `high_scores`
--
ALTER TABLE `high_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
