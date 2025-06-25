<?php
require_once 'config.php';

// Récupération des jeux depuis la base de données
$stmt = $pdo->query("SELECT * FROM games ORDER BY created_at DESC");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement des messages de succès ou d'erreur
$message = '';
$message_type = '';

if (isset($_GET['success'])) {
    $message_type = 'success';
    if ($_GET['success'] === 'edit') {
        $message = 'Le jeu a été modifié avec succès.';
    } elseif ($_GET['success'] === 'delete') {
        $message = 'Le jeu a été supprimé avec succès.';
    }
}

if (isset($_GET['error'])) {
    $message_type = 'error';
    if ($_GET['error'] === 'edit') {
        $message = 'Une erreur est survenue lors de la modification du jeu.';
    } elseif ($_GET['error'] === 'delete') {
        $message = 'Une erreur est survenue lors de la suppression du jeu.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Jeux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .admin-table th, .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .admin-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        .admin-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .edit-button, .delete-button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .edit-button {
            background-color: #3498db;
            color: white;
        }
        
        .edit-button:hover {
            background-color: #2980b9;
        }
        
        .delete-button {
            background-color: #e74c3c;
            color: white;
        }
        
        .delete-button:hover {
            background-color: #c0392b;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .home-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .home-button:hover {
            background-color: #2980b9;
        }
        
        .add-button {
            display: inline-block;
            margin-left: 10px;
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .add-button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Gestion des Jeux</h1>
        <nav class="nav">
            <a href="index.php">Accueil</a>
            <a href="formulaire/formulaire_game.html">Ajouter un jeu</a>
        </nav>
    </header>

    <main class="admin-container">
        <a href="index.php" class="home-button">← Retour à l'accueil</a>
        <a href="formulaire/formulaire_game.html" class="add-button">+ Ajouter un jeu</a>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du jeu</th>
                    <th>Description</th>
                    <th>Chemin</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($games)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucun jeu trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($game['id']); ?></td>
                            <td><?php echo htmlspecialchars($game['nom_du_jeux']); ?></td>
                            <td><?php echo htmlspecialchars($game['description']); ?></td>
                            <td><?php echo htmlspecialchars($game['path']); ?></td>
                            <td><?php echo htmlspecialchars($game['created_at']); ?></td>
                            <td class="action-buttons">
                                <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="edit-button">Modifier</a>
                                <button class="delete-button" onclick="confirmDelete(<?php echo $game['id']; ?>, '<?php echo addslashes(htmlspecialchars($game['nom_du_jeux'])); ?>')">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Collection de Jeux. Tous droits réservés.</p>
    </footer>

    <script>
        function confirmDelete(id, name) {
            if (confirm("Êtes-vous sûr de vouloir supprimer le jeu '" + name + "' ?")) {
                window.location.href = "delete_game.php?id=" + id;
            }
        }
    </script>
</body>
</html>