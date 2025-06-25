// Configurations statiques
const GAME_CONFIG = {
    INITIAL_MONEY: 100,
    INITIAL_LIVES: 10,
    GRID_WIDTH: 15,
    GRID_HEIGHT: 8,
    CELL_SIZE: 50,
    INITIAL_ENEMIES: 5,
    INITIAL_SPAWN_INTERVAL: 3500
};

const TOWER_CONFIGS = {
    'basic': {
        range: 2,
        damage: 10,
        cooldown: 500,
        color: '#44ff44',
        upgradeCost: 75,
        level: 1
    },
    'laser': {
        range: 2,
        damage: 1,
        cooldown: 5,
        color: '#8844ff',
        upgradeCost: 100,
        level: 1
    },
    'sniper': {
        range: 4,
        damage: 50,
        cooldown: 4000,
        color: '#ff4444',
        upgradeCost: 150,
        level: 1
    },
    'splash': {
        range: 2,
        damage: 15,
        splashRadius: 1,
        cooldown: 1500,
        color: '#ffff44',
        upgradeCost: 200,
        level: 1
    },
    'frost': {
        range: 2,
        damage: 5,
        slowEffect: 0.5,
        slowDuration: 3000,
        cooldown: 1000,
        color: '#44ffff',
        upgradeCost: 250,
        level: 1
    }
};

const ENEMY_TYPES = {
    'normal': {
        health: 80,
        speed: 0.4,
        color: '#ff4444',
        healthScale: 20,
        speedScale: 0.03,
        size: 40
    },
    'fast': {
        health: 45,
        speed: 0.85,
        color: '#ffff44',
        healthScale: 10,
        speedScale: 0.05,
        size: 35
    },
    'armored': {
        health: 200,
        speed: 0.3,
        color: '#888888',
        healthScale: 30,
        speedScale: 0.02,
        size: 45
    },
    'boss': {
        health: 1000,
        speed: 0.25,
        color: '#ff0000',
        healthScale: 150,
        speedScale: 0.015,
        size: 70
    },
    'elite': {
        health: 180,
        speed: 0.4,
        color: '#ff00ff',
        healthScale: 40,
        speedScale: 0.025,
        size: 50
    }
};

class TowerDefense {
    constructor() {
        this.grid = [];
        this.towers = [];
        this.enemies = [];
        this.projectiles = [];
        this.money = GAME_CONFIG.INITIAL_MONEY;
        this.score = 0;
        this.lives = GAME_CONFIG.INITIAL_LIVES;
        this.level = 1;
        this.highScore = 0;
        this.gameId = null;
        this.gameOver = false;
        
        this.initializeGame();
    }

    async initializeGame() {
        try {
            // Récupérer l'ID du jeu Tower Defense
                this.gameId = document.getElementById('game_id') ? document.getElementById('game_id').value : null;
                
                // Mettre à jour le lien vers les high-scores avec l'ID correct
                const scoresButton = document.querySelector('.scores-button');
                if (scoresButton) {
                    scoresButton.href = `../../scores.php?game_id=${this.gameId}`;
                }
                
                // Charger les meilleurs scores pour ce jeu
                const scoresResponse = await fetch(`../../api/get_high_scores.php?game_id=${this.gameId}`);
                const scoresData = await scoresResponse.json();
                
                if (scoresData.success && scoresData.scores.length > 0) {
                    this.highScore = scoresData.scores[0].score;
                    document.getElementById('highScore').textContent = this.highScore;
                }else {
                    this.highScore =0 ;
                    document.getElementById('highScore').textContent = this.highScore;
            }}
        catch (error) {
            console.error('Erreur lors de l\'initialisation:', error);
        }
        
        this.gameGrid = document.getElementById('gameGrid');
        this.selectedTower = null;
        this.enemiesInWave = 5; // Nombre initial d'ennemis par vague
        this.spawnInterval = 3500; // Intervalle initial plus long entre les ennemis
        
        // Générer le chemin une seule fois au début
        this.path = this.generateRandomPath();
        console.log("Nouveau chemin généré pour la partie");
        
        this.initializeGrid();
        this.setupEventListeners();
        this.gameLoop();
        this.startWave();
    }

    initializeGrid() {
        for (let y = 0; y < GAME_CONFIG.GRID_HEIGHT; y++) {
            this.grid[y] = [];
            for (let x = 0; x < GAME_CONFIG.GRID_WIDTH; x++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell';
                
                // Vérifier si la cellule fait partie du chemin
                if (this.path.some(p => p.x === x && p.y === y)) {
                    cell.classList.add('path');
                } else {
                    cell.classList.add('tower-placement');
                    cell.dataset.x = x;
                    cell.dataset.y = y;
                }
                
                this.gameGrid.appendChild(cell);
                this.grid[y][x] = cell;
            }
        }
    }

