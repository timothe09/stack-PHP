<?php
require_once 'config.php';

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gestion_jeux.php?error=edit');
    exit;
}

$id = (int)$_GET['id'];

// Récupérer les informations du jeu
try {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$id]);
    $game = $stmt->fetch();
    
    if (!$game) {
        header('Location: gestion_jeux.php?error=edit');
        exit;
    }
} catch (PDOException $e) {
    header('Location: gestion_jeux.php?error=edit');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un jeu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
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

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
        
        .error-message {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }
        
        .success-message {
            color: #27ae60;
            background-color: #d4efdf;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <a href="gestion_jeux.php" class="home-button">← Retour à la gestion</a>
    <div class="form-container">
        <h2>Modifier un jeu</h2>
        <div id="error-message" class="error-message"></div>
        <div id="success-message" class="success-message"></div>
        <form action="edit_game_process.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($game['id']); ?>">
            
            <label for="nom_du_jeux">Nom du jeu:</label>
            <input type="text" id="nom_du_jeux" name="nom_du_jeux" value="<?php echo htmlspecialchars($game['nom_du_jeux']); ?>" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($game['description']); ?>" required>

            <label for="path">Chemin:</label>
            <input type="text" id="path" name="path" value="<?php echo htmlspecialchars($game['path']); ?>" required>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>

    <script>
        // Fonction pour récupérer les paramètres de l'URL
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Vérifier s'il y a un message d'erreur dans l'URL
        window.onload = function() {
            var error = getUrlParameter('error');
            var success = getUrlParameter('success');
            var game = getUrlParameter('game');
            
            if (error === 'duplicate' && game) {
                var errorMessage = document.getElementById('error-message');
                errorMessage.textContent = 'Un jeu avec le nom "' + game + '" existe déjà dans la base de données. Veuillez choisir un autre nom.';
                errorMessage.style.display = 'block';
            } else if (success === 'true' && game) {
                var successMessage = document.getElementById('success-message');
                successMessage.textContent = 'Le jeu "' + game + '" a été modifié avec succès.';
                successMessage.style.display = 'block';
            }
        };
    </script>
</body>
</html>