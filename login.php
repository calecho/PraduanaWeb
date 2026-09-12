<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
    exit;
}

$correo = trim($_POST["correo"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

if ($correo === "" || $contrasena === "") {
    echo json_encode(["exito" => false, "mensaje" => "Ingresa tu correo y contraseña."]);
    exit;
}

$stmt = $conexion->prepare("SELECT id, nombre, correo, contrasena FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario || !password_verify($contrasena, $usuario["contrasena"])) {
    echo json_encode(["exito" => false, "mensaje" => "El correo o la contraseña no son correctos."]);
    exit;
}

$_SESSION["usuario_id"] = (int) $usuario["id"];
$_SESSION["usuario_nombre"] = $usuario["nombre"];
$_SESSION["usuario_correo"] = $usuario["correo"];

echo json_encode([
    "exito" => true,
    "mensaje" => "Sesión iniciada.",
    "usuario" => ["id" => $usuario["id"], "nombre" => $usuario["nombre"]]
]);
$stmt->close();
$conexion->close();
?>