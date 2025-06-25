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

        // Insertion dans la base de données
        try {
            // Remplacez 'messages' par 'utilisateurs' si c'est le vrai nom de la table
            $sql = "INSERT INTO `games` (`nom_du_jeux`, `description`, `path`,`created_at`) VALUES (?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom_du_jeux, $description, $path]);

            // Affichage des données
            echo "<h2>Données reçues :</h2>";
            echo "nom_du_jeux : " . $nom_du_jeux . "<br>";
            echo "description : " . $description . "<br>";
            echo "path : " . $path . "<br>";
        } catch (PDOException $e) {
            echo "<h2>Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage()) . "</h2>";
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