    setupEventListeners() {
        // Cacher le bouton d'amélioration quand on clique ailleurs

        // Gestion des clics sur les types de tours
        document.querySelectorAll('.tower').forEach(tower => {
            tower.addEventListener('click', () => {
                this.selectedTower = {
                    type: tower.dataset.type,
                    cost: parseInt(tower.dataset.cost)
                };
            });
        });

        // Gestion des clics sur les cellules de la grille
        const handleCellClick = (e) => {
            const cell = e.currentTarget;
            
            // Si on a une tour sélectionnée et assez d'argent, on place la tour
            if (this.selectedTower && this.money >= this.selectedTower.cost) {
                const x = parseInt(cell.dataset.x);
                const y = parseInt(cell.dataset.y);
                if (!this.towers.some(t => t.x === x && t.y === y)) {
                    this.placeTower(x, y);
                }
                return;
            }

            // Si la cellule contient une tour existante
            if (cell.classList.contains('has-tower')) {
                e.stopPropagation();

                e.stopPropagation();

                // Trouver la tour correspondante
                const x = parseInt(cell.dataset.x);
                const y = parseInt(cell.dataset.y);
                const tower = this.towers.find(t => t.x === x && t.y === y);
                
                if (tower) {
                    const currentCell = this.grid[tower.y][tower.x];
                    const upgradeButton = currentCell.querySelector('.upgrade-button');
                    const sellButton = currentCell.querySelector('.sell-button');
                    const levelDisplay = currentCell.querySelector('.tower-level');

                    // Cacher d'abord tous les autres boutons
                    document.querySelectorAll('.upgrade-button, .sell-button, .tower-level').forEach(el => {
                        if (el !== upgradeButton && el !== sellButton && el !== levelDisplay) {
                            el.style.display = 'none';
                        }
                    });

                    // S'assurer que les boutons sont toujours visibles
                    upgradeButton.style.display = 'block';
                    if (sellButton) sellButton.style.display = 'block';
                    levelDisplay.style.display = 'block';
                }
            }
        };

        document.querySelectorAll('.tower-placement').forEach(cell => {
            cell.addEventListener('click', handleCellClick);
        });
    }

    placeTower(x, y) {
        if (!this.towers.some(t => t.x === x && t.y === y)) {
            this.money -= this.selectedTower.cost;
            document.getElementById('money').textContent = this.money;
            
            const towerConfigs = {
                'basic': {
                    range: 2,
                    damage: 10,
                    cooldown: 500,
                    color: '#44ff44',
                    upgradeCost: 75,
                    level: 1
                },
                'laser': {
                    range: 2,
                    damage: 1,
                    cooldown: 5,
                    color: '#8844ff',
                    upgradeCost: 100,
                    level: 1
                },
                'sniper': {
                    range: 4,
                    damage: 50,
                    cooldown: 7000,
                    color: '#ff4444',
                    upgradeCost: 150,
                    level: 1
                },
                'splash': {
                    range: 2,
                    damage: 15,
                    splashRadius: 1,
                    cooldown: 1500,
                    color: '#ffff44',
                    upgradeCost: 200,
                    level: 1
                },
                'frost': {
                    range: 2,
                    damage: 5,
                    slowEffect: 0.5,
                    slowDuration: 3000,
                    cooldown: 1000,
                    color: '#44ffff',
                    upgradeCost: 250,
                    level: 1
                }
            };

            const config = towerConfigs[this.selectedTower.type];
            const tower = {
                x: x,
                y: y,
                type: this.selectedTower.type,
                lastShot: 0,
                totalInvested: this.selectedTower.cost, // Montant total investi
                ...config
            };
            
            this.towers.push(tower);
            const cell = this.grid[y][x];
            cell.style.backgroundColor = tower.color;
            cell.classList.add('has-tower');
            
            // Ajouter le niveau de la tour
            const levelDisplay = document.createElement('div');
            levelDisplay.className = 'tower-level';
            levelDisplay.textContent = '1';
            levelDisplay.style.display = 'block';
            this.grid[y][x].appendChild(levelDisplay);
            
            // Ajouter le bouton d'amélioration
            const upgradeButton = document.createElement('div');
            upgradeButton.className = 'upgrade-button';
            upgradeButton.textContent = `⬆️ ${tower.upgradeCost}€`;
            upgradeButton.style.display = 'block';
            
            upgradeButton.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                if (this.money >= tower.upgradeCost && tower.level < 3) {
                    this.upgradeTower(tower);
                } else if (tower.level >= 3) {
                    alert('Cette tour est déjà au niveau maximum !');
                } else {
                    alert('Pas assez d\'argent pour améliorer cette tour !');
                }
            });
            cell.appendChild(upgradeButton);
            
            // Ajouter le bouton de vente
            const sellButton = document.createElement('div');
            sellButton.className = 'sell-button';
            sellButton.textContent = `💰 ${Math.floor(tower.totalInvested * 0.75)}€`;
            sellButton.style.display = 'block';
            
