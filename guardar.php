<?php
header('Content-Type: application/json');

// ── MOSTRAR ERRORES (solo para pruebas) ─────────
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── CONFIGURACIÓN ───────────────────────────────
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sgbellavista"; // ✅ TU BASE DE DATOS

// ── CONEXIÓN ───────────────────────────────────
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  echo json_encode([
    "ok" => false,
    "mensaje" => "Error de conexión: " . $conn->connect_error
  ]);
  exit;
}

// ── LEER DATOS JSON ────────────────────────────
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo json_encode([
    "ok" => false,
    "mensaje" => "No se recibieron datos"
  ]);
  exit;
}

// ── LIMPIAR DATOS ──────────────────────────────
$nombre   = trim($data['nombre'] ?? '');
$email    = trim($data['email'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$servicio = trim($data['servicio'] ?? '');
$mensaje  = trim($data['mensaje'] ?? '');

// ── VALIDACIÓN ─────────────────────────────────
if (!$nombre || !$email || !$mensaje) {
  echo json_encode([
    "ok" => false,
    "mensaje" => "Faltan campos obligatorios"
  ]);
  exit;
}

// ── INSERTAR EN TU TABLA clientes ──────────────
$stmt = $conn->prepare("
  INSERT INTO clientes (nombre, email, telefono, servicio, mensaje)
  VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
  echo json_encode([
    "ok" => false,
    "mensaje" => "Error en la consulta: " . $conn->error
  ]);
  exit;
}

$stmt->bind_param("sssss", $nombre, $email, $telefono, $servicio, $mensaje);

// ── EJECUTAR ───────────────────────────────────
if ($stmt->execute()) {
  echo json_encode([
    "ok" => true,
    "mensaje" => "✅ Cotización enviada correctamente"
  ]);
} else {
  echo json_encode([
    "ok" => false,
    "mensaje" => "❌ Error al guardar: " . $stmt->error
  ]);
}

// ── CERRAR ─────────────────────────────────────
$stmt->close();
$conn->close();
?>