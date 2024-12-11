<?php
session_start();
require 'db.php';

// Initialisation du panier si ce n'est pas déjà fait
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Gestion des actions (ajout, suppression, mise à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $productId = intval($_POST['idProduit']);
        $quantity = intval($_POST['quantite'] ?? 1);

        switch ($action) {
            case 'add':
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
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantite'] = max(1, $quantity); // Minimum 1
                }
                break;

            case 'remove':
                if (isset($_SESSION['cart'][$productId])) {
                    unset($_SESSION['cart'][$productId]);
                }
                break;

            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
    }
    header("Location: panier.php");
    exit;
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

// Logique pour la commande
// if (isset($_POST['action']) && $_POST['action'] === 'commander') {
//     // Vérifier si l'utilisateur est connecté
//     $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
//     if ($userId) {
//         // Créer la chaîne de produits pour la procédure
//         $productsStr = '';
//         foreach ($cart as $product) {
//             $productsStr .= $product['idProduit'] . ':' . $product['quantite'] . ',';
//         }
//         $productsStr = rtrim($productsStr, ',');

//         // Préparer et exécuter la procédure de commande
//         try {
//             $stmt = $con->prepare("CALL passerCommande(:status, :deliveryDate, :userId, :products)");
//             $stmt->bindParam(':status', $status);
//             $stmt->bindParam(':deliveryDate', $deliveryDate);
//             $stmt->bindParam(':userId', $userId);
//             $stmt->bindParam(':products', $productsStr);

//             // Définir les valeurs des paramètres
//             $status = 'En cours'; // Exemple de statut
//             $deliveryDate = '2024-12-20'; // Exemple de date de livraison

//             // Exécuter la procédure stockée
//             $stmt->execute();

//             // Vider le panier après la commande
//             $_SESSION['cart'] = [];

//             // Redirige après la commande
//             header("Location: confirmation.php");
//             exit;
//         } catch (PDOException $e) {
//             echo "Erreur lors de la commande : " . $e->getMessage();
//             exit;
//         }
//     } else {
//         echo "Utilisateur non connecté";
//     }
// }
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