            sellButton.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                if (confirm(`Vendre cette tourelle pour ${Math.floor(tower.totalInvested * 0.75)}€ ?`)) {
                    this.sellTower(tower);
                }
            });
            cell.appendChild(sellButton);
        }
    }

    startWave() {
        let enemyCount = 0;
        const spawnInterval = setInterval(() => {
            if (enemyCount < this.enemiesInWave) {
                this.spawnEnemy();
                enemyCount++;
            } else {
                clearInterval(spawnInterval);
                // Préparer le prochain niveau
                setTimeout(() => this.nextLevel(), 10000);
            }
        }, this.spawnInterval);
    }

    nextLevel() {
        this.level++;
        document.getElementById('level').textContent = this.level;
        
        // Ajuster la difficulté selon le niveau
        if (this.level % 5 === 0) { // Niveau boss
            this.enemiesInWave = 1; // Un seul boss
            this.spawnInterval = 1000; // Apparition rapide
            this.money += 100 + (this.level * 20); // Plus d'argent pour les niveaux boss
            
            // Afficher un message d'avertissement
            const warning = document.createElement('div');
            warning.textContent = `⚠️ BOSS - NIVEAU ${this.level} ⚠️`;
            warning.className = 'level-warning';
            this.gameGrid.appendChild(warning);
            setTimeout(() => warning.remove(), 3000);
            
        } else if (this.level % 3 === 0) { // Niveau élite
            this.enemiesInWave = Math.min(3 + (this.level * 2), 20);
            this.spawnInterval = Math.max(3000 - (this.level * 100), 1500);
            this.money += 100 + (this.level * 15);
            
        } else { // Niveau normal
            this.enemiesInWave = Math.min(5 + (this.level * 3), 30);
            this.spawnInterval = Math.max(3500 - (this.level * 150), 2000);
            this.money += 50 + (this.level * 10);
        }
        
        document.getElementById('money').textContent = this.money;
        this.startWave();
    }


    spawnEnemy() {
        // Sélection du type d'ennemi selon le niveau
        let type = this.determineEnemyType();
        const config = ENEMY_TYPES[type];
        const maxHealth = config.health + (this.level * config.healthScale);
        const speed = (config.speed + (this.level * config.speedScale)) * (1 + this.level * 0.05);

        // Créer les éléments DOM pour l'ennemi
        const element = document.createElement('div');
        const healthBar = document.createElement('div');
        const healthFill = document.createElement('div');
        
        // Configuration de l'ennemi
        const enemy = {
            x: this.path[0].x * GAME_CONFIG.CELL_SIZE + 50,
            y: this.path[0].y * GAME_CONFIG.CELL_SIZE + 50,
            type,
            health: maxHealth,
            maxHealth,
            pathIndex: 0,
            speed,
            slowed: false,
            slowTimer: 0,
            hasCrossedEdge: false,
            element,
            healthBar: healthFill
        };

        // Style de l'ennemi
        element.className = 'enemy';
        element.style.width = config.size + 'px';
        element.style.height = config.size + 'px';
        element.style.backgroundColor = config.color;
        element.style.left = enemy.x + 'px';
        element.style.top = enemy.y + 'px';

        // Barre de vie
        healthBar.className = 'enemy-health-bar';
        healthFill.className = 'enemy-health';
        healthBar.appendChild(healthFill);
        element.appendChild(healthBar);
        
        // Ajouter au DOM
        this.gameGrid.appendChild(element);
        
        this.enemies.push(enemy);
    }

    determineEnemyType() {
        if (this.level % 5 === 0) return 'boss';
        if (this.level % 3 === 0) return Math.random() < 0.7 ? 'elite' : 'armored';
        if (this.level < 3) return 'normal';
        
        const rand = Math.random();
        if (rand < 0.3) return 'fast';
        if (rand < 0.6) return 'armored';
        return 'normal';
    }

    updateEnemies() {
        this.enemies = this.enemies.filter(enemy => {
            // Gestion de la mort de l'ennemi
            if (enemy.health <= 0) {
                // Effet d'explosion
                const explosion = document.createElement('div');
                explosion.className = 'explosion';
                explosion.style.position = 'absolute';
                explosion.style.left = enemy.x + 'px';
                explosion.style.top = enemy.y + 'px';
                this.gameGrid.appendChild(explosion);
                setTimeout(() => explosion.remove(), 500);

                // Mise à jour du score et de l'argent
                const bonusScore = 100 + (this.level * 10);
                const bonusGold = 20 + Math.floor(this.level * 0.5);
                this.score += bonusScore;
                this.money += bonusGold;

                // Afficher les bonus
                const bonusText = document.createElement('div');
                bonusText.className = 'combo-text';
                bonusText.textContent = `+${bonusScore}`;
                bonusText.style.left = enemy.x + 'px';
                bonusText.style.top = enemy.y + 'px';
                this.gameGrid.appendChild(bonusText);
                setTimeout(() => bonusText.remove(), 1000);

                // Mettre à jour l'interface
                document.getElementById('money').textContent = this.money;
                document.getElementById('score').textContent = this.score;

                // Supprimer l'ennemi
                enemy.element.remove();
                return false;
            }

            // Faire perdre une vie quand l'ennemi sort de la grille visible
            const rightEdge = GAME_CONFIG.GRID_WIDTH * GAME_CONFIG.CELL_SIZE; // Largeur de la grille en pixels
            if (enemy.x >= rightEdge && !enemy.hasCrossedEdge) {
                enemy.hasCrossedEdge = true;
                enemy.element.remove();
                this.lives--;
                document.getElementById('lives').textContent = this.lives;
                if (this.lives <= 0 && !this.gameOver) {
                    this.gameOver = true;
                    this.handleGameOver();
                }
                return false;
            }

            // Application d'un mouvement continu
            const speed = enemy.slowed ? enemy.speed * 0.7 : enemy.speed;
            
            // Mouvement continu
            const nextPoint = this.path[Math.min(enemy.pathIndex + 1, this.path.length - 1)];
            const targetX = nextPoint.x * 50 + 25;
            const targetY = nextPoint.y * 50 + 25;
            
            const dx = targetX - enemy.x;
            const dy = targetY - enemy.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            // Déplacement fluide
            enemy.x += (dx / dist) * speed;
            enemy.y += (dy / dist) * speed;
            
            // Faire perdre une vie quand on traverse la dernière case
            if (enemy.pathIndex === this.path.length - 2 && dist < 5) {
                this.lives--;
                document.getElementById('lives').textContent = this.lives;
                enemy.element.remove();
                if (this.lives <= 0 && !this.gameOver) {
                    this.gameOver = true;
                    this.handleGameOver();
                }
                return false;
            }
            
            // Passer au point suivant si on est assez proche
            if (dist < 5) {
                enemy.pathIndex = Math.min(enemy.pathIndex + 1, this.path.length - 1);
            }
            
            // Mettre à jour la barre de vie
            enemy.healthBar.style.width = `${(enemy.health / enemy.maxHealth) * 100}%`;
            
            // Toujours mettre à jour la position visuelle
            enemy.element.style.left = Math.round(enemy.x) + 'px';
            enemy.element.style.top = Math.round(enemy.y) + 'px';
            
            // Faire disparaître progressivement l'ennemi après la grille
            if (enemy.x > (GAME_CONFIG.GRID_WIDTH* GAME_CONFIG.CELL_SIZE)) {
                const opacity = Math.max(0, 1 - (enemy.x - 500) / 50);
                enemy.element.style.opacity = opacity;
            }
            
            return true;
        });
    }

    updateTowers() {
        const now = Date.now();
        this.towers.forEach(tower => {
            if (now - tower.lastShot >= tower.cooldown) {
                const target = this.findTarget(tower);
                if (target) {
                    this.shoot(tower, target);
                    tower.lastShot = now;
                }
            }
        });
    }

    // Mise en cache des positions des tours
    initTowerCache(tower) {
        if (!tower.cached) {
            tower.cached = {
                x: tower.x * GAME_CONFIG.CELL_SIZE + 25,
                y: tower.y * GAME_CONFIG.CELL_SIZE + 25,
                rangeSquared: Math.pow(tower.range * GAME_CONFIG.CELL_SIZE, 2)
            };
        }
        return tower.cached;
    }

    findTarget(tower) {
        const cached = this.initTowerCache(tower);
        let bestTarget = {
            enemy: null,
            progress: -1,
            distance: Infinity
        };

        // Utiliser for...of pour de meilleures performances
        for (const enemy of this.enemies) {
            const dx = cached.x - enemy.x;
            const dy = cached.y - enemy.y;
            const distanceSquared = dx * dx + dy * dy;

            if (distanceSquared <= cached.rangeSquared) {
                // Calcul optimisé du score de priorité
                const progress = enemy.pathIndex + (1 - distanceSquared / cached.rangeSquared);
                
                if (progress > bestTarget.progress) {
                    bestTarget = {
                        enemy,
                        progress,
                        distance: distanceSquared
                    };
                }
            }
        }

        return bestTarget.enemy;
    }

    showEnemyDeath(enemy) {
        // Effet d'explosion
        const explosion = document.createElement('div');
        explosion.className = 'explosion';
        explosion.style.position = 'absolute';
        explosion.style.left = enemy.x + 'px';
        explosion.style.top = enemy.y + 'px';
        this.gameGrid.appendChild(explosion);
        
        // Nettoyer l'explosion après l'animation
        setTimeout(() => explosion.remove(), 500);
        
        // Supprimer l'élément ennemi
        enemy.element.remove();
    }

    updateScoreAndMoney(enemy) {
        // Calculer les bonus
        const moneyBonus = 20 + Math.floor(this.level * 0.5);
        const scoreBonus = 100 + (this.level * 10);
        
        // Mettre à jour le score et l'argent
        this.money += moneyBonus;
        this.score += scoreBonus;
        
        // Mettre à jour l'affichage
        document.getElementById('money').textContent = this.money;
        document.getElementById('score').textContent = this.score;
        
        // Afficher le bonus
        const bonusText = document.createElement('div');
        bonusText.className = 'combo-text';
        bonusText.textContent = `+${scoreBonus}`;
        bonusText.style.cssText = `
            position: absolute;
            left: ${enemy.x}px;
            top: ${enemy.y}px;
        `;
        this.gameGrid.appendChild(bonusText);
        
        // Nettoyer le texte de bonus après l'animation
        setTimeout(() => bonusText.remove(), 1000);
    }

    shoot(tower, target) {
        const config = TOWER_CONFIGS[tower.type];
        const startX = tower.x * GAME_CONFIG.CELL_SIZE + 25;
        const startY = tower.y * GAME_CONFIG.CELL_SIZE + 25;

        // Créer l'élément du projectile
        const element = document.createElement('div');
        element.className = 'projectile';
        element.style.cssText = `
            position: absolute;
            left: ${startX}px;
            top: ${startY}px;
            width: 10px;
            height: 10px;
            background-color: ${config.color};
            border-radius: 50%;
        `;

        // Créer l'objet projectile avec les propriétés de mouvement
        const projectile = {
            x: startX,
            y: startY,
            targetX: target.x,
            targetY: target.y,
            speed: 5,
            damage: tower.damage,
            type: tower.type,
            tower: tower,
            element: element
        };

        this.gameGrid.appendChild(element);
        this.projectiles.push(projectile);
    }

    updateProjectiles() {
        for (let i = this.projectiles.length - 1; i >= 0; i--) {
            const projectile = this.projectiles[i];
            
            // Calculer le mouvement
            const dx = projectile.targetX - projectile.x;
            const dy = projectile.targetY - projectile.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 5) { // Collision avec la cible
                // Rechercher les ennemis dans la zone d'effet
                const hitbox = projectile.type === 'splash' ? 50 : 15;
                const hits = this.enemies.filter(e =>
                    Math.abs(e.x - projectile.targetX) < hitbox &&
                    Math.abs(e.y - projectile.targetY) < hitbox
                );
                
                // Appliquer les effets
                if (hits.length > 0) {
                    if (projectile.type === 'splash') {
                        hits.forEach(enemy => enemy.health -= projectile.damage);
                    } else if (projectile.type === 'frost') {
                        hits.forEach(enemy => {
                            enemy.slowed = true;
                            enemy.slowTimer = Date.now();
                        });
                        hits[0].health -= projectile.damage;
                    } else if (projectile.type === 'laser') {
                        // Tour laser : dégâts faibles mais très rapides
                        hits[0].health -= projectile.damage;
                    } else {
                        hits[0].health -= projectile.damage;
                    }
                }
                
                // Supprimer le projectile
                projectile.element.remove();
                this.projectiles.splice(i, 1);
            } else { // Continuer le mouvement
                projectile.x += (dx / distance) * projectile.speed;
                projectile.y += (dy / distance) * projectile.speed;
                projectile.element.style.left = projectile.x + 'px';
                projectile.element.style.top = projectile.y + 'px';
            }
        }
    }

    handleProjectileCollision(projectile) {
        const hitboxSize = projectile.type === 'splash' ? 50 : 15;
        
        // Recherche optimisée des ennemis touchés
        const hitEnemies = this.enemies.filter(e =>
            Math.abs(e.x - projectile.targetX) < hitboxSize &&
            Math.abs(e.y - projectile.targetY) < hitboxSize
        );

        if (hitEnemies.length > 0) {
            if (projectile.type === 'splash') {
                // Dégâts de zone optimisés
                const splashRange = 60;
                for (const enemy of this.enemies) {
                    const dx = enemy.x - projectile.targetX;
                    const dy = enemy.y - projectile.targetY;
                    if (dx * dx + dy * dy < splashRange * splashRange) {
                        enemy.health -= projectile.damage;
                    }
                }
            } else if (projectile.type === 'frost') {
                for (const enemy of hitEnemies) {
                    enemy.slowed = true;
                    enemy.slowTimer = Date.now();
                    enemy.slowAmount = projectile.tower.slowEffect;
                }
            } else {
                hitEnemies[0].health -= projectile.damage;
            }
        }
    }

    upgradeTower(tower) {
        console.log('Amélioration de la tour:', tower);  // Debug
        try {
            this.money -= tower.upgradeCost;
            tower.totalInvested += tower.upgradeCost; // Ajouter le coût d'amélioration
            document.getElementById('money').textContent = this.money;
            
            // Améliorer les stats de la tour (bonus réduits)
            tower.level++;
            tower.damage *= 1.25;  // 25% d'augmentation au lieu de 50%
            tower.range += 0.25;   // +0.25 au lieu de +0.5
            tower.cooldown *= 0.9; // 10% plus rapide au lieu de 20%
            
            if (tower.type === 'splash') {
                tower.splashRadius += 0.25; // +0.25 au lieu de +0.5
            } else if (tower.type === 'frost') {
                tower.slowEffect += 0.05;   // +5% au lieu de +10%
                tower.slowDuration += 500;  // +0.5s au lieu de +1s
            }

            // Mettre à jour le coût d'amélioration
            tower.upgradeCost *= 2;
            
            // Récupérer la cellule et mettre à jour l'affichage
            const cell = this.grid[tower.y][tower.x];
            
            // Mettre à jour le niveau affiché
            const levelDisplay = cell.querySelector('.tower-level');
            levelDisplay.textContent = tower.level;
            
            // Mettre à jour le bouton d'amélioration
            const upgradeButton = cell.querySelector('.upgrade-button');
            upgradeButton.textContent = tower.level < 3 ?
                `⬆️ ${tower.upgradeCost}€` : '⭐';
            if (tower.level >= 3) {
                upgradeButton.style.backgroundColor = '#666';
                upgradeButton.style.cursor = 'not-allowed';
                upgradeButton.style.color = '#fff';
            }
            
            // Mettre à jour le bouton de vente
            const sellButton = cell.querySelector('.sell-button');
            if (sellButton) {
                sellButton.textContent = `💰 ${Math.floor(tower.totalInvested * 0.75)}€`;
            }

            // Ajouter l'effet visuel d'amélioration
            const flash = document.createElement('div');
            flash.className = 'upgrade-flash';
            flash.style.width = '50px';
            flash.style.height = '50px';
            flash.style.position = 'absolute';
            flash.style.top = '0';
            flash.style.left = '0';
            flash.style.backgroundColor = '#ffffff';
            flash.style.animation = 'flash 0.5s ease-out';
            cell.appendChild(flash);
            
            // Supprimer l'effet après l'animation
            setTimeout(() => flash.remove(), 500);
            
            console.log('Tour améliorée avec succès au niveau', tower.level);
        } catch (error) {
            console.error('Erreur lors de l\'amélioration de la tour:', error);
            alert('Une erreur est survenue lors de l\'amélioration de la tour');
            
            // Rembourser le coût en cas d'erreur
            this.money += tower.upgradeCost;
            document.getElementById('money').textContent = this.money;
        }
    }

    sellTower(tower) {
        try {
            // Calculer le remboursement (75% du montant total investi)
            const refund = Math.floor(tower.totalInvested * 0.75);
            this.money += refund;
            document.getElementById('money').textContent = this.money;
            
            // Trouver et supprimer la tourelle de la liste
            const towerIndex = this.towers.findIndex(t => t.x === tower.x && t.y === tower.y);
            if (towerIndex !== -1) {
                this.towers.splice(towerIndex, 1);
            }
            
            // Nettoyer la cellule
            const cell = this.grid[tower.y][tower.x];
            cell.style.backgroundColor = '#446655'; // Couleur de base pour les emplacements
            cell.classList.remove('has-tower');
            
            // Supprimer tous les éléments UI de la tourelle
            const levelDisplay = cell.querySelector('.tower-level');
            const upgradeButton = cell.querySelector('.upgrade-button');
            const sellButton = cell.querySelector('.sell-button');
            
            if (levelDisplay) levelDisplay.remove();
            if (upgradeButton) upgradeButton.remove();
            if (sellButton) sellButton.remove();
            
            // Effet visuel de vente
            const flash = document.createElement('div');
            flash.className = 'sell-flash';
            flash.style.width = '50px';
            flash.style.height = '50px';
            flash.style.position = 'absolute';
            flash.style.top = '0';
            flash.style.left = '0';
            flash.style.backgroundColor = '#00ff00';
            flash.style.animation = 'flash 0.5s ease-out';
            flash.style.borderRadius = '3px';
            flash.style.zIndex = '2';
            flash.style.pointerEvents = 'none';
            cell.appendChild(flash);
            
            // Supprimer l'effet après l'animation
            setTimeout(() => flash.remove(), 500);
            
            // Afficher le montant récupéré
            const refundText = document.createElement('div');
            refundText.className = 'combo-text';
            refundText.textContent = `+${refund}€`;
            refundText.style.color = '#00ff00';
            refundText.style.left = (tower.x * 50 + 25) + 'px';
            refundText.style.top = (tower.y * 50 + 25) + 'px';
            this.gameGrid.appendChild(refundText);
            setTimeout(() => refundText.remove(), 1000);
            
            console.log(`Tourelle vendue pour ${refund}€`);
        } catch (error) {
            console.error('Erreur lors de la vente de la tourelle:', error);
            alert('Une erreur est survenue lors de la vente de la tourelle');
        }
    }

    handleGameOver() {
        // Arrêter le jeu
        this.gameOver = true;
        
        // Créer une modal pour la saisie du nom
        const modal = document.createElement('div');
        modal.className = 'game-over-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>🎮 Partie Terminée !</h2>
                <p><strong>Score final :</strong> ${this.score.toLocaleString()}</p>
                <p><strong>Niveau atteint :</strong> ${this.level}</p>
                ${this.score > this.highScore ? '<p class="new-record">🏆 Nouveau Record !</p>' : ''}
                <div class="name-input-section">
                    <label for="playerName">Entrez votre nom pour sauvegarder votre score :</label>
                    <input type="text" id="playerName" maxlength="50" placeholder="Votre nom ou pseudo">
                    <div class="modal-buttons">
                        <button id="saveScore" class="save-btn">💾 Sauvegarder</button>
                        <button id="skipSave" class="skip-btn">⏭️ Passer</button>
                    </div>
                </div>
            </div>
        `;
        
        // Styles pour la modal
        const style = document.createElement('style');
        style.textContent = `
            .game-over-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
                font-family: Arial, sans-serif;
            }
            .modal-content {
                background: white;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
            .modal-content h2 {
                color: #333;
                margin-bottom: 20px;
                font-size: 24px;
            }
            .modal-content p {
                margin: 10px 0;
                font-size: 16px;
                color: #555;
            }
            .new-record {
                color: #ff6b35 !important;
                font-weight: bold;
                font-size: 18px !important;
                animation: pulse 1.5s infinite;
            }
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            .name-input-section {
                margin-top: 25px;
            }
            .name-input-section label {
                display: block;
                margin-bottom: 10px;
                font-weight: bold;
                color: #333;
            }
            #playerName {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                margin-bottom: 20px;
                box-sizing: border-box;
            }
            #playerName:focus {
                border-color: #4CAF50;
                outline: none;
            }
            .modal-buttons {
                display: flex;
                gap: 10px;
                justify-content: center;
            }
            .save-btn, .skip-btn {
                padding: 12px 20px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            .save-btn {
                background-color: #4CAF50;
                color: white;
            }
            .save-btn:hover {
                background-color: #45a049;
            }
            .save-btn:disabled {
                background-color: #cccccc;
                cursor: not-allowed;
            }
            .skip-btn {
                background-color: #f44336;
                color: white;
            }
            .skip-btn:hover {
                background-color: #da190b;
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(modal);
        
        // Focus sur le champ de saisie
        const nameInput = document.getElementById('playerName');
        nameInput.focus();
        
        // Gestion des événements
        const saveBtn = document.getElementById('saveScore');
        const skipBtn = document.getElementById('skipSave');
        
        const saveScore = async () => {
            const playerName = nameInput.value.trim();
            
            if (!playerName) {
                alert('Veuillez entrer votre nom !');
                nameInput.focus();
                return;
            }
            
            if (!this.gameId) {
                alert('Erreur : ID du jeu non trouvé');
                this.restartGame();
                return;
            }
            
            saveBtn.disabled = true;
            saveBtn.textContent = '💾 Sauvegarde...';
            
            try {
                const response = await fetch('../../api/save_score.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        game_id: parseInt(this.gameId),
                        player_name: playerName,
                        score: parseInt(this.score)
                    })
                });
                
                const data = await response.json();
                console.log('Réponse de l\'API:', data); // Debug
                
                if (data.success) {
                    alert(`🎉 Score sauvegardé avec succès !\n\n` +
                          `Joueur : ${playerName}\n` +
                          `Score : ${this.score.toLocaleString()}\n` +
                          `Rang : ${data.rank}${this.getOrdinalSuffix(data.rank)}`);
                } else {
                    alert('❌ Erreur lors de la sauvegarde : ' + (data.error || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Erreur de connexion lors de la sauvegarde');
            }
            
            this.restartGame();
        };
        
        const skipSave = () => {
            if (confirm('Êtes-vous sûr de ne pas vouloir sauvegarder votre score ?')) {
                this.restartGame();
            }
        };
        
        // Événements des boutons
        saveBtn.addEventListener('click', saveScore);
        skipBtn.addEventListener('click', skipSave);
        
        // Sauvegarder avec Entrée
        nameInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                saveScore();
            }
        });
    }
    
    getOrdinalSuffix(num) {
        const suffixes = ['er', 'ème', 'ème'];
        return num === 1 ? 'er' : 'ème';
    }
    
    restartGame() {
        // Supprimer la modal
        const modal = document.querySelector('.game-over-modal');
        if (modal) {
            modal.remove();
        }
        
        // Recharger la page pour recommencer
        location.reload();
    }

    gameLoop() {
        if (this.gameOver) {
            return;
        }

        // Mettre à jour les éléments du jeu
        this.updateEnemies();
        this.updateTowers();
        this.updateProjectiles();

        // Continuer la boucle de jeu
        requestAnimationFrame(() => this.gameLoop());
    }
}

