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
    <title>Carré Surfers</title>
    <link rel="stylesheet" href="carre-surfers.css">
    <style>
        .home-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            font-family: Arial, sans-serif;
            transition: background-color 0.2s;
        }
        .home-button:hover {
            background-color: #2980b9;
        }
        .user-info {
            position: fixed;
            top: 20px;
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
    <div class="user-info">
        Connecté en tant que : <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
    </div>
    <div class="game-container">
        <div class="hud">
            <div class="score">Score: <span id="score">0</span></div>
            <div class="high-score">Meilleur score: <span id="highScore">0</span></div>
            <div class="lives">Vies: <span id="lives">3</span></div>
            <div class="combo">Combo: x<span id="combo">1</span></div>
            <div class="rage-meter">
                <div class="rage-fill" id="rageMeter"></div>
            </div>
        </div>
        
        <div class="power-up-indicator" id="powerUpIndicator">
            <div id="powerUpName">Invincibilité</div>
            <div class="power-up-timer">
                <div class="power-up-timer-fill" id="powerUpTimer"></div>
            </div>
        </div>

        <div class="jumps-indicator">
            Sauts: <span id="jumpsLeft">2</span>
        </div>

        <canvas id="gameCanvas"></canvas>

        <div id="gameOver" class="hidden">
            <h2>Game Over!</h2>
            <p>Score final: <span id="finalScore">0</span></p>
            <p>Plus long combo: x<span id="maxCombo">1</span></p>
            <button id="restartBtn">Rejouer</button>
        </div>
    </div>
    <script src="carre-surfers.js"></script>
</body>
</html>