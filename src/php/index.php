<?php
include 'db.php';

$stmt = $con->query("SELECT Id, Libellé, Prix, Description, stock FROM produit");
$produits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
    <header>
        <h1>Liste des Produits</h1>
    </header>
    <main>
        <div class="product-container">
            <?php foreach ($produits as $produit): ?>
                <div class="product-card">
                    <h2><?= htmlspecialchars($produit['Libellé']); ?></h2>
                    <p class="price"><?= htmlspecialchars($produit['Prix']); ?> €</p>
                    <p class="description"><?= htmlspecialchars($produit['Description']); ?></p>
                    <p class="stock">
                        Stock : 
                        <?= $produit['stock'] > 0 ? htmlspecialchars($produit['stock']) : '<span class="out-of-stock">Rupture</span>'; ?>
                    </p>
                    <?php if ($produit['stock'] > 0): ?>
                        <a href="commande.php?id=<?= $produit['Id']; ?>" class="btn">Commander</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="gestion_utilisateurs.php" class="btn user-btn">Gérer les Utilisateurs</a>
    </main>
</body>
</html>
