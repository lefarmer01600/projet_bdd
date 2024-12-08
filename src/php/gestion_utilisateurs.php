<?php
require 'db.php';

// Récupération des utilisateurs
$stmt = $con->query("SELECT Id, Username, Adresse_Facturation, telephone FROM utilisateur");
$utilisateurs = $stmt->fetchAll();

// Ajouter un utilisateur (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $adresse_facturation = $_POST['adresse_facturation'];
    $telephone = $_POST['telephone'];
    $id_role = $_POST['id_role']; // Par exemple, 1 pour Admin, 2 pour Client, etc.

    $stmt = $con->prepare("INSERT INTO utilisateur (Username, Password, Adresse_Facturation, telephone, IdRole) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $password, $adresse_facturation, $telephone, $id_role]);

    header("Location: gestion_utilisateurs.php"); // Redirection après ajout
    exit();
}

// Supprimer un utilisateur (GET)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $stmt = $con->prepare("DELETE FROM utilisateur WHERE Id = ?");
    $stmt->execute([$id]);

    header("Location: gestion_utilisateurs.php"); // Redirection après suppression
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>Gestion des Utilisateurs</title>
</head>
<body>
    <header>
        <h1>Utilisateurs</h1>
    </header>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Adresse de Facturation</th>
            <th>Téléphone</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($utilisateurs as $utilisateur): ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['Id']); ?></td>
            <td><?= htmlspecialchars($utilisateur['Username']); ?></td>
            <td><?= htmlspecialchars($utilisateur['Adresse_Facturation']); ?></td>
            <td><?= htmlspecialchars($utilisateur['telephone']); ?></td>
            <td>
                <a href="?delete_id=<?= $utilisateur['Id']; ?>" class="btn delete-btn">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="index.php" class="btn user-btn">Revenir aux produits</a>
</body>
</html>
