<?php
require_once 'config.php';

try {
    // Afficher tous les jeux dans la table
    $stmt = $pdo->query("SELECT * FROM games");
    $games = $stmt->fetchAll();
    
    echo "Liste des jeux dans la base de données:\n";
    foreach ($games as $game) {
        echo "ID: " . $game['id'] . " - Titre: " . $game['title'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}