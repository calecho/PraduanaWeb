<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

if ($nombre === "" || $correo === "" || $contrasena === "") {
    echo json_encode(["exito" => false, "mensaje" => "Todos los campos son obligatorios."]);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["exito" => false, "mensaje" => "El correo electrónico no es válido."]);
    exit;
}

if (strlen($contrasena) < 6) {
    echo json_encode(["exito" => false, "mensaje" => "La contraseña debe tener al menos 6 caracteres."]);
    exit;
}

$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ese correo ya está registrado."]);
    $stmt->close();
    $conexion->close();
    exit;
}
$stmt->close();

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $correo, $contrasena_hash);

if ($stmt->execute()) {
    $_SESSION['usuario_id'] = $stmt->insert_id;
    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['usuario_correo'] = $correo;
    echo json_encode(["exito" => true, "mensaje" => "¡Registro exitoso!"]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "Error al guardar el registro: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
