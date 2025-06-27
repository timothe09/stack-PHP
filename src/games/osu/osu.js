/**
 * Jeu OSU! - Version web
 * Un jeu de rythme inspiré du célèbre jeu OSU!
 */
class OsuGame {
    /**
     * Initialisation du jeu
     */
    constructor() {
        // Configuration du canvas
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.setupCanvas();
        window.addEventListener('resize', () => this.setupCanvas());

        // Configuration du jeu
        this.gameSpeed = 1.0;
        this.score = 0;
        this.highScore = 0;
        this.gameOver = false;
        this.gameStarted = false;
        this.gameId = document.getElementById('game_id') ? document.getElementById('game_id').value : null;
        
        // Charger les meilleurs scores
        this.loadHighScores();
        
        // Système de santé
        this.health = 100;
        this.healthDecay = 0.1;
        this.healthGain = 5;
        this.healthLoss = 10;
        
        // Système de combo
        this.combo = 0;
        this.maxCombo = 0;
        
        // Système de précision
        this.totalHits = 0;
        this.goodHits = 0;
        this.accuracy = 100;
        
        // Système de niveau et difficulté
        this.level = 1;
        this.maxLevel = 20;
        this.levelUpScore = 2000;
        this.difficulty = 1;
        this.maxDifficulty = 10;
        this.difficultyIncreaseInterval = 10000; // 10 secondes
        this.lastDifficultyIncrease = 0;
        
        // Modes de jeu
        this.gameModes = ['Normal', 'Hard', 'Expert', 'Hidden', 'Sudden Death'];
        this.currentGameMode = 0;
        
        // Cercles de jeu
        this.circles = [];
        this.lastCircleTime = 0;
        this.circleInterval = 2000; // 2 secondes au début
        this.minCircleInterval = 300; // 0.3 seconde au niveau max
        this.approachTime = 1500; // 1.5 secondes pour que le cercle disparaisse
        this.hitWindow = {
            perfect: 100, // ms
            good: 200,    // ms
            ok: 300       // ms
        };
        
        // Types de cercles
        this.circleTypes = [
            { name: 'normal', probability: 0.8, scoreMultiplier: 1.0, color: '#ff66aa' },
            { name: 'spinner', probability: 0.2, scoreMultiplier: 2.0, color: '#ffcc00' }
        ];
        
        // Powerups
        this.powerups = [];
        this.powerupInterval = 15000; // 15 secondes
        this.lastPowerupTime = 0;
        this.activePowerups = [];
        
        // Effets visuels
        this.hitFeedbacks = [];
        
        // Audio
        this.sounds = {
            hit: null,
            miss: null,
            levelUp: null
        };
        
        // Initialisation
        this.setupEventListeners();
        this.startCountdown();
    }

    /**
     * Configuration du canvas et adaptation à la taille de l'écran
     */
    setupCanvas() {
        // Ajuster la taille du canvas en fonction de la taille de son conteneur
        const container = this.canvas.parentElement;
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        
        // Définir la taille du canvas en pixels
        this.canvas.width = Math.min(800, containerWidth);
        this.canvas.height = Math.min(600, containerHeight);
        
        // Définir la taille CSS du canvas
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        
        // Redimensionner le canvas avec un ratio correct
        const dpr = window.devicePixelRatio || 1;
        this.canvas.width = this.canvas.width * dpr;
        this.canvas.height = this.canvas.height * dpr;
        this.ctx.scale(dpr, dpr);
    }

    /**
     * Configuration des événements (clics, touches)
     */
    setupEventListeners() {
        // Événement de clic sur le canvas
        this.canvas.addEventListener('click', (e) => {
            if (!this.gameStarted || this.gameOver) return;
            
            const rect = this.canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left) * (this.canvas.width / rect.width);
            const y = (e.clientY - rect.top) * (this.canvas.height / rect.height);
            
            this.checkHitCircles(x, y);
        });
        
        // Événement pour le bouton de redémarrage
        document.getElementById('restartBtn').addEventListener('click', () => this.restart());
        
