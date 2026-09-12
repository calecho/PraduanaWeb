<?php
$host = "localhost";
$usuario_db = "root";
$clave_db = "";
$nombre_db = "praduana_db";

$conexion = new mysqli($host, $usuario_db, $clave_db, $nombre_db);

if ($conexion->connect_error) {
    die(json_encode([
        "exito" => false,
        "mensaje" => "Error de conexión a la base de datos: " . $conexion->connect_error
    ]));
}

$conexion->set_charset("utf8mb4");
?>
