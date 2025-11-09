<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../../config/conexion.php');

header('Content-Type: application/json');

// Verificar login
if (empty($_POST['usuario_id']) || empty($_POST['evento_id'])) {
    echo json_encode(['status' => 'nologin']);
    exit;
}

$usuario_id = intval($_POST['usuario_id']);
$evento_id = intval($_POST['evento_id']);

// Verificar si ya está inscrito
$sqlCheck = "SELECT id FROM inscripciones WHERE usuario_id = ? AND evento_id = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $usuario_id, $evento_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'exists']);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();

// Insertar nueva inscripción
$sql = "INSERT INTO inscripciones (usuario_id, evento_id, fecha_registro, asistencia, estado)
        VALUES (?, ?, NOW(), 0, 'activo')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $evento_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error']);
}

$stmt->close();
$conn->close();
