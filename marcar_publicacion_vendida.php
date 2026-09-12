<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["exito" => false, "mensaje" => "Debes iniciar sesión."]);
    exit;
}

$publicacionId = isset($_POST['publicacion_id']) ? (int) $_POST['publicacion_id'] : 0;
$usuarioId = (int) $_SESSION['usuario_id'];
$stmt = $conexion->prepare("UPDATE publicaciones SET vendido = 1 WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $publicacionId, $usuarioId);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    echo json_encode(["exito" => true, "mensaje" => "Publicación marcada como vendida."]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "No puedes marcar esta publicación."]);
}

$stmt->close();
$conexion->close();
?>