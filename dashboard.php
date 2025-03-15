<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('../uploading/IMG40.png');
            background-size: cover;
            background-position: center;
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 900px;
        }

        h1, h2 {
            text-align: center;
            color: #5e432e;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: center;
        }

        table th {
            background-color: #d9c9b2;
            color: #4b3a2f;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f6f2;
        }

        table tr:nth-child(odd) {
            background-color: #fff;
        }

        table tr:hover {
            background-color: #f1e6d6;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 2px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-modifier {
            background-color: #8b5e3c;
        }

        .btn-modifier:hover {
            background-color: #6e452b;
        }

        .btn-supprimer {
            background-color: #b23b3b;
        }

        .btn-supprimer:hover {
            background-color: #8b2c2c;
        }

        .btn-ajouter {
            background-color: #3c8b5e;
        }

        .btn-ajouter:hover {
            background-color: #2b6e45;
        }

        .btn-deconnexion {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            background-color: #333;
        }

        .btn-deconnexion:hover {
            background-color: #555;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>

        <h2>Liste des utilisateurs</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Login</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td>
                        <a class="btn btn-modifier" href="modif.php?id=<?= $user['id'] ?>">Modifier</a>
                        <a class="btn btn-supprimer" href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <a class="btn btn-ajouter" href="ajoutusers.php">Ajouter un utilisateur</a>

        <a class="btn btn-deconnexion" href="logout.php">Se déconnecter</a>
    </div>
</body>
</html>
