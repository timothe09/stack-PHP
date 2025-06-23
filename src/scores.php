<?php
require_once 'config.php';

// Vérification de l'ID du jeu
if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header('Location: index.php');
    exit();
}

$game_id = (int)$_GET['game_id'];

// Récupération des informations du jeu
$stmt = $pdo->prepare("SELECT title FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header('Location: index.php');
    exit();
}

// Récupération des high scores
$stmt = $pdo->prepare("
    SELECT player_name, score, achieved_at 
    FROM high_scores 
    WHERE game_id = ? 
    ORDER BY score DESC 
    LIMIT 10
");
$stmt->execute([$game_id]);
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High Scores - <?php echo htmlspecialchars($game['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .scores-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
        .no-scores {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>High Scores - <?php echo htmlspecialchars($game['title']); ?></h1>
    
    <div class="scores-container">
        <?php if (count($scores) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Joueur</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($scores as $index => $score): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($score['player_name']); ?></td>
                            <td><?php echo number_format($score['score']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($score['achieved_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-scores">Aucun score enregistré pour ce jeu.</p>
        <?php endif; ?>
        
        <a href="index.php" class="back-button">Retour aux jeux</a>
    </div>
</body>
</html>