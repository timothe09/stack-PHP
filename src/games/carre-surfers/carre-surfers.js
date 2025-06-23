class CarreSurfers {
    constructor() {
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.setupCanvas();
        window.addEventListener('resize', () => this.setupCanvas());

        // Système de particules
        this.particles = [];
        this.playerTrail = [];

        // Configuration du jeu
        this.gameSpeed = 5;
        this.score = 0;
        this.highScore = localStorage.getItem('highScore') || 0;
        this.gameOver = false;
        
        // Système de vies
        this.lives = 3;
        
        // Système de combo
        this.combo = 1;
        this.maxCombo = 1;
        this.comboTimeout = null;
        this.comboResetTime = 2000;
        
        // Système de rage
        this.rage = 0;
        this.maxRage = 100;
        this.rageDecayRate = 0.5;
        this.isRageActive = false;
        this.rageSpeedMultiplier = 1.5;

        // Configuration du joueur
        this.player = {
            size: 40,
            x: 0,
            y: 0,
            speed: 8,
            dy: 0,
            jumpForce: -15,
            gravity: 0.8,
            onGround: false,
            color: '#e74c3c',
            glowColor: 'rgba(231, 76, 60, 0.5)',
            jumpsLeft: 2,
            maxJumps: 2
        };

        // Power-ups
        this.powerUps = {
            invincible: false,
            speed: false,
            multiplier: 1
        };

        this.powerUpTimeouts = {};

        // Collectibles et obstacles
        this.collectibles = [];
        this.obstacles = [];
        this.lastCollectibleTime = performance.now();
        this.lastObstacleTime = performance.now();
        this.collectibleInterval = 1500;
        this.obstacleInterval = 2000;
        this.minObstacleInterval = 400;

        // Contrôles
        this.keys = {
            left: false,
            right: false,
            up: false
        };

        this.setupEventListeners();
        this.resetPlayerPosition();
        this.updateUI();
        this.gameLoop();
    }

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

    resetPlayerPosition() {
        // Positionner le joueur au centre en bas
        this.player.x = this.canvas.width / 2 - this.player.size / 2;
        this.player.y = this.canvas.height - 100;
    }

    updateUI() {
        document.getElementById('score').textContent = this.score;
        document.getElementById('highScore').textContent = this.highScore;
        document.getElementById('lives').textContent = this.lives;
        document.getElementById('combo').textContent = this.combo;
        document.getElementById('jumpsLeft').textContent = this.player.jumpsLeft;
        document.getElementById('rageMeter').style.width = `${this.rage}%`;
        
        const comboElement = document.getElementById('combo').parentElement;
        comboElement.classList.toggle('high', this.combo >= 5);
        
        document.getElementById('rageMeter').classList.toggle('active', this.isRageActive);
    }

    setupEventListeners() {
        window.addEventListener('keydown', (e) => {
            switch(e.key.toLowerCase()) {
                case 'arrowleft':
                case 'q':
                    this.keys.left = true;
                    break;
                case 'arrowright':
                case 'd':
                    this.keys.right = true;
                    break;
                case 'arrowup':
                case 'z':
                case ' ':
                    if (this.player.jumpsLeft > 0) {
                        this.keys.up = true;
                    }
                    break;
            }
        });

        window.addEventListener('keyup', (e) => {
            switch(e.key.toLowerCase()) {
                case 'arrowleft':
                case 'q':
                    this.keys.left = false;
                    break;
                case 'arrowright':
                case 'd':
                    this.keys.right = false;
                    break;
                case 'arrowup':
                case 'z':
                case ' ':
                    this.keys.up = false;
                    break;
            }
        });

        document.getElementById('restartBtn').addEventListener('click', () => this.restart());
    }

    increaseCombo() {
        if (this.comboTimeout) clearTimeout(this.comboTimeout);
        
        this.combo++;
        this.maxCombo = Math.max(this.maxCombo, this.combo);
        
        this.rage = Math.min(this.maxRage, this.rage + 10);
        if (this.rage >= this.maxRage && !this.isRageActive) {
            this.activateRageMode();
        }
        
        this.comboTimeout = setTimeout(() => {
            this.combo = 1;
            this.updateUI();
        }, this.comboResetTime);
        
        this.updateUI();
    }

    activateRageMode() {
        this.isRageActive = true;
        this.player.color = '#ff4444';
        this.player.glowColor = 'rgba(255, 68, 68, 0.7)';
        this.player.speed *= this.rageSpeedMultiplier;
        
        setTimeout(() => {
            this.isRageActive = false;
            this.rage = 0;
            this.player.color = '#e74c3c';
            this.player.glowColor = 'rgba(231, 76, 60, 0.5)';
            this.player.speed /= this.rageSpeedMultiplier;
            this.updateUI();
        }, 5000);
    }

    update() {
        if (this.gameOver) return;

        // Mise à jour des particules
        this.particles = this.particles.filter(particle => {
            particle.life -= 1;
            particle.x += particle.dx;
            particle.y += particle.dy;
            particle.size *= 0.95;
            return particle.life > 0 && particle.size > 0.5;
        });

        // Mise à jour de la traînée du joueur
        if (this.isRageActive || Math.abs(this.player.dy) > 2) {
            this.playerTrail.push({
                x: this.player.x + this.player.size/2,
                y: this.player.y + this.player.size/2,
                size: this.player.size * 0.8,
                life: 10,
                color: this.isRageActive ? 'rgba(255, 68, 68, 0.2)' : 'rgba(231, 76, 60, 0.2)'
            });
        }
        this.playerTrail = this.playerTrail.filter(particle => {
            particle.life -= 1;
            particle.size *= 0.9;
            return particle.life > 0;
        });

        if (!this.isRageActive) {
            this.rage = Math.max(0, this.rage - this.rageDecayRate);
            this.updateUI();
        }

        if (this.keys.left) this.player.x -= this.player.speed * (this.isRageActive ? this.rageSpeedMultiplier : 1);
        if (this.keys.right) this.player.x += this.player.speed * (this.isRageActive ? this.rageSpeedMultiplier : 1);
        if (this.keys.up && this.player.jumpsLeft > 0) {
            this.player.dy = this.player.jumpForce;
            this.player.jumpsLeft--;
            this.player.onGround = false;
            this.keys.up = false;
            this.updateUI();
        }

        this.player.dy += this.player.gravity;
        this.player.y += this.player.dy;

        if (this.player.y + this.player.size > this.canvas.height - 20) {
            this.player.y = this.canvas.height - 20 - this.player.size;
            this.player.dy = 0;
            this.player.onGround = true;
            this.player.jumpsLeft = this.player.maxJumps;
            this.updateUI();
        }

        this.player.x = Math.max(0, Math.min(this.canvas.width - this.player.size, this.player.x));

        this.generateCollectible();
        this.collectibles = this.collectibles.filter(collectible => {
            collectible.y += collectible.speed * (this.isRageActive ? this.rageSpeedMultiplier : 1);
            
            if (this.checkCollisionWithCollectible(collectible)) {
                this.score += collectible.value * this.combo;
                this.increaseCombo();
                if (collectible.effect) collectible.effect();
                
                // Créer des particules pour la collecte
                for (let i = 0; i < 8; i++) {
                    const angle = (Math.PI * 2 * i) / 8;
                    this.particles.push({
                        x: collectible.x + collectible.size/2,
                        y: collectible.y + collectible.size/2,
                        dx: Math.cos(angle) * 3,
                        dy: Math.sin(angle) * 3,
                        size: 5,
                        color: collectible.color,
                        life: 20
                    });
                }
                return false;
            }

            return collectible.y <= this.canvas.height;
        });

        this.generateObstacle();
        this.obstacles = this.obstacles.filter(obstacle => {
            obstacle.y += obstacle.speed * (this.isRageActive ? this.rageSpeedMultiplier : 1);
            
            if (!this.powerUps.invincible && this.checkCollision(obstacle)) {
                this.lives--;
                this.updateUI();
                
                // Créer des particules pour la collision
                for (let i = 0; i < 12; i++) {
                    const angle = (Math.PI * 2 * i) / 12;
                    this.particles.push({
                        x: this.player.x + this.player.size/2,
                        y: this.player.y + this.player.size/2,
                        dx: Math.cos(angle) * 5,
                        dy: Math.sin(angle) * 5,
                        size: 8,
                        color: '#e74c3c',
                        life: 25
                    });
                }
                
                if (this.lives <= 0) {
                    this.endGame();
                } else {
                    this.powerUps.invincible = true;
                    setTimeout(() => {
                        this.powerUps.invincible = false;
                    }, 2000);
                }
                return false;
            }

            return obstacle.y <= this.canvas.height;
        });

        this.gameSpeed = 5 + Math.floor(this.score / 100);
    }

    draw() {
        // Effacer le canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Fond avec dégradé
        const gradient = this.ctx.createLinearGradient(0, 0, 0, this.canvas.height);
        gradient.addColorStop(0, '#2c3e50');
        gradient.addColorStop(1, '#3498db');
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        // Sol avec dégradé
        const groundGradient = this.ctx.createLinearGradient(0, this.canvas.height - 20, 0, this.canvas.height);
        groundGradient.addColorStop(0, '#7f8c8d');
        groundGradient.addColorStop(1, '#95a5a6');
        this.ctx.fillStyle = groundGradient;
        this.ctx.fillRect(0, this.canvas.height - 20, this.canvas.width, 20);

        // Dessiner la traînée du joueur
        this.playerTrail.forEach(particle => {
            this.ctx.save();
            this.ctx.globalAlpha = particle.life / 10;
            this.ctx.shadowBlur = 10;
            this.ctx.shadowColor = particle.color;
            this.ctx.fillStyle = particle.color;
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size/2, 0, Math.PI * 2);
            this.ctx.fill();
            this.ctx.restore();
        });

        // Dessiner les obstacles avec effet de brillance
        this.obstacles.forEach(obstacle => {
            this.ctx.save();
            this.ctx.shadowBlur = 10;
            this.ctx.shadowColor = 'rgba(46, 204, 113, 0.5)';
            this.ctx.fillStyle = '#2ecc71';
            this.ctx.fillRect(obstacle.x, obstacle.y, obstacle.width, obstacle.height);
            this.ctx.restore();
        });

        // Dessiner les collectibles avec effet de brillance
        this.collectibles.forEach(collectible => {
            this.ctx.save();
            this.ctx.shadowBlur = 15;
            this.ctx.shadowColor = collectible.glowColor;
            this.ctx.fillStyle = collectible.color;
            this.ctx.beginPath();
            this.ctx.arc(
                collectible.x + collectible.size/2,
                collectible.y + collectible.size/2,
                collectible.size/2,
                0,
                Math.PI * 2
            );
            this.ctx.fill();
            this.ctx.restore();
        });

        // Dessiner les particules
        this.particles.forEach(particle => {
            this.ctx.save();
            this.ctx.globalAlpha = particle.life / 25;
            this.ctx.shadowBlur = 5;
            this.ctx.shadowColor = particle.color;
            this.ctx.fillStyle = particle.color;
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fill();
            this.ctx.restore();
        });

        // Dessiner le joueur avec effet de brillance
        this.ctx.save();
        this.ctx.shadowBlur = 20;
        this.ctx.shadowColor = this.player.glowColor;
        this.ctx.fillStyle = this.powerUps.invincible ? 'rgba(231, 76, 60, 0.5)' : this.player.color;
        this.ctx.fillRect(this.player.x, this.player.y, this.player.size, this.player.size);
        this.ctx.restore();
    }

    generateCollectible() {
        const now = performance.now();
        if (now - this.lastCollectibleTime > this.collectibleInterval) {
            const laneWidth = this.canvas.width / 3;
            const lane = Math.floor(Math.random() * 3);
            const x = lane * laneWidth + laneWidth / 2 - 10;

            this.collectibles.push({
                x: x,
                y: -20,
                size: 20,
                speed: this.gameSpeed * 0.7,
                color: '#f1c40f',
                glowColor: 'rgba(241, 196, 15, 0.5)',
                value: 10
            });

            this.lastCollectibleTime = now;
            this.collectibleInterval = Math.random() * 1000 + 1500;
        }
    }

    generateObstacle() {
        const now = performance.now();
        if (now - this.lastObstacleTime > this.obstacleInterval) {
            const laneWidth = this.canvas.width / 3;
            const lane = Math.floor(Math.random() * 3);
            const x = lane * laneWidth + laneWidth / 2 - 25;

            this.obstacles.push({
                x: x,
                y: -50,
                width: 50,
                height: 50,
                speed: this.gameSpeed
            });

            this.lastObstacleTime = now;
            this.obstacleInterval = Math.max(
                this.minObstacleInterval,
                2000 - (this.score * 4)
            );
        }
    }

    checkCollision(obstacle) {
        return this.player.x < obstacle.x + obstacle.width &&
               this.player.x + this.player.size > obstacle.x &&
               this.player.y < obstacle.y + obstacle.height &&
               this.player.y + this.player.size > obstacle.y;
    }

    checkCollisionWithCollectible(collectible) {
        const distance = Math.sqrt(
            Math.pow((this.player.x + this.player.size/2) - (collectible.x + collectible.size/2), 2) +
            Math.pow((this.player.y + this.player.size/2) - (collectible.y + collectible.size/2), 2)
        );
        return distance < (this.player.size/2 + collectible.size/2);
    }

    endGame() {
        this.gameOver = true;
        if (this.score > this.highScore) {
            this.highScore = this.score;
            localStorage.setItem('highScore', this.highScore);
        }
        document.getElementById('finalScore').textContent = this.score;
        document.getElementById('maxCombo').textContent = this.maxCombo;
        document.getElementById('gameOver').classList.remove('hidden');
    }

    restart() {
        this.score = 0;
        this.lives = 3;
        this.combo = 1;
        this.maxCombo = 1;
        this.rage = 0;
        this.isRageActive = false;
        this.gameSpeed = 5;
        this.obstacles = [];
        this.collectibles = [];
        this.particles = [];
        this.playerTrail = [];
        
        this.resetPlayerPosition();
        this.player.dy = 0;
        this.player.jumpsLeft = this.player.maxJumps;
        this.player.color = '#e74c3c';
        this.player.glowColor = 'rgba(231, 76, 60, 0.5)';
        
        this.powerUps = { invincible: false, speed: false, multiplier: 1 };
        this.gameOver = false;
        
        document.getElementById('gameOver').classList.add('hidden');
        
        if (this.comboTimeout) {
            clearTimeout(this.comboTimeout);
        }
        
        Object.values(this.powerUpTimeouts).forEach(timeout => {
            if (timeout) clearTimeout(timeout);
        });
        
        this.updateUI();
    }

    gameLoop() {
        this.update();
        this.draw();
        requestAnimationFrame(() => this.gameLoop());
    }
}

window.addEventListener('load', () => {
    new CarreSurfers();
});