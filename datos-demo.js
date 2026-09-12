const publicacionesDemo = [
    {
        id: 1,
        usuario_id: 1,
        usuario_nombre: 'Hacienda El Roble',
        correo: 'hacienda.roble@praduana.co',
        fecha_registro: '2026-08-14',
        nombre: 'Toro Brahman El Roble',
        peso: '520',
        ubicacion: 'Ubalá',
        raza: 'Brahman',
        genero: 'Macho',
        edad: 30,
        precio: 12800000,
        whatsapp: '573212585926',
        imagen: 'Captura%20de%20pantalla%202026-09-12%20110730.png',
        descripcion: 'Toro de excelente conformación, fuerte y saludable. Ideal para mejorar el hato y reproducción.',
        vendido: 0
    },
    {
        id: 2,
        usuario_id: 2,
        usuario_nombre: 'Finca La Esperanza',
        correo: 'la.esperanza@praduana.co',
        fecha_registro: '2026-08-21',
        nombre: 'Toro Gyr La Esperanza',
        peso: '465',
        ubicacion: 'Gachetá',
        raza: 'Gyr',
        genero: 'Macho',
        edad: 26,
        precio: 11500000,
        whatsapp: '573108765432',
        imagen: 'Captura%20de%20pantalla%202026-09-12%20110816.png',
        descripcion: 'Ejemplar manso y bien cuidado, con buena genética y adaptación a clima de montaña.',
        vendido: 0
    },
    {
        id: 3,
        usuario_id: 3,
        usuario_nombre: 'Ganadería Los Arrayanes',
        correo: 'arrayanes@praduana.co',
        fecha_registro: '2026-08-29',
        nombre: 'Novilla Simmental Arrayanes',
        peso: '390',
        ubicacion: 'Gachalá',
        raza: 'Simmental',
        genero: 'Hembra',
        edad: 22,
        precio: 9800000,
        whatsapp: '573001234567',
        imagen: 'Captura%20de%20pantalla%202026-09-12%20105659.png',
        descripcion: 'Novilla joven de excelente estado corporal, dócil y lista para continuar su proceso productivo.',
        vendido: 0
    },
    {
        id: 4,
        usuario_id: 1,
        usuario_nombre: 'Hacienda El Roble',
        correo: 'hacienda.roble@praduana.co',
        fecha_registro: '2026-08-14',
        nombre: 'Ternera Holstein Vendida',
        peso: '280',
        ubicacion: 'Ubalá',
        raza: 'Holstein',
        genero: 'Hembra',
        edad: 14,
        precio: 6800000,
        whatsapp: '573212585926',
        imagen: 'terneraholstein.jfif',
        descripcion: 'Ejemplar vendido. Esta publicación se conserva en el perfil del vendedor como historial de ventas.',
        vendido: 1
    },
    {
        id: 5,
        usuario_id: 4,
        usuario_nombre: 'Ganadería El Horizonte',
        correo: 'el.horizonte@praduana.co',
        fecha_registro: '2026-09-02',
        nombre: 'Toro Rojo El Horizonte',
        peso: '490',
        ubicacion: 'Guasca',
        raza: 'Cebú',
        genero: 'Macho',
        edad: 28,
        precio: 10900000,
        whatsapp: '573155555555',
        imagen: 'Captura%20de%20pantalla%202026-09-12%20110830.png',
        descripcion: 'Toro joven, fuerte y bien adaptado al terreno. Excelente opción para fortalecer el hato.',
        vendido: 0
    },
    {
        id: 6,
        usuario_id: 5,
        usuario_nombre: 'Jeferson Benítez',
        correo: 'jeferson.benitez@praduana.co',
        fecha_registro: '2026-09-05',
        nombre: 'Toro Brahman Horizonte',
        peso: '510',
        ubicacion: 'Ubalá',
        raza: 'Brahman',
        genero: 'Macho',
        edad: 31,
        precio: 12400000,
        whatsapp: '573155555555',
        imagen: 'TORO%20jeferson.jpg',
        descripcion: 'Ejemplar de buena presencia y condición corporal, disponible para nuevos negocios ganaderos.',
        vendido: 0
    }
];

function obtenerPublicacionDemo(id) {
    return publicacionesDemo.find(publicacion => publicacion.id === Number(id));
}

function obtenerUsuarioDemo(id) {
    const publicacion = publicacionesDemo.find(item => item.usuario_id === Number(id));
    return publicacion ? {
        id: publicacion.usuario_id,
        nombre: publicacion.usuario_nombre,
        correo: publicacion.correo,
        fecha_registro: publicacion.fecha_registro
    } : null;
}

function formatoPrecioDemo(precio) {
    return Number(precio).toLocaleString('es-CO');
}
