<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idProduit = $_POST['idProduit'];
    $quantite = $_POST['quantite'];
    $idUser = 1; // Exemple : ID utilisateur fictif

    // Insérer la commande
    $con->beginTransaction();
    try {
        $stmt = $con->prepare("INSERT INTO commande (Status, DateLivraison, IdUser) VALUES (?, ?, ?)");
        $stmt->execute(['En attente', date('Y-m-d H:i:s', strtotime('+7 days')), $idUser]);

        $numCommande = $con->lastInsertId();

        // Lier la commande et le produit
        $stmt = $con->prepare("INSERT INTO contenir (IdProduit, NuméroCommande) VALUES (?, ?)");
        $stmt->execute([$idProduit, $numCommande]);

        // Mettre à jour le stock
        $stmt = $con->prepare("UPDATE produit SET stock = stock - ? WHERE Id = ?");
        $stmt->execute([$quantite, $idProduit]);

        $con->commit();
        $successMessage = "Commande passée avec succès !";
    } catch (Exception $e) {
        $con->rollBack();
        $errorMessage = "Erreur : " . $e->getMessage();
    }
}

$idProduit = $_GET['id'] ?? 0;
$stmt = $con->prepare("SELECT * FROM produit WHERE Id = ?");
$stmt->execute([$idProduit]);
$produit = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer une Commande</title>
    <link rel="stylesheet" href="assets/commande.css">
</head>

<body>
    <div class="container">
        <h1>Commander le produit : <span><?= htmlspecialchars($produit['Libellé']); ?></span></h1>

        <?php if (isset($successMessage)): ?>
            <div class="alert success"><?= htmlspecialchars($successMessage); ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="alert error"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="product-details">
            <p><strong>Description :</strong> <?= htmlspecialchars($produit['Description']); ?></p>
            <p><strong>Prix :</strong> <?= htmlspecialchars($produit['Prix']); ?> €</p>
            <p><strong>Stock disponible :</strong> <?= htmlspecialchars($produit['stock']); ?></p>
        </div>

        <form method="post" class="order-form">
            <input type="hidden" name="idProduit" value="<?= htmlspecialchars($produit['Id']); ?>">
            <label for="quantite">Quantité :</label>
            <input type="number" name="quantite" id="quantite" min="1" max="<?= htmlspecialchars($produit['stock']); ?>" required>
            <button type="submit" class="btn">Commander</button>
        </form>

        <a href="index.php" class="btn back-btn">Revenir aux produits</a>

    </div>
</body>

</html>