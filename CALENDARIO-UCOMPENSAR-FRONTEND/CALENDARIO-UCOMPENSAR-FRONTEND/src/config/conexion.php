<?php
$host = "localhost";     // usualmente localhost
$user = "root";          // tu usuario de MySQL
$pass = "";              // tu contraseña de MySQL
$db   = "gestioneventos"; // nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
