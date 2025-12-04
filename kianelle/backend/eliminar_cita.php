<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID de cita no recibido"]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM citas WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(["success" => true, "message" => "Cita cancelada correctamente"]);
} else {
    echo json_encode(["success" => false, "message" => "No se encontró la cita a cancelar"]);
}
?>
