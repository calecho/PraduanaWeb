<?php
session_start();
require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
    header('Location: pagina.html');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
$tokenSesion = $_SESSION['csrf_eliminar_cuenta'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if ($tokenSesion === '' || !hash_equals($tokenSesion, $token)) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

$id = (int) $_SESSION['usuario_id'];
$stmt = $conexion->prepare('SELECT contrasena FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario || !password_verify($contrasena, $usuario['contrasena'])) {
    http_response_code(403);
    exit('La contraseña no es correcta.');
}

$conexion->begin_transaction();
try {
    $stmt = $conexion->prepare('DELETE FROM publicaciones WHERE usuario_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $eliminado = $stmt->affected_rows === 1;
    $stmt->close();

    if (!$eliminado) {
        throw new RuntimeException('La cuenta no existe.');
    }

    $conexion->commit();
} catch (Throwable $error) {
    $conexion->rollback();
    http_response_code(500);
    exit('No se pudo eliminar la cuenta.');
}

$conexion->close();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
}
session_destroy();
header('Location: pagina.html');
exit;
?>