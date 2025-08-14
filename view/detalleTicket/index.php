<?php
require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Detalle Ticket </title>
<html>

<head>
    <link rel="icon" href="/ESTADIAS/public/img/favicon.ico">
</head>

<body class="with-side-menu">
    <?php require_once('../MainHead/header.php'); ?>
    <div class="mobile-menu-left-overlay"></div>
    <?php require_once('../MainHead/nav.php'); ?>
    <div class="page-content">
        <div class="container-fluid">
            <div id="ticket-info" class="box-typical" style="margin-bottom: 30px;">
                <!-- Aquí se mostrará la información del ticket -->
            </div>
            <section class="activity-line" id="detalle-lista">
                <!-- Aquí se imprimirán los detalles dinámicamente -->
            </section><!--.activity-line-->
            <!-- Formulario para responder -->
            <div id="responder" style="margin-top:40px;">
                <form id="form-respuesta" style="background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.07); border:1px solid #e3e3e3; padding:24px;">
                    <h5 style="margin-bottom:18px;">Responder al ticket</h5>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="respuesta"></label>
                        <div class="summernote-theme-1">
                            <textarea id="respuesta" class="summernote" name="respuesta" required></textarea>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary">Enviar respuesta</button>
                </form>
            </div>
            <!-- Icono flotante para ir al formulario de respuesta -->
            <a href="#responder" id="scroll-to-reply" title="Responder" style="position:fixed;bottom:32px;right:32px;z-index:9999;background:#007bff;color:#fff;width:56px;height:56px;display:flex;align-items:center;justify-content:center;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.18);font-size:32px;cursor:pointer;transition:background 0.2s;">
                💬
            </a>
        </div><!--.container-fluid-->
    </div><!--.page-content-->
    <?php require_once('../MainHead/js.php'); ?>
    <script>
        var user_id = <?php echo $_SESSION['user_id']; ?>;
        var rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
    <script type="text/javascript" src="/ESTADIAS/view/detalleTicket/detalleTicket.js"></script>
</body>
</html>