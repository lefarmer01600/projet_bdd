<?php
$servername = "localhost";
$username = "root";
$password = "";




try {
    $conn = new PDO("mysql:host=$servername;dbname=ProjectBDD", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM Utilisateur");
    $stmt->execute();
    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($stmt->fetchAll() as $key => $row) {
        foreach ($row as $_ => $value) {
            echo $value . "\n";
        }
    }
    echo "\nConnected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
