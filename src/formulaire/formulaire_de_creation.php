<?php
require_once '../config.php';

$nom = '';
$email = '';
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
    
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = "Le nom d'utilisateur est obligatoire";
    }
    
    if (empty($email)) {
        $errors[] = "L'adresse email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide";
    }
    
    if (empty($mot_de_passe)) {
        $errors[] = "Le mot de passe est obligatoire";
    } elseif (strlen($mot_de_passe) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if ($mot_de_passe !== $confirmer_mot_de_passe) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM connexion_games WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Cette adresse email est déjà utilisée";
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO connexion_games (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt->execute([$nom, $email, $hashed_password]);
            
            $success = true;
            $message = "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.";
            
            $nom = '';
            $email = '';
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de la création du compte : " . $e->getMessage();
        }
    } else {
        $message = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Plateforme de Jeux</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="nom">Nom d'utilisateur</label>
                    <input type="text" id="nom" name="nom" value="<?php echo escape_html($nom); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="<?php echo escape_html($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmer_mot_de_passe">Confirmer le mot de passe</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">S'inscrire</button>
                </div>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            Vous avez déjà un compte ? <a href="formulaire_de_connexion.php">Connectez-vous</a>
        </div>
    </div>
</body>
</html>
