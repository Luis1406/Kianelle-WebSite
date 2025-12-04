<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once "db.php";

try {
    $stmt = $pdo->query("SELECT id, servicio, fecha, hora, nombre, apellido, telefono, email, comentarios FROM citas ORDER BY fecha, hora");
    $citas = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "citas" => $citas
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al listar citas: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
