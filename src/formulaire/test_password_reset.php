<?php
require_once '../config.php';

// Fonction pour afficher un message
function display_message($message, $type = 'info') {
    echo '<div style="padding: 10px; margin: 10px 0; border-radius: 5px; ';
    
    if ($type === 'success') {
        echo 'background-color: #d4edda; color: #155724;';
    } elseif ($type === 'error') {
        echo 'background-color: #f8d7da; color: #721c24;';
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
    } else {
        display_message("La colonne 'date_naissance' n'existe pas dans la table 'connexion_games'. Veuillez exécuter le script update_database.php.", 'error');
    }
} catch (PDOException $e) {
    display_message("Erreur lors de la vérification de la colonne : " . $e->getMessage(), 'error');
}

// Vérifier si des utilisateurs ont une date de naissance définie
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM connexion_games WHERE date_naissance IS NOT NULL");
    $stmt->execute();
    $users_with_dob = $stmt->fetchColumn();
    
    if ($users_with_dob > 0) {
        display_message("$users_with_dob utilisateur(s) ont une date de naissance définie.", 'success');
    } else {
        display_message("Aucun utilisateur n'a de date de naissance définie. Les utilisateurs existants devront mettre à jour leur profil.", 'info');
    }
} catch (PDOException $e) {
    display_message("Erreur lors de la vérification des utilisateurs : " . $e->getMessage(), 'error');
}

// Vérifier les liens entre les pages
$links_to_check = [
    'formulaire_de_connexion.php' => 'mot_de_passe_oublie.php',
    'mot_de_passe_oublie.php' => 'formulaire_de_connexion.php'
];

foreach ($links_to_check as $source => $target) {
    $source_content = file_get_contents(__DIR__ . '/' . $source);
    if (strpos($source_content, $target) !== false) {
        display_message("Le lien vers '$target' existe bien dans '$source'.", 'success');
    } else {
        display_message("Le lien vers '$target' est manquant dans '$source'.", 'error');
    }
}

// Vérifier que le formulaire de récupération de mot de passe contient les étapes nécessaires
$reset_form = file_get_contents(__DIR__ . '/mot_de_passe_oublie.php');
$steps_to_check = [
    'step 1' => 'Étape 1: Email',
    'step 2' => 'Étape 2: Vérification',
    'step 3' => 'Étape 3: Nouveau mot de passe'
];

foreach ($steps_to_check as $key => $step) {
    if (strpos($reset_form, $step) !== false) {
        display_message("L'étape '$step' existe bien dans le formulaire de récupération.", 'success');
    } else {
        display_message("L'étape '$step' est manquante dans le formulaire de récupération.", 'error');
    }
}

// Afficher un résumé des fonctionnalités
echo '<h2>Résumé des fonctionnalités</h2>';
echo '<ul>';
echo '<li>Formulaire d\'inscription avec champ date de naissance</li>';
echo '<li>Lien "Mot de passe oublié" sur la page de connexion</li>';
echo '<li>Processus de récupération de mot de passe en 3 étapes :</li>';
echo '<ul>';
echo '<li>Étape 1 : Saisie de l\'adresse email</li>';
echo '<li>Étape 2 : Vérification de la date de naissance</li>';
echo '<li>Étape 3 : Définition d\'un nouveau mot de passe</li>';
echo '</ul>';
echo '</ul>';

// Afficher les instructions pour tester manuellement
echo '<h2>Instructions pour tester manuellement</h2>';
echo '<ol>';
echo '<li>Créez un nouveau compte en utilisant le <a href="formulaire_de_creation.php">formulaire d\'inscription</a> (n\'oubliez pas de saisir votre date de naissance)</li>';
echo '<li>Déconnectez-vous et allez sur la <a href="formulaire_de_connexion.php">page de connexion</a></li>';
echo '<li>Cliquez sur le lien "Mot de passe oublié"</li>';
echo '<li>Suivez le processus de récupération de mot de passe en 3 étapes</li>';
echo '<li>Connectez-vous avec votre nouveau mot de passe</li>';
echo '</ol>';

// Lien pour retourner à l'accueil
echo '<p><a href="../index.php">Retour à l\'accueil</a></p>';
?>