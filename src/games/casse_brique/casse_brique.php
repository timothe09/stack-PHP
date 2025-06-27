<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casse-Briques</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .game-container {
            position: relative;
            width: 800px;
            height: 600px;
            margin: 20px auto;
            background-color: #1a1a2e;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        canvas {
            display: block;
            background: linear-gradient(to bottom, #1a1a2e, #16213e);
        }

        .ui-container {
            width: 800px;
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #16213e;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .ui-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .ui-label {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 5px;
        }

        .ui-value {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        #healthBar {
            width: 200px;
            height: 20px;
            background-color: #333;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }

        #healthBarFill {
            height: 100%;
            background: linear-gradient(to right, #ff0055, #ff66aa);
            width: 100%;
            transition: width 0.3s;
        }

        .high {
            color: #ffcc00 !important;
            text-shadow: 0 0 10px rgba(255, 204, 0, 0.5);
        }

        #gameOver {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        #gameOver.hidden {
            display: none;
        }

        #gameOver h2 {
            font-size: 48px;
            color: #ff66aa;
            margin-bottom: 20px;
            text-shadow: 0 0 10px rgba(255, 102, 170, 0.5);
        }

        #gameOver .stats {
            margin-bottom: 30px;
            text-align: center;
        }

        #gameOver .stat-item {
            margin: 10px 0;
            font-size: 24px;
        }

        #gameOver .stat-value {
            font-weight: bold;
            color: #ffcc00;
        }

        button {
            background: linear-gradient(to bottom, #ff66aa, #ff0055);
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 18px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 10px rgba(255, 102, 170, 0.5);
            margin: 0 10px;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 102, 170, 0.7);
        }
        
        .game-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        #countdown {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 100px;
            color: white;
            opacity: 0;
            z-index: 5;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes countdown {
            0% {
                transform: translate(-50%, -50%) scale(0.5);
                opacity: 0;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0;
            }
        }

        .scores-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .scores-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .home-button {
            right: 80px;
        }

        #gameMode {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php
    // Récupérer l'ID du jeu depuis l'URL
    $game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    ?>

    <div class="ui-container">
        <div class="ui-item">
            <div class="ui-label">SCORE</div>
            <div id="score" class="ui-value">0</div>
        </div>
        <div class="ui-item">
            <div class="ui-label">MEILLEUR SCORE</div>
            <div id="highScore" class="ui-value">0</div>
        </div>
        <div class="ui-item">
            <div class="ui-label">NIVEAU</div>
            <div id="level" class="ui-value">1</div>
        </div>
        <div class="ui-item">
            <div class="ui-label">VIES</div>
            <div id="lives" class="ui-value">1</div>
        </div>
        <div class="ui-item">
            <div class="ui-label">SANTÉ</div>
            <div id="healthBar">
                <div id="healthBarFill"></div>
            </div>
        </div>
    </div>

    <div class="game-container">
        <div id="gameMode">Normal</div>
        <a href="../../scores.php?game_id=<?php echo $game_id; ?>" class="scores-button">Scores</a>
        <a href="../../index.php" class="scores-button home-button">Accueil</a>
        <div id="countdown">3</div>
        <canvas id="gameCanvas" width="800" height="600"></canvas>
        <div id="gameOver" class="hidden">
            <h2>Game Over</h2>
            <div class="stats">
                <div class="stat-item">Score final: <span id="finalScore" class="stat-value">0</span></div>
                <div class="stat-item">Niveau atteint: <span id="finalLevel" class="stat-value">1</span></div>
                <div class="stat-item">Briques cassées: <span id="bricksBroken" class="stat-value">0</span></div>
            </div>
            <div class="game-buttons">
                <button id="restartBtn">Rejouer</button>
                <button id="homeBtn">Retour à l'accueil</button>
            </div>
        </div>
    </div>

    <input type="hidden" id="game_id" value="<?php echo $game_id; ?>">

    <script>
    // Attendre que le DOM soit chargé
    document.addEventListener('DOMContentLoaded', () => {
        // Créer et démarrer le jeu
        const game = new BrickBreaker();
    });

    /**
     * Jeu de Casse-Briques
     * Un jeu classique de casse-briques avec des power-ups et plusieurs niveaux
     */
    class BrickBreaker {
        /**
         * Initialisation du jeu
         */
        constructor() {
            // Configuration du canvas
            this.canvas = document.getElementById('gameCanvas');
            this.ctx = this.canvas.getContext('2d');
            
            // Configuration du jeu
            this.gameSpeed = 1.0;
            this.score = 0;
            this.highScore = 0;
            this.gameOver = false;
            this.gameStarted = false;
            this.gameId = document.getElementById('game_id') ? document.getElementById('game_id').value : null;
            
            // Charger les meilleurs scores
            this.loadHighScores();
            
            // Système de santé et vies
            this.health = 100;
            this.lives = 1;
            
            // Système de niveau et difficulté
            this.level = 1;
            this.maxLevel = 10;
            this.difficulty = 1;
            this.bricksDestroyed = 0;
            
            // Modes de jeu
            this.gameModes = ['Normal', 'Hard', 'Expert', 'Chaos'];
            this.currentGameMode = 0;
            
            // Configuration de la balle
            this.ball = {
                x: this.canvas.width / 2,
                y: this.canvas.height - 50,
                radius: 8,
                dx: 4,
                dy: -4,
                color: '#ffffff',
                speed: 1.0
            };
            
            // Configuration de la raquette
            this.paddle = {
                x: this.canvas.width / 2 - 50,
                y: this.canvas.height - 30,
                width: 100,
                height: 15,
                dx: 8,
                color: '#ff66aa'
            };
            
            // Configuration des briques
            this.brickRowCount = 5;
            this.brickColumnCount = 10;
            this.brickWidth = 70;
            this.brickHeight = 20;
            this.brickPadding = 10;
            this.brickOffsetTop = 60;
            this.brickOffsetLeft = 35;
            this.bricks = [];
            
            // Types de briques
            this.brickTypes = [
                { name: 'normal', color: '#ff66aa', points: 10, health: 1, probability: 0.7 },
                { name: 'tough', color: '#ffcc00', points: 20, health: 2, probability: 0.2 },
                { name: 'super', color: '#66ccff', points: 30, health: 3, probability: 0.1 }
            ];
            
            // Powerups
            this.powerups = [];
            this.activePowerups = [];
            this.powerupTypes = [
                { name: 'expand', color: '#66ff66', duration: 10000, effect: 'Raquette élargie' },
                { name: 'slow', color: '#66ccff', duration: 8000, effect: 'Balle ralentie' },
                { name: 'multiball', color: '#ffcc00', duration: 0, effect: 'Balle multiple' },
                { name: 'extraLife', color: '#ff66aa', duration: 0, effect: 'Vie supplémentaire' }
            ];
            
            // Balles multiples (pour le powerup multiball)
            this.balls = [this.ball];
            
            // Effets visuels
            this.effects = [];
            
            // Initialisation
            this.initBricks();
            this.setupEventListeners();
            this.startCountdown();
        }
        
        /**
         * Initialise les briques avec différents types
         */
        initBricks() {
            this.bricks = [];
            
            for (let c = 0; c < this.brickColumnCount; c++) {
                this.bricks[c] = [];
                for (let r = 0; r < this.brickRowCount; r++) {
                    // Déterminer le type de brique en fonction des probabilités
                    let brickType = this.brickTypes[0]; // Par défaut, brique normale
                    const rand = Math.random();
                    let probSum = 0;
                    
                    for (const type of this.brickTypes) {
                        probSum += type.probability;
                        if (rand <= probSum) {
                            brickType = type;
                            break;
                        }
                    }
                    
                    // Créer la brique avec son type
                    this.bricks[c][r] = {
                        x: c * (this.brickWidth + this.brickPadding) + this.brickOffsetLeft,
                        y: r * (this.brickHeight + this.brickPadding) + this.brickOffsetTop,
                        status: 1,
                        type: brickType.name,
                        color: brickType.color,
                        points: brickType.points,
                        health: brickType.health,
                        maxHealth: brickType.health
                    };
                }
            }
        }
        
        /**
         * Configuration des événements (clavier, souris)
         */
        setupEventListeners() {
            // Contrôle de la raquette avec la souris
            this.canvas.addEventListener('mousemove', (e) => {
                if (!this.gameStarted || this.gameOver) return;
                
                const rect = this.canvas.getBoundingClientRect();
                const relativeX = e.clientX - rect.left;
                
                if (relativeX > 0 && relativeX < this.canvas.width) {
                    this.paddle.x = relativeX - this.paddle.width / 2;
                    
                    // Limiter la raquette aux bords du canvas
                    if (this.paddle.x < 0) {
                        this.paddle.x = 0;
                    } else if (this.paddle.x + this.paddle.width > this.canvas.width) {
                        this.paddle.x = this.canvas.width - this.paddle.width;
                    }
                }
            });
            
            // Contrôle de la raquette avec le clavier
            document.addEventListener('keydown', (e) => {
                if (!this.gameStarted || this.gameOver) return;
                
                if (e.key === 'ArrowRight') {
                    this.paddle.x += this.paddle.dx;
                    if (this.paddle.x + this.paddle.width > this.canvas.width) {
                        this.paddle.x = this.canvas.width - this.paddle.width;
                    }
                } else if (e.key === 'ArrowLeft') {
                    this.paddle.x -= this.paddle.dx;
                    if (this.paddle.x < 0) {
                        this.paddle.x = 0;
                    }
                } else if (e.key === 'm' || e.key === 'M') {
                    this.changeGameMode();
                }
            });
            
            // Événements pour les boutons
            document.getElementById('restartBtn').addEventListener('click', () => this.restart());
            document.getElementById('homeBtn').addEventListener('click', () => window.location.href = '../../index.php');
        }
        
        /**
         * Change le mode de jeu actuel
         */
        changeGameMode() {
            if (!this.gameStarted || this.gameOver) return;
            
            this.currentGameMode = (this.currentGameMode + 1) % this.gameModes.length;
            document.getElementById('gameMode').textContent = this.gameModes[this.currentGameMode];
            
            // Appliquer les effets du mode de jeu
            switch (this.gameModes[this.currentGameMode]) {
                case 'Hard':
                    this.ball.speed = 1.3;
                    this.paddle.width = 80;
                    break;
                case 'Expert':
                    this.ball.speed = 1.6;
                    this.paddle.width = 60;
                    break;
                case 'Chaos':
                    this.ball.speed = 1.4;
                    this.paddle.width = 70;
                    // Ajouter une balle supplémentaire en mode Chaos
                    this.addBall();
                    break;
                default: // Normal
                    this.ball.speed = 1.0;
                    this.paddle.width = 100;
                    break;
            }
            
            // Mettre à jour la vitesse de toutes les balles
            this.balls.forEach(ball => {
                const speed = Math.sqrt(ball.dx * ball.dx + ball.dy * ball.dy);
                const angle = Math.atan2(ball.dy, ball.dx);
                const newSpeed = speed * this.ball.speed;
                ball.dx = Math.cos(angle) * newSpeed;
                ball.dy = Math.sin(angle) * newSpeed;
            });
        }
        
        /**
         * Démarre le compte à rebours avant le début du jeu
         */
        startCountdown() {
            const countdownElement = document.getElementById('countdown');
            let count = 3;
            
            const updateCountdown = () => {
                countdownElement.textContent = count;
                countdownElement.style.opacity = '1';
                countdownElement.style.animation = 'countdown 1s ease-in-out';
                
                setTimeout(() => {
                    countdownElement.style.opacity = '0';
                    
                    if (count > 1) {
                        count--;
                        setTimeout(updateCountdown, 1000);
                    } else {
                        this.startGame();
                    }
                }, 1000);
            };
            
            updateCountdown();
        }
        
        /**
         * Démarre le jeu après le compte à rebours
         */
        startGame() {
            this.gameStarted = true;
            this.gameOver = false;
            this.score = 0;
            this.level = 1;
            this.lives = 1;
            this.health = 100;
            this.difficulty = 1;
            this.bricksDestroyed = 0;
            
            // Réinitialiser la balle
            this.ball = {
                x: this.canvas.width / 2,
                y: this.canvas.height - 50,
                radius: 8,
                dx: 4,
                dy: -4,
                color: '#ffffff',
                speed: 1.0
            };
            
            // Réinitialiser la raquette
            this.paddle = {
                x: this.canvas.width / 2 - 50,
                y: this.canvas.height - 30,
                width: 100,
                height: 15,
                dx: 8,
                color: '#ff66aa'
            };
            
            // Réinitialiser les balles multiples
            this.balls = [this.ball];
            
            // Réinitialiser les powerups
            this.powerups = [];
            this.activePowerups = [];
            
            // Réinitialiser les effets visuels
            this.effects = [];
            
            // Réinitialiser les briques
            this.initBricks();
            
            // Réinitialiser le mode de jeu
            this.currentGameMode = 0;
            document.getElementById('gameMode').textContent = this.gameModes[this.currentGameMode];
            document.getElementById('level').textContent = this.level;
            
            // Mettre à jour l'interface
            this.updateUI();
            
            // Démarrer la boucle de jeu
            this.gameLoop();
        }
        
        /**
         * Boucle principale du jeu
         */
        gameLoop() {
            if (this.gameOver) return;
            
            // Effacer le canvas
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            // Dessiner le fond
            this.drawBackground();
            
            // Dessiner les briques
            this.drawBricks();
            
            // Dessiner la raquette
            this.drawPaddle();
            
            // Dessiner les balles
            this.drawBalls();
            
            // Dessiner les powerups
            this.drawPowerups();
            
            // Dessiner les effets visuels
            this.drawEffects();
            
            // Mettre à jour la position des balles et détecter les collisions
            this.updateBalls();
            
            // Mettre à jour les powerups
            this.updatePowerups();
            
            // Mettre à jour les powerups actifs
            this.updateActivePowerups();
            
            // Mettre à jour les effets visuels
            this.updateEffects();
            
            // Vérifier si toutes les briques sont détruites
            if (this.checkLevelComplete()) {
                this.levelUp();
            }
            
            // Mettre à jour l'interface
            this.updateUI();
            
            // Continuer la boucle de jeu
            requestAnimationFrame(() => this.gameLoop());
        }
        
        /**
         * Dessine le fond du jeu
         */
        drawBackground() {
            const gradient = this.ctx.createLinearGradient(0, 0, 0, this.canvas.height);
            gradient.addColorStop(0, '#1a1a2e');
            gradient.addColorStop(1, '#16213e');
            this.ctx.fillStyle = gradient;
            this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        }
        
        /**
         * Dessine les briques
         */
        drawBricks() {
            for (let c = 0; c < this.brickColumnCount; c++) {
                for (let r = 0; r < this.brickRowCount; r++) {
                    const brick = this.bricks[c][r];
                    if (brick.status > 0) {
                        // Calculer l'opacité en fonction de la santé restante
                        const opacity = 0.5 + 0.5 * (brick.health / brick.maxHealth);
                        
                        // Dessiner la brique
                        this.ctx.beginPath();
                        this.ctx.rect(brick.x, brick.y, this.brickWidth, this.brickHeight);
                        this.ctx.fillStyle = brick.color;
                        this.ctx.globalAlpha = opacity;
                        this.ctx.fill();
                        this.ctx.globalAlpha = 1.0;
                        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
                        this.ctx.lineWidth = 2;
                        this.ctx.stroke();
                        
                        // Afficher la santé pour les briques avec plus d'un point de vie
                        if (brick.maxHealth > 1) {
                            this.ctx.font = 'bold 12px Arial';
                            this.ctx.fillStyle = 'white';
                            this.ctx.textAlign = 'center';
                            this.ctx.textBaseline = 'middle';
                            this.ctx.fillText(brick.health, brick.x + this.brickWidth / 2, brick.y + this.brickHeight / 2);
                        }
                    }
                }
            }
        }
        
        /**
         * Dessine la raquette
         */
        drawPaddle() {
            // Dessiner le corps de la raquette
            this.ctx.beginPath();
            this.ctx.roundRect(this.paddle.x, this.paddle.y, this.paddle.width, this.paddle.height, 8);
            
            // Créer un dégradé pour la raquette
            const gradient = this.ctx.createLinearGradient(
                this.paddle.x, this.paddle.y, 
                this.paddle.x, this.paddle.y + this.paddle.height
            );
            gradient.addColorStop(0, '#ff66aa');
            gradient.addColorStop(1, '#ff0055');
            
            this.ctx.fillStyle = gradient;
            this.ctx.fill();
            this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.7)';
            this.ctx.lineWidth = 2;
            this.ctx.stroke();
            
            // Ajouter un effet de brillance
            this.ctx.beginPath();
            this.ctx.roundRect(
                this.paddle.x + 10, 
                this.paddle.y + 2, 
                this.paddle.width - 20, 
                4, 
                2
            );
            this.ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
            this.ctx.fill();
        }
        
        /**
         * Dessine toutes les balles
         */
        drawBalls() {
            this.balls.forEach(ball => {
                // Dessiner la balle
                this.ctx.beginPath();
                this.ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
                this.ctx.fillStyle = ball.color;
                this.ctx.fill();
                this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.7)';
                this.ctx.lineWidth = 2;
                this.ctx.stroke();
                
                // Ajouter un effet de brillance
                this.ctx.beginPath();
                this.ctx.arc(ball.x - ball.radius / 3, ball.y - ball.radius / 3, ball.radius / 3, 0, Math.PI * 2);
                this.ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                this.ctx.fill();
            });
        }
        
        /**
         * Dessine les powerups
         */
        drawPowerups() {
            this.powerups.forEach(powerup => {
                // Dessiner le cercle du powerup
                this.ctx.beginPath();
                this.ctx.arc(
                    powerup.x + powerup.size / 2,
                    powerup.y + powerup.size / 2,
                    powerup.size / 2,
                    0,
                    Math.PI * 2
                );
                this.ctx.fillStyle = powerup.color;
                this.ctx.fill();
                this.ctx.strokeStyle = 'white';
                this.ctx.lineWidth = 2;
                this.ctx.stroke();
                
                // Dessiner l'icône du powerup
                this.ctx.font = 'bold 16px Arial';
                this.ctx.fillStyle = 'white';
                this.ctx.textAlign = 'center';
                this.ctx.textBaseline = 'middle';
                
                let icon;
                switch (powerup.type) {
                    case 'expand':
                        icon = '↔';
                        break;
                    case 'slow':
                        icon = '⏱';
                        break;
                    case 'multiball':
                        icon = '+';
                        break;
                    case 'extraLife':
                        icon = '♥';
                        break;
                    default:
                        icon = '?';
                }
                
                this.ctx.fillText(icon, powerup.x + powerup.size / 2, powerup.y + powerup.size / 2);
            });
        }
        
        /**
         * Dessine les effets visuels
         */
        drawEffects() {
            this.effects.forEach(effect => {
                this.ctx.save();
                this.ctx.globalAlpha = effect.opacity;
                
                let color;
                let text = effect.type.toUpperCase();
                
                switch (effect.type) {
                    case 'brick':
                        color = '#66ccff';
                        text = `+${effect.points}`;
                        break;
                    case 'powerup':
                        color = '#ff66aa';
                        text = 'POWER UP!';
                        break;
                    case 'levelUp':
                        color = '#ffcc00';
                        text = `NIVEAU ${this.level}!`;
                        break;
                    case 'extraLife':
                        color = '#66ff66';
                        text = '+1 VIE';
                        break;
                    default:
                        color = 'white';
                }
                
                this.ctx.font = `bold ${20 * effect.scale}px Arial`;
                this.ctx.fillStyle = color;
                this.ctx.textAlign = 'center';
                this.ctx.textBaseline = 'middle';
                this.ctx.fillText(text, effect.x, effect.y);
                this.ctx.restore();
            });
            
            // Afficher les powerups actifs
            if (this.activePowerups.length > 0) {
                let y = 30;
                this.ctx.font = '16px Arial';
                this.ctx.textAlign = 'left';
                this.ctx.textBaseline = 'middle';
                
                this.activePowerups.forEach(powerup => {
                    if (powerup.duration > 0) {
                        const timeLeft = Math.ceil((powerup.activatedAt + powerup.duration - performance.now()) / 1000);
                        let text;
                        let color;
                        
                        switch (powerup.type) {
                            case 'expand':
                                text = `Raquette élargie (${timeLeft}s)`;
                                color = '#66ff66';
                                break;
                            case 'slow':
                                text = `Balle ralentie (${timeLeft}s)`;
                                color = '#66ccff';
                                break;
                        }
                        
                        this.ctx.fillStyle = color;
                        this.ctx.fillText(text, 10, y);
                        y += 25;
                    }
                });
            }
        }
        
        /**
         * Met à jour la position des balles et détecte les collisions
         */
        updateBalls() {
            // Tableau pour stocker les balles à supprimer
const ballsToRemove = [];
            
            // Mettre à jour chaque balle
            this.balls.forEach((ball, index) => {
                // Appliquer le powerup slow si actif
                let speedMultiplier = 1;
                if (this.hasPowerup('slow')) {
                    speedMultiplier = 0.6;
                }
                
                // Mettre à jour la position de la balle
                ball.x += ball.dx * speedMultiplier;
                ball.y += ball.dy * speedMultiplier;
                
                // Collision avec les bords horizontaux
                if (ball.x + ball.radius > this.canvas.width || ball.x - ball.radius < 0) {
                    ball.dx = -ball.dx;
                    this.playSound('wall');
                }
                
                // Collision avec le bord supérieur
                if (ball.y - ball.radius < 0) {
                    ball.dy = -ball.dy;
                    this.playSound('wall');
                }
                
                // Collision avec le bord inférieur (perte de vie)
                if (ball.y + ball.radius > this.canvas.height) {
                    // Si c'est la dernière balle, perdre une vie
                    if (this.balls.length === 1) {
                        this.loseLife();
                    } else {
                        // Sinon, supprimer cette balle
                        ballsToRemove.push(index);
                    }
                }
                
                // Collision avec la raquette
                if (
                    ball.y + ball.radius > this.paddle.y &&
                    ball.y - ball.radius < this.paddle.y + this.paddle.height &&
                    ball.x > this.paddle.x &&
                    ball.x < this.paddle.x + this.paddle.width
                ) {
                    // Calculer la position relative de la balle sur la raquette (de -1 à 1)
                    const hitPosition = (ball.x - (this.paddle.x + this.paddle.width / 2)) / (this.paddle.width / 2);
                    
                    // Calculer l'angle de rebond en fonction de la position d'impact
                    const angle = hitPosition * Math.PI / 3; // -60° à +60°
                    
                    // Calculer la vitesse actuelle
                    const speed = Math.sqrt(ball.dx * ball.dx + ball.dy * ball.dy);
                    
                    // Appliquer le nouvel angle et la vitesse
                    ball.dx = Math.sin(angle) * speed;
                    ball.dy = -Math.cos(angle) * speed;
                    
                    // Ajouter un petit boost à la vitesse
                    const boost = 1.05;
                    ball.dx *= boost;
                    ball.dy *= boost;
                    
                    // Jouer un son
                    this.playSound('paddle');
                    
                    // Ajouter un effet visuel
                    this.addEffect('paddle', ball.x, this.paddle.y);
                }
                
                // Collision avec les briques
                this.checkBrickCollision(ball);
            });
            
            // Supprimer les balles qui sont sorties de l'écran
            for (let i = ballsToRemove.length - 1; i >= 0; i--) {
                this.balls.splice(ballsToRemove[i], 1);
            }
        }
        
        /**
         * Vérifie les collisions entre une balle et les briques
         */
        checkBrickCollision(ball) {
            for (let c = 0; c < this.brickColumnCount; c++) {
                for (let r = 0; r < this.brickRowCount; r++) {
                    const brick = this.bricks[c][r];
                    
                    if (brick.status === 1) {
                        // Vérifier si la balle touche la brique
                        if (
                            ball.x > brick.x - ball.radius &&
                            ball.x < brick.x + this.brickWidth + ball.radius &&
                            ball.y > brick.y - ball.radius &&
                            ball.y < brick.y + this.brickHeight + ball.radius
                        ) {
                            // Déterminer de quel côté la balle a touché la brique
                            const hitLeft = ball.x < brick.x;
                            const hitRight = ball.x > brick.x + this.brickWidth;
                            const hitTop = ball.y < brick.y;
                            const hitBottom = ball.y > brick.y + this.brickHeight;
                            
                            // Changer la direction de la balle en fonction du côté touché
                            if ((hitLeft || hitRight) && !(hitTop || hitBottom)) {
                                ball.dx = -ball.dx;
                            } else {
                                ball.dy = -ball.dy;
                            }
                            
                            // Réduire la santé de la brique
                            brick.health--;
                            
                            // Si la brique est détruite
                            if (brick.health <= 0) {
                                brick.status = 0;
                                this.score += brick.points * this.difficulty;
                                this.bricksDestroyed++;
                                
                                // Ajouter un effet visuel
                                this.addEffect('brick', brick.x + this.brickWidth / 2, brick.y + this.brickHeight / 2, brick.points);
                                
                                // Chance de faire apparaître un powerup
                                if (Math.random() < 0.2) {
                                    this.spawnPowerup(brick.x + this.brickWidth / 2, brick.y + this.brickHeight / 2);
                                }
                            }
                            
                            // Jouer un son
                            this.playSound('brick');
                            
                            // Sortir de la boucle après avoir trouvé une collision
                            return;
                        }
                    }
                }
            }
        }
        
        /**
         * Met à jour les powerups (mouvement et collision avec la raquette)
         */
        updatePowerups() {
            const powerupsToRemove = [];
            
            this.powerups.forEach((powerup, index) => {
                // Déplacer le powerup vers le bas
                powerup.y += powerup.speed;
                
                // Vérifier si le powerup est sorti de l'écran
                if (powerup.y > this.canvas.height) {
                    powerupsToRemove.push(index);
                    return;
                }
                
                // Vérifier la collision avec la raquette
                if (
                    powerup.y + powerup.size > this.paddle.y &&
                    powerup.y < this.paddle.y + this.paddle.height &&
                    powerup.x + powerup.size > this.paddle.x &&
                    powerup.x < this.paddle.x + this.paddle.width
                ) {
                    // Activer le powerup
                    this.activatePowerup(powerup.type);
                    
                    // Ajouter un effet visuel
                    this.addEffect('powerup', powerup.x + powerup.size / 2, powerup.y + powerup.size / 2);
                    
                    // Jouer un son
                    this.playSound('powerup');
                    
                    // Supprimer le powerup
                    powerupsToRemove.push(index);
                }
            });
            
            // Supprimer les powerups collectés ou sortis de l'écran
            for (let i = powerupsToRemove.length - 1; i >= 0; i--) {
                this.powerups.splice(powerupsToRemove[i], 1);
            }
        }
        
        /**
         * Met à jour les powerups actifs (durée)
         */
        updateActivePowerups() {
            const now = performance.now();
            const powerupsToRemove = [];
            
            this.activePowerups.forEach((powerup, index) => {
                // Vérifier si le powerup a une durée limitée
                if (powerup.duration > 0) {
                    // Vérifier si le powerup est expiré
                    if (now > powerup.activatedAt + powerup.duration) {
                        // Désactiver le powerup
                        this.deactivatePowerup(powerup.type);
                        
                        // Marquer le powerup pour suppression
                        powerupsToRemove.push(index);
                    }
                }
            });
            
            // Supprimer les powerups expirés
            for (let i = powerupsToRemove.length - 1; i >= 0; i--) {
                this.activePowerups.splice(powerupsToRemove[i], 1);
            }
        }
        
        /**
         * Met à jour les effets visuels (opacité, échelle, etc.)
         */
        updateEffects() {
            const effectsToRemove = [];
            
            this.effects.forEach((effect, index) => {
                // Mettre à jour l'opacité
                effect.opacity -= 0.02;
                
                // Mettre à jour l'échelle
                effect.scale += 0.02;
                
                // Mettre à jour la position
                effect.y -= 0.5;
                
                // Vérifier si l'effet est terminé
                if (effect.opacity <= 0) {
                    effectsToRemove.push(index);
                }
            });
            
            // Supprimer les effets terminés
            for (let i = effectsToRemove.length - 1; i >= 0; i--) {
                this.effects.splice(effectsToRemove[i], 1);
            }
        }
        
        /**
         * Vérifie si toutes les briques sont détruites
         */
        checkLevelComplete() {
            for (let c = 0; c < this.brickColumnCount; c++) {
                for (let r = 0; r < this.brickRowCount; r++) {
                    if (this.bricks[c][r].status === 1) {
                        return false;
                    }
                }
            }
            return true;
        }
        
        /**
         * Passe au niveau suivant
         */
        levelUp() {
            // Augmenter le niveau
            this.level++;
            
            // Mettre à jour l'interface
            document.getElementById('level').textContent = this.level;
            
            // Ajouter un effet visuel
            this.addEffect('levelUp', this.canvas.width / 2, this.canvas.height / 2);
            
            // Jouer un son
            this.playSound('levelUp');
            
            // Augmenter la difficulté
            this.difficulty = 1 + (this.level - 1) * 0.2;
            
            // Réinitialiser les briques
            this.initBricks();
            
            // Réinitialiser la position de la balle
            this.ball.x = this.canvas.width / 2;
            this.ball.y = this.canvas.height - 50;
            
            // Augmenter légèrement la vitesse de la balle
            const speedIncrease = 1.1;
            this.balls.forEach(ball => {
                ball.dx *= speedIncrease;
                ball.dy *= speedIncrease;
            });
            
            // Vérifier si le niveau maximum est atteint
            if (this.level > this.maxLevel) {
                this.winGame();
            }
        }
        
        /**
         * Perd une vie
         */
        loseLife() {
            // Réduire la santé
            this.health -= 20;
            
            // Mettre à jour la barre de santé
            document.getElementById('healthBarFill').style.width = `${this.health}%`;
            
            // Si la santé est épuisée, perdre une vie
            if (this.health <= 0) {
                this.lives--;
                this.health = 100;
                
                // Mettre à jour l'interface
                document.getElementById('lives').textContent = this.lives;
                document.getElementById('healthBarFill').style.width = '100%';
                
                // Vérifier si le jeu est terminé
                if (this.lives <= 0) {
                    this.endGame();
                    return;
                }
            }
            
            // Réinitialiser la position de la balle
            this.ball.x = this.canvas.width / 2;
            this.ball.y = this.canvas.height - 50;
            this.ball.dx = 4 * (Math.random() > 0.5 ? 1 : -1);
            this.ball.dy = -4;
            
            // Réinitialiser les balles multiples
            this.balls = [this.ball];
            
            // Jouer un son
            this.playSound('loseLife');
        }
        
        /**
         * Termine le jeu (game over)
         */
        endGame() {
            this.gameOver = true;
            
            // Mettre à jour l'interface de game over
            document.getElementById('finalScore').textContent = this.score;
            document.getElementById('finalLevel').textContent = this.level;
            document.getElementById('bricksBroken').textContent = this.bricksDestroyed;
            
            // Afficher l'écran de game over
            document.getElementById('gameOver').classList.remove('hidden');
            
            // Sauvegarder le score
            this.saveScore();
            
            // Jouer un son
            this.playSound('gameOver');
        }
        
        /**
         * Gagne le jeu (tous les niveaux terminés)
         */
        winGame() {
            this.gameOver = true;
            
            // Mettre à jour l'interface de game over
            document.getElementById('gameOver').querySelector('h2').textContent = 'Victoire !';
            document.getElementById('finalScore').textContent = this.score;
            document.getElementById('finalLevel').textContent = this.level;
            document.getElementById('bricksBroken').textContent = this.bricksDestroyed;
            
            // Afficher l'écran de game over
            document.getElementById('gameOver').classList.remove('hidden');
            
            // Sauvegarder le score
            this.saveScore();
            
            // Jouer un son
            this.playSound('win');
        }
        
        /**
         * Redémarre le jeu
         */
        restart() {
            document.getElementById('gameOver').classList.add('hidden');
            this.startCountdown();
        }
        
        /**
         * Met à jour l'interface utilisateur
         */
        updateUI() {
            document.getElementById('score').textContent = this.score;
            
            // Mettre à jour le meilleur score si nécessaire
            if (this.score > this.highScore) {
                this.highScore = this.score;
                document.getElementById('highScore').textContent = this.highScore;
                document.getElementById('highScore').classList.add('high');
            }
        }
        
        /**
         * Fait apparaître un powerup à une position donnée
         */
        spawnPowerup(x, y) {
            // Choisir un type de powerup aléatoire
            const powerupType = this.powerupTypes[Math.floor(Math.random() * this.powerupTypes.length)];
            
            // Créer le powerup
            const powerup = {
                x: x - 15,
                y: y - 15,
                size: 30,
                speed: 2,
                type: powerupType.name,
                color: powerupType.color
            };
            
            // Ajouter le powerup à la liste
            this.powerups.push(powerup);
        }
        
        /**
         * Active un powerup
         */
        activatePowerup(type) {
            // Trouver le type de powerup
            const powerupType = this.powerupTypes.find(p => p.name === type);
            
            // Vérifier si le powerup est déjà actif
            const existingPowerup = this.activePowerups.find(p => p.type === type);
            
            if (existingPowerup) {
                // Réinitialiser la durée du powerup
                existingPowerup.activatedAt = performance.now();
            } else {
                // Ajouter le powerup à la liste des powerups actifs
                this.activePowerups.push({
                    type: type,
                    duration: powerupType.duration,
                    activatedAt: performance.now()
                });
                
                // Appliquer l'effet du powerup
                switch (type) {
                    case 'expand':
                        this.paddle.width *= 1.5;
                        break;
                    case 'slow':
                        // L'effet est appliqué dans updateBalls
                        break;
                    case 'multiball':
                        this.addBall();
                        this.addBall();
                        break;
                    case 'extraLife':
                        this.lives++;
                        document.getElementById('lives').textContent = this.lives;
                        this.addEffect('extraLife', this.canvas.width / 2, this.canvas.height / 2);
                        break;
                }
            }
        }
        
        /**
         * Désactive un powerup
         */
        deactivatePowerup(type) {
            // Supprimer l'effet du powerup
            switch (type) {
                case 'expand':
                    this.paddle.width /= 1.5;
                    break;
                case 'slow':
                    // L'effet est appliqué dans updateBalls
                    break;
            }
        }
        
        /**
         * Vérifie si un powerup est actif
         */
        hasPowerup(type) {
            return this.activePowerups.some(p => p.type === type);
        }
        
        /**
         * Ajoute une balle supplémentaire
         */
        addBall() {
            // Créer une nouvelle balle avec une direction aléatoire
            const angle = Math.random() * Math.PI * 2;
            const speed = 5;
            
            const newBall = {
                x: this.canvas.width / 2,
                y: this.canvas.height - 50,
                radius: 8,
                dx: Math.cos(angle) * speed,
                dy: Math.sin(angle) * speed,
                color: '#ffffff',
                speed: this.ball.speed
            };
            
            // S'assurer que la balle va vers le haut
            if (newBall.dy > 0) {
                newBall.dy = -newBall.dy;
            }
            
            // Ajouter la balle à la liste
            this.balls.push(newBall);
        }
        
        /**
         * Ajoute un effet visuel
         */
        addEffect(type, x, y, points = 0) {
            this.effects.push({
                type: type,
                x: x,
                y: y,
                opacity: 1,
                scale: 1,
                points: points
            });
        }
        
        /**
         * Joue un son
         */
        playSound(type) {
            // Cette fonction est un placeholder pour l'ajout de sons
            // Vous pouvez implémenter la lecture de sons ici
        }
        
        /**
         * Charge les meilleurs scores
         */
        loadHighScores() {
            // Essayer de charger le meilleur score depuis le localStorage
            const savedHighScore = localStorage.getItem('brickBreakerHighScore');
            if (savedHighScore) {
                this.highScore = parseInt(savedHighScore);
                document.getElementById('highScore').textContent = this.highScore;
            }
        }
        
        /**
         * Sauvegarde le score
         */
        saveScore() {
            // Sauvegarder le meilleur score dans le localStorage
            if (this.score > this.highScore) {
                localStorage.setItem('brickBreakerHighScore', this.score);
            }
            
            // Si un ID de jeu est défini, envoyer le score au serveur
            if (this.gameId) {
                const data = new FormData();
                data.append('game_id', this.gameId);
                data.append('score', this.score);
                
                fetch('../../save_score.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Score sauvegardé:', data);
                })
                .catch(error => {
                    console.error('Erreur lors de la sauvegarde du score:', error);
                });
            }
        }
    }
    </script>
</body>
</html>