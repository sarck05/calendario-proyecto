<?php
include 'sql.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];

    $sql = "INSERT INTO contacto (nombre, correo, asunto, mensaje) VALUES (?, ?, ?, ?)";
    $params = array($nombre, $correo, $asunto, $mensaje);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "<script>alert('Mensaje enviado correctamente.');</script>";
    } else {
        echo "<script>alert('Error al enviar el mensaje.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contáctanos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #0078D7;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Contáctanos</h2>
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Correo:</label>
        <input type="email" name="correo" required>

        <label>Asunto:</label>
        <input type="text" name="asunto" required>

        <label>Mensaje:</label>
        <textarea name="mensaje" rows="5" required></textarea>

        <button type="submit">Enviar</button>
    </form>
</body>
</html>
