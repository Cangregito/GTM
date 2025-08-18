<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: /ESTADIAS/index.php");
    exit;
}

require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Centro de Ayuda</title>
<html>
<head>
    <style>
        .help-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .help-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #3c8dbc 0%, #2d6284 100%);
            color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .help-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .help-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .help-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab-item {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            position: relative;
            transition: all 0.3s;
        }
        
        .tab-item.active {
            color: #3c8dbc;
        }
        
        .tab-item.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #3c8dbc;
        }
        
        .tab-item:hover {
            color: #3c8dbc;
        }
        
        .tab-content {
            display: none;
            padding: 20px 0;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .guide-card, .faq-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }
        
        .guide-card:hover, .faq-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .guide-card .card-header, .faq-card .card-header {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaecef;
        }
        
        .guide-card .card-body, .faq-card .card-body {
            padding: 20px;
        }
        
        .guide-card h3, .faq-card h3 {
            margin: 0;
            color: #334155;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .guide-card h3 i, .faq-card h3 i {
            margin-right: 10px;
            color: #3c8dbc;
        }
        
        .guide-card img {
            max-width: 100%;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #eaecef;
        }
        
        .step {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eaecef;
        }
        
        .step:last-child {
            border-bottom: none;
        }
        
        .step-number {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            background-color: #3c8dbc;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: 600;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-content h4 {
            margin-top: 0;
            color: #334155;
            font-size: 16px;
        }
        
        .tip-box {
            background-color: #f0f7ff;
            border-left: 4px solid #3c8dbc;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        
        .tip-box i {
            color: #3c8dbc;
            margin-right: 10px;
        }
        
        .faq-item {
            margin-bottom: 15px;
        }
        
        .faq-question {
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        
        .faq-question:hover {
            background-color: #e9ecef;
        }
        
        .faq-question i {
            transition: transform 0.3s;
        }
        
        .faq-answer {
            display: none;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        
        .faq-item.active .faq-question {
            background-color: #e9ecef;
            border-radius: 5px 5px 0 0;
        }
        
        .faq-item.active .faq-answer {
            display: block;
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .contact-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #334155;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #dce6f1;
            border-radius: 5px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
        }
        
        textarea.form-control {
            height: 120px;
            resize: vertical;
        }
        
        .btn-primary {
            background-color: #3c8dbc;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2d6284;
        }
        
        .search-container {
            margin-bottom: 30px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #dce6f1;
            border-radius: 30px;
            font-size: 16px;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            border-color: #3c8dbc;
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
            outline: none;
        }
        
        .search-icon {
            position: absolute;
            right: 20px;
            top: 12px;
            color: #3c8dbc;
        }
        
        .contact-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .contact-info h3 {
            color: #334155;
            margin-top: 0;
            font-size: 18px;
        }
        
        .contact-info p {
            margin-bottom: 10px;
        }
        
        .contact-info i {
            width: 25px;
            color: #3c8dbc;
        }
        
        .guide-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .guide-category {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .guide-category:hover {
            background-color: #e3f0fa;
            transform: translateY(-3px);
        }
        
        .guide-category.active {
            background-color: #3c8dbc;
            color: white;
        }
        
        .guide-category i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #3c8dbc;
        }
        
        .guide-category.active i {
            color: white;
        }
    </style>
</head>
<body class="with-side-menu">
    <?php
    require_once('../MainHead/header.php');
    ?>
    <div class="mobile-menu-left-overlay"></div>

    <?php
    require_once('../MainHead/nav.php');
    ?>

    <div class="page-content">
        <div class="container-fluid">
            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Centro de Ayuda</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li class="active">Centro de Ayuda</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <div class="help-container">
                <div class="help-header">
                    <h1><i class="fa fa-life-ring"></i> Centro de Ayuda GTM</h1>
                    <p>Encuentra respuestas a tus preguntas y aprende cómo usar el sistema GTM de manera eficiente.</p>
                </div>

                <div class="search-container position-relative">
                    <input type="text" class="search-input" id="helpSearch" placeholder="Buscar ayuda...">
                    <i class="fa fa-search search-icon"></i>
                </div>

                <div class="help-tabs">
                    <div class="tab-item active" data-tab="guides">
                        <i class="fa fa-book"></i> Guías de Uso
                    </div>
                    <div class="tab-item" data-tab="faq">
                        <i class="fa fa-question-circle"></i> Preguntas Frecuentes
                    </div>
                    <div class="tab-item" data-tab="contact">
                        <i class="fa fa-envelope"></i> Contactar Soporte
                    </div>
                </div>

                <!-- Tab: Guías de Uso -->
                <div class="tab-content active" id="guides">
                    <h3 class="mb-4">Seleccione una categoría:</h3>
                    
                    <div class="guide-categories">
                        <div class="guide-category active" data-category="tickets">
                            <i class="fa fa-ticket"></i>
                            <h4>Tickets</h4>
                        </div>
                        <div class="guide-category" data-category="admin">
                            <i class="fa fa-cogs"></i>
                            <h4>Uso General</h4>
                        </div>
                        <div class="guide-category" data-category="reports">
                            <i class="fa fa-bar-chart"></i>
                            <h4>Reportes</h4>
                        </div>
                        <div class="guide-category" data-category="profile">
                            <i class="fa fa-user"></i>
                            <h4>Perfil</h4>
                        </div>
                    </div>

                    <!-- Guías para Tickets -->
                    <div class="guide-section active" id="tickets-guides">
                        <div class="guide-card">
                            <div class="card-header">
                                <h3><i class="fa fa-plus-circle"></i> Cómo Crear un Nuevo Ticket</h3>
                            </div>
                            <div class="card-body">
                                <p>Aprende a crear un nuevo ticket de soporte para reportar problemas o solicitar asistencia.</p>
                                
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Accede al módulo de tickets</h4>
                                        <p>En el menú lateral, haz clic en "Nuevo Ticket" para iniciar el proceso de creación.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Completa la información requerida</h4>
                                        <p>Selecciona la categoría apropiada y proporciona un título claro para tu ticket.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Describe el problema o solicitud</h4>
                                        <p>Utiliza el editor de texto para describir detalladamente el problema o solicitud. Sé específico y proporciona toda la información relevante.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h4>Adjunta archivos (opcional)</h4>
                                        <p>Si es necesario, puedes adjuntar capturas de pantalla u otros archivos que ayuden a entender el problema.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">5</div>
                                    <div class="step-content">
                                        <h4>Envía el ticket</h4>
                                        <p>Haz clic en "Crear Ticket" y tu solicitud será enviada al equipo de soporte.</p>
                                    </div>
                                </div>
                                
                                <div class="tip-box">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Proporciona un título claro y conciso que refleje el problema. Por ejemplo, "Error al cargar reportes" es mejor que "No funciona".
                                </div>
                            </div>
                        </div>

                        <div class="guide-card">
                            <div class="card-header">
                                <h3><i class="fa fa-search"></i> Cómo Consultar el Estado de Tickets</h3>
                            </div>
                            <div class="card-body">
                                <p>Aprende a consultar y dar seguimiento a tus tickets existentes.</p>
                                
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Accede a "Consultar Ticket"</h4>
                                        <p>En el menú lateral, selecciona la opción "Consultar Ticket" para ver todos tus tickets activos.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Filtra tus tickets</h4>
                                        <p>Utiliza las opciones de filtrado para encontrar tickets específicos por categoría, fecha o estado.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Revisa detalles del ticket</h4>
                                        <p>Haz clic en el botón "Detalles" para ver toda la información y seguimiento del ticket seleccionado.</p>
                                    </div>
                                </div>
                                
                                <div class="tip-box">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Mantén un seguimiento regular de tus tickets para responder oportunamente a las preguntas del equipo de soporte.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guías para usuarios generales -->
                    <div class="guide-section" id="admin-guides">
                        <div class="guide-card">
                            <div class="card-header">
                                <h3><i class="fa fa-tasks"></i> Uso General del Sistema</h3>
                            </div>
                            <div class="card-body">
                                <p>Consejos generales para aprovechar al máximo el sistema GTM.</p>
                                
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Navega eficientemente</h4>
                                        <p>Utiliza el menú lateral para moverte entre las diferentes secciones del sistema de forma rápida y sencilla.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Revisa tus notificaciones</h4>
                                        <p>El icono de notificaciones te mantendrá informado sobre actualizaciones en tus tickets.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Actualiza tu información personal</h4>
                                        <p>Mantén tu perfil actualizado para facilitar la comunicación con el equipo de soporte.</p>
                                    </div>
                                </div>
                                
                                <div class="tip-box">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Guarda los números de tus tickets para futuras referencias y seguimiento de casos.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guías para Reportes -->
                    <div class="guide-section" id="reports-guides">
                        <div class="guide-card">
                            <div class="card-header">
                                <h3><i class="fa fa-line-chart"></i> Interpretación de Estadísticas</h3>
                            </div>
                            <div class="card-body">
                                <p>Aprende a interpretar las estadísticas y gráficos del dashboard principal.</p>
                                
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Accede al Dashboard</h4>
                                        <p>Al iniciar sesión, verás automáticamente el dashboard con estadísticas clave.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Comprende los contadores principales</h4>
                                        <p>Los contadores muestran el total de tickets, tickets abiertos y tickets cerrados para tener una visión general del sistema.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Analiza las gráficas</h4>
                                        <p>Las gráficas muestran la distribución de tickets por categoría y por estado, ayudándote a identificar tendencias.</p>
                                    </div>
                                </div>
                                
                                <div class="tip-box">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Revisa regularmente las estadísticas para identificar áreas que requieran mayor atención o recursos.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guías para Perfil -->
                    <div class="guide-section" id="profile-guides">
                        <div class="guide-card">
                            <div class="card-header">
                                <h3><i class="fa fa-id-card"></i> Gestión de Perfil de Usuario</h3>
                            </div>
                            <div class="card-body">
                                <p>Aprende a actualizar tu información personal y cambiar tu contraseña.</p>
                                
                                <div class="step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Accede a tu perfil</h4>
                                        <p>Haz clic en tu nombre de usuario en la esquina superior derecha y selecciona "Mi Perfil".</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Revisa tu información</h4>
                                        <p>Verifica que tu información personal sea correcta. Algunos campos pueden ser de solo lectura.</p>
                                    </div>
                                </div>
                                
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Cambia tu contraseña</h4>
                                        <p>En la sección "Cambiar Contraseña", ingresa tu nueva contraseña, confírmala y haz clic en "Actualizar Contraseña".</p>
                                    </div>
                                </div>
                                
                                <div class="tip-box">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Utiliza una contraseña segura combinando letras mayúsculas, minúsculas, números y símbolos. La contraseña debe tener al menos 6 caracteres.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Preguntas Frecuentes -->
                <div class="tab-content" id="faq">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Cómo puedo crear un ticket?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para crear un nuevo ticket, sigue estos pasos:</p>
                            <ol>
                                <li>En el menú lateral, haz clic en "Nuevo Ticket"</li>
                                <li>Selecciona la categoría apropiada</li>
                                <li>Proporciona un título descriptivo</li>
                                <li>Detalla el problema o solicitud en el área de descripción</li>
                                <li>Adjunta archivos si es necesario</li>
                                <li>Haz clic en "Crear Ticket"</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Cómo puedo saber el estado de mi ticket?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para consultar el estado de tus tickets, ve a "Consultar Ticket" en el menú lateral. Allí podrás ver todos tus tickets con su estado actual (abierto o cerrado). Al hacer clic en "Detalles", podrás ver todas las actualizaciones y comentarios realizados.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Puedo reabrir un ticket cerrado?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>No, una vez que un ticket ha sido cerrado, no puede ser reabierto. Si tienes el mismo problema o necesitas seguimiento adicional, deberás crear un nuevo ticket haciendo referencia al ticket anterior.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Cómo cambio mi contraseña?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para cambiar tu contraseña:</p>
                            <ol>
                                <li>Haz clic en tu nombre de usuario en la esquina superior derecha</li>
                                <li>Selecciona "Mi Perfil"</li>
                                <li>Desplázate hasta la sección "Cambiar Contraseña"</li>
                                <li>Ingresa tu nueva contraseña y confírmala</li>
                                <li>Haz clic en "Actualizar Contraseña"</li>
                            </ol>
                            <p>Recuerda que la contraseña debe tener al menos 6 caracteres.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Cómo puedo actualizar mi información personal?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para actualizar tu información personal:</p>
                            <ol>
                                <li>Haz clic en tu nombre de usuario en la parte superior derecha</li>
                                <li>Selecciona "Perfil"</li>
                                <li>En esta página podrás ver tu información actual</li>
                                <li>Algunos campos pueden ser de solo lectura, contacta al administrador para actualizar estos campos</li>
                                <li>Para cambiar tu contraseña, usa la sección correspondiente en la misma página</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Qué significa cada estado de ticket?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Los estados de los tickets son:</p>
                            <ul>
                                <li><strong>Abierto (1):</strong> El ticket ha sido creado y está pendiente de atención.</li>
                                <li><strong>Cerrado (0):</strong> El problema ha sido resuelto o la solicitud ha sido atendida.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Quién puede ver mis tickets?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Tus tickets pueden ser vistos por:</p>
                            <ul>
                                <li>Tú mismo</li>
                                <li>Usuarios con rol de Soporte asignados para atender tickets</li>
                                <li>Administradores del sistema</li>
                            </ul>
                            <p>Los usuarios regulares solo pueden ver sus propios tickets.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fa fa-question-circle"></i> ¿Cómo puedo adjuntar archivos a un ticket?</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Para adjuntar archivos al crear un ticket:</p>
                            <ol>
                                <li>En el formulario de nuevo ticket, verás un editor de texto enriquecido</li>
                                <li>Haz clic en el ícono de "Insertar imagen" en la barra de herramientas del editor</li>
                                <li>Selecciona "Subir" y elige el archivo que deseas adjuntar</li>
                                <li>El archivo se subirá y se insertará automáticamente en el contenido del ticket</li>
                            </ol>
                            <p>Nota: Los archivos adjuntos deben ser imágenes o documentos comunes (jpg, png, pdf, doc, etc.).</p>
                        </div>
                    </div>
                </div>

                <!-- Tab: Contactar Soporte -->
                <div class="tab-content" id="contact">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-form">
                                <h3 class="mb-4">Enviar Mensaje de Soporte</h3>
                                <p>Si no encuentras la respuesta que buscas, puedes contactar directamente con nuestro equipo de soporte.</p>
                                
                                <form id="contactForm">
                                    <div class="form-group">
                                        <label for="contactSubject">Asunto</label>
                                        <input type="text" class="form-control" id="contactSubject" placeholder="Ej: Problema con la creación de tickets">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="contactMessage">Mensaje</label>
                                        <textarea class="form-control" id="contactMessage" rows="5" placeholder="Describe tu problema o pregunta con detalle..."></textarea>
                                    </div>
                                    
                                    <div class="tip-box mb-3">
                                        <i class="fa fa-info-circle"></i> Tu mensaje será enviado junto con tu información de usuario para poder brindarte una mejor atención.
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-paper-plane mr-2"></i>Enviar Mensaje
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="contact-info">
                                <h3>Información de Contacto</h3>
                                <p><i class="fa fa-envelope"></i> Jassiel.rr1502@gmail.com</p>
                                <p><i class="fa fa-phone"></i> 656-775-1155</p>
                                <p><i class="fa fa-clock-o"></i> Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                                
                                <div class="tip-box mt-4">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Consejo:</strong> Para una respuesta más rápida, considera crear un ticket a través del sistema.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once('../MainHead/js.php');
    ?>
    <script>
    $(document).ready(function() {
        // Manejo de tabs
        $('.tab-item').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Activar tab
            $('.tab-item').removeClass('active');
            $(this).addClass('active');
            
            // Mostrar contenido
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
        });
        
        // Manejo de categorías de guías
        $('.guide-category').on('click', function() {
            var category = $(this).data('category');
            
            // Activar categoría
            $('.guide-category').removeClass('active');
            $(this).addClass('active');
            
            // Mostrar guías
            $('.guide-section').removeClass('active');
            $('#' + category + '-guides').addClass('active');
        });
        
        // Manejo de preguntas frecuentes
        $('.faq-question').on('click', function() {
            var $item = $(this).parent();
            
            if ($item.hasClass('active')) {
                $item.removeClass('active');
            } else {
                // Opcional: cerrar otras preguntas
                // $('.faq-item').removeClass('active');
                $item.addClass('active');
            }
        });
        
        // Búsqueda
        $('#helpSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            if (value.length > 2) {
                // Buscar en guías
                $('.guide-card').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
                
                // Buscar en FAQs
                $('.faq-item').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                    
                    // Si hay coincidencia, expandir la pregunta
                    if (text.indexOf(value) > -1) {
                        $(this).addClass('active');
                    }
                });
            } else {
                // Si la búsqueda está vacía o es muy corta, mostrar todo
                $('.guide-card').show();
                $('.faq-item').show();
                
                // Opcionalmente, colapsar todas las preguntas
                if (value.length === 0) {
                    $('.faq-item').removeClass('active');
                }
            }
        });
        
        // Formulario de contacto
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            var subject = $('#contactSubject').val();
            var message = $('#contactMessage').val();
            
            if (subject && message) {
                // Usar AJAX para enviar el mensaje al controlador
                $.ajax({
                    type: "POST",
                    url: "/ESTADIAS/controller/send_help_message.php",
                    data: {
                        subject: subject,
                        message: message,
                        recipient: "Jassiel.rr1502@gmail.com" // Tu correo electrónico
                    },
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.status === "success") {
                                swal({
                                    title: "Mensaje Enviado",
                                    text: "Tu mensaje ha sido enviado al equipo de soporte. Te responderemos a la brevedad.",
                                    type: "success",
                                    confirmButtonClass: "btn-success"
                                });
                                
                                // Limpiar el formulario
                                $('#contactSubject').val('');
                                $('#contactMessage').val('');
                            } else {
                                swal({
                                    title: "Error",
                                    text: "No se pudo enviar el mensaje. Por favor intenta nuevamente o contacta al soporte por teléfono.",
                                    type: "error",
                                    confirmButtonClass: "btn-danger"
                                });
                            }
                        } catch (e) {
                            // En caso de que la respuesta no sea JSON válido
                            swal({
                                title: "Mensaje Enviado",
                                text: "Tu mensaje ha sido enviado al equipo de soporte. Te responderemos a la brevedad.",
                                type: "success",
                                confirmButtonClass: "btn-success"
                            });
                            
                            // Limpiar el formulario
                            $('#contactSubject').val('');
                            $('#contactMessage').val('');
                        }
                    },
                    error: function() {
                        swal({
                            title: "Error",
                            text: "No se pudo enviar el mensaje. Por favor intenta nuevamente o contacta al soporte por teléfono.",
                            type: "error",
                            confirmButtonClass: "btn-danger"
                        });
                    }
                });
            } else {
                swal({
                    title: "Error",
                    text: "Por favor completa todos los campos del formulario.",
                    type: "error",
                    confirmButtonClass: "btn-danger"
                });
            }
        });
    });
    </script>
</body>
</html>
