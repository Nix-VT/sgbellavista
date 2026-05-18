<?php
header('Content-Type: application/json');

// ── CONEXIÓN ───────────────────────────────────
$conexion = new mysqli("localhost", "root", "", "sgbellavista");

if ($conexion->connect_error) {
    echo json_encode(["ok" => false, "mensaje" => "Error de conexión: " . $conexion->connect_error]);
    exit;
}

// ── LEER DATOS (el JS envía JSON) ──────────────
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["ok" => false, "mensaje" => "No se recibieron datos"]);
    exit;
}

$nombre   = trim($data['nombre']   ?? '');
$correo   = trim($data['email']    ?? '');
$telefono = trim($data['telefono'] ?? '');
$servicio = trim($data['servicio'] ?? '');
$mensaje  = trim($data['mensaje']  ?? '');

// ── VALIDACIÓN ─────────────────────────────────
// Solo nombre, correo y mensaje son obligatorios
if (empty($nombre) || empty($correo) || empty($mensaje)) {
    echo json_encode(["ok" => false, "mensaje" => "Completa los campos obligatorios"]);
    exit;
}

// ── INSERT — usa el nombre correcto de tu tabla ─
$stmt = $conexion->prepare(
    "INSERT INTO clientes (nombre, correo, telefono, servicio, mensaje) VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["ok" => false, "mensaje" => "Error en consulta: " . $conexion->error]);
    exit;
}

// ✅ 5 campos → "sssss" (cinco s)
$stmt->bind_param("sssss", $nombre, $correo, $telefono, $servicio, $mensaje);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "mensaje" => "✅ Cotización enviada correctamente"]);
} else {
    echo json_encode(["ok" => false, "mensaje" => "Error al guardar: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>