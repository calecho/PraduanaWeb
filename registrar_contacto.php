<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "conexion.php";

$publicacionId = isset($_POST['publicacion_id']) ? (int) $_POST['publicacion_id'] : 0;
if ($publicacionId <= 0) {
    echo json_encode(["exito" => false, "mensaje" => "Publicación no válida."]);
    exit;
}

$stmt = $conexion->prepare("SELECT usuario_id FROM publicaciones WHERE id = ?");
$stmt->bind_param("i", $publicacionId);
$stmt->execute();
$publicacion = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$publicacion) {
    echo json_encode(["exito" => false, "mensaje" => "Publicación no encontrada."]);
    exit;
}

$visitante = isset($_SESSION['usuario_id'])
    ? 'usuario:' . (int) $_SESSION['usuario_id']
    : 'visitante:' . hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? ''));

$stmt = $conexion->prepare("INSERT IGNORE INTO contactos_publicacion (publicacion_id, vendedor_id, visitante_clave) VALUES (?, ?, ?)");
$vendedorId = (int) $publicacion['usuario_id'];
$stmt->bind_param("iis", $publicacionId, $vendedorId, $visitante);
$stmt->execute();

echo json_encode(["exito" => true]);
$stmt->close();
$conexion->close();
?>