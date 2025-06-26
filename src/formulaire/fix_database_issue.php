<?php
require_once '../config.php';

// Fonction pour afficher un message
function display_message($message, $type = 'info') {
    echo '<div style="padding: 10px; margin: 10px 0; border-radius: 5px; ';
    
    if ($type === 'success') {
        echo 'background-color: #d4edda; color: #155724;';
    } elseif ($type === 'error') {
        echo 'background-color: #f8d7da; color: #721c24;';
    } elseif ($type === 'warning') {
        echo 'background-color: #fff3cd; color: #856404;';
    } else {
        echo 'background-color: #cce5ff; color: #004085;';
    }
    
    echo '">' . $message . '</div>';
}

// Vérifier si la colonne date_naissance existe dans la table connexion_games
try {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM connexion_games LIKE 'date_naissance'");
    $stmt->execute();
    $column_exists = $stmt->rowCount() > 0;
    
    if ($column_exists) {
        display_message("La colonne 'date_naissance' existe bien dans la table 'connexion_games'.", 'success');
        
        // Utiliser les fichiers originaux
        if (file_exists(__DIR__ . '/formulaire_de_creation_alt.php')) {
            display_message("Le fichier 'formulaire_de_creation_alt.php' existe mais n'est pas nécessaire car la colonne date_naissance existe déjà.", 'info');
        }
        
        if (file_exists(__DIR__ . '/mot_de_passe_oublie_alt.php')) {
            display_message("Le fichier 'mot_de_passe_oublie_alt.php' existe mais n'est pas nécessaire car la colonne date_naissance existe déjà.", 'info');
        }
    } else {
        display_message("La colonne 'date_naissance' n'existe pas dans la table 'connexion_games'.", 'warning');
        
        // Essayer d'ajouter la colonne
        try {
            $sql = file_get_contents(__DIR__ . '/add_date_naissance_column.sql');
            $result = $pdo->exec($sql);
            
            // Vérifier si la colonne a été ajoutée
            $stmt = $pdo->prepare("SHOW COLUMNS FROM connexion_games LIKE 'date_naissance'");
            $stmt->execute();
            $column_added = $stmt->rowCount() > 0;
            
            if ($column_added) {
                display_message("La colonne 'date_naissance' a été ajoutée avec succès à la table 'connexion_games' !", 'success');
            } else {
                display_message("Impossible d'ajouter la colonne 'date_naissance' à la table 'connexion_games'.", 'error');
                
                // Utiliser les fichiers alternatifs
                if (file_exists(__DIR__ . '/formulaire_de_creation_alt.php') && file_exists(__DIR__ . '/formulaire_de_creation.php')) {
                    if (copy(__DIR__ . '/formulaire_de_creation_alt.php', __DIR__ . '/formulaire_de_creation.php')) {
                        display_message("Le fichier 'formulaire_de_creation.php' a été remplacé par la version alternative.", 'success');
                    } else {
                        display_message("Impossible de remplacer le fichier 'formulaire_de_creation.php'.", 'error');
                    }
                }
                
                if (file_exists(__DIR__ . '/mot_de_passe_oublie_alt.php') && file_exists(__DIR__ . '/mot_de_passe_oublie.php')) {
                    if (copy(__DIR__ . '/mot_de_passe_oublie_alt.php', __DIR__ . '/mot_de_passe_oublie.php')) {
                        display_message("Le fichier 'mot_de_passe_oublie.php' a été remplacé par la version alternative.", 'success');
                    } else {
                        display_message("Impossible de remplacer le fichier 'mot_de_passe_oublie.php'.", 'error');
                    }
                }
            }
        } catch (PDOException $e) {
            display_message("Erreur lors de la modification de la table : " . $e->getMessage(), 'error');
            
            // Utiliser les fichiers alternatifs
            if (file_exists(__DIR__ . '/formulaire_de_creation_alt.php') && file_exists(__DIR__ . '/formulaire_de_creation.php')) {
                if (copy(__DIR__ . '/formulaire_de_creation_alt.php', __DIR__ . '/formulaire_de_creation.php')) {
                    display_message("Le fichier 'formulaire_de_creation.php' a été remplacé par la version alternative.", 'success');
                } else {
                    display_message("Impossible de remplacer le fichier 'formulaire_de_creation.php'.", 'error');
                }
            }
            
            if (file_exists(__DIR__ . '/mot_de_passe_oublie_alt.php') && file_exists(__DIR__ . '/mot_de_passe_oublie.php')) {
                if (copy(__DIR__ . '/mot_de_passe_oublie_alt.php', __DIR__ . '/mot_de_passe_oublie.php')) {
                    display_message("Le fichier 'mot_de_passe_oublie.php' a été remplacé par la version alternative.", 'success');
                } else {
                    display_message("Impossible de remplacer le fichier 'mot_de_passe_oublie.php'.", 'error');
                }
            }
        }
    }
} catch (PDOException $e) {
    display_message("Erreur lors de la vérification de la colonne : " . $e->getMessage(), 'error');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résolution des problèmes de base de données</title>
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
    <h1>Résolution des problèmes de base de données</h1>
    
    <h2>Résumé des actions</h2>
    <p>Ce script a tenté de résoudre les problèmes liés à la colonne 'date_naissance' dans la table 'connexion_games'.</p>
    
    <h2>Que faire maintenant ?</h2>
    <p>Si la colonne 'date_naissance' a été ajoutée avec succès, vous pouvez utiliser le système de récupération de mot de passe normalement.</p>
    <p>Si la colonne n'a pas pu être ajoutée, les fichiers ont été remplacés par des versions alternatives qui fonctionnent sans cette colonne, mais avec une sécurité réduite.</p>
    
    <div class="actions">
        <a href="../index.php">Retour à l'accueil</a>
        <a href="formulaire_de_creation.php">Tester le formulaire d'inscription</a>
        <a href="formulaire_de_connexion.php">Tester le formulaire de connexion</a>
        <a href="mot_de_passe_oublie.php">Tester la récupération de mot de passe</a>
    </div>
</body>
</html>