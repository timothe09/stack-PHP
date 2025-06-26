<?php
require_once '/var/www/html/config.php';

try {
    $sql = file_get_contents('/var/www/html/formulaire/create_connexion_games_table.sql');
    
    $result = $pdo->exec($sql);
    
    echo "La table connexion_games a été créée avec succès !";
} catch (PDOException $e) {
    echo "Erreur lors de la création de la table : " . $e->getMessage();
}
?>