CREATE DATABASE IF NOT EXISTS praduana_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE praduana_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    ventas_concretadas INT NOT NULL DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    peso VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(150) NOT NULL,
    raza VARCHAR(100) NOT NULL,
    genero ENUM('Macho','Hembra') NOT NULL,
    edad INT NOT NULL COMMENT 'edad aproximada en meses',
    precio DECIMAL(12,2) NOT NULL,
    descripcion TEXT,
    whatsapp VARCHAR(20) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    imagenes TEXT NULL,
    vendido TINYINT(1) NOT NULL DEFAULT 0,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_publicaciones_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS contactos_publicacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT NOT NULL,
    vendedor_id INT NOT NULL,
    visitante_clave VARCHAR(128) NOT NULL,
    fecha_contacto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY contacto_unico (publicacion_id, visitante_clave),
    CONSTRAINT fk_contactos_publicacion FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_contactos_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

