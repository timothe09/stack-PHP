<?php
require_once '../config.php';

session_start();

// Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$email = '';
$date_naissance = '';
$message = '';
$success = false;
$step = 1; // Étape 1: Demande d'email, Étape 2: Vérification date de naissance, Étape 3: Nouveau mot de passe

// Si l'utilisateur a soumis son email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 1) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = "L'adresse email est obligatoire";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, email, date_naissance FROM connexion_games WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // L'email existe, passer à l'étape 2
                $step = 2;
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_user_id'] = $user['id'];
            } else {
                $message = "Aucun compte n'est associé à cette adresse email";
            }
        } catch (PDOException $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
        }
    }
}

// Si l'utilisateur a soumis sa date de naissance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 2) {
    $date_naissance = $_POST['date_naissance'] ?? '';
    
    if (empty($date_naissance)) {
        $message = "La date de naissance est obligatoire";
        $step = 2;
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, date_naissance FROM connexion_games WHERE email = ?");
            $stmt->execute([$_SESSION['reset_email']]);
            $user = $stmt->fetch();
            
            if ($user && $user['date_naissance'] == $date_naissance) {
                // La date de naissance correspond, passer à l'étape 3
                $step = 3;
            } else {
                $message = "La date de naissance ne correspond pas à nos enregistrements";
                $step = 2;
            }
        } catch (PDOException $e) {
            $message = "Une erreur est survenue : " . $e->getMessage();
            $step = 2;
        }
    }
}

// Si l'utilisateur a soumis son nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 3) {
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
    
    if (empty($nouveau_mot_de_passe)) {
        $message = "Le nouveau mot de passe est obligatoire";
        $step = 3;
    } elseif ($nouveau_mot_de_passe != $confirmer_mot_de_passe) {
        $message = "Les mots de passe ne correspondent pas";
        $step = 3;
    } else {
        try {
            $hashed_password = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE connexion_games SET mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['reset_user_id']]);
            
            $success = true;
            $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
            
            // Nettoyer les variables de session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_user_id']);
            
            // Rediriger vers la page de connexion après 3 secondes
            header("refresh:3;url=formulaire_de_connexion.php");
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de la réinitialisation du mot de passe : " . $e->getMessage();
            $step = 3;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Plateforme de Jeux</title>
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
        input[type="date"],
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
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 4px;
            margin: 0 5px;
        }
        .step.active {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Récupération de mot de passe</h1>
        
        <div class="step-indicator">
            <div class="step <?php echo $step == 1 ? 'active' : ''; ?>">Étape 1: Email</div>
            <div class="step <?php echo $step == 2 ? 'active' : ''; ?>">Étape 2: Vérification</div>
            <div class="step <?php echo $step == 3 ? 'active' : ''; ?>">Étape 3: Nouveau mot de passe</div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step == 1 && !$success): ?>
            <!-- Étape 1: Demande d'email -->
            <form method="post" action="">
                <input type="hidden" name="step" value="1">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="<?php echo escape_html($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Continuer</button>
                </div>
            </form>
        <?php elseif ($step == 2 && !$success): ?>
            <!-- Étape 2: Vérification date de naissance -->
            <form method="post" action="">
                <input type="hidden" name="step" value="2">
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" required>
                    <small>Veuillez entrer votre date de naissance pour vérifier votre identité.</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Vérifier</button>
                </div>
            </form>
        <?php elseif ($step == 3 && !$success): ?>
            <!-- Étape 3: Nouveau mot de passe -->
            <form method="post" action="">
                <input type="hidden" name="step" value="3">
                <div class="form-group">
                    <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmer_mot_de_passe">Confirmer le mot de passe</label>
                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Réinitialiser le mot de passe</button>
                </div>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            <a href="formulaire_de_connexion.php">Retour à la page de connexion</a>
        </div>
    </div>
</body>
</html>