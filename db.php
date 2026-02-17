<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "spotifyDB";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Eroare conexiune: " . $conn->connect_error);
}
?>
