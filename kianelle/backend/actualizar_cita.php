<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id        = $input["id"] ?? null;
$servicio  = $input["servicio"] ?? "";
$fecha     = $input["fecha"] ?? "";
$hora      = $input["hora"] ?? "";
$nombre    = $input["nombre"] ?? "";
$apellido  = $input["apellido"] ?? "";
$telefono  = $input["telefono"] ?? "";
$email     = $input["email"] ?? "";
$comentarios = $input["comentarios"] ?? "";

// Validación básica
if (!$id || !$servicio || !$fecha || !$hora || !$nombre || !$apellido || !$telefono || !$email) {
    echo json_encode(["success" => false, "message" => "Faltan campos obligatorios"]);
    exit;
}

// Verificar que la cita exista
$stmt = $pdo->prepare("SELECT id FROM citas WHERE id = ?");
$stmt->execute([$id]);
$existe = $stmt->fetch();

if (!$existe) {
    echo json_encode(["success" => false, "message" => "La cita que intentas actualizar no existe"]);
    exit;
}

// Verificar si ya existe otra cita en esa fecha/hora (distinta a esta)
$stmt = $pdo->prepare("SELECT id FROM citas WHERE fecha = ? AND hora = ? AND id <> ?");
$stmt->execute([$fecha, $hora, $id]);
$conflicto = $stmt->fetch();

if ($conflicto) {
    echo json_encode(["success" => false, "message" => "La hora seleccionada ya está ocupada por otra cita"]);
    exit;
}

// Actualizar cita
$stmt = $pdo->prepare("UPDATE citas 
                       SET servicio = ?, fecha = ?, hora = ?, nombre = ?, apellido = ?, telefono = ?, email = ?, comentarios = ?
                       WHERE id = ?");
$stmt->execute([$servicio, $fecha, $hora, $nombre, $apellido, $telefono, $email, $comentarios, $id]);

echo json_encode(["success" => true, "message" => "Cita actualizada con éxito"]);
?>
