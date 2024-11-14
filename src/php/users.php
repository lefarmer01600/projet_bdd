<?php

function addUser($conn, $username, $password, $adresse, $telephone, $role, $email)
{
    $sql = "INSERT INTO `Utilisateur`(`Username`, `Password`, `Adresse_Facturation`, `telephone`, `IdRole`, `Email`) 
    VALUES ('$username', '$password', '$adresse', '$telephone', '$role', '$email')";
    $conn->exec($sql);
}

function getUsers($conn)
{
    $stmt = $conn->prepare("SELECT * FROM Utilisateur");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function getUser($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE IdUtilisateur = $id");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}

function updateUser($conn, $id, $username, $password, $adresse, $telephone, $role, $email)
{
    $sql = "UPDATE Utilisateur SET Username = '$username', Password = '$password', Adresse_Facturation = '$adresse', telephone = '$telephone', IdRole = '$role', Email = '$email' WHERE IdUtilisateur = $id";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

function deleteUser($conn, $id)
{
    $sql = "DELETE FROM Utilisateur WHERE IdUtilisateur = $id";
    // add the deletion of all other tables that have a foreign key to this user

    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

?>