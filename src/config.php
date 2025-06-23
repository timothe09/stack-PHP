<?php
// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'games_db';
$db_user = 'root';
$db_pass = 'root_password';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données: " . $e->getMessage());
    die("Impossible de se connecter à la base de données");
}

// Fonction pour échapper les caractères spéciaux HTML
function escape_html($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
