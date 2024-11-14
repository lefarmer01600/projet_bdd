<?php


function addProduct($conn, $price, $desc, $title, $stock, $IdFournisseur)
{
    $sql = "INSERT INTO Produit (`Prix`, `Description`, `Libellé`, `stock`, `IdFournisseur`)
    VALUES ($price, $desc, $title, $stock, $IdFournisseur)";
    
    $conn->exec($sql);
}

function getProducts($conn)
{
    $stmt = $conn->prepare("SELECT * FROM Produit");
    $stmt->execute();
    
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function getProduct($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM Produit WHERE IdProduit = $id");
    $stmt->execute();
    
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function updateProduct($conn, $id, $price, $desc, $title, $stock, $IdFournisseur)
{
    $sql = "UPDATE Produit SET Prix = $price, Description = $desc, Libellé = $title, stock = $stock, IdFournisseur = $IdFournisseur WHERE IdProduit = $id";

    $stmt = $conn->prepare($sql);
    
    $stmt->execute();
}

function deleteProduct($conn, $id)
{
    $sql = "DELETE FROM Produit WHERE IdProduit = $id";

    $stmt = $conn->prepare($sql);
    
    $stmt->execute();
}

?>