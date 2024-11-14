<?php

function addFournisseur($conn, $nom, $adresse, $ville, $codePostal, $telephone)
{
    $sql = "INSERT INTO Fournisseur (`Nom`, `Adresse`, `Ville`, `CodePostal`, `Telephone`)
    VALUES ('$nom', '$adresse', '$ville', '$codePostal', '$telephone')";
    
    $conn->exec($sql);
}

function getFournisseurs($conn)
{
    $stmt = $conn->prepare("SELECT * FROM Fournisseur");
    $stmt->execute();
    
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function getFournisseur($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM Fournisseur WHERE IdFournisseur = $id");
    $stmt->execute();
    
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function updateFournisseur($conn, $id, $nom, $adresse, $ville, $codePostal, $telephone)
{
    $sql = "UPDATE Fournisseur SET Nom = '$nom', Adresse = '$adresse', Ville = '$ville', CodePostal = '$codePostal', Telephone = '$telephone' WHERE IdFournisseur = $id";

    $stmt = $conn->prepare($sql);
    
    $stmt->execute();
}

function deleteFournisseur($conn, $id)
{
    $sql = "DELETE FROM Fournisseur WHERE IdFournisseur = $id";

    $stmt = $conn->prepare($sql);
    
    $stmt->execute();
}

?>