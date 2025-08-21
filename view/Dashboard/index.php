<?php
session_start();

// Verificar que el usuario esté logueado y sea gerente
if (!isset($_SESSION["user_id"]) || $_SESSION["rol_id"] != 1) {
    header("Location: /ESTADIAS/index.php");
    exit;
}

$title = "Dashboard Gerencial - GTM";
?>

<?php require_once("../MainHead/head.php"); ?>

<body class="with-side-menu">
    <?php require_once("../MainHead/header.php"); ?>

    <div class="mobile-menu-left-overlay"></div>
    
    <?php require_once("../MainHead/nav.php"); ?>

    <div class="page-content">
        <div class="container-fluid">
            
            <!-- Título de la página -->
            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h2>📊 Dashboard Gerencial</h2>
                            <div class="subtitle">Panel de control y métricas ejecutivas</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Tarjetas de métricas principales -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card metric-card metric-primary">
                        <div class="card-body">
                            <div class="metric-icon">
                                <i class="font-icon font-icon-check-circle"></i>
                            </div>
                            <div class="metric-content">
                                <h3 class="metric-value stats-tickets-semana">-</h3>
                                <p class="metric-label">Tickets Resueltos (Semana)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card metric-card metric-warning">
                        <div class="card-body">
                            <div class="metric-icon">
                                <i class="font-icon font-icon-clock"></i>
                            </div>
                            <div class="metric-content">
                                <h3 class="metric-value stats-tickets-pendientes">-</h3>
                                <p class="metric-label">Tickets Pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card metric-card metric-info">
                        <div class="card-body">
                            <div class="metric-icon">
                                <i class="font-icon font-icon-speed"></i>
                            </div>
                            <div class="metric-content">
                                <h3 class="metric-value stats-tiempo-promedio">-</h3>
                                <p class="metric-label">Tiempo Promedio</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card metric-card metric-success">
                        <div class="card-body">
                            <div class="metric-icon">
                                <i class="font-icon font-icon-user"></i>
                            </div>
                            <div class="metric-content">
                                <h3 class="metric-value">95%</h3>
                                <p class="metric-label">Satisfacción Cliente</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de notificaciones críticas -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                🚨 Notificaciones Críticas
                                <button class="btn btn-sm btn-outline-primary float-right" onclick="refreshCriticalNotifications()">
                                    <i class="font-icon font-icon-refresh"></i> Actualizar
                                </button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="critical-notifications-container">
                                <div class="text-center py-4">
                                    <i class="font-icon font-icon-refresh spinning text-muted"></i>
                                    <p class="text-muted">Cargando notificaciones...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">📈 Tendencias</h4>
                        </div>
                        <div class="card-body">
                            <div class="trend-item">
                                <div class="trend-label">Productividad Semanal</div>
                                <div class="trend-value trend-up">+12%</div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-label">Tickets Críticos</div>
                                <div class="trend-value trend-down">-8%</div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-label">Tiempo Resolución</div>
                                <div class="trend-value trend-down">-15%</div>
                            </div>
                            <div class="trend-item">
                                <div class="trend-label">Inventario Crítico</div>
                                <div class="trend-value trend-neutral">0%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de acciones rápidas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title">⚡ Acciones Rápidas</h4>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="/ESTADIAS/view/ConsultarTicket/" class="btn btn-outline-primary btn-block mb-2">
                                    <i class="font-icon font-icon-eye"></i> Ver Todos los Tickets
                                </a>
                                <a href="/ESTADIAS/view/Inventario/" class="btn btn-outline-warning btn-block mb-2">
                                    <i class="font-icon font-icon-package"></i> Revisar Inventario
                                </a>
                                <a href="/ESTADIAS/view/GestionUsuarios/" class="btn btn-outline-info btn-block mb-2">
                                    <i class="font-icon font-icon-users"></i> Gestionar Usuarios
                                </a>
                                <button class="btn btn-outline-success btn-block" onclick="generateReport()">
                                    <i class="font-icon font-icon-download"></i> Generar Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de tickets críticos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">🎯 Tickets Que Requieren Atención</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="critical-tickets-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Título</th>
                                            <th>Usuario</th>
                                            <th>Prioridad</th>
                                            <th>Días Pendiente</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Contenido cargado por JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--.container-fluid-->
    </div><!--.page-content-->

    <!-- Modal para detalles de ticket -->
    <div class="modal fade" id="ticketDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ticketDetailContent">
                    <!-- Contenido cargado dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="viewFullTicket()">Ver Ticket Completo</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("../MainHead/js.php"); ?>

    <script>
        // Funciones específicas del dashboard gerencial
        let currentTicketId = null;

        function refreshCriticalNotifications() {
            if (window.gerenteNotificaciones) {
                window.gerenteNotificaciones.cargarNotificaciones();
                loadCriticalNotificationsDetailed();
            }
        }

        function loadCriticalNotificationsDetailed() {
            $.ajax({
                url: '/ESTADIAS/controller/notificaciones.php?op=listar&limit=10',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayCriticalNotifications(response.notificaciones);
                    }
                },
                error: function() {
                    $('#critical-notifications-container').html(`
                        <div class="alert alert-warning">
                            <i class="font-icon font-icon-warning"></i>
                            Error al cargar las notificaciones críticas
                        </div>
                    `);
                }
            });
        }

        function displayCriticalNotifications(notifications) {
            const container = $('#critical-notifications-container');
            
            if (notifications.length === 0) {
                container.html(`
                    <div class="text-center py-4">
                        <i class="font-icon font-icon-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="text-success mt-2">¡Excelente!</h5>
                        <p class="text-muted">No hay notificaciones críticas pendientes</p>
                    </div>
                `);
                return;
            }

            let html = '<div class="critical-notifications-list">';
            
            notifications.forEach(function(notif) {
                const priorityClass = getPriorityDisplayClass(notif.tipo);
                const iconClass = getNotificationIcon(notif.tipo);
                
                html += `
                    <div class="critical-notification-item ${priorityClass}" data-id="${notif.id}" data-tipo="${notif.tipo}">
                        <div class="notification-icon">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="notification-details">
                            <div class="notification-title">${notif.mensaje}</div>
                            <div class="notification-meta">
                                <span class="badge ${notif.color}">${notif.categoria}</span>
                                <span class="text-muted ml-2">${notif.tiempo}</span>
                            </div>
                        </div>
                        <div class="notification-actions">
                            ${getNotificationActions(notif.tipo, notif.id)}
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.html(html);
        }

        function getPriorityDisplayClass(tipo) {
            switch(tipo) {
                case 'ticket_alta_prioridad':
                    return 'priority-critical';
                case 'inventario_critico':
                    return 'priority-critical';
                case 'ticket_pendiente_largo':
                    return 'priority-high';
                default:
                    return 'priority-normal';
            }
        }

        function getNotificationIcon(tipo) {
            switch(tipo) {
                case 'ticket_alta_prioridad':
                    return 'font-icon font-icon-fire text-danger';
                case 'inventario_critico':
                    return 'font-icon font-icon-warning text-danger';
                case 'ticket_pendiente_largo':
                    return 'font-icon font-icon-clock text-warning';
                default:
                    return 'font-icon font-icon-info text-info';
            }
        }

        function getNotificationActions(tipo, id) {
            if (tipo.startsWith('ticket_')) {
                return `
                    <button class="btn btn-sm btn-outline-primary" onclick="viewTicketDetails(${id})">
                        <i class="font-icon font-icon-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success ml-1" onclick="goToTicket(${id})">
                        <i class="font-icon font-icon-arrow-right"></i>
                    </button>
                `;
            } else if (tipo === 'inventario_critico') {
                return `
                    <button class="btn btn-sm btn-outline-warning" onclick="goToInventory(${id})">
                        <i class="font-icon font-icon-package"></i>
                    </button>
                `;
            }
            return '';
        }

        function viewTicketDetails(ticketId) {
            currentTicketId = ticketId;
            // Aquí cargarías los detalles del ticket vía AJAX
            $('#ticketDetailModal').modal('show');
        }

        function goToTicket(ticketId) {
            window.open(`/ESTADIAS/view/detalleTicket/?ID=${ticketId}`, '_blank');
        }

        function goToInventory(inventoryId) {
            window.open('/ESTADIAS/view/Inventario/', '_blank');
        }

        function viewFullTicket() {
            if (currentTicketId) {
                window.open(`/ESTADIAS/view/detalleTicket/?ID=${currentTicketId}`, '_blank');
            }
        }

        function generateReport() {
            swal({
                title: "Generar Reporte",
                text: "¿Qué tipo de reporte deseas generar?",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Reporte Semanal",
                cancelButtonText: "Reporte Mensual"
            }, function(isConfirm) {
                if (isConfirm) {
                    window.open('/ESTADIAS/controller/reportes.php?tipo=semanal', '_blank');
                } else {
                    window.open('/ESTADIAS/controller/reportes.php?tipo=mensual', '_blank');
                }
            });
        }

        // Inicializar dashboard al cargar la página
        $(document).ready(function() {
            setTimeout(function() {
                refreshCriticalNotifications();
            }, 1000);

            // Actualizar cada 5 minutos
            setInterval(function() {
                refreshCriticalNotifications();
            }, 300000);
        });
    </script>

    <style>
        .metric-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-card .card-body {
            display: flex;
            align-items: center;
            padding: 1.5rem;
        }

        .metric-icon {
            font-size: 3rem;
            margin-right: 1rem;
            opacity: 0.8;
        }

        .metric-primary .metric-icon { color: #007bff; }
        .metric-warning .metric-icon { color: #ffc107; }
        .metric-info .metric-icon { color: #17a2b8; }
        .metric-success .metric-icon { color: #28a745; }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .metric-label {
            color: #6c757d;
            margin: 0;
            font-size: 0.875rem;
        }

        .critical-notification-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border-left: 4px solid #ddd;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .critical-notification-item:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
        }

        .critical-notification-item.priority-critical {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }

        .critical-notification-item.priority-high {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }

        .notification-icon {
            margin-right: 1rem;
            font-size: 1.5rem;
        }

        .notification-details {
            flex-grow: 1;
        }

        .notification-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .notification-meta {
            font-size: 0.875rem;
        }

        .notification-actions {
            margin-left: 1rem;
        }

        .trend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .trend-item:last-child {
            border-bottom: none;
        }

        .trend-value {
            font-weight: bold;
        }

        .trend-up { color: #28a745; }
        .trend-down { color: #dc3545; }
        .trend-neutral { color: #6c757d; }

        .quick-actions .btn {
            text-align: left;
        }

        .quick-actions .btn i {
            width: 20px;
        }

        .spinning {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

</body>
</html>
