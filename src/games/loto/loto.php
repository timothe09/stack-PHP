<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirage du Loto</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1a5276;
        }
        .tirage {
            margin: 30px 0;
        }
        .numeros {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .numero {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            margin: 0 10px;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }
        .etoiles {
            display: flex;
            justify-content: center;
        }
        .etoile {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #f39c12;
            color: white;
            border-radius: 50%;
            margin: 0 10px;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }
        .refresh {
            margin-top: 30px;
        }
        .btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #27ae60;
        }
        .date {
            margin-top: 20px;
            color: #7f8c8d;
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
    </style>
</head>
<body>
    <a href="../../index.php" class="home-button">← Retour à l'accueil</a>
    <div class="container">
        <h1>Tirage du Loto</h1>
        
        <?php
        // Fonction pour générer un tirage de loto
        function genererTirageLoto() {
            // Tirage des 5 numéros principaux (1-49)
            $numeros = [];
            while (count($numeros) < 5) {
                $numero = rand(1, 49);
                if (!in_array($numero, $numeros)) {
                    $numeros[] = $numero;
                }
            }
            
            // Tri des numéros par ordre croissant
            sort($numeros);
            
            // Tirage des 2 numéros étoiles (1-12)
            $etoiles = [];
            while (count($etoiles) < 2) {
                $etoile = rand(1, 12);
                if (!in_array($etoile, $etoiles)) {
                    $etoiles[] = $etoile;
                }
            }
            
            // Tri des étoiles par ordre croissant
            sort($etoiles);
            
            return [
                'numeros' => $numeros,
                'etoiles' => $etoiles
            ];
        }
        
        // Générer le tirage
        $tirage = genererTirageLoto();
        ?>
        
        <div class="tirage">
            <h2>Numéros tirés</h2>
            <div class="numeros">
                <?php foreach ($tirage['numeros'] as $numero): ?>
                    <div class="numero"><?php echo $numero; ?></div>
                <?php endforeach; ?>
            </div>
            
            <h2>Numéros étoiles</h2>
            <div class="etoiles">
                <?php foreach ($tirage['etoiles'] as $etoile): ?>
                    <div class="etoile"><?php echo $etoile; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="date">
            Tirage effectué le <?php echo date('l/d/m/Y à H:i:s'); ?>
        </div>
        
        <div class="refresh">
            <button class="btn" onclick="window.location.reload()">Nouveau tirage</button>
        </div>
    </div>
</body>
</html>