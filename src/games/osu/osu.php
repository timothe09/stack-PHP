<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../formulaire/formulaire_de_connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSU!</title>
    <link rel="stylesheet" href="osu.css">
    <style>
        .home-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #ff66aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            font-family: Arial, sans-serif;
            transition: background-color 0.2s;
        }
        .home-button:hover {
            background-color: #e64c8a;
        }
        .scores-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #66ccff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            font-family: Arial, sans-serif;
            transition: background-color 0.2s;
        }
        .scores-button:hover {
            background-color: #4ca6e6;
        }
        .user-info {
            position: fixed;
            top: 60px;
            right: 20px;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 4px;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <a href="../../index.php" class="home-button">← Retour à l'accueil</a>
    <a href="../../scores.php?game_id=6" class="scores-button">🏆 High Scores</a>
    <div class="user-info">
        Connecté en tant que : <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
    </div>
    <input type="hidden" id="game_id" value="6"/>
    
    <div class="game-container">
        <div class="game-area">
            <div id="countdown" class="countdown">3</div>
            <canvas id="gameCanvas"></canvas>
            
            <div id="gameOver" class="hidden">
                <h2>Game Over!</h2>
                <p>Score final: <span id="finalScore">0</span></p>
                <p>Précision finale: <span id="finalAccuracy">100</span>%</p>
                <p>Combo maximum: x<span id="maxCombo">0</span></p>
                <button id="restartBtn">Rejouer</button>
            </div>
        </div>
        
        <div class="hud">
            <div class="stats-title">OSU! Stats</div>
            <div class="score">Score: <span id="score">0</span></div>
            <div class="high-score">Meilleur score: <span id="highScore">0</span></div>
            <div class="combo">Combo: x<span id="combo">0</span></div>
            <div class="accuracy">Précision: <span id="accuracy">100</span>%</div>
            <div class="level">Niveau: <span id="level">1</span></div>
            <div class="mode">Mode: <span id="gameMode">Normal</span></div>
            <div class="health-bar">
                <div class="health-fill" id="healthBar"></div>
            </div>
        </div>
    </div>
    
    <script src="osu.js"></script>
</body>
</html>