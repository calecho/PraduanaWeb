<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["exito" => false, "mensaje" => "Debes iniciar sesión."]);
    exit;
}

$usuarioId = (int) $_SESSION['usuario_id'];
$stmt = $conexion->prepare("UPDATE usuarios SET ventas_concretadas = ventas_concretadas + 1 WHERE id = ?");
$stmt->bind_param("i", $usuarioId);

if ($stmt->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Venta marcada como concretada."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No se pudo actualizar el perfil."]);
}
$stmt->close();
$conexion->close();
?>