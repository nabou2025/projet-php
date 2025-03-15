<?php
session_start();
require_once 'config.php'; // Connexion à la base de données

// Activer les rapports d'erreurs (uniquement en développement, à désactiver en prod)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = ''; // Pour afficher un message à l'utilisateur si besoin

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $login = $_POST['login'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$login || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // On hash le mot de passe entré par l'utilisateur en SHA-1
        $hashedInputPassword = sha1($password);

        try {
            // Vérifier si l'utilisateur existe
            $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérifier si le hash correspond
                if ($hashedInputPassword === $user['password_hash']) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['login'] = $user['login'];

                    // Redirection vers le tableau de bord
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Login ou mot de passe incorrect.";
                }
            } else {
                $error = "Login ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la connexion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        /* Vidéo de fond */

        .background-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            overflow: hidden;
        }

        /* Centrage du container */
        .container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            
            width: 400px;
            padding: 30px;
            
            /* Fond transparent */
            background-color: rgba(255, 255, 255, 0.1); /* très transparent */
            border: 2px solid #d2b48c; /* marron clair */
            border-radius: 15px;

            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px); /* pour un effet verre dépoli */
        }

        h2 {
            text-align: center;
            color: #fff; /* Blanc pour bien contraster */
            margin-bottom: 30px;
        }

        label {
            color: #fff; /* Blanc */
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0 20px 0;
            border: 1px solid #d2b48c;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            box-sizing: border-box;
            color: #333;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #8b5e3c;
            outline: none;
        }

        button {
            width: 100%;
            background-color: #8b5e3c;
            color: #fff8f0;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #5e3d1b;
        }

        .error {
            color: #fff;
            background-color: rgba(179, 58, 58, 0.8);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #e0b4b4;
        }

        footer {
            text-align: center;
            font-size: 12px;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Vidéo de fond -->
    <video class="background-video" autoplay muted loop>
        <source src="../uploading/coffeform.mp4" type="video/mp4">
        
    </video>

    <div class="container">
        <h2>Connexion</h2>

        <?php if (!empty($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="login">Login :</label><br>
            <input type="text" id="login" name="login" required><br>

            <label for="password">Mot de passe :</label><br>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Se connecter</button>
        </form>

        <footer>
            &copy; <?php echo date("Y"); ?> MonSite. Tous droits réservés.
        </footer>
    </div>

</body>
</html>
