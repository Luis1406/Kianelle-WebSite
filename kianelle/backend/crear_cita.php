<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

$servicio = $input["servicio"] ?? "";
$fecha = $input["fecha"] ?? "";
$hora = $input["hora"] ?? "";
$nombre = $input["nombre"] ?? "";
$apellido = $input["apellido"] ?? "";
$telefono = $input["telefono"] ?? "";
$email = $input["email"] ?? "";
$comentarios = $input["comentarios"] ?? "";

// Validación básica
if (!$servicio || !$fecha || !$hora || !$nombre || !$apellido || !$telefono || !$email) {
    echo json_encode(["success" => false, "message" => "Faltan campos obligatorios"]);
    exit;
}

// Verificar si ya existe una cita en esa fecha/hora
$stmt = $pdo->prepare("SELECT id FROM citas WHERE fecha = ? AND hora = ?");
$stmt->execute([$fecha, $hora]);
$existe = $stmt->fetch();

if ($existe) {
    echo json_encode(["success" => false, "message" => "La hora seleccionada ya está ocupada"]);
    exit;
}

// Insertar cita
$stmt = $pdo->prepare("INSERT INTO citas (servicio, fecha, hora, nombre, apellido, telefono, email, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$servicio, $fecha, $hora, $nombre, $apellido, $telefono, $email, $comentarios]);

echo json_encode(["success" => true, "message" => "Cita reservada con éxito"]);
?>
