<?php
include("../../config/conexion.php"); // Ajusta la ruta a tu conexión

$result = $conn->query("SELECT id, clave FROM usuarios");

while ($row = $result->fetch_assoc()) {
    $hash = password_hash($row['clave'], PASSWORD_DEFAULT);
    $conn->query("UPDATE usuarios SET clave='$hash' WHERE id=".$row['id']);
}

echo "Contraseñas convertidas a hash correctamente.";
?>
