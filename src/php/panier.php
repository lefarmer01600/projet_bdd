<?php
session_start();
require 'db.php';

// Initialisation du panier si ce n'est pas déjà fait
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Récupération des produits du panier
$cart = $_SESSION['cart'];
$total = 0;
$products = [];
$productIds = [];

if (!empty($cart)) {
    // Récupérer les IDs des produits du panier
    foreach ($cart as $product) {
        $productIds[] = $product['idProduit'];
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $con->prepare("SELECT Id, Libellé, Prix FROM produit WHERE Id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll();
}

// Gestion des actions (ajout, suppression, mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add':
                $productId = intval($_POST['idProduit']);
                $quantity = intval($_POST['quantite'] ?? 1);
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantite'] += $quantity;
                } else {
                    $_SESSION['cart'][$productId] = [
                        'idProduit' => $productId,
                        'quantite' => $quantity
                    ];
                }
                break;

            case 'update':
                $productId = intval($_POST['idProduit']);
                $quantity = intval($_POST['quantite'] ?? 1);
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantite'] = max(1, $quantity); // Minimum 1
                }
                break;

            case 'remove':
                $productId = intval($_POST['idProduit']);
                if (isset($_SESSION['cart'][$productId])) {
                    unset($_SESSION['cart'][$productId]);
                }
                break;

            case 'clear':
                $_SESSION['cart'] = [];
                echo ("Panier vidé");
                break;
            case 'commander':
                // Logic de commande
                if (!empty($products)) {
                    $idUtilisateur = 1;
                    $produits = [];
                    $status = 'En attente';
                    $date = date('Y-m-d H:i:s', strtotime('+7 days')); // Ensure datetime format
                    foreach ($products as $product) {
                        $produits[] = $product['Id'] . ':' . $cart[$product['Id']]['quantite'];
                    }
                    $produits = implode(',', $produits);

                    $stmt = $con->prepare("CALL passerCommande(:p_Status, :p_DateLivraison, :p_IdUser, :p_Produits)");
                    $stmt->bindParam(':p_IdUser', $idUtilisateur);
                    $stmt->bindParam(':p_Status', $status);
                    $stmt->bindParam(':p_DateLivraison', $date);
                    $stmt->bindParam(':p_Produits', $produits);
                    $stmt->execute();

                    $_SESSION['cart'] = [];
                    $successMessage = "Commande passée avec succès ! Date de livraison estimée : $date";
                }
                break;
        }
    }
    header("Location: panier.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="assets/panier.css">
</head>

<body>
    <header>
        <h1>Votre Panier</h1>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $productId = $product['Id'];
                        $quantity = $cart[$productId]['quantite'];
                        $lineTotal = $product['Prix'] * $quantity;
                        $total += $lineTotal;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($product['Libellé']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="idProduit" value="<?= $productId; ?>">
                                    <input type="number" name="quantite" value="<?= $quantity; ?>" min="1">
                                    <button type="submit">Mettre à jour</button>
                                </form>
                            </td>
                            <td><?= number_format($product['Prix'], 2); ?> €</td>
                            <td><?= number_format($lineTotal, 2); ?> €</td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="idProduit" value="<?= $productId; ?>">
                                    <button type="submit" class="btn delete-btn">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Votre panier est vide.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="total">
            <strong>Total : <?= number_format($total, 2); ?> €</strong>
        </div>
        <div class="actions">
            <a href="index.php" class="btn ctn-btn">Continuer vos achats</a>
            <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn delete-btn">Vider le panier</button>
            </form>
            <form method="post" style="display:inline;">
                <input type="hidden" name="action" value="commander">
                <button type="submit" class="btn cmd-btn">Commander</button>
            </form>
        </div>
    </main>
</body>

</html>