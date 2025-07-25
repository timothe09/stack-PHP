<?php
require_once 'config.php';

try {
    // Lire le contenu du fichier SQL
    $sql = file_get_contents(__DIR__ . '/add_osu_game.sql');
    
    // Exécuter le script SQL
    $pdo->exec($sql);
    
    // Récupérer l'ID du jeu OSU!
    $stmt = $pdo->prepare("SELECT id FROM games WHERE title = 'OSU!'");
    $stmt->execute();
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($game) {
        echo "Le jeu OSU! a été ajouté avec succès. ID: " . $game['id'];
    } else {
        echo "Erreur: Le jeu n'a pas été trouvé après l'insertion.";
    }
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>