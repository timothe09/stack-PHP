<?php
require_once 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gestion_jeux.php?error=delete');
    exit;
}

$id = (int)$_GET['id'];

try {
    // Vérifier si le jeu existe
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM games WHERE id = ?");
    $check_stmt->execute([$id]);
    $jeu_existe = $check_stmt->fetchColumn() > 0;
    
    if (!$jeu_existe) {
        header('Location: gestion_jeux.php?error=delete');
        exit;
    }
    
    // Supprimer le jeu
    $delete_stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
    $delete_stmt->execute([$id]);
    
    // Rediriger vers la page de gestion avec un message de succès
    header('Location: gestion_jeux.php?success=delete');
    exit;
} catch (PDOException $e) {
    // En cas d'erreur, rediriger vers la page de gestion avec un message d'erreur
    header('Location: gestion_jeux.php?error=delete');
    exit;
}
?>