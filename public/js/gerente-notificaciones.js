/**
 * Manejo de notificaciones específicas para gerentes
 */

class GerenteNotificaciones {
    constructor() {
        this.inicializar();
        this.configurarActualizacionAutomatica();
    }

    inicializar() {
        this.cargarNotificaciones();
        this.cargarEstadisticas();
        this.configurarEventos();
    }

    configurarEventos() {
        // Actualizar notificaciones al hacer click en el dropdown
        $(document).on('click', '.dropdown-notification', () => {
            this.cargarNotificaciones();
        });

        // Marcar notificación como vista al hacer click
        $(document).on('click', '.notification-item', function() {
            $(this).removeClass('unread');
        });
    }

    cargarNotificaciones() {
        $.ajax({
            url: '/ESTADIAS/controller/notificaciones.php?op=listar&limit=8',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.status === 'success') {
                    this.mostrarNotificaciones(response.notificaciones);
                    this.actualizarContador();
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al cargar notificaciones:', error);
            }
        });
    }

    mostrarNotificaciones(notificaciones) {
        const container = $('.dropdown-menu-notif .jscroll');
        container.empty();

        if (notificaciones.length === 0) {
            container.html(`
                <div class="dropdown-item text-center text-muted py-3">
                    <i class="font-icon-check-circle"></i>
                    <br>No hay notificaciones pendientes
                </div>
            `);
            return;
        }

        notificaciones.forEach((notif, index) => {
            const notifHtml = this.crearElementoNotificacion(notif);
            container.append(notifHtml);
        });

        // Agregar botón para ver todas las notificaciones
        container.append(`
            <div class="dropdown-item text-center border-top">
                <a href="/ESTADIAS/view/Dashboard/" class="btn btn-sm btn-outline-primary">
                    Ver Panel de Control
                </a>
            </div>
        `);
    }

    crearElementoNotificacion(notif) {
        const prioridadClass = this.getPrioridadClass(notif.tipo);
        const iconoColor = this.getIconoColor(notif.tipo);
        
        return `
            <div class="dropdown-item notification-item ${prioridadClass}" data-id="${notif.id}" data-tipo="${notif.tipo}">
                <div class="notification-content">
                    <div class="notification-header">
                        <i class="${notif.icono} ${iconoColor}"></i>
                        <span class="${notif.color}">${notif.categoria}</span>
                        <small class="text-muted float-right">${notif.tiempo}</small>
                    </div>
                    <div class="notification-message">
                        ${notif.mensaje}
                    </div>
                </div>
            </div>
        `;
    }

    getPrioridadClass(tipo) {
        switch(tipo) {
            case 'ticket_alta_prioridad':
                return 'priority-high';
            case 'inventario_critico':
                return 'priority-high';
            case 'ticket_pendiente_largo':
                return 'priority-medium';
            default:
                return 'priority-normal';
        }
    }

    getIconoColor(tipo) {
        switch(tipo) {
            case 'ticket_alta_prioridad':
                return 'text-danger';
            case 'inventario_critico':
                return 'text-danger';
            case 'ticket_pendiente_largo':
                return 'text-warning';
            default:
                return 'text-info';
        }
    }

    actualizarContador() {
        $.ajax({
            url: '/ESTADIAS/controller/notificaciones.php?op=contar',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.status === 'success') {
                    const badge = $('.header-alarm .label');
                    const count = response.total;
                    
                    if (count > 0) {
                        badge.text(count > 99 ? '99+' : count).show();
                        $('.header-alarm').addClass('active');
                    } else {
                        badge.hide();
                        $('.header-alarm').removeClass('active');
                    }
                }
            }
        });
    }

    cargarEstadisticas() {
        $.ajax({
            url: '/ESTADIAS/controller/notificaciones.php?op=estadisticas',
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                if (response.status === 'success') {
                    this.mostrarEstadisticas(response.estadisticas);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al cargar estadísticas:', error);
            }
        });
    }

    mostrarEstadisticas(stats) {
        // Actualizar estadísticas en el header o dashboard si existen
        if ($('.stats-tickets-semana').length) {
            $('.stats-tickets-semana').text(stats.tickets_resueltos_semana);
        }
        if ($('.stats-tickets-pendientes').length) {
            $('.stats-tickets-pendientes').text(stats.tickets_pendientes);
        }
        if ($('.stats-tiempo-promedio').length) {
            $('.stats-tiempo-promedio').text(stats.tiempo_promedio_resolucion + ' días');
        }

        // Agregar indicador de rendimiento en el header
        this.actualizarIndicadorRendimiento(stats);
    }

    actualizarIndicadorRendimiento(stats) {
        const headerStats = $('.header-performance-indicator');
        if (headerStats.length === 0) return;

        let statusClass = 'text-success';
        let statusIcon = 'font-icon-check-circle';
        let statusText = 'Óptimo';

        // Determinar estado según métricas
        if (stats.tickets_pendientes > 10 || stats.tiempo_promedio_resolucion > 5) {
            statusClass = 'text-danger';
            statusIcon = 'font-icon-warning';
            statusText = 'Requiere Atención';
        } else if (stats.tickets_pendientes > 5 || stats.tiempo_promedio_resolucion > 3) {
            statusClass = 'text-warning';
            statusIcon = 'font-icon-clock';
            statusText = 'Monitorear';
        }

        headerStats.html(`
            <i class="${statusIcon} ${statusClass}"></i>
            <span class="${statusClass}">${statusText}</span>
        `);
    }

    configurarActualizacionAutomatica() {
        // Actualizar cada 2 minutos
        setInterval(() => {
            this.actualizarContador();
            this.cargarEstadisticas();
        }, 120000);

        // Actualizar notificaciones cada 5 minutos
        setInterval(() => {
            this.cargarNotificaciones();
        }, 300000);
    }

    // Método para mostrar notificación toast personalizada
    mostrarNotificacionToast(mensaje, tipo = 'info') {
        const toastHtml = `
            <div class="toast gerente-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="font-icon-${tipo === 'danger' ? 'fire' : tipo === 'warning' ? 'warning' : 'info'}"></i>
                    <strong class="mr-auto">Notificación Gerencial</strong>
                    <small class="text-muted">Ahora</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${mensaje}
                </div>
            </div>
        `;

        $('body').append(toastHtml);
        $('.gerente-toast').toast('show');
        
        // Remover el toast después de que se oculte
        $('.gerente-toast').on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
}

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    // Solo inicializar para gerentes
    if (typeof userRole !== 'undefined' && userRole == '1') {
        window.gerenteNotificaciones = new GerenteNotificaciones();
    }
});
