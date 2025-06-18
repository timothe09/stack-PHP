<?php
// Fichier de test pour la stack PHP + MySQL
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stack PHP + MySQL - Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐳 Stack PHP + MySQL</h1>
        <h2>État des services</h2>
        
        <?php
        // Test de la version PHP
        echo '<div class="status success">';
        echo '<strong>✅ PHP:</strong> Version ' . phpversion();
        echo '</div>';
        
        // Test des extensions PHP
        $extensions = ['mysqli', 'pdo', 'pdo_mysql', 'gd', 'zip'];
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="status success">';
                echo '<strong>✅ Extension ' . $ext . ':</strong> Chargée';
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo '<strong>❌ Extension ' . $ext . ':</strong> Non chargée';
                echo '</div>';
            }
        }
        
        // Test de connexion MySQL
        try {
            $host = 'mysql';
            $dbname = 'app_database';
            $username = 'app_user';
            $password = 'app_password';
            
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="status success">';
            echo '<strong>✅ MySQL:</strong> Connexion réussie à la base de données';
            echo '</div>';
            
            // Affichage de la version MySQL
            $stmt = $pdo->query('SELECT VERSION() as version');
            $version = $stmt->fetch(PDO::FETCH_ASSOC);
            echo '<div class="status info">';
            echo '<strong>📊 MySQL Version:</strong> ' . $version['version'];
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="status error">';
            echo '<strong>❌ MySQL:</strong> Erreur de connexion - ' . $e->getMessage();
            echo '</div>';
        }
        
        // Test Redis (optionnel)
        if (extension_loaded('redis')) {
            try {
                $redis = new Redis();
                $redis->connect('redis', 6379);
                echo '<div class="status success">';
                echo '<strong>✅ Redis:</strong> Connexion réussie';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="status error">';
                echo '<strong>❌ Redis:</strong> Erreur de connexion - ' . $e->getMessage();
                echo '</div>';
            }
        }
        
        // Informations du système
        echo '<h2>Informations système</h2>';
        echo '<div class="status info">';
        echo '<strong>🖥️ Serveur:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '<br>';
        echo '<strong>📁 Document Root:</strong> ' . $_SERVER['DOCUMENT_ROOT'] . '<br>';
        echo '<strong>🕒 Date/Heure:</strong> ' . date('Y-m-d H:i:s');
        echo '</div>';
        ?>
        
        <h2>Accès aux services</h2>
        <div class="status info">
            <strong>🌐 Application PHP:</strong> <a href="http://localhost:8080" target="_blank">http://localhost:8080</a><br>
            <strong>🗄️ PhpMyAdmin:</strong> <a href="http://localhost:8081" target="_blank">http://localhost:8081</a><br>
            <strong>🔧 MySQL:</strong> localhost:3306<br>
            <strong>⚡ Redis:</strong> localhost:6379
        </div>
    </div>
</body>
</html>