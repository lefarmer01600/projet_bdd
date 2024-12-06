<?php
require 'db.php';

$stmt = $con->query("SELECT Id, Username, Adresse_Facturation, telephone FROM utilisateur");
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Utilisateurs</title>
</head>
<body>
    <h1>Utilisateurs</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Adresse de Facturation</th>
            <th>Téléphone</th>
        </tr>
        <?php foreach ($utilisateurs as $utilisateur): ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['Id']); ?></td>
            <td><?= htmlspecialchars($utilisateur['Username']); ?></td>
            <td><?= htmlspecialchars($utilisateur['Adresse_Facturation']); ?></td>
            <td><?= htmlspecialchars($utilisateur['telephone']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
