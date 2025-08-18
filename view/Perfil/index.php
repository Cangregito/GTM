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
<title>Mi Perfil</title>
<html>
<head>
    <style>
        .profile-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
        }
        
        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 5px solid #fff;
        }
        
        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-name {
            color: #334155;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-role {
            display: inline-block;
            background: #3c8dbc;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .user-info-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .user-info-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, #3c8dbc 0%, #2d6284 100%);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 15px 20px;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-control-label {
            font-weight: 600;
            color: #334155;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #dce6f1;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
        }
        
        .btn-primary {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2d6284;
            border-color: #2d6284;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(60, 141, 188, 0.3);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .password-section {
            margin-top: 30px;
            border-top: 1px solid #e9ecef;
            padding-top: 25px;
        }
        
        .section-title {
            font-size: 18px;
            color: #334155;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .stat-box {
            text-align: center;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .stat-box:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-3px);
        }
        
        .stat-box h4 {
            font-size: 24px;
            color: #3c8dbc;
            margin: 0;
            font-weight: 700;
        }
        
        .stat-box p {
            font-size: 12px;
            color: #334155;
            margin: 5px 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .mt-4 {
            margin-top: 20px;
        }
        
        .mr-2 {
            margin-right: 8px;
        }
        
        .text-muted {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        /* Estilos de animación */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-card, .user-info-card {
            animation: fadeIn 0.5s ease-out forwards;
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
                            <h3>Mi Perfil</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li class="active">Mi Perfil</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <div class="box-typical box-typical-padding">
                <div class="row">
                    <div class="col-lg-4 col-md-5">
                        <div class="profile-card">
                            <div class="profile-img-container">
                                <?php if($_SESSION["rol_id"] == 1): ?>
                                    <img src="/ESTADIAS/public/img/Gerente.png" alt="Gerente" class="profile-img">
                                <?php else: ?>
                                    <img src="/ESTADIAS/public/img/Soporte.png" alt="Soporte" class="profile-img">
                                <?php endif; ?>
                            </div>
                            <h3 class="profile-name">
                                <?php echo $_SESSION["user_nom"] . ' ' . $_SESSION["user_ape"]; ?>
                            </h3>
                            <div class="profile-role">
                                <?php echo $_SESSION["rol_id"] == 1 ? 'Usuario' : 'Soporte'; ?>
                            </div>
                            
                            <div class="profile-stats">
                                <?php
                                // Obtener las estadísticas del usuario si es posible
                                if (isset($_SESSION["user_id"])) {
                                    require_once('../../models/Ticket.php');
                                    $ticket = new Ticket();
                                    $stats = $ticket->get_ticket_totales_x_usuario($_SESSION["user_id"]);
                                    
                                    if (isset($stats['total']) && isset($stats['abiertos']) && isset($stats['cerrados'])):
                                ?>
                                <div class="row mt-4">
                                    <div class="col-xs-4">
                                        <div class="stat-box">
                                            <h4><?php echo $stats['total']; ?></h4>
                                            <p>Total</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="stat-box">
                                            <h4><?php echo $stats['abiertos']; ?></h4>
                                            <p>Abiertos</p>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="stat-box">
                                            <h4><?php echo $stats['cerrados']; ?></h4>
                                            <p>Cerrados</p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-7">
                        <div class="user-info-card">
                            <div class="card-header">
                                <h5><i class="fa fa-user-circle-o mr-2"></i> Información de Usuario</h5>
                            </div>
                            <div class="card-body">
                                <form id="user_form">
                                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Nombre</label>
                                                <input type="text" class="form-control" id="user_nom" name="user_nom" value="<?php echo $_SESSION["user_nom"]; ?>" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Apellido</label>
                                                <input type="text" class="form-control" id="user_ape" name="user_ape" value="<?php echo $_SESSION["user_ape"]; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="user_correo" name="user_correo" value="<?php 
                                            if (isset($_SESSION["user_correo"])) {
                                                echo $_SESSION["user_correo"];
                                            } else {
                                                // Si el correo no está en la sesión, lo obtenemos de la base de datos
                                                require_once('../../models/Usuario.php');
                                                $usuario = new Usuario();
                                                $datos = $usuario->get_usuario_x_id($_SESSION["user_id"]);
                                                if ($datos && isset($datos["user_correo"])) {
                                                    echo $datos["user_correo"];
                                                } else {
                                                    echo "";
                                                }
                                            }
                                        ?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Tipo de Usuario</label>
                                        <input type="text" class="form-control" value="<?php echo $_SESSION["rol_id"] == 1 ? 'Usuario' : 'Soporte'; ?>" readonly>
                                    </div>

                                    <div class="password-section">
                                        <h4 class="section-title">Cambiar Contraseña</h4>
                                        
                                        <div class="form-group">
                                            <label class="form-control-label">Nueva Contraseña</label>
                                            <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Ingresa tu nueva contraseña">
                                            <small class="text-muted">La contraseña debe tener al menos 6 caracteres.</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-control-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" placeholder="Confirma tu nueva contraseña">
                                        </div>

                                        <div class="form-group">
                                            <button type="button" id="btnUpdatePass" class="btn btn-primary">
                                                <i class="fa fa-refresh mr-2"></i>Actualizar Contraseña
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
    <script src="/ESTADIAS/view/Perfil/perfil.js"></script>
</body>
</html>