        // Événement pour changer de mode de jeu avec la touche M
        window.addEventListener('keydown', (e) => {
            if (e.key === 'm' || e.key === 'M') {
                this.changeGameMode();
            }
        });
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
                this.healthDecay = 0.2;
                this.approachTime = 1200;
                break;
            case 'Expert':
                this.healthDecay = 0.3;
                this.approachTime = 1000;
                break;
            case 'Hidden':
                this.healthDecay = 0.15;
                // Les cercles disparaissent avant d'être cliqués
                break;
            case 'Sudden Death':
                this.healthDecay = 0.1;
                this.healthLoss = 100; // Un miss = game over
                break;
            default: // Normal
                this.healthDecay = 0.1;
                this.approachTime = 1500;
                this.healthLoss = 10;
                break;
        }
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
        // Réinitialiser les variables du jeu
        this.gameStarted = true;
        this.gameOver = false;
        this.score = 0;
        this.combo = 0;
        this.maxCombo = 0;
        this.health = 100;
        this.totalHits = 0;
        this.goodHits = 0;
        this.accuracy = 100;
        this.level = 1;
        this.difficulty = 1;
        
        // Réinitialiser les timers
        this.lastDifficultyIncrease = performance.now();
        this.lastCircleTime = performance.now();
        this.lastPowerupTime = performance.now();
        
        // Vider les tableaux
        this.circles = [];
        this.powerups = [];
        this.activePowerups = [];
        this.hitFeedbacks = [];
        
        // Réinitialiser le mode de jeu
        this.currentGameMode = 0;
        document.getElementById('gameMode').textContent = this.gameModes[this.currentGameMode];
        document.getElementById('level').textContent = this.level;
        
        // Mettre à jour l'interface et démarrer la boucle de jeu
        this.updateUI();
        this.gameLoop();
    }

    /**
     * Boucle principale du jeu
     */
    gameLoop() {
        if (this.gameOver) return;
        
        const now = performance.now();
        
        // Augmenter la difficulté avec le temps
        if (now - this.lastDifficultyIncrease > this.difficultyIncreaseInterval) {
            this.difficulty = Math.min(this.maxDifficulty, this.difficulty + 0.5);
            this.lastDifficultyIncrease = now;
        }
        
        // Vérifier la progression de niveau
        if (this.score >= this.level * this.levelUpScore) {
            this.levelUp();
        }
        
        // Générer et mettre à jour les éléments du jeu
        this.generateCircle(now);
        this.generatePowerup(now);
        this.updateCircles(now);
        this.updatePowerups(now);
        this.updateActivePowerups(now);
        this.updateHitFeedbacks();
        
        // Diminuer la santé avec le temps (sauf si un powerup l'empêche)
        if (!this.hasPowerup('healthFreeze')) {
            this.health = Math.max(0, this.health - this.healthDecay * this.difficulty);
        }
        this.updateUI();
        
        // Vérifier la fin du jeu (mode infini: régénérer la santé si elle est trop basse)
        if (this.health <= 0) {
            if (this.gameModes[this.currentGameMode] === 'Sudden Death') {
                this.endGame();
                return;
            } else {
                // Mode infini: régénération silencieuse
                this.health = 30;
                this.score = Math.max(0, this.score - 1000);
                this.combo = 0;
            }
        }
        
        // Dessiner le jeu
        this.draw();
        
        // Continuer la boucle de jeu
        requestAnimationFrame(() => this.gameLoop());
    }

    /**
     * Génère un nouveau cercle
     */
    generateCircle(now) {
        // Calculer l'intervalle entre les cercles en fonction de la difficulté
        let interval = Math.max(this.minCircleInterval, this.circleInterval - (this.difficulty - 1) * 300);
        if (this.hasPowerup('slowTime')) {
            interval *= 1.5; // Ralentir l'apparition des cercles
        }
        
        if (now - this.lastCircleTime > interval) {
            // Générer une position aléatoire
            const size = Math.floor(Math.random() * 20) + 40; // Taille entre 40 et 60
            const x = Math.random() * (this.canvas.width - size);
            const y = Math.random() * (this.canvas.height - size);
            
            // Déterminer le type de cercle en fonction des probabilités
            let circleType = this.circleTypes[0]; // Par défaut, cercle normal
            const rand = Math.random();
            let probSum = 0;
            
            for (const type of this.circleTypes) {
                probSum += type.probability;
                if (rand <= probSum) {
                    circleType = type;
                    break;
                }
            }
            
            // Créer le cercle avec son type
            this.circles.push({
                x: x,
                y: y,
                size: size,
                createdAt: now,
                approachSize: size * 3,
                hit: false,
                missed: false,
                type: circleType.name,
                color: circleType.color,
                scoreMultiplier: circleType.scoreMultiplier,
                // Propriétés spécifiques aux spinners
                isSpinner: circleType.name === 'spinner',
                spinRequired: circleType.name === 'spinner' ? 3 : 0,
                spinCount: 0
            });
            
            this.lastCircleTime = now;
        }
    }

    /**
     * Met à jour l'état des cercles
     */
    updateCircles(now) {
        this.circles = this.circles.filter(circle => {
            // Vérifier si le cercle a expiré
            const timeElapsed = now - circle.createdAt;
            
            if (timeElapsed > this.approachTime && !circle.hit && !circle.missed) {
                // Le cercle a expiré sans être touché
                circle.missed = true;
                this.missCircle();
                return false;
            }
            
            return !circle.hit && !circle.missed;
        });
    }

    /**
     * Vérifie si un clic a touché un cercle
     */
    checkHitCircles(x, y) {
        // Vérifier d'abord si le joueur a cliqué sur un powerup
        if (this.collectPowerup(x, y)) {
            return; // Si un powerup a été collecté, ne pas vérifier les cercles
        }
        
        let hitAny = false;
        
        for (let i = 0; i < this.circles.length; i++) {
            const circle = this.circles[i];
            const dx = x - (circle.x + circle.size / 2);
            const dy = y - (circle.y + circle.size / 2);
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance <= circle.size / 2) {
                // Le cercle a été touché
                hitAny = true;
                
                // Traitement spécial pour les différents types de cercles
                if (circle.isSpinner) {
                    // Pour les spinners, on incrémente le compteur de spin
                    circle.spinCount++;
                    
                    if (circle.spinCount >= circle.spinRequired) {
                        // Le spinner est complété
                        circle.hit = true;
                        this.score += 500 * circle.scoreMultiplier * (1 + this.combo * 0.1);
                        this.health = Math.min(100, this.health + this.healthGain * 2);
                    } else {
                        // Le spinner n'est pas encore complété
                        continue;
                    }
                } else {
                    // Cercle normal
                    circle.hit = true;
                }
                
                // Calculer la précision du hit
                const timeElapsed = performance.now() - circle.createdAt;
                const timeRemaining = this.approachTime - timeElapsed;
                
                let hitType;
                let baseScore;
                
                if (timeRemaining <= this.hitWindow.perfect) {
                    hitType = 'perfect';
                    baseScore = 300;
                    this.health = Math.min(100, this.health + this.healthGain);
                } else if (timeRemaining <= this.hitWindow.good) {
                    hitType = 'good';
                    baseScore = 100;
                    this.health = Math.min(100, this.health + this.healthGain / 2);
                } else {
                    hitType = 'ok';
                    baseScore = 50;
                    this.health = Math.min(100, this.health + this.healthGain / 4);
                }
                
                // Appliquer le multiplicateur de score du type de cercle et du powerup
                let scoreMultiplier = circle.scoreMultiplier || 1.0;
                if (this.hasPowerup('scoreBoost')) {
                    scoreMultiplier *= 2;
                }
                
                this.score += baseScore * scoreMultiplier * (1 + this.combo * 0.1);
                
                // Augmenter le combo
                this.combo++;
                this.maxCombo = Math.max(this.maxCombo, this.combo);
                
                // Mettre à jour les statistiques de précision
                this.totalHits++;
                this.goodHits++;
                this.accuracy = Math.round((this.goodHits / this.totalHits) * 100);
                
                // Ajouter un effet visuel
                this.addHitFeedback(circle.x + circle.size / 2, circle.y + circle.size / 2, hitType);
                
                // Mettre à jour l'interface
                this.updateUI();
                
                // Supprimer le cercle
                this.circles.splice(i, 1);
                i--;
            }
        }
        
        if (!hitAny) {
            // Le joueur a cliqué mais n'a pas touché de cercle
            this.combo = 0;
            this.health = Math.max(0, this.health - this.healthLoss / 2);
            this.updateUI();
        }
    }

    /**
     * Gère un cercle manqué
     */
    missCircle() {
        // Réinitialiser le combo
        this.combo = 0;
        
        // Diminuer la santé
        this.health = Math.max(0, this.health - this.healthLoss);
        
        // Mettre à jour les statistiques de précision
        this.totalHits++;
        this.accuracy = Math.round((this.goodHits / this.totalHits) * 100);
        
        // Mettre à jour l'interface
        this.updateUI();
    }

    /**
     * Ajoute un effet visuel de feedback
     */
    addHitFeedback(x, y, type) {
        this.hitFeedbacks.push({
            x: x,
            y: y,
            type: type,
            opacity: 1,
            scale: 1
        });
    }

    /**
     * Met à jour les effets visuels de feedback
     */
    updateHitFeedbacks() {
        this.hitFeedbacks = this.hitFeedbacks.filter(feedback => {
            feedback.opacity -= 0.05;
            feedback.scale += 0.05;
            return feedback.opacity > 0;
        });
    }

    /**
     * Dessine tous les éléments du jeu
     */
    draw() {
        // Effacer le canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Dessiner le fond
        const gradient = this.ctx.createLinearGradient(0, 0, 0, this.canvas.height);
        gradient.addColorStop(0, '#1a1a2e');
        gradient.addColorStop(1, '#16213e');
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Dessiner les powerups
        this.drawPowerups();
        
        // Dessiner les cercles
        this.drawCircles();
        
        // Dessiner les effets de feedback
        this.drawFeedbacks();
        
        // Afficher les powerups actifs
        this.drawActivePowerups();
    }

    /**
     * Dessine les powerups
     */
    drawPowerups() {
        this.powerups.forEach(powerup => {
            // Cercle du powerup
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
            
            // Icône du powerup
            this.ctx.font = 'bold 16px Arial';
            this.ctx.fillStyle = 'white';
            this.ctx.textAlign = 'center';
            this.ctx.textBaseline = 'middle';
            
            let icon;
            switch (powerup.type) {
                case 'healthBoost':
                    icon = '+';
                    break;
                case 'scoreBoost':
                    icon = '×2';
                    break;
                case 'slowTime':
                    icon = '⏱';
                    break;
                case 'healthFreeze':
                    icon = '❄';
                    break;
                default:
                    icon = '?';
            }
            
            this.ctx.fillText(icon, powerup.x + powerup.size / 2, powerup.y + powerup.size / 2);
        });
    }

    /**
     * Dessine les cercles
     */
    drawCircles() {
        this.circles.forEach(circle => {
            const timeElapsed = performance.now() - circle.createdAt;
            const progress = timeElapsed / this.approachTime;
            const approachSize = circle.approachSize * (1 - progress) + circle.size;
            
            // Mode Hidden: les cercles disparaissent progressivement
            let opacity = 1;
            if (this.gameModes[this.currentGameMode] === 'Hidden') {
                opacity = Math.max(0, 1 - progress * 2); // Disparaît à mi-chemin
            }
            
            // Cercle d'approche
            this.ctx.beginPath();
            this.ctx.arc(
                circle.x + circle.size / 2,
                circle.y + circle.size / 2,
                approachSize / 2,
                0,
                Math.PI * 2
            );
            this.ctx.strokeStyle = `rgba(255, 255, 255, ${opacity * 0.5})`;
            this.ctx.lineWidth = 2;
            this.ctx.stroke();
            
            // Cercle principal
            this.ctx.beginPath();
            this.ctx.arc(
                circle.x + circle.size / 2,
                circle.y + circle.size / 2,
                circle.size / 2,
                0,
                Math.PI * 2
            );
            this.ctx.fillStyle = circle.color || '#ff66aa';
            this.ctx.globalAlpha = opacity;
            this.ctx.fill();
            this.ctx.strokeStyle = 'white';
            this.ctx.lineWidth = 3;
            this.ctx.stroke();
            this.ctx.globalAlpha = 1;
            
            // Dessiner des éléments spécifiques selon le type de cercle
            if (circle.isSpinner) {
                // Dessiner l'indicateur de spin
                this.ctx.font = 'bold 18px Arial';
                this.ctx.fillStyle = 'white';
                this.ctx.textAlign = 'center';
                this.ctx.textBaseline = 'middle';
                this.ctx.fillText(
                    `${circle.spinCount}/${circle.spinRequired}`, 
                    circle.x + circle.size / 2, 
                    circle.y + circle.size / 2
                );
                
                // Dessiner un cercle de progression
                const spinProgress = circle.spinCount / circle.spinRequired;
                this.ctx.beginPath();
                this.ctx.arc(
                    circle.x + circle.size / 2,
                    circle.y + circle.size / 2,
                    circle.size * 0.8,
                    -Math.PI / 2,
                    -Math.PI / 2 + Math.PI * 2 * spinProgress
                );
                this.ctx.strokeStyle = '#ffcc00';
                this.ctx.lineWidth = 5;
                this.ctx.stroke();
            }
        });
    }

    /**
     * Dessine les effets de feedback
     */
    drawFeedbacks() {
        this.hitFeedbacks.forEach(feedback => {
            this.ctx.save();
            this.ctx.globalAlpha = feedback.opacity;
            
            let color;
            let text = feedback.type.toUpperCase();
            
            switch (feedback.type) {
                case 'perfect':
                    color = '#66ccff';
                    break;
                case 'good':
                    color = '#66ff66';
                    break;
                case 'ok':
                    color = '#ffcc00';
                    break;
                case 'powerup':
                    color = '#ff66aa';
                    text = 'POWER UP!';
                    break;
                case 'levelUp':
                    color = '#ffcc00';
                    text = `NIVEAU ${this.level}!`;
                    break;
                default:
                    color = 'white';
            }
            
            this.ctx.font = `bold ${20 * feedback.scale}px Arial`;
            this.ctx.fillStyle = color;
            this.ctx.textAlign = 'center';
            this.ctx.textBaseline = 'middle';
            this.ctx.fillText(text, feedback.x, feedback.y);
            this.ctx.restore();
        });
    }

    /**
     * Dessine les powerups actifs
     */
    drawActivePowerups() {
        if (this.activePowerups.length > 0) {
            let y = 30;
            this.ctx.font = '16px Arial';
            this.ctx.textAlign = 'left';
            this.ctx.textBaseline = 'middle';
            
            this.activePowerups.forEach(powerup => {
                const timeLeft = Math.ceil((powerup.activatedAt + powerup.duration - performance.now()) / 1000);
                let text;
                let color;
                
                switch (powerup.type) {
                    case 'scoreBoost':
                        text = `Score x2 (${timeLeft}s)`;
                        color = '#ffcc00';
                        break;
                    case 'slowTime':
                        text = `Ralenti (${timeLeft}s)`;
                        color = '#66ccff';
                        break;
                    case 'healthFreeze':
                        text = `Santé figée (${timeLeft}s)`;
                        color = '#ff66aa';
                        break;
                }
                
                this.ctx.fillStyle = color;
                this.ctx.fillText(text, 10, y);
                y += 25;
            });
        }
    }

    /**
     * Met à jour l'interface utilisateur
     */
    updateUI() {
        document.getElementById('score').textContent = Math.floor(this.score);
        document.getElementById('highScore').textContent = Math.floor(this.highScore);
        document.getElementById('combo').textContent = this.combo;
        document.getElementById('accuracy').textContent = this.accuracy;
        document.getElementById('healthBar').style.width = `${this.health}%`;
        
        const comboElement = document.getElementById('combo').parentElement;
        comboElement.classList.toggle('high', this.combo >= 10);
    }

    /**
     * Charge les meilleurs scores depuis l'API
     */
    async loadHighScores() {
        try {
            // Mettre à jour le lien vers les high-scores avec l'ID correct
            const scoresButton = document.querySelector('.scores-button');
            if (scoresButton && this.gameId) {
                scoresButton.href = `../../scores.php?game_id=${this.gameId}`;
            }
            
            // Charger les meilleurs scores pour ce jeu
            if (this.gameId) {
                const scoresResponse = await fetch(`../../api/get_high_scores.php?game_id=${this.gameId}`);
                const scoresData = await scoresResponse.json();
                
                if (scoresData.success && scoresData.scores.length > 0) {
                    this.highScore = scoresData.scores[0].score;
                    document.getElementById('highScore').textContent = this.highScore;
                } else {
                    this.highScore = 0;
                    document.getElementById('highScore').textContent = this.highScore;
                }
            }
        } catch (error) {
            console.error('Erreur lors du chargement des high scores:', error);
        }
    }

    /**
     * Termine le jeu et affiche l'écran de fin
     */
    endGame() {
        this.gameOver = true;
        
        // Mettre à jour l'affichage du score final
        document.getElementById('finalScore').textContent = Math.floor(this.score);
        document.getElementById('finalAccuracy').textContent = this.accuracy;
        document.getElementById('maxCombo').textContent = this.maxCombo;
        
        // Afficher l'écran de fin de jeu
        document.getElementById('gameOver').classList.remove('hidden');
        
        // Sauvegarder le score si c'est un nouveau record
        if (this.score > this.highScore) {
            this.highScore = this.score;
            this.saveScore();
        }
    }

    /**
     * Sauvegarde le score dans la base de données
     */
    async saveScore() {
        if (!this.gameId) return;
        
        try {
            const response = await fetch('../../api/save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    game_id: parseInt(this.gameId),
                    score: Math.floor(this.score)
                })
            });
            
            const data = await response.json();
            console.log('Score sauvegardé:', data);
        } catch (error) {
            console.error('Erreur lors de la sauvegarde du score:', error);
        }
    }

    /**
     * Redémarre le jeu
     */
    restart() {
        document.getElementById('gameOver').classList.add('hidden');
        this.startCountdown();
    }
    
    /**
     * Augmente le niveau du joueur
     */
    levelUp() {
        if (this.level < this.maxLevel) {
            this.level++;
            document.getElementById('level').textContent = this.level;
            
            // Effets visuels pour le level up
            this.addHitFeedback(this.canvas.width / 2, this.canvas.height / 2, 'levelUp');
            
            // Augmenter légèrement la difficulté
            this.difficulty = Math.min(this.maxDifficulty, this.difficulty + 0.2);
            
            // Réduire l'intervalle entre les cercles
            this.circleInterval = Math.max(this.minCircleInterval, this.circleInterval - 100);
            
            // Bonus de santé pour le level up
            this.health = Math.min(100, this.health + 20);
        }
    }
    
    /**
     * Génère un nouveau powerup
     */
    generatePowerup(now) {
        // Générer des powerups aléatoirement
        if (now - this.lastPowerupTime > this.powerupInterval) {
            const x = Math.random() * (this.canvas.width - 40);
            const y = Math.random() * (this.canvas.height - 40);
            
            // Types de powerups
            const powerupTypes = [
                { name: 'healthBoost', duration: 0, color: '#66ff66', effect: 'Santé +30' },
                { name: 'scoreBoost', duration: 10000, color: '#ffcc00', effect: 'Score x2' },
                { name: 'slowTime', duration: 5000, color: '#66ccff', effect: 'Ralentir' },
                { name: 'healthFreeze', duration: 8000, color: '#ff66aa', effect: 'Santé figée' }
            ];
            
            const powerupType = powerupTypes[Math.floor(Math.random() * powerupTypes.length)];
            
            this.powerups.push({
                x: x,
                y: y,
                size: 40,
                type: powerupType.name,
                color: powerupType.color,
                effect: powerupType.effect,
                duration: powerupType.duration,
                createdAt: now,
                collected: false
            });
            
            this.lastPowerupTime = now;
        }
    }
    
    /**
     * Met à jour les powerups
     */
    updatePowerups(now) {
        // Mettre à jour les powerups et vérifier les collisions
        for (let i = 0; i < this.powerups.length; i++) {
            const powerup = this.powerups[i];
            
            // Supprimer les powerups après 5 secondes s'ils ne sont pas collectés
            if (now - powerup.createdAt > 5000 && !powerup.collected) {
                this.powerups.splice(i, 1);
                i--;
                continue;
            }
            
            // Vérifier si le joueur a cliqué sur un powerup
            if (powerup.collected) {
                this.powerups.splice(i, 1);
                i--;
            }
        }
    }
    
    /**
     * Met à jour les powerups actifs
     */
    updateActivePowerups(now) {
        // Mettre à jour les powerups actifs et supprimer ceux qui ont expiré
        for (let i = 0; i < this.activePowerups.length; i++) {
            const powerup = this.activePowerups[i];
            
            if (now - powerup.activatedAt > powerup.duration) {
                this.activePowerups.splice(i, 1);
                i--;
            }
        }
    }
    
    /**
     * Vérifie si un powerup spécifique est actif
     */
    hasPowerup(type) {
        return this.activePowerups.some(p => p.type === type);
    }
    
    /**
     * Collecte un powerup si le joueur a cliqué dessus
     */
    collectPowerup(x, y) {
        for (let i = 0; i < this.powerups.length; i++) {
            const powerup = this.powerups[i];
            const dx = x - (powerup.x + powerup.size / 2);
            const dy = y - (powerup.y + powerup.size / 2);
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance <= powerup.size / 2) {
                powerup.collected = true;
                
                // Appliquer l'effet du powerup
                switch (powerup.type) {
                    case 'healthBoost':
                        this.health = Math.min(100, this.health + 30);
                        break;
                    case 'scoreBoost':
                    case 'slowTime':
                    case 'healthFreeze':
                        this.activePowerups.push({
                            type: powerup.type,
                            duration: powerup.duration,
                            activatedAt: performance.now()
                        });
                        break;
                }
                
                // Afficher un effet visuel
                this.addHitFeedback(powerup.x + powerup.size / 2, powerup.y + powerup.size / 2, 'powerup');
                
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Initialisation du jeu au chargement de la page
 */
window.addEventListener('load', () => {
    new OsuGame();
});