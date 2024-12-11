<?php
session_start(); // Démarrer la session pour stocker le panier
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idProduit = $_POST['idProduit'];
    $quantite = $_POST['quantite'];

    // Récupérer les informations du produit depuis la base de données
    $stmt = $con->prepare("SELECT * FROM produit WHERE Id = ?");
    $stmt->execute([$idProduit]);
    $produit = $stmt->fetch();

    if (!$produit) {
        $errorMessage = "Produit introuvable.";
    } elseif ($produit['stock'] < $quantite) {
        $errorMessage = "Quantité demandée supérieure au stock disponible.";
    } else {
        // Ajouter le produit au panier dans la session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Vérifier si le produit est déjà dans le panier
        if (isset($_SESSION['cart'][$idProduit])) {
            // Ajouter la quantité demandée au produit existant
            $_SESSION['cart'][$idProduit]['quantite'] += $quantite;
        } else {
            // Ajouter un nouveau produit au panier
            $_SESSION['cart'][$idProduit] = [
                'idProduit' => $idProduit,
                'libelle' => $produit['Libellé'],
                'prix' => $produit['Prix'],
                'quantite' => $quantite,
                'stock' => $produit['stock']
            ];
        }

        $successMessage = "Produit ajouté au panier avec succès !";
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
    <title>Ajouter au Panier</title>
    <link rel="stylesheet" href="assets/commande.css">
</head>

<body>
    <div class="container">
        <h1>Ajouter au panier : <span><?= htmlspecialchars($produit['Libellé']); ?></span></h1>

        <?php if (isset($successMessage)): ?>
            <div class="alert success"><?= htmlspecialchars($successMessage); ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="alert error"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="product-details">
            <p><strong>Description :</strong> <?= htmlspecialchars($produit['Description']); ?></p>
            <p><strong>Prix :</strong> <?= htmlspecialchars($produit['Prix']); ?> €</p>
            <p><strong>Stock disponible :</strong> <?= htmlspecialchars($produit['stock']); ?></p>
        </div>

        <form method="post" class="order-form">
            <input type="hidden" name="idProduit" value="<?= htmlspecialchars($produit['Id']); ?>">
            <label for="quantite">Quantité :</label>
            <input type="number" name="quantite" id="quantite" min="1" max="<?= htmlspecialchars($produit['stock']); ?>" required>
            <button type="submit" class="btn">Ajouter au Panier</button>
        </form>

        <a href="panier.php" class="btn">Voir le Panier</a>
        <a href="index.php" class="btn back-btn">Revenir aux produits</a>
    </div>
</body>

</html>
