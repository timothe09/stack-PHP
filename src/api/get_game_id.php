<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../config.php';

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Titre du jeu en dur pour Tower Defense
$game_title = 'Tower Defense';

try {
    // Debug: Log de la tentative de recherche
    error_log("Recherche du jeu: " . $game_title);

    // Recherche de l'ID du jeu par son titre
    $stmt = $pdo->prepare("SELECT id FROM games WHERE title = ?");
    $stmt->execute([$game_title]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($game) {
        error_log("Jeu trouvé avec l'ID: " . $game['id']);
        echo json_encode([
            'success' => true,
            'game_id' => $game['id']
        ]);
    } else {
        error_log("Jeu non trouvé, tentative d'insertion");
        
        // Le jeu n'existe pas, on le crée
        $stmt = $pdo->prepare("INSERT INTO games (title) VALUES (?)");
        $stmt->execute([$game_title]);
        $newId = $pdo->lastInsertId();
        
        error_log("Nouveau jeu créé avec l'ID: " . $newId);
        echo json_encode([
            'success' => true,
            'game_id' => $newId,
            'created' => true
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la recherche/création du jeu',
        'details' => $e->getMessage()
    ]);
}
?>