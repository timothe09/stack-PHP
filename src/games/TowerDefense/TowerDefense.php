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
    <title>Tower Defense</title>
    <link rel="stylesheet" href="TowerDefense.css">
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
        .scores-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
            font-family: Arial, sans-serif;
            transition: background-color 0.2s;
        }
        .scores-button:hover {
            background-color: #e67e22;
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
    <a href="../../scores.php?game_id=1" class="scores-button">🏆 High Scores</a>
    <div class="user-info">
        Connecté en tant que : <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
    </div>
    <input type="hidden" id="game_id" value="1"/>
    <div class="game-container">
        <div class="hud">
            <div class="score">Score: <span id="score">0</span></div>
            <div class="high-score">Record: <span id="highScore">0</span></div>
            <div class="money">Argent: <span id="money">100</span>€</div>
            <div class="lives">Vies: <span id="lives">10</span></div>
            <div class="level">Niveau: <span id="level">1</span></div>
        </div>
        <div class="game-grid" id="gameGrid"></div>
        <div class="tower-selection">
            <div class="tower" data-cost="50" data-type="basic">
                <div class="tower-basic">Tour Basique - 50€</div>
                <div class="tower-desc">Tir rapide, dégâts moyens</div>
            </div>
             <div class="tower" data-cost="100" data-type="laser">
                <div class="tower-frost">Tour Laser - 100€</div>
                <div class="tower-desc">peu de deg mais tout le temps</div>
            </div>
            <div class="tower" data-cost="100" data-type="sniper">
                <div class="tower-sniper">Tour Sniper - 100€</div>
                <div class="tower-desc">Tir lent, dégâts élevés</div>
            </div>
            <div class="tower" data-cost="150" data-type="splash">
                <div class="tower-splash">Tour Explosive - 150€</div>
                <div class="tower-desc">Dégâts de zone</div>
            </div>
            <div class="tower" data-cost="200" data-type="frost">
                <div class="tower-frost">Tour Gel - 200€</div>
                <div class="tower-desc">Ralentit les ennemis</div>
            </div>
        </div>
    </div>
    <script src="TowerDefense.js"></script>
</body>
</html>