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

// Récupération de l'ID du jeu
if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du jeu manquant ou invalide']);
    exit();
}

$game_id = (int)$_GET['game_id'];

try {
    // Récupération des high scores
    $stmt = $pdo->prepare("
        SELECT player_name, score, achieved_at 
        FROM high_scores 
        WHERE game_id = ? 
        ORDER BY score DESC 
        LIMIT 10
    ");
    $stmt->execute([$game_id]);
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'scores' => $scores
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des scores']);
}
?>