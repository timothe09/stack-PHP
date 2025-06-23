<?php
require_once 'config.php';

// Récupération des jeux depuis la base de données
$stmt = $pdo->query("SELECT * FROM games ORDER BY created_at DESC");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection de Jeux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <h1>Collection de Jeux</h1>
        <nav class="nav">
            <a href="index.php">Accueil</a>
            <a href="#classement">Classement Global</a>
            <a href="#nouveautes">Nouveautés</a>
        </nav>
    </header>

    <main>
        <div class="games-container">
            <?php foreach($games as $game): ?>
                <div class="game-card">
                    <h2><?php echo htmlspecialchars($game['title']); ?></h2>
                    <p><?php echo htmlspecialchars($game['description']); ?></p>
                    <div class="game-buttons">
                        <a href="<?php echo htmlspecialchars($game['path']); ?>" class="play-button">Jouer</a>
                        <a href="scores.php?game_id=<?php echo $game['id']; ?>" class="scores-button">High Scores</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Collection de Jeux. Tous droits réservés.</p>
    </footer>
</body>
</html>