// Démarrer le jeu quand la page est chargée
// Debug des erreurs globales
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.error('Erreur:', msg, 'à la ligne:', lineNo, error);
    return false;
};

window.addEventListener('load', () => {
    console.log('Démarrage du jeu...');  // Debug
    new TowerDefense();
});

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

TowerDefense.prototype.generateRandomPath = function() {
    const gridWidth = GAME_CONFIG.GRID_WIDTH;
    const gridHeight = GAME_CONFIG.GRID_HEIGHT;
    const visited = Array(gridHeight).fill().map(() => Array(gridWidth).fill(false));
    const pathDirections = Array(gridHeight).fill().map(() => Array(gridWidth).fill(null));
    
    // Points de contrôle pour assurer une progression vers la droite
    const checkpoints = [
        {x: Math.floor(gridWidth * 0.2), y: getRandomInt(2, gridHeight - 3)},      // Premier tiers
        {x: Math.floor(gridWidth * 0.4), y: getRandomInt(2, gridHeight - 3)},      // Deuxième tiers
        {x: Math.floor(gridWidth * 0.6), y: getRandomInt(2, gridHeight - 3)},      // Troisième tiers
        {x: Math.floor(gridWidth * 0.8), y: getRandomInt(2, gridHeight - 3)},      // Quatrième tiers
        {x: gridWidth - 1, y: getRandomInt(2, gridHeight - 3)}                     // Point final
    ];
    
    // Choisir un point de départ aléatoire sur le bord gauche
    const startY = getRandomInt(1, gridHeight - 2);
    const path = [{x: -1, y: startY}];
    let currentX = 0;
    let currentY = startY;
    let lastDirection = {dx: 1, dy: 0}; // Direction initiale vers la droite
    
    // Vérifie si une cellule a des chemins adjacents
    const hasAdjacentPath = (x, y) => {
        for (let dx = -1; dx <= 1; dx++) {
            for (let dy = -1; dy <= 1; dy++) {
                if (dx === 0 && dy === 0) continue;
                const checkX = x + dx;
                const checkY = y + dy;
                if (checkX >= 0 && checkX < gridWidth && checkY >= 0 && checkY < gridHeight) {
                    if (visited[checkY][checkX]) return true;
                }
            }
        }
        return false;
    };
    
    // Vérifie si un mouvement est valide
    const isValidMove = (x, y, direction) => {
        if (x < 0 || x >= gridWidth || y < 1 || y >= gridHeight - 1) return false;
        if (visited[y][x]) return false;
        
        // Empêcher les demi-tours
        if (lastDirection.dx * direction.dx < 0) return false;
        
        // Vérifier qu'il n'y a pas de chemin adjacent (sauf pour la cellule précédente)
        let adjacentPaths = 0;
        for (let dx = -1; dx <= 1; dx++) {
            for (let dy = -1; dy <= 1; dy++) {
                if (dx === 0 && dy === 0) continue;
                const checkX = x + dx;
                const checkY = y + dy;
                if (checkX >= 0 && checkX < gridWidth && checkY >= 1 && checkY < gridHeight - 1) {
                    if (visited[checkY][checkX]) {
                        if (Math.abs(dx) + Math.abs(dy) === 1) { // Seulement cellules orthogonalement adjacentes
                            adjacentPaths++;
                        }
                    }
                }
            }
        }
        return adjacentPaths <= 1; // Permettre seulement la connexion avec la cellule précédente
    };
    
    // Atteindre chaque point de contrôle
    for (let i = 0; i < checkpoints.length; i++) {
        const target = checkpoints[i];
        let attempts = 0;
        const maxAttempts = 100;
        
        while ((currentX !== target.x || currentY !== target.y) && attempts < maxAttempts) {
            visited[currentY][currentX] = true;
            path.push({x: currentX, y: currentY});
            
            const possibleMoves = [];
            // Mouvements possibles : préférer aller vers la droite et éviter les retours
            const moves = [
                {dx: 1, dy: 0, weight: 5},   // Droite (plus probable)
                {dx: 0, dy: -1, weight: 2},  // Haut
                {dx: 0, dy: 1, weight: 2}    // Bas
            ];
            
            for (const move of moves) {
                const newX = currentX + move.dx;
                const newY = currentY + move.dy;
                const newDirection = {dx: move.dx, dy: move.dy};
                
                if (isValidMove(newX, newY, newDirection)) {
                    for (let w = 0; w < move.weight; w++) {
                        possibleMoves.push(newDirection);
                    }
                }
            }
            
            if (possibleMoves.length > 0) {
                const move = possibleMoves[getRandomInt(0, possibleMoves.length - 1)];
                lastDirection = move;
                currentX += move.dx;
                currentY += move.dy;
                pathDirections[currentY][currentX] = move;
            } else {
                // Si bloqué, reculer d'une case et réessayer
                if (path.length > 1) {
                    const lastPos = path.pop();
                    visited[lastPos.y][lastPos.x] = false;
                    currentX = path[path.length - 1].x;
                    currentY = path[path.length - 1].y;
                }
            }
            attempts++;
        }
        
        if (attempts >= maxAttempts) {
            // Si on n'arrive pas à atteindre le point de contrôle, forcer un chemin direct
            while (currentX < target.x) {
                currentX++;
                visited[currentY][currentX] = true;
                path.push({x: currentX, y: currentY});
            }
        }
    }
    
    // Ajouter juste un point final
    path.push({x: gridWidth, y: path[path.length - 1].y});
    return path;
};
