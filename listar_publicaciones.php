<?php
header('Content-Type: application/json; charset=utf-8');

require_once "conexion.php";

$resultado = $conexion->query("SELECT p.id, p.nombre, p.peso, p.ubicacion, p.raza, p.genero, p.edad, p.precio, p.descripcion, p.whatsapp, p.imagen, p.usuario_id, p.vendido, u.nombre AS usuario_nombre FROM publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id WHERE p.vendido = 0 ORDER BY p.fecha_publicacion DESC");

$publicaciones = [];
while ($fila = $resultado->fetch_assoc()) {
    $publicaciones[] = $fila;
}

echo json_encode(["exito" => true, "publicaciones" => $publicaciones]);

$conexion->close();
?>
