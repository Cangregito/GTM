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
<title>Nuevo Tiket</title>
<html>
<head>
    <style>
        /* Animación shake para validación de errores */
        @keyframes errorShake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .error-shake {
            animation: errorShake 0.6s cubic-bezier(.36,.07,.19,.97) both;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
            perspective: 1000px;
            border-color: #ff5b57 !important;
            box-shadow: 0 0 5px rgba(255, 91, 87, 0.5) !important;
        }
        
        /* Estilo para campos con error */
        .error-field {
            border-color: #ff5b57 !important;
            box-shadow: 0 0 5px rgba(255, 91, 87, 0.3) !important;
            background-color: rgba(255, 91, 87, 0.05) !important;
        }
        
        /* Mensaje de error con animación */
        .error-message {
            animation: fadeInDown 0.3s ease-in-out;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Estilo para el botón de enviar */
        .btn-submit-ticket {
            position: relative;
            overflow: hidden;
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-submit-ticket:hover {
            background: linear-gradient(45deg, #21CBF3, #2196F3);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-submit-ticket:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(33, 150, 243, 0.3);
        }
        
        /* Efecto de onda para el botón */
        .btn-submit-ticket::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform 0.5s, opacity 0.8s;
        }
        
        .btn-submit-ticket:active::after {
            transform: scale(0, 0);
            opacity: 0.3;
            transition: 0s;
        }
        
        /* Mejorar apariencia del formulario */
        .form-control:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        /* Mejoras para el editor Summernote */
        .note-editor.note-frame {
            border-color: #ddd;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .note-editor.note-frame:focus-within {
            border-color: #2196F3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
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
                            <h3>Nuevo Ticket</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="#">Home</a></li>
                                <li class="active">Nuevo ticket</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <div class="box-typical box-typical-padding">
                <p>
                    Desde esta ventana puedes crear un nuevo ticket para que el administrador lo revise y te brinde una
                    respuesta. Asegúrate de proporcionar toda la información necesaria para que el proceso sea más
                    eficiente.
                </p>

                <h5 class="m-t-lg with-border">Ingresar información.</h5>
                <div class="row">
                    <form method="post" id="ticket_form">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?>">

                        <!-- Sección 1: Categoría -->
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold">Categoría</label>
                                <select id="categoria_id" name="cat_id" class="form-control">
                                </select>
                            </fieldset>
                        </div>

                        <!-- Sección 2: Título -->
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="ticket_titulo">Titulo</label>
                                <input type="text" class="form-control" name="ticket_titulo" id="ticket_titulo"
                                    placeholder="Ingrese Titulo">
                            </fieldset>
                        </div>
                        
                        <!-- Sección 3: Prioridad -->
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="prioridad">Prioridad</label>
                                <select id="prioridad" name="prioridad" class="form-control">
                                    <option value="Urgente">Urgente</option>
                                    <option value="Alto">Alto</option>
                                    <option value="Medio" selected>Medio</option>
                                    <option value="Bajo">Bajo</option>
                                </select>
                            </fieldset>
                        </div>

                        <!-- Sección 4: Descripción -->
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="ticket_descripcion">Descripción</label>
                                <div class="summernote-theme-1">
                                    <textarea id="ticket_descripcion" class="summernote"
                                        name="ticket_descripcion">Descripción</textarea>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" name="action" value="add" class="btn-submit-ticket">
                                <i class="fa fa-paper-plane"></i> Crear Ticket
                            </button>
                        </div>
                        </form>
                        
                        <!-- Mensajes de validación -->
                        <div class="col-lg-12 mt-3">
                            <div id="validation-summary" class="alert alert-info" style="display: none;">
                                <h4><i class="fa fa-info-circle"></i> Información Importante</h4>
                                <p>Todos los campos son obligatorios. Por favor complete el formulario correctamente.</p>
                                <ul>
                                    <li>Seleccione una categoría que mejor describa su solicitud</li>
                                    <li>El título debe ser claro y conciso</li>
                                    <li>En la descripción, proporcione todos los detalles necesarios</li>
                                </ul>
                            </div>
                        </div>
                </div>

            </div><!--.box-typical-->
        </div><!--.container-fluid-->
    </div><!--.page-content-->
    <?php
    require_once('../MainHead/js.php');
    ?>
    <script type="text/javascript" src="nuevoTicket.js"></script>
</body>

</html>