<?php
require_once '../config.php';

try {
    // Mise à jour des chemins pour TowerDefense
    $stmt = $pdo->prepare("UPDATE games SET path = 'games/TowerDefense/TowerDefense.php' WHERE path = 'games/TowerDefense/TowerDefense.html'");
    $stmt->execute();
    
    // Mise à jour des chemins pour TicTacToe
    $stmt = $pdo->prepare("UPDATE games SET path = 'games/TicTacToe/tictactoe.php' WHERE path = 'games/TicTacToe/tictactoe.html'");
    $stmt->execute();
    
    // Mise à jour des chemins pour carre-surfers
    $stmt = $pdo->prepare("UPDATE games SET path = 'games/carre-surfers/carre-surfers.php' WHERE path = 'games/carre-surfers/carre-surfers.html'");
    $stmt->execute();
    
    // Mise à jour des chemins pour pc-clickers
    $stmt = $pdo->prepare("UPDATE games SET path = 'games/pc-clickers/pc-clickers.php' WHERE path = 'games/pc-clickers/pc-clickers.html'");
    $stmt->execute();
    
    // Mise à jour des chemins pour loto
    $stmt = $pdo->prepare("UPDATE games SET path = 'games/loto/loto.php' WHERE path = 'games/loto/loto.html'");
    $stmt->execute();
    
    echo "Les chemins des jeux ont été mis à jour avec succès !";
    
    // Afficher les chemins actuels pour vérification
    $stmt = $pdo->query("SELECT id, nom_du_jeux, path FROM games");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Liste des jeux avec leurs chemins actuels :</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nom du jeu</th><th>Chemin</th></tr>";
    
    foreach ($games as $game) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($game['id']) . "</td>";
        echo "<td>" . htmlspecialchars($game['nom_du_jeux']) . "</td>";
        echo "<td>" . htmlspecialchars($game['path']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<p><a href='../index.php'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "Erreur lors de la mise à jour des chemins : " . $e->getMessage();
}
?>