<?php
require_once '../config.php';

try {
    // Vérifier si la colonne existe déjà
    $stmt = $pdo->prepare("SHOW COLUMNS FROM connexion_games LIKE 'date_naissance'");
    $stmt->execute();
    $column_exists = $stmt->rowCount() > 0;
    
    if ($column_exists) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "La colonne 'date_naissance' existe déjà dans la table 'connexion_games'.";
        echo "</div>";
    } else {
        // Lire le contenu du fichier SQL
        $sql = file_get_contents(__DIR__ . '/add_date_naissance_column.sql');
        
        // Afficher le contenu du script SQL
        echo "<div style='background-color: #cce5ff; color: #004085; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>Script SQL à exécuter :</strong><br>";
        echo "<pre>" . htmlspecialchars($sql) . "</pre>";
        echo "</div>";
        
        // Exécuter le script SQL
        $result = $pdo->exec($sql);
        
        // Vérifier si la colonne a été ajoutée
        $stmt = $pdo->prepare("SHOW COLUMNS FROM connexion_games LIKE 'date_naissance'");
        $stmt->execute();
        $column_added = $stmt->rowCount() > 0;
        
        if ($column_added) {
            echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "La colonne 'date_naissance' a été ajoutée avec succès à la table 'connexion_games' !";
            echo "</div>";
        } else {
            echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "La colonne 'date_naissance' n'a pas été ajoutée à la table 'connexion_games'. Vérifiez les erreurs.";
            echo "</div>";
        }
    }
    
    // Afficher la structure de la table
    $stmt = $pdo->prepare("DESCRIBE connexion_games");
    $stmt->execute();
    $table_structure = $stmt->fetchAll();
    
    echo "<div style='background-color: #cce5ff; color: #004085; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>Structure de la table 'connexion_games' :</strong><br>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($table_structure as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . (isset($column['Default']) ? htmlspecialchars($column['Default']) : 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "Erreur lors de la modification de la table : " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de la base de données</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .actions {
            margin-top: 20px;
        }
        .actions a {
            display: inline-block;
            margin-right: 10px;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .actions a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Mise à jour de la base de données</h1>
    
    <div class="actions">
        <a href="../index.php">Retour à l'accueil</a>
        <a href="test_password_reset.php">Tester le système de récupération de mot de passe</a>
    </div>
</body>
</html>