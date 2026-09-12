<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["exito" => false, "mensaje" => "Método no permitido."]);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "exito" => false,
        "requiere_registro" => true,
        "mensaje" => "Debes registrarte antes de publicar."
    ]);
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$peso = trim($_POST["peso"] ?? "");
$ubicacion = trim($_POST["ubicacion"] ?? "");
$raza = trim($_POST["raza"] ?? "");
$genero = trim($_POST["genero"] ?? "");
$edad = trim($_POST["edad"] ?? "");
$precio = trim($_POST["precio"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$whatsapp = trim($_POST["whatsapp"] ?? "");

if ($nombre === "" || $peso === "" || $ubicacion === "" || $raza === "" || $whatsapp === "") {
    echo json_encode(["exito" => false, "mensaje" => "Todos los campos obligatorios deben estar completos."]);
    exit;
}

if (!in_array($genero, ["Macho", "Hembra"])) {
    echo json_encode(["exito" => false, "mensaje" => "Selecciona un género válido."]);
    exit;
}

if (!is_numeric($edad) || $edad < 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ingresa una edad válida (en meses)."]);
    exit;
}

if (!is_numeric($precio) || $precio < 0) {
    echo json_encode(["exito" => false, "mensaje" => "Ingresa un precio válido."]);
    exit;
}

$whatsapp_limpio = preg_replace("/[^0-9]/", "", $whatsapp);
if (strlen($whatsapp_limpio) < 10) {
    echo json_encode(["exito" => false, "mensaje" => "Ingresa un número de WhatsApp válido, con código de país (ej: 573001234567)."]);
    exit;
}

if (!isset($_FILES["imagenes"]) || !is_array($_FILES["imagenes"]["name"])) {
    echo json_encode(["exito" => false, "mensaje" => "Debes subir entre 2 y 5 imágenes del animal."]);
    exit;
}

$indicesImagenes = [];
foreach ($_FILES["imagenes"]["name"] as $indice => $nombreImagen) {
    if ($_FILES["imagenes"]["error"][$indice] !== UPLOAD_ERR_NO_FILE) {
        $indicesImagenes[] = $indice;
    }
}

$cantidadImagenes = count($indicesImagenes);
if ($cantidadImagenes < 2 || $cantidadImagenes > 5) {
    echo json_encode(["exito" => false, "mensaje" => "Debes subir entre 2 y 5 imágenes del animal."]);
    exit;
}

$extensionesPermitidas = ["jpg", "jpeg", "png", "webp", "jfif"];

$carpetaDestino = "uploads/";
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0777, true);
}

$rutasImagenes = [];
foreach ($indicesImagenes as $indice) {
    if ($_FILES["imagenes"]["error"][$indice] !== UPLOAD_ERR_OK) {
        echo json_encode(["exito" => false, "mensaje" => "Una de las imágenes no pudo cargarse."]);
        exit;
    }

    $extension = strtolower(pathinfo($_FILES["imagenes"]["name"][$indice], PATHINFO_EXTENSION));
    if (!in_array($extension, $extensionesPermitidas)) {
        echo json_encode(["exito" => false, "mensaje" => "Formato de imagen no permitido. Usa JPG, PNG, WEBP o JFIF."]);
        exit;
    }

    if ($_FILES["imagenes"]["size"][$indice] > 5 * 1024 * 1024) {
        echo json_encode(["exito" => false, "mensaje" => "Cada imagen no debe superar los 5 MB."]);
        exit;
    }

    $rutaImagen = $carpetaDestino . uniqid("ganado_") . "." . $extension;
    if (!move_uploaded_file($_FILES["imagenes"]["tmp_name"][$indice], $rutaImagen)) {
        echo json_encode(["exito" => false, "mensaje" => "Ocurrió un error al guardar una imagen."]);
        exit;
    }
    $rutasImagenes[] = $rutaImagen;
}

$rutaDestino = $rutasImagenes[0];
$imagenesJson = json_encode($rutasImagenes, JSON_UNESCAPED_SLASHES);

$usuarioId = (int) $_SESSION['usuario_id'];
$stmt = $conexion->prepare("INSERT INTO publicaciones (usuario_id, nombre, peso, ubicacion, raza, genero, edad, precio, descripcion, whatsapp, imagen, imagenes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssidssss", $usuarioId, $nombre, $peso, $ubicacion, $raza, $genero, $edad, $precio, $descripcion, $whatsapp_limpio, $rutaDestino, $imagenesJson);

if ($stmt->execute()) {
    $idNuevo = $stmt->insert_id;
    echo json_encode([
        "exito" => true,
        "mensaje" => "¡Tu publicación fue registrada!",
        "publicacion" => [
            "id" => $idNuevo,
            "nombre" => $nombre,
            "peso" => $peso,
            "ubicacion" => $ubicacion,
            "raza" => $raza,
            "genero" => $genero,
            "edad" => $edad,
            "precio" => $precio,
            "descripcion" => $descripcion,
            "whatsapp" => $whatsapp_limpio,
            "imagen" => $rutaDestino,
            "imagenes" => $rutasImagenes
        ]
    ]);
} else {
    echo json_encode(["exito" => false, "mensaje" => "Error al guardar la publicación: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
