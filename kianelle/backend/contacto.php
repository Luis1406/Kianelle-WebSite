<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "No se recibió JSON"]);
    exit;
}

$nombre = $input["nombre"] ?? "";
$email = $input["email"] ?? "";
$mensaje = $input["mensaje"] ?? "";

if (empty($nombre) || empty($email) || empty($mensaje)) {
    echo json_encode(["success" => false, "message" => "Completa todos los campos"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO contactos (nombre, email, mensaje) VALUES (?, ?, ?)");
$stmt->execute([$nombre, $email, $mensaje]);

echo json_encode(["success" => true, "message" => "Mensaje enviado correctamente"]);
?>
