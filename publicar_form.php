<?php
session_start();
$autenticado = isset($_SESSION['usuario_id']);
$nombreUsuario = $_SESSION['usuario_nombre'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar ganado - PRADUANA</title>
    <style>
        *{box-sizing:border-box;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
        body{margin:0;min-height:100vh;background:linear-gradient(rgba(0,0,0,.7),rgba(0,0,0,.78)),url('imagen-fondo.avif') center/cover fixed;color:#fff}
        header{padding:22px;text-align:center;background:linear-gradient(90deg,#8b1e14,#d94a2b,#f5b942)}
        header a{color:#fff;text-decoration:none;font-size:28px;font-weight:700;letter-spacing:2px}
        main{width:620px;max-width:92%;margin:48px auto 80px}
        .panel{background:rgba(20,20,20,.9);border:1px solid rgba(255,255,255,.18);border-radius:16px;padding:32px;box-shadow:0 18px 45px rgba(0,0,0,.35)}
        h1{margin-top:0;color:#ffd37a} h2{margin-top:0;color:#ffd37a}
        form{display:flex;flex-direction:column;gap:14px}
        input,textarea,select{width:100%;padding:12px 14px;border:1px solid rgba(255,255,255,.25);border-radius:8px;background:rgba(255,255,255,.08);color:#fff;font-size:15px}
        select option{background:#1a1a1a}.campo-imagenes{display:flex;flex-direction:column;gap:8px}.lista-imagenes{display:flex;flex-direction:column;gap:8px}.ayuda,.mensaje{color:#d8d8d8;font-size:14px}.mensaje{text-align:center;display:none}.error{color:#ff8b8b}
        button,.boton{border:0;border-radius:28px;padding:13px 24px;background:linear-gradient(135deg,#d94a2b,#f5b942);color:#fff;font-weight:700;font-size:15px;cursor:pointer;text-decoration:none;text-align:center}
        .acciones{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-top:22px}.acciones a{color:#ffd37a}.oculto{display:none}
    </style>
</head>
<body>
<header><a href="pagina.html">PRADUANA</a></header>
<main>
    <?php if ($autenticado): ?>
        <section class="panel">
            <h1>Publicar ganado</h1>
            <p>Publicando como <strong><?= htmlspecialchars($nombreUsuario) ?></strong></p>
            <form id="form-publicar" enctype="multipart/form-data">
                <input type="text" name="nombre" placeholder="Nombre del lote" required>
                <input type="text" name="peso" placeholder="Peso (kg)" required>
                <input type="text" name="ubicacion" placeholder="Ubicación" required>
                <input type="text" name="raza" placeholder="Raza" required>
                <select name="genero" required><option value="" disabled selected>Selecciona el género</option><option value="Macho">Macho</option><option value="Hembra">Hembra</option></select>
                <input type="number" name="edad" placeholder="Edad aproximada (meses)" min="0" required>
                <input type="number" name="precio" placeholder="Precio (COP)" min="0" required>
                <input type="text" name="whatsapp" placeholder="WhatsApp con código de país" required>
                <div class="campo-imagenes"><strong>Imágenes del animal</strong><div class="lista-imagenes">
                    <input type="file" name="imagenes[]" accept="image/*" required>
                    <input type="file" name="imagenes[]" accept="image/*" required>
                    <input type="file" name="imagenes[]" accept="image/*">
                    <input type="file" name="imagenes[]" accept="image/*">
                    <input type="file" name="imagenes[]" accept="image/*">
                </div><span class="ayuda">Sube entre 2 y 5 imágenes.</span></div>
                <textarea name="descripcion" placeholder="Descripción" rows="4"></textarea>
                <button type="submit">Publicar ganado</button>
            </form>
            <p class="mensaje" id="mensaje-publicar"></p>
            <div class="acciones"><a href="pagina.html">Volver al inicio</a><a href="cerrar_sesion.php">Cerrar sesión</a></div>
        </section>
    <?php else: ?>
        <section class="panel" id="panel-login">
            <h2>Inicia sesión para publicar</h2>
            <p>Necesitas una cuenta para publicar y administrar tu ganado.</p>
            <form id="form-login"><input type="email" name="correo" placeholder="Correo electrónico" required><input type="password" name="contrasena" placeholder="Contraseña" required><button type="submit">Iniciar sesión</button></form>
            <p class="mensaje" id="mensaje-login"></p>
            <div class="acciones"><a href="pagina.html">Volver al inicio</a><a href="pagina.html#registro">Crear una cuenta</a></div>
        </section>
    <?php endif; ?>
</main>
<script>
const mostrar = (elemento, texto, error = false) => { elemento.textContent = texto; elemento.className = error ? 'mensaje error' : 'mensaje'; elemento.style.display = 'block'; };
const parsear = async (respuesta) => { const texto = await respuesta.text(); try { return JSON.parse(texto); } catch (e) { throw new Error('El servidor devolvió una respuesta inválida.'); } };
<?php if ($autenticado): ?>
document.getElementById('form-publicar').addEventListener('submit', async (evento) => { evento.preventDefault(); const formulario = evento.target; const mensaje = document.getElementById('mensaje-publicar'); const imagenes = Array.from(formulario.querySelectorAll('input[name="imagenes[]"]')).map(input => input.files[0]).filter(Boolean); if (imagenes.length < 2 || imagenes.length > 5) { mostrar(mensaje, 'Debes seleccionar entre 2 y 5 imágenes.', true); return; } try { const respuesta = await fetch('publicar.php', { method:'POST', body:new FormData(formulario) }); const datos = await parsear(respuesta); if (!datos.exito) { mostrar(mensaje, datos.mensaje, true); return; } window.location.href = 'pagina.html#ganado'; } catch (error) { mostrar(mensaje, error.message || 'No se pudo conectar con el servidor.', true); } });
<?php else: ?>
document.getElementById('form-login').addEventListener('submit', async (evento) => { evento.preventDefault(); const mensaje = document.getElementById('mensaje-login'); try { const respuesta = await fetch('login.php', { method:'POST', body:new FormData(evento.target) }); const datos = await parsear(respuesta); if (!datos.exito) { mostrar(mensaje, datos.mensaje, true); return; } window.location.reload(); } catch (error) { mostrar(mensaje, error.message || 'No se pudo conectar con el servidor.', true); } });
<?php endif; ?>
</script>
</body>
</html>