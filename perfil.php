<?php
session_start();
require_once "conexion.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$esPropietario = isset($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === $id;
if ($esPropietario && empty($_SESSION['csrf_eliminar_cuenta'])) {
    $_SESSION['csrf_eliminar_cuenta'] = bin2hex(random_bytes(32));
}
$stmt = $conexion->prepare("SELECT id, nombre, correo, fecha_registro FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$ventasDetectadas = false;
$publicaciones = [];

if ($usuario) {
    $ventasStmt = $conexion->prepare("SELECT COUNT(*) AS total FROM publicaciones WHERE usuario_id = ? AND vendido = 1");
    $ventasStmt->bind_param("i", $id);
    $ventasStmt->execute();
    $ventasDetectadas = (int) $ventasStmt->get_result()->fetch_assoc()['total'] > 0;
    $ventasStmt->close();

    $publicacionesStmt = $conexion->prepare("SELECT id, nombre, precio, ubicacion, raza, imagen, descripcion, vendido FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicacion DESC");
    $publicacionesStmt->bind_param("i", $id);
    $publicacionesStmt->execute();
    $publicaciones = $publicacionesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $publicacionesStmt->close();
}

$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $usuario ? htmlspecialchars($usuario['nombre']) : 'Perfil no encontrado' ?> - PRADUANA</title>
    <style>
        *{box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        body{margin:0;background:#111;color:#fff}
        header{padding:22px;text-align:center;background:linear-gradient(90deg,#8b1e14,#d94a2b,#f5b942)}
        header a{color:#fff;text-decoration:none;font-size:28px;font-weight:700;letter-spacing:2px}
        .contenedor{width:1000px;max-width:92%;margin:40px auto 80px}
        .perfil{display:flex;align-items:center;gap:22px;padding:28px;background:#1d1d1d;border:1px solid #444;border-radius:14px}
        .silueta{width:76px;height:76px;border-radius:50%;background:#f5b942;color:#8b1e14;display:grid;place-items:center;font-size:36px}
        .perfil h1{margin:0;color:#ffd37a}.perfil p{margin:5px 0;color:#ddd}
        .estado{color:#7ee2a8!important;font-weight:700}.badge-venta{color:#ffd37a!important;font-weight:700}.estado-vendido{color:#ff9a8f!important;font-weight:700}.estado-disponible{color:#7ee2a8!important;font-weight:700}
        .imagenes{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:28px}
        .card{display:block;color:#fff;text-decoration:none;background:#1d1d1d;border:1px solid #444;border-radius:12px;overflow:hidden}
        .card:hover{border-color:#f5b942}.card img{width:100%;height:190px;object-fit:cover;object-position:center 78%;background:#222}
        .card div{padding:16px}.card h2{font-size:20px;color:#ffd37a;margin:0 0 8px}.card p{margin:5px 0;color:#ddd}
        .volver{display:inline-block;margin-top:28px;color:#ffd37a}
        .zona-cuenta{margin-top:32px;padding-top:24px;border-top:1px solid #444}
        .zona-cuenta h3{margin:0 0 8px;color:#ff9a8f}
        .zona-cuenta p{margin:0 0 14px;color:#bbb}
        .btn-eliminar{padding:11px 18px;border:1px solid #ff7066;border-radius:8px;background:#5c211f;color:#fff;cursor:pointer;font:inherit;font-weight:700}
        .btn-eliminar:hover{background:#8b2d28}
        .campo-confirmacion{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        .campo-confirmacion input{padding:10px;border:1px solid #666;border-radius:8px;background:#111;color:#fff}
    </style>
</head>
<body>
    <header><a href="pagina.html">PRADUANA</a></header>
    <main class="contenedor">
        <?php if ($usuario): ?>
            <section class="perfil">
                <div class="silueta">&#128100;</div>
                <div>
                    <h1><?= htmlspecialchars($usuario['nombre']) ?></h1>
                    <p><?= htmlspecialchars($usuario['correo']) ?></p>
                    <p><?= count($publicaciones) ?> publicación(es)</p>
                    <?php if ($ventasDetectadas): ?>
                        <p class="badge-venta">&#10132; Este usuario ha concretado ventas en PRADUANA</p>
                    <?php else: ?>
                        <p class="estado">Vendedor activo en PRADUANA</p>
                    <?php endif; ?>
                </div>
            </section>
            <h2>Publicaciones de <?= htmlspecialchars($usuario['nombre']) ?></h2>
            <section class="imagenes">
                <?php foreach ($publicaciones as $publicacion): ?>
                    <a class="card" href="publicacion.php?id=<?= (int) $publicacion['id'] ?>">
                        <img src="<?= htmlspecialchars($publicacion['imagen']) ?>" alt="<?= htmlspecialchars($publicacion['nombre']) ?>">
                        <div>
                            <h2><?= htmlspecialchars($publicacion['nombre']) ?></h2>
                            <p><?= htmlspecialchars($publicacion['raza']) ?> · <?= htmlspecialchars($publicacion['ubicacion']) ?></p>
                            <p>$<?= number_format($publicacion['precio'], 0, ',', '.') ?> COP</p>
                            <p class="<?= (int) $publicacion['vendido'] === 1 ? 'estado-vendido' : 'estado-disponible' ?>">
                                <?= (int) $publicacion['vendido'] === 1 ? 'Vendido' : 'Disponible' ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php if (!$publicaciones): ?><p>Este usuario todavía no tiene publicaciones.</p><?php endif; ?>
            </section>
            <?php if ($esPropietario): ?>
                <section class="zona-cuenta">
                    <h3>Eliminar mi cuenta</h3>
                    <p>Esta acción eliminará tu cuenta y tus publicaciones de forma permanente.</p>
                    <form action="eliminar_cuenta.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_eliminar_cuenta']) ?>">
                        <div class="campo-confirmacion">
                            <input type="password" name="contrasena" placeholder="Contraseña actual" required>
                            <button class="btn-eliminar" type="submit">Eliminar cuenta</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>
        <?php else: ?>
            <section class="perfil"><div><h1>Perfil no encontrado</h1><p>El usuario ya no está disponible.</p></div></section>
        <?php endif; ?>
        <a class="volver" href="pagina.html">&#8592; Volver a PRADUANA</a>
    </main>
</body>
</html>