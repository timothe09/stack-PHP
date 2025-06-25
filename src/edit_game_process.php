<?php
require_once 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérification des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification des champs attendus
    if (
        isset($_POST['id'], $_POST['nom_du_jeux'], $_POST['description'], $_POST['path']) &&
        !empty($_POST['id']) && !empty($_POST['nom_du_jeux']) && !empty($_POST['description']) && !empty($_POST['path'])
    ) {
        $id = (int)$_POST['id'];
        $nom_du_jeux = htmlspecialchars($_POST['nom_du_jeux']);
        $description = htmlspecialchars($_POST['description']);
        $path = htmlspecialchars($_POST['path']);

        // Vérifier si un jeu avec le même nom existe déjà (sauf le jeu actuel)
        $check_sql = "SELECT COUNT(*) FROM `games` WHERE `nom_du_jeux` = ? AND `id` != ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$nom_du_jeux, $id]);
        $jeu_existe = $check_stmt->fetchColumn() > 0;

        if ($jeu_existe) {
            // Le jeu existe déjà, rediriger vers le formulaire avec un message d'erreur
            header("Location: edit_game.php?id=$id&error=duplicate&game=" . urlencode($nom_du_jeux));
            exit;
        } else {
            try {
                // Le jeu n'existe pas, procéder à la mise à jour
                $sql = "UPDATE `games` SET `nom_du_jeux` = ?, `description` = ?, `path` = ? WHERE `id` = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nom_du_jeux, $description, $path, $id]);

                // Rediriger vers la page de gestion avec un message de succès
                header("Location: gestion_jeux.php?success=edit");
                exit;
            } catch (PDOException $e) {
                // En cas d'erreur, rediriger vers la page de gestion avec un message d'erreur
                header("Location: gestion_jeux.php?error=edit");
                exit;
            }
        }
    } else {
        // Rediriger vers la page de gestion avec un message d'erreur si tous les champs ne sont pas remplis
        header("Location: gestion_jeux.php?error=edit");
        exit;
    }  
} else {
    // Rediriger vers la page de gestion si la méthode n'est pas POST
    header("Location: gestion_jeux.php");
    exit;
}
?>