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
    <title>Tic Tac Toe</title>
    <link rel="stylesheet" href="tictactoe.css">
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
        <div class="menu" id="mainMenu">
            <h1>Tic Tac Toe</h1>
            <button class="menu-btn" id="vsPlayerBtn">Joueur vs Joueur</button>
            <button class="menu-btn" id="vsAiBtn">Joueur vs IA</button>
            <button class="menu-btn" id="slidingModeBtn">Mode SPECIAL
            </button>
        </div>

        <div class="menu hidden" id="difficultyMenu">
            <h2>Choisir la difficulté</h2>
            <button class="menu-btn" data-difficulty="easy">Facile</button>
            <button class="menu-btn" data-difficulty="medium">Moyen</button>
            <button class="menu-btn" data-difficulty="hard">Difficile</button>
            <button class="menu-btn back-btn">Retour</button>
        </div>

        <div class="menu hidden" id="slidingModeMenu">
            <h2>Mode Glissant</h2>
            <p class="mode-desc">
                Règles spéciales :<br>
                - Maximum 3 symboles par joueur<br>
                - Cliquez sur votre symbole pour le sélectionner<br>
                - Cliquez sur une case vide pour le déplacer<br>
                - Les symboles plus anciens deviennent transparents
            </p>
            <button class="menu-btn" id="vsPlayerSlidingBtn">Joueur vs Joueur</button>
            <button class="menu-btn" id="vsAiSlidingBtn">Joueur vs IA</button>
            <button class="menu-btn back-btn">Retour</button>
        </div>

        <div class="game-board hidden" id="gameBoard">
            <div class="status" id="gameStatus">Tour du Joueur X</div>
            <div class="board">
                <div class="cell" data-index="0"></div>
                <div class="cell" data-index="1"></div>
                <div class="cell" data-index="2"></div>
                <div class="cell" data-index="3"></div>
                <div class="cell" data-index="4"></div>
                <div class="cell" data-index="5"></div>
                <div class="cell" data-index="6"></div>
                <div class="cell" data-index="7"></div>
                <div class="cell" data-index="8"></div>
            </div>
            <button class="menu-btn" id="restartBtn">Recommencer</button>
            <button class="menu-btn back-btn">Menu Principal</button>
        </div>

        <div id="gameOver" class="hidden">
            <h2>Partie terminée !</h2>
            <p id="winnerText"></p>
            <button class="menu-btn" id="restartBtn">Rejouer</button>
            <button class="menu-btn back-btn">Menu Principal</button>
        </div>
    </div>
    <script src="tictactoe.js"></script>
</body>
</html>