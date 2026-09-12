<?php
header('Content-Type: application/json; charset=utf-8');

require_once "conexion.php";

$condiciones = ["p.vendido = 0"];
$parametros = [];
$tipos = "";

if (!empty($_GET["ubicacion"])) {
    $condiciones[] = "ubicacion LIKE ?";
    $parametros[] = "%" . $_GET["ubicacion"] . "%";
    $tipos .= "s";
}

if (!empty($_GET["raza"])) {
    $condiciones[] = "raza LIKE ?";
    $parametros[] = "%" . $_GET["raza"] . "%";
    $tipos .= "s";
}

if (!empty($_GET["genero"])) {
    $condiciones[] = "genero = ?";
    $parametros[] = $_GET["genero"];
    $tipos .= "s";
}

if (!empty($_GET["precio_min"]) && is_numeric($_GET["precio_min"])) {
    $condiciones[] = "precio >= ?";
    $parametros[] = $_GET["precio_min"];
    $tipos .= "d";
}

if (!empty($_GET["precio_max"]) && is_numeric($_GET["precio_max"])) {
    $condiciones[] = "precio <= ?";
    $parametros[] = $_GET["precio_max"];
    $tipos .= "d";
}

if (!empty($_GET["edad_min"]) && is_numeric($_GET["edad_min"])) {
    $condiciones[] = "edad >= ?";
    $parametros[] = $_GET["edad_min"];
    $tipos .= "d";
}

if (!empty($_GET["edad_max"]) && is_numeric($_GET["edad_max"])) {
    $condiciones[] = "edad <= ?";
    $parametros[] = $_GET["edad_max"];
    $tipos .= "d";
}

$sql = "SELECT p.id, p.nombre, p.peso, p.ubicacion, p.raza, p.genero, p.edad, p.precio, p.descripcion, p.whatsapp, p.imagen, p.usuario_id, u.nombre AS usuario_nombre FROM publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id";

if (count($condiciones) > 0) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY fecha_publicacion DESC";

$stmt = $conexion->prepare($sql);

if (count($parametros) > 0) {
    $stmt->bind_param($tipos, ...$parametros);
}

$stmt->execute();
$resultado = $stmt->get_result();

$publicaciones = [];
while ($fila = $resultado->fetch_assoc()) {
    $publicaciones[] = $fila;
}

echo json_encode(["exito" => true, "publicaciones" => $publicaciones]);

$stmt->close();
$conexion->close();
?>
