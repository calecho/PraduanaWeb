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
        imagen: 'TORO%202.jpeg',
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
        imagen: 'TORO%20jeferson.jpg',
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
        imagen: 'terneraholstein.jfif',
        descripcion: 'Novilla joven de excelente estado corporal, dócil y lista para continuar su proceso productivo.',
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
