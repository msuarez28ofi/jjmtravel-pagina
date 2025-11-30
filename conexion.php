<?php
// conexion.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "jjm_travel_3";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
