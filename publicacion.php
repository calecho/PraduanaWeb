<?php
session_start();
require_once "conexion.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = $conexion->prepare("SELECT p.nombre, p.peso, p.ubicacion, p.raza, p.genero, p.edad, p.precio, p.descripcion, p.whatsapp, p.imagen, p.imagenes, p.usuario_id, p.vendido, u.nombre AS usuario_nombre FROM publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$publicacion = $resultado->fetch_assoc();
$esPropietario = $publicacion && isset($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === (int) $publicacion['usuario_id'];
$imagenes = $publicacion && !empty($publicacion["imagenes"]) ? json_decode($publicacion["imagenes"], true) : [];
if (empty($imagenes) && $publicacion) {
    $imagenes = [$publicacion["imagen"]];
}
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $publicacion ? htmlspecialchars($publicacion["nombre"]) : "Publicación no encontrada" ?> - PRADUANA</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            background:#111;
            color:#ffffff;
            line-height:1.6;
        }

        header{
            background:linear-gradient(90deg, #8b1e14, #d94a2b, #f5b942);
            padding:20px 0;
            text-align:center;
            box-shadow:0 4px 12px rgba(0,0,0,0.25);
        }

        header h1{
            font-size:28px;
            letter-spacing:2px;
        }

        .contenedor{
            width:900px;
            max-width:92%;
            margin:auto;
            padding:40px 0 80px;
        }

        .imagen-grande{
            width:auto;
            height:auto;
            max-width:100%;
            max-height:100%;
            object-fit:contain;
            object-position:center;
            background:#000;
            display:block;
            cursor:zoom-in;
        }

        .imagen-grande.imagen-vertical{
            object-fit:contain;
            transform:none;
        }

        .imagen-grande.imagen-horizontal{
            object-fit:contain;
            transform:none;
        }

        .galeria{
            position:relative;
            width:min(100%, 760px);
            margin:0 auto;
            overflow:hidden;
            aspect-ratio:16 / 10;
            background:#080808;
            border-radius:18px;
            box-shadow:0 14px 30px rgba(0,0,0,0.4);
        }

        .galeria.galeria-vertical{
            width:min(100%, 760px);
            aspect-ratio:16 / 10;
        }

        .galeria-pista{
            display:flex;
            height:100%;
            transition:transform .45s ease;
        }

        .galeria-slide{
            flex:0 0 100%;
            min-width:100%;
            height:100%;
            display:grid;
            place-items:center;
            padding:18px;
            background:#000;
        }

        .galeria-flecha{
            position:absolute;
            top:50%;
            transform:translateY(-50%);
            width:44px;
            height:44px;
            border:1px solid rgba(255,255,255,.35);
            border-radius:50%;
            background:rgba(0,0,0,.58);
            color:#fff;
            font-size:28px;
            line-height:1;
            cursor:pointer;
            z-index:2;
        }

        .galeria-flecha:hover{background:#d94a2b}
        .galeria-anterior{left:16px}
        .galeria-siguiente{right:16px}

        .galeria-contador{
            position:absolute;
            right:16px;
            bottom:16px;
            padding:5px 10px;
            border-radius:14px;
            background:rgba(0,0,0,.65);
            color:#fff;
            font-size:13px;
        }

        .galeria-miniaturas{
            width:min(100%, 760px);
            margin:0 auto;
            display:flex;
            gap:10px;
            overflow-x:auto;
            padding:14px 2px 4px;
            justify-content:center;
            scrollbar-width:thin;
        }

        .galeria-miniatura{
            flex:0 0 86px;
            width:86px;
            height:64px;
            padding:0;
            border:2px solid transparent;
            border-radius:8px;
            overflow:hidden;
            background:#222;
            cursor:pointer;
        }

        .galeria-miniatura.activa{border-color:#f5b942}

        .galeria-miniatura img{
            width:100%;
            height:100%;
            object-fit:cover;
            object-position:center 100%;
            display:block;
        }

        .visor-imagen{
            display:none;
            position:fixed;
            inset:0;
            z-index:300;
            padding:30px;
            background:rgba(0,0,0,.92);
            align-items:center;
            justify-content:center;
        }

        .visor-imagen.activo{display:flex}

        .visor-imagen img{
            max-width:92vw;
            max-height:82vh;
            width:auto;
            height:auto;
            object-fit:contain;
            background:#000;
            cursor:zoom-in;
            transition:transform .25s ease;
        }

        .visor-imagen img.ampliada{
            transform:scale(1.3);
            transform-origin:center;
            cursor:zoom-out;
        }

        .visor-cerrar{
            position:absolute;
            top:18px;
            right:24px;
            border:0;
            background:none;
            color:#fff;
            font-size:36px;
            cursor:pointer;
        }

        .visor-ayuda{
            position:absolute;
            bottom:18px;
            color:#ddd;
            font-size:14px;
        }

        .info{
            background:rgba(255,255,255,0.08);
            border:1px solid rgba(255,255,255,0.15);
            border-radius:16px;
            padding:30px;
            margin-top:30px;
        }

        .info h2{
            color:#ffd37a;
            font-size:30px;
            margin-bottom:10px;
        }

        .info .detalle{
            font-size:18px;
            color:#f2f2f2;
            margin-bottom:18px;
        }

        .info .descripcion{
            color:#e2e2e2;
            margin-bottom:25px;
        }

        .btn-whatsapp{
            display:inline-flex;
            align-items:center;
            gap:10px;
            background:#25D366;
            color:#0f0f0f;
            font-weight:700;
            padding:14px 28px;
            border-radius:30px;
            text-decoration:none;
            transition:.3s;
        }

        .btn-whatsapp:hover{
            transform:translateY(-3px) scale(1.03);
            box-shadow:0 8px 18px rgba(0,0,0,0.35);
        }

        .btn-vendido{
            display:block;
            margin-top:14px;
            padding:12px 20px;
            border:0;
            border-radius:24px;
            background:#f5b942;
            color:#40150f;
            font-weight:700;
            cursor:pointer;
        }

        .estado-vendido{
            color:#7ee2a8;
            font-weight:700;
        }

        .estado-disponible{
            color:#ffd37a;
            font-weight:700;
        }

        .vendedor{
            display:inline-block;
            color:#ffd37a;
            margin-bottom:20px;
        }

        .no-encontrada{
            text-align:center;
            padding:80px 0;
            font-size:20px;
        }

        .no-encontrada a{
            color:#ffd37a;
        }
    </style>
</head>
<body>

    <header>
        <h1>PRADUANA</h1>
    </header>

    <div class="contenedor">
        <?php if ($publicacion): ?>
            <div class="galeria" id="galeria" tabindex="0" aria-label="Galería de imágenes">
                <div class="galeria-pista" id="galeria-pista">
                    <?php foreach ($imagenes as $indice => $imagen): ?>
                        <div class="galeria-slide">
                            <img class="imagen-grande" src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($publicacion["nombre"]) ?>, imagen <?= $indice + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($imagenes) > 1): ?>
                    <button class="galeria-flecha galeria-anterior" id="galeria-anterior" type="button" aria-label="Imagen anterior">&#8249;</button>
                    <button class="galeria-flecha galeria-siguiente" id="galeria-siguiente" type="button" aria-label="Imagen siguiente">&#8250;</button>
                <?php endif; ?>
                <span class="galeria-contador" id="galeria-contador">1 / <?= count($imagenes) ?></span>
            </div>
            <div class="galeria-miniaturas" id="galeria-miniaturas">
                <?php foreach ($imagenes as $indice => $imagen): ?>
                    <button class="galeria-miniatura<?= $indice === 0 ? ' activa' : '' ?>" type="button" data-indice="<?= $indice ?>" aria-label="Ver imagen <?= $indice + 1 ?>"><img src="<?= htmlspecialchars($imagen) ?>" alt="Miniatura <?= $indice + 1 ?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="info">
                <h2><?= htmlspecialchars($publicacion["nombre"]) ?></h2>
                <p class="detalle"><?= htmlspecialchars($publicacion["peso"]) ?> kg • <?= htmlspecialchars($publicacion["ubicacion"]) ?></p>
                <p class="detalle">
                    <?= htmlspecialchars($publicacion["raza"]) ?> • <?= htmlspecialchars($publicacion["genero"]) ?> • <?= htmlspecialchars($publicacion["edad"]) ?> meses
                </p>
                <p class="detalle" style="color:#ffd37a; font-size:24px; font-weight:700;">
                    $<?= number_format($publicacion["precio"], 0, ',', '.') ?> COP
                </p>
                <a class="vendedor" href="perfil.php?id=<?= (int) $publicacion["usuario_id"] ?>">
                    Publicado por <?= htmlspecialchars($publicacion["usuario_nombre"]) ?> · Ver perfil
                </a>
                <?php if ((int) $publicacion["vendido"] === 1): ?>
                    <p class="estado-vendido">✓ Ejemplar vendido</p>
                <?php else: ?>
                    <p class="estado-disponible">✓ Ejemplar disponible</p>
                <?php endif; ?>
                <?php if (!empty($publicacion["descripcion"])): ?>
                    <p class="descripcion"><?= nl2br(htmlspecialchars($publicacion["descripcion"])) ?></p>
                <?php endif; ?>

                <a class="btn-whatsapp" target="_blank"
                         data-publicacion-id="<?= (int) $id ?>"
                   href="https://wa.me/<?= htmlspecialchars($publicacion["whatsapp"]) ?>?text=<?= urlencode("Hola, estoy interesado en " . $publicacion["nombre"] . " publicado en PRADUANA.") ?>">
                    Contactar por WhatsApp
                </a>
                <?php if ($esPropietario && (int) $publicacion["vendido"] !== 1): ?>
                    <button class="btn-vendido" id="marcar-vendido" type="button">Marcar como vendido</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-encontrada">
                <p>Esta publicación ya no está disponible.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($publicacion): ?>
        <div class="visor-imagen" id="visor-imagen" role="dialog" aria-label="Imagen ampliada">
            <button class="visor-cerrar" id="visor-cerrar" type="button" aria-label="Cerrar visor">&times;</button>
            <img id="visor-imagen-elemento" src="<?= htmlspecialchars($imagenes[0]) ?>" alt="<?= htmlspecialchars($publicacion["nombre"]) ?>">
            <span class="visor-ayuda">Haz clic en la imagen para ampliar o reducir</span>
        </div>
    <?php endif; ?>

    <?php if ($publicacion): ?>
    <script>
        const imagenesGaleria = <?= json_encode(array_values($imagenes), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
        const galeria = document.getElementById('galeria');
        const pista = document.getElementById('galeria-pista');
        const contador = document.getElementById('galeria-contador');
        const miniaturas = Array.from(document.querySelectorAll('.galeria-miniatura'));
        const visor = document.getElementById('visor-imagen');
        const visorImagen = document.getElementById('visor-imagen-elemento');
        const imagenesPrincipales = Array.from(document.querySelectorAll('.imagen-grande'));
        let indiceActual = 0;

        function ajustarGaleriaAFormato() {
            const tieneImagenVertical = imagenesPrincipales.some(imagen => imagen.naturalHeight > imagen.naturalWidth);
            galeria.classList.toggle('galeria-vertical', tieneImagenVertical);
            const anchoDisponible = galeria.clientWidth - 36;
            const altoDisponible = galeria.clientHeight - 36;
            imagenesPrincipales.forEach(imagen => {
                imagen.classList.toggle('imagen-vertical', imagen.naturalHeight > imagen.naturalWidth);
                imagen.classList.toggle('imagen-horizontal', imagen.naturalWidth >= imagen.naturalHeight);
                imagen.style.maxWidth = `${anchoDisponible}px`;
                imagen.style.maxHeight = `${altoDisponible}px`;
            });
        }

        imagenesPrincipales.forEach(imagen => {
            if (imagen.complete) {
                ajustarGaleriaAFormato();
            } else {
                imagen.addEventListener('load', ajustarGaleriaAFormato);
            }
        });
        window.addEventListener('resize', ajustarGaleriaAFormato);

        function mostrarImagen(indice) {
            indiceActual = (indice + imagenesGaleria.length) % imagenesGaleria.length;
            pista.style.transform = `translateX(-${indiceActual * 100}%)`;
            contador.textContent = `${indiceActual + 1} / ${imagenesGaleria.length}`;
            miniaturas.forEach((miniatura, posicion) => miniatura.classList.toggle('activa', posicion === indiceActual));
            visorImagen.src = imagenesGaleria[indiceActual];
        }

        document.getElementById('galeria-anterior')?.addEventListener('click', () => mostrarImagen(indiceActual - 1));
        document.getElementById('galeria-siguiente')?.addEventListener('click', () => mostrarImagen(indiceActual + 1));
        miniaturas.forEach(miniatura => miniatura.addEventListener('click', () => mostrarImagen(Number(miniatura.dataset.indice))));
        galeria.addEventListener('keydown', evento => {
            if (evento.key === 'ArrowLeft') mostrarImagen(indiceActual - 1);
            if (evento.key === 'ArrowRight') mostrarImagen(indiceActual + 1);
        });
        galeria.addEventListener('click', evento => {
            if (evento.target.classList.contains('imagen-grande')) {
                visor.classList.add('activo');
                visorImagen.classList.remove('ampliada');
            }
        });
        document.getElementById('visor-cerrar').addEventListener('click', () => visor.classList.remove('activo'));
        visor.addEventListener('click', evento => {
            if (evento.target === visor) visor.classList.remove('activo');
        });
        visorImagen.addEventListener('click', () => visorImagen.classList.toggle('ampliada'));
        document.addEventListener('keydown', evento => {
            if (evento.key === 'Escape') visor.classList.remove('activo');
        });

        document.querySelector('.btn-whatsapp')?.addEventListener('click', () => {
            const datos = new FormData();
            datos.append('publicacion_id', '<?= (int) $id ?>');
            fetch('registrar_contacto.php', { method: 'POST', body: datos, keepalive: true }).catch(() => {});
        });

        document.getElementById('marcar-vendido')?.addEventListener('click', async () => {
            if (!confirm('¿Confirmas que este ejemplar fue vendido?')) return;
            const datos = new FormData();
            datos.append('publicacion_id', '<?= (int) $id ?>');
            const respuesta = await fetch('marcar_publicacion_vendida.php', { method: 'POST', body: datos });
            const resultado = await respuesta.json();
            if (resultado.exito) {
                window.location.reload();
            } else {
                alert(resultado.mensaje);
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>
