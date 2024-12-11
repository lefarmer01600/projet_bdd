<?php
session_start();
include 'db.php';

// Si le formulaire d'ajout de produit est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProduct'])) {
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $libelle = $_POST['libelle'];
    $stock = $_POST['stock'];
    $idFournisseur = $_POST['idFournisseur'];

    // Appel à la procédure stockée pour ajouter un produit
    $stmt = $con->prepare("CALL ajouterProduit(:p_Prix, :p_Description, :p_Libelle, :p_Stock, :p_IdFournisseur)");
    $stmt->bindParam(':p_Prix', $prix);
    $stmt->bindParam(':p_Description', $description);
    $stmt->bindParam(':p_Libelle', $libelle);
    $stmt->bindParam(':p_Stock', $stock);
    $stmt->bindParam(':p_IdFournisseur', $idFournisseur);

    try {
        $stmt->execute();
        $successMessage = "Produit ajouté avec succès !";
    } catch (Exception $e) {
        $errorMessage = "Erreur : " . $e->getMessage();
    }
}

// Récupération des produits
$stmt = $con->query("SELECT Id, Libellé, Prix, Description, stock FROM produit");
$produits = $stmt->fetchAll();

// Calcul du nombre total d'articles dans le panier
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantite'];
    }
}
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
        <a href="panier.php" class="cart-button">
            Panier
        </a>
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
                        <form method="post" action="panier.php">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="idProduit" value="<?= $produit['Id']; ?>">
                            <input type="hidden" name="quantite" value="1">
                            <button type="submit" class="btn">Ajouter au Panier</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="btn user-btn" id="addProductBtn">Ajouter un Produit</button>
        <a href="gestion_utilisateurs.php" class="btn user-btn">Gérer les Utilisateurs</a>

        <!-- Formulaire modal pour ajouter un produit -->
        <div class="overlay" id="overlay"></div>
        <div class="modal" id="addProductModal">
            <h2>Ajouter un Nouveau Produit</h2>
            <?php if (isset($successMessage)): ?>
                <div class="alert success"><?= htmlspecialchars($successMessage); ?></div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="alert error"><?= htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <form method="post">
                <label for="libelle">Libellé :</label>
                <input type="text" name="libelle" id="libelle" required>

                <label for="description">Description :</label>
                <textarea name="description" id="description" required></textarea>

                <label for="prix">Prix :</label>
                <input type="number" name="prix" id="prix" step="0.01" required>

                <label for="stock">Stock :</label>
                <input type="number" name="stock" id="stock" required>

                <label for="idFournisseur">ID Fournisseur :</label>
                <input type="number" name="idFournisseur" id="idFournisseur" required>

                <button type="submit" name="addProduct" class="btn">Ajouter</button>
                <button type="button" class="btn" id="closeModalBtn">Annuler</button>
            </form>
        </div>
    </main>

    <script>
        const addProductBtn = document.getElementById('addProductBtn');
        const overlay = document.getElementById('overlay');
        const modal = document.getElementById('addProductModal');
        const closeModalBtn = document.getElementById('closeModalBtn');

        addProductBtn.addEventListener('click', () => {
            overlay.classList.add('active');
            modal.classList.add('active');
        });

        closeModalBtn.addEventListener('click', () => {
            overlay.classList.remove('active');
            modal.classList.remove('active');
        });

        overlay.addEventListener('click', () => {
            overlay.classList.remove('active');
            modal.classList.remove('active');
        });
    </script>
</body>

</html>