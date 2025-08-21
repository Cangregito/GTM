/**
 * Script para manejar las notificaciones en el header
 */

$(document).ready(function() {
    // Ejecutar para gerentes o usuarios con acceso al inventario
    if (esGerente() || tieneAccesoInventario()) {
        // Cargar las notificaciones al cargar la página
        cargarNotificaciones();
        
        // Configurar un intervalo para actualizar las notificaciones cada 5 minutos
        setInterval(function() {
            cargarNotificaciones();
        }, 5 * 60 * 1000); // 5 minutos
        
        // Añadir event listener para el click en la campana de notificaciones
        $('#dd-notification').on('click', function() {
            // Si hay notificaciones sin leer, actualizarlas
            cargarNotificaciones();
        });
    }
});

/**
 * Verifica si el usuario actual es gerente
 * @return {Boolean} Verdadero si es gerente, falso si no
 */
function esGerente() {
    const rol_id = $('#rol_idx').val() || userRole;
    return rol_id == '1';
}

/**
 * Verifica si el usuario actual tiene acceso al módulo de inventario
 * @return {Boolean} Verdadero si tiene acceso, falso si no
 */
function tieneAccesoInventario() {
    // Obtener datos del usuario actual
    const rol_id = $('#rol_idx').val() || userRole;
    
    // Si no hay información de usuario, no tiene acceso
    if (!rol_id) return false;
    
    // Obtener el nombre y apellido del usuario desde el span que lo muestra
    const nombreCompleto = $('.lblcontactonomx').text().trim();
    
    // Verificar si es un usuario con acceso (rol_id = 2 y es admin o Mantenimiento planta)
    return (rol_id == 2 && 
           (nombreCompleto.includes('admin') || 
            (nombreCompleto.includes('Mantenimiento') && nombreCompleto.includes('planta'))));
}

/**
 * Carga y actualiza las notificaciones en el header
 */
function cargarNotificaciones() {
    // Primero cargamos la cantidad de notificaciones para actualizar el contador
    $.ajax({
        url: '/ESTADIAS/controller/notificaciones.php?op=contar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success') {
                actualizarContadorNotificaciones(data.total);
                
                // Si hay notificaciones, cargar el listado
                if (data.total > 0) {
                    cargarListaNotificaciones();
                } else {
                    // Si no hay notificaciones, mostrar mensaje de "No hay notificaciones"
                    const html = `
                        <div class="dropdown-menu-notif-header">
                            Notificaciones
                            <span class="label label-pill label-default">0</span>
                        </div>
                        <div class="dropdown-menu-notif-list">
                            <div class="dropdown-menu-notif-item text-center">
                                <p class="color-blue-grey-lighter">No hay notificaciones pendientes</p>
                            </div>
                        </div>
                    `;
                    $('.dropdown-menu-notif').html(html);
                }
            }
        },
        error: function(error) {
            console.error('Error al cargar contador de notificaciones:', error);
        }
    });
}

/**
 * Actualiza el contador de notificaciones en el badge
 * @param {Number} total Número total de notificaciones
 */
function actualizarContadorNotificaciones(total) {
    // Si hay notificaciones, mostrar el contador y agregar la clase 'active'
    if (total > 0) {
        $('#dd-notification').addClass('active');
    } else {
        $('#dd-notification').removeClass('active');
    }
}

/**
 * Carga la lista de notificaciones del inventario
 */
function cargarListaNotificaciones() {
    $.ajax({
        url: '/ESTADIAS/controller/notificaciones.php?op=listar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success') {
                const notificaciones = data.notificaciones;
                let html = '';
                
                // Construir el encabezado con el contador
                html += `
                    <div class="dropdown-menu-notif-header">
                        Notificaciones
                        <span class="label label-pill ${notificaciones.length > 0 ? 'label-danger' : 'label-default'}">${notificaciones.length}</span>
                    </div>
                `;
                
                // Construir la lista de notificaciones
                html += '<div class="dropdown-menu-notif-list">';
                
                if (notificaciones.length > 0) {
                    notificaciones.forEach(function(notif) {
                        html += `
                            <div class="dropdown-menu-notif-item">
                                <div class="font-icon ${notif.icono} notif-icon"></div>
                                <div class="dot"></div>
                                <div>${notif.mensaje}</div>
                                ${notif.categoria ? `<div class="color-blue-grey-lighter categoria-notif">${notif.categoria}</div>` : ''}
                                <div class="color-blue-grey-lighter">${notif.tiempo}</div>
                            </div>
                        `;
                    });
                } else {
                    html += `
                        <div class="dropdown-menu-notif-item text-center">
                            <p class="color-blue-grey-lighter">No hay notificaciones pendientes</p>
                        </div>
                    `;
                }
                
                html += '</div>';
                
                // Agregar enlace a la página de inventario
                html += `
                    <div class="dropdown-menu-notif-more">
                        <a href="/ESTADIAS/view/Inventario/">Ver inventario</a>
                    </div>
                `;
                
                // Actualizar el contenido del dropdown
                $('.dropdown-menu-notif').html(html);
            }
        },
        error: function(error) {
            console.error('Error al cargar notificaciones:', error);
        }
    });
}
