<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["autenticado" => false]);
    exit;
}

echo json_encode([
    "autenticado" => true,
    "usuario" => [
        "id" => (int) $_SESSION['usuario_id'],
        "nombre" => $_SESSION['usuario_nombre'] ?? "Usuario",
        "correo" => $_SESSION['usuario_correo'] ?? ""
    ]
]);
?>