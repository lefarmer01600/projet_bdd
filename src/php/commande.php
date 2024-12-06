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
        echo "Commande passée avec succès !";
    } catch (Exception $e) {
        $con->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
    exit;
}

$idProduit = $_GET['id'] ?? 0;
$stmt = $con->prepare("SELECT * FROM produit WHERE Id = ?");
$stmt->execute([$idProduit]);
$produit = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passer une Commande</title>
</head>
<body>
    <h1>Commander le Produit : <?= $produit['Libellé']; ?></h1>
    <form method="post">
        <input type="hidden" name="idProduit" value="<?= $produit['Id']; ?>">
        <label>Quantité :</label>
        <input type="number" name="quantite" min="1" max="<?= $produit['stock']; ?>" required>
        <button type="submit">Commander</button>
    </form>
</body>
</html>
