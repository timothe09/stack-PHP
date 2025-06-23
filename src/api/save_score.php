<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json');
require_once '../config.php';

// Debug: Log des données reçues
error_log("Données reçues: " . file_get_contents('php://input'));

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupération et validation des données
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['game_id']) || !isset($input['player_name']) || !isset($input['score'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit();
}
// Validation des types de données


$game_id = filter_var($input['game_id'], FILTER_VALIDATE_INT);
$player_name = trim($input['player_name']);
$score = filter_var($input['score'], FILTER_VALIDATE_INT);

if ($game_id === false || $score === false || empty($player_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Données invalides']);
    exit();
}

// Vérification de l'existence du jeu
$stmt = $pdo->prepare("SELECT id FROM games WHERE id = ?");
$stmt->execute([$game_id]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Jeu non trouvé']);
    exit();
}

try {
    // Enregistrement du score
    $stmt = $pdo->prepare("
        INSERT INTO high_scores (game_id, player_name, score) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$game_id, $player_name, $score]);

    // Récupération du rang du score
    $stmt = $pdo->prepare("SELECT COUNT(*)+1  AS `rank` FROM high_scores WHERE game_id = ? AND score > ?");
    $stmt->execute([$game_id, $score]);
    $rank = $stmt->fetch(PDO::FETCH_ASSOC)['rank'];

    echo json_encode([
        'success' => true,
        'message' => 'Score enregistré avec succès',
        'rank' => $rank
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode([
        'error' => 'Erreur lors de l\'enregistrement du score',
        'details' => $e->getMessage()
    ]);
}