<?php
session_start();
include('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Vérifier si l'ID de l'utilisateur à modifier est fourni
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification si l'utilisateur existe
if (!$user) {
    echo "<div style='color:red; text-align:center; font-size:18px;'>Utilisateur non trouvé.</div>";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password'];

    // Si un nouveau mot de passe est renseigné
    if (!empty($password)) {
        $password_hash = sha1($password);
    } else {
        $password_hash = $user['password_hash'];
    }

    // Mise à jour des informations
    $stmt = $pdo->prepare("UPDATE users SET nom = :nom, prenom = :prenom, login = :login, password_hash = :password_hash WHERE id = :id");
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'login' => $login,
        'password_hash' => $password_hash,
        'id' => $id
    ]);


    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'utilisateur</title>
    <style>
        
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-image: url('../uploading/boheme.png'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: chocolate;
        }

        h2 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
            font-size: 1em;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="content">
        <h2>Modifier l'utilisateur</h2>

        <form action="" method="POST">
            <label>Nom :</label><br>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br>

            <label>Prénom :</label><br>
            <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br>

            <label>Login :</label><br>
            <input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>" required><br>

            <label>Nouveau mot de passe :</label><br>
            <input type="password" name="password" placeholder="Laisser vide pour ne pas changer"><br><br>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>

</body>
</html>
