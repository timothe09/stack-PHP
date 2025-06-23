<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hosts = ['mysql_db', 'localhost', '127.0.0.1', '172.18.0.3'];

foreach ($hosts as $host) {
    echo "Tentative de connexion à $host...\n";
    try {
        $dsn = "mysql:host=$host;port=3306;dbname=app_database;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 3,
        ];
        
        $pdo = new PDO($dsn, 'app_user', 'app_password', $options);
        echo "✓ Connexion réussie à $host!\n";
        
        $stmt = $pdo->query("SELECT NOW()");
        $result = $stmt->fetch();
        echo "  Heure du serveur: " . $result['NOW()'] . "\n";
        break;
        
    } catch(PDOException $e) {
        echo "✗ Échec de connexion à $host : " . $e->getMessage() . "\n";
    }
    echo "\n";
}