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
    <title>PC Clicker</title>
    <link rel="stylesheet" href="./pc-clickers.css">
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
        <!-- Stats -->
        <div class="stats">
            <div id="pc-count">0 PC</div>
            <div id="pc-per-second">0 PC/s</div>
            <div id="multiplier">x1 Multiplicateur</div>
        </div>

        <!-- Menu principal -->
        <div class="main-menu">
            <button class="menu-btn active" data-menu="game">🎮 Jeu</button>
            <button class="menu-btn" data-menu="achievements">🏆 Succès</button>
        </div>

        <!-- Zone de jeu -->
        <div class="game-section active">
            <div class="click-area">
                <div class="clickable-pc">
                    <span class="emoji">💻</span>
                    <div class="pc-text">CLIQUEZ !</div>
                </div>
            </div>

            <div class="shop">
                <div class="shop-categories">
                    <button class="cat-btn active" data-category="hardware">🔧 Hardware</button>
                    <button class="cat-btn" data-category="software">💿 Software</button>
                    <button class="cat-btn" data-category="network">📡 Network</button>
                </div>

                <!-- Hardware -->
                <div class="shop-items" id="hardware">
                    <div class="shop-item" data-item="mouse">
                        <span class="emoji" tabindex="0">🖱️</span>
                        <div class="item-info">
                            <h3>Souris Gaming</h3>
                            <p>+0.1 PC par clic</p>
                            <p class="cost">15 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="keyboard">
                        <span class="emoji" tabindex="0">⌨️</span>
                        <div class="item-info">
                            <h3>Clavier Mécanique</h3>
                            <p>+0.5 PC/s</p>
                            <p class="cost">100 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="screen">
                        <span class="emoji" tabindex="0">🖥️</span>
                        <div class="item-info">
                            <h3>Écran 4K</h3>
                            <p>+2 PC/s</p>
                            <p class="cost">500 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="gpu">
                        <span class="emoji" tabindex="0">🎮</span>
                        <div class="item-info">
                            <h3>Carte Graphique</h3>
                            <p>+5 PC/s</p>
                            <p class="cost">2000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="ram">
                        <span class="emoji" tabindex="0">💾</span>
                        <div class="item-info">
                            <h3>Barrette RAM</h3>
                            <p>+8 PC/s</p>
                            <p class="cost">5000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="cpu">
                        <span class="emoji" tabindex="0">🧠</span>
                        <div class="item-info">
                            <h3>Processeur</h3>
                            <p>+12 PC/s</p>
                            <p class="cost">10000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="watercooling">
                        <span class="emoji" tabindex="0">💧</span>
                        <div class="item-info">
                            <h3>Watercooling</h3>
                            <p>+20 PC/s</p>
                            <p class="cost">25000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                </div>

                <!-- Software -->
                <div class="shop-items hidden" id="software">
                    <div class="shop-item" data-item="antivirus">
                        <span class="emoji" tabindex="0">🛡️</span>
                        <div class="item-info">
                            <h3>Antivirus</h3>
                            <p>+5 PC par clic</p>
                            <p class="cost">100000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="system">
                        <span class="emoji" tabindex="0">💽</span>
                        <div class="item-info">
                            <h3>Système d'exploitation</h3>
                            <p>+5 PC/s</p>
                            <p class="cost">250000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="miner">
                        <span class="emoji" tabindex="0">⛏️</span>
                        <div class="item-info">
                            <h3>Logiciel de minage</h3>
                            <p>+15 PC/s</p>
                            <p class="cost">500000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="ai">
                        <span class="emoji" tabindex="0">🤖</span>
                        <div class="item-info">
                            <h3>Intelligence Artificielle</h3>
                            <p>+30 PC/s</p>
                            <p class="cost">1000000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                </div>

                <!-- Network -->
                <div class="shop-items hidden" id="network">
                    <div class="shop-item" data-item="wifi">
                        <span class="emoji" tabindex="0">📶</span>
                        <div class="item-info">
                            <h3>Wi-Fi Ultra-Rapide</h3>
                            <p>+10 PC/s</p>
                            <p class="cost">2500000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="server">
                        <span class="emoji" tabindex="0">🗄️</span>
                        <div class="item-info">
                            <h3>Serveur Dédié</h3>
                            <p>+40 PC/s</p>
                            <p class="cost">10000000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                    <div class="shop-item" data-item="datacenter">
                        <span class="emoji" tabindex="0">🏢</span>
                        <div class="item-info">
                            <h3>Data Center</h3>
                            <p>+80 PC/s</p>
                            <p class="cost">50000000 PC</p>
                        </div>
                        <span class="owned">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Succès -->
        <div class="achievements-section hidden">
            <h2>🏆 Succès (0/30)</h2>
            <div class="achievement-list">
                <!-- Succès faciles -->
                <div class="achievement" data-id="first-pc">
                    <div class="achievement-icon">💻</div>
                    <div class="achievement-info">
                        <h3>Premier PC</h3>
                        <p>Gagner votre premier PC</p>
                        <p class="reward">Récompense: +10% PC par clic</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <div class="achievement" data-id="beginner-clicker">
                    <div class="achievement-icon">🖱️</div>
                    <div class="achievement-info">
                        <h3>Débutant</h3>
                        <p>100 clics</p>
                        <p class="reward">Récompense: +20% PC par clic</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <div class="achievement" data-id="hardware-fan">
                    <div class="achievement-icon">🔧</div>
                    <div class="achievement-info">
                        <h3>Fan de Hardware</h3>
                        <p>Acheter 5 améliorations hardware</p>
                        <p class="reward">Récompense: -10% prix hardware</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <!-- Succès moyens -->
                <div class="achievement" data-id="click-master">
                    <div class="achievement-icon">👆</div>
                    <div class="achievement-info">
                        <h3>Click Master</h3>
                        <p>1,000 clics</p>
                        <p class="reward">Récompense: x2 PC par clic</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <div class="achievement" data-id="golden-hunter">
                    <div class="achievement-icon">⭐</div>
                    <div class="achievement-info">
                        <h3>Chasseur d'Or</h3>
                        <p>Trouver 10 PC dorés</p>
                        <p class="reward">Récompense: +1% chance de PC doré</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <div class="achievement" data-id="software-expert">
                    <div class="achievement-icon">💿</div>
                    <div class="achievement-info">
                        <h3>Expert Software</h3>
                        <p>Acheter 3 améliorations software</p>
                        <p class="reward">Récompense: -15% prix software</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <!-- Succès difficiles -->
                <div class="achievement" data-id="millionaire">
                    <div class="achievement-icon">💰</div>
                    <div class="achievement-info">
                        <h3>Millionnaire</h3>
                        <p>Avoir 1M PC</p>
                        <p class="reward">Récompense: x2 tous les gains</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>

                <div class="achievement" data-id="network-king">
                    <div class="achievement-icon">👑</div>
                    <div class="achievement-info">
                        <h3>Roi du Réseau</h3>
                        <p>Acheter un Data Center</p>
                        <p class="reward">Récompense: x3 production</p>
                        <div class="progress-bar"><div class="progress"></div></div>
                    </div>
                </div>


                <!-- [Plus de succès à ajouter...] -->
            </div>
        </div>

        <!-- Contrôles -->
        <div class="save-controls">
            <button id="save">💾 Sauvegarder</button>
            <button id="reset">🔄 Réinitialiser</button>
        </div>

        <div id="golden-pc-area"></div>
    </div>
    <script src="./pc-clickers.js"></script>
</body>
</html>