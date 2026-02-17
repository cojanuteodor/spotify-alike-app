<?php
// config.php

$servername = "localhost";
$username = "root";      // schimbă dacă folosești alt user MySQL
$password = "";          // parola ta MySQL
$dbname = "spotifyDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Setăm modul de eroare la excepție
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexiune eșuată: " . $e->getMessage());
}
?>
