<?php 
session_start();
include('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Déclaration des extensions de fichiers autorisées
$allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sécurisation des entrées
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password'];

    // Vérification de l'unicité du login (email)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = :login");
    $stmt->execute(['login' => $login]);
    $existingLogin = $stmt->fetchColumn();

    if ($existingLogin > 0) {
    echo "
    <div style='
        position: fixed; top: 0; left:  0; width: 100%; height: 100%;
        background-color: beige; display: flex; justify-content: center; align-items: center;'>
        <div style='background-color: white; padding: 30px; border-radius: 8px; text-align: center;'>
            <h3 style='color: red;'>Le login existe déjà !</h3>
            <p>Veuillez en choisir un autre.</p>
            <a href='ajoutusers.php' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Recommencer</a>

        </div>
    </div>";
    exit;
}


    // Hachage du mot de passe (algorithme sécurisé)
    $password_hash = sha1($password);

    // Gestion du fichier uploadé
    $photo_profil_path = '';
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {

        $fileTmpPath = $_FILES['profile']['tmp_name'];
        $fileName = $_FILES['profile']['name'];
        $fileSize = $_FILES['profile']['size'];
        $fileType = $_FILES['profile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        if (in_array($fileExtension, $allowedfileExtensions)) {

            $uploadFolder = 'uploading/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            // Nouveau nom unique pour éviter les collisions
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            $destination = $uploadFolder . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $photo_profil_path = $destination;
            } else {
                echo "<script>alert('Erreur lors du téléchargement du fichier.');</script>";
                exit;
            }

        } else {
            echo "<script>alert('Type de fichier non autorisé. Extensions autorisées : jpg, jpeg, png, gif');</script>";
            exit;
        }
    }

    // Insertion dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, login, password_hash, profile) VALUES (:nom, :prenom, :login, :password_hash, :profile)");

    $stmt->execute([
        'nom'           => $nom,
        'prenom'        => $prenom,
        'login'         => $login,
        'password_hash' => $password_hash,
        'profile'       => $photo_profil_path
    ]);

    // Redirection après insertion réussie
    // Redirection après insertion
    header('Location: dashboard.php');
    exit;
   
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
    <style>
        /* Style pour l'image en fond */
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-image: url('../uploading/IMG40.png'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); 
            border-radius: 8px;
            color: chocolate;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
        h2 {
            font-size: 2em;
        }
        input[type="text"], input[type="password"], input[type="file"] {
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
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="content">
        <h2>Ajouter un utilisateur</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Nom :</label><br>
            <input type="text" name="nom" required><br>

            <label>Prénom :</label><br>
            <input type="text" name="prenom" required><br>

            <label>Login :</label><br>
            <input type="text" name="login" required><br>

            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br>

            <label>Photo de profil :</label><br>
            <input type="file" name="photo_profil" accept="image/*"><br><br>

            <button type="submit">Ajouter</button>
        </form>
    </div>

</body>
</html>
