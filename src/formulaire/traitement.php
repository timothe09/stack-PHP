<?php
require_once '../config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérification des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification des champs attendus
    if (
        isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['message']) &&
        !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['message'])
    ) {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        // Insertion dans la base de données
        try {
            // INSERT INTO `messages` (`id`, `nom`, `prenom`, `email`, `message`) VALUES (NULL, 'toto', 'tata', 'titi@titi.Fr', 'coucou');
            $sql = "INSERT INTO `messages` (`id`, `nom`, `prenom`, `email`, `message`) VALUES (NULL,?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $prenom, $email, $message]);

            // Affichage des données
            echo "<h2>Données reçues :</h2>";
            echo "Nom : " . $nom . "<br>";
            echo "Prénom : " . $prenom . "<br>";
            echo "Email : " . $email . "<br>";
            echo "message : " . nl2br($message);
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
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/
?>
