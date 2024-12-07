<?php
require 'db.php';

$stmt = $con->query("SELECT Id, Libellé, Prix, Description, stock FROM produit");
$produits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Produits</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Liste des Produits</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Libellé</th>
            <th>Prix</th>
            <th>Description</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php foreach ($produits as $produit): ?>
        <tr>
            <td><?= htmlspecialchars($produit['Id']); ?></td>
            <td><?= htmlspecialchars($produit['Libellé']); ?></td>
            <td><?= htmlspecialchars($produit['Prix']); ?> €</td>
            <td><?= htmlspecialchars($produit['Description']); ?></td>
            <td><?= htmlspecialchars($produit['stock']); ?></td>
            <td><a href="commande.php?id=<?= $produit['Id']; ?>">Commander</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
