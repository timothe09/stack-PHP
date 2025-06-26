<?php
require_once '../config.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$email = '';
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "L'adresse email est obligatoire";
    }
    
    if (empty($mot_de_passe)) {
        $errors[] = "Le mot de passe est obligatoire";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, nom, email, mot_de_passe FROM connexion_games WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                
                $success = true;
                $message = "Connexion réussie. Vous allez être redirigé vers la page d'accueil.";
                
                header("refresh:2;url=../index.php");
            } else {
                $message = "Adresse email ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de la connexion : " . $e->getMessage();
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
    <title>Connexion - Plateforme de Jeux</title>
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
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="<?php echo escape_html($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Se connecter</button>
                </div>
            </form>
        <?php endif; ?>
        
        <div class="register-link">
            Vous n'avez pas de compte ? <a href="formulaire_de_creation.php">Inscrivez-vous</a>
        </div>
    </div>
</body>
</html>