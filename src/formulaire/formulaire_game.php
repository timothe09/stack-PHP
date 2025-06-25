<?php
require_once '../config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérification des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification des champs attendus
    if (
        isset($_POST['nom_du_jeux'], $_POST['description'], $_POST['path']) &&
        !empty($_POST['nom_du_jeux']) && !empty($_POST['description']) && !empty($_POST['path'])
    ) {
        $nom_du_jeux = htmlspecialchars($_POST['nom_du_jeux']);
        $description = htmlspecialchars($_POST['description']);
        $path = htmlspecialchars($_POST['path']);

        // Vérifier si un jeu avec le même nom existe déjà
        $check_sql = "SELECT COUNT(*) FROM `games` WHERE `nom_du_jeux` = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$nom_du_jeux]);
        $jeu_existe = $check_stmt->fetchColumn() > 0;

        if ($jeu_existe) {
            // Le jeu existe déjà, rediriger vers le formulaire avec un message d'erreur
            header("Location: formulaire_game.html?error=duplicate&game=" . urlencode($nom_du_jeux));
            exit;
        } else {
            try {
            // Le jeu n'existe pas, procéder à l'insertion
            $sql = "INSERT INTO `games` (`nom_du_jeux`, `description`, `path`,`created_at`) VALUES (?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom_du_jeux, $description, $path]);

            // Rediriger vers le formulaire avec un message de succès
            header("Location: formulaire_game.html?success=true&game=" . urlencode($nom_du_jeux));
            exit;
            }
            catch (PDOException $e) {
                echo "<h2>Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage()) . "</h2>";
            }
        }
    } else {
        echo "<h2>Veuillez remplir tous les champs du formulaire.</h2>";
    }  
} else {
    echo "Méthode non autorisée.";
}

/*
-- À exécuter dans MySQL si ce n'est pas déjà fait :
CREATE DATABASE IF NOT EXISTS formulaire_db;
USE formulaire_db;
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_du_jeux VARCHAR(100),
    description VARCHAR(100),
    path VARCHAR(100),
    message TEXT,
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/
?>
?>
