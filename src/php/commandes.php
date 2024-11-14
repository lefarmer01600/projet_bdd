<?php

    function addCommande($conn,  $date, $idClient)
    {
        $sql = "INSERT INTO Commande (`Status`, `DateLivraison`, `IdUser`)
        VALUES ('En cours de validation', '$date', $idClient)";
        
        $conn->exec($sql);
    }

    function getCommandes($conn)
    {
        $stmt = $conn->prepare("SELECT * FROM Commande");
        $stmt->execute();
        
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    function getCommande($conn, $id)
    {
        $stmt = $conn->prepare("SELECT * FROM Commande WHERE IdCommande = $id");
        $stmt->execute();
        
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    function updateCommandeStatus($conn, $id, $status)
    {
        $sql = "UPDATE Commande SET Status = '$status' WHERE IdCommande = $id";
    
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
    }

    function updateCommandeDate($conn, $id, $date)
    {
        $sql = "UPDATE Commande SET DateLivraison = '$date' WHERE IdCommande = $id";
    
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
    }

    function deleteCommande($conn, $id)
    {
        $sql = "UPDATE Commande SET Status = 'Annulé' WHERE IdCommande = $id";
    
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
    }

    function addProduitCommande($conn, $IdProduit, $IdCommande)
    {
        $sql = "INSERT INTO Contenir (`IdProduit`, `IdCommande`)
        VALUES ($IdProduit, $IdCommande)";
        
        $conn->exec($sql);
    }

    function getProduitsFromCommande($conn, $IdCommande)
    {
        $stmt = $conn->prepare("SELECT `IdProduit` FROM `Contenir` WHERE NuméroCommande = $IdCommande");
        $stmt->execute();
        
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }
?>

