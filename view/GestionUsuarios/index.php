<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Verificar que el usuario tenga rol de soporte y sea "admin"
if ($_SESSION["rol_id"] != 2 || $_SESSION["user_nom"] != "admin") {
    header("Location: ../Home/");
    exit;
}

require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Gestión de Usuarios</title>
<html>
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
                            <h3>Gestión de Usuarios</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li class="active">Gestión de Usuarios</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            <div class="box-typical box-typical-padding">
                <button type="button" id="btnNuevo" class="btn btn-success" data-toggle="modal" data-target="#modalUsuario">
                    <i class="fa fa-plus"></i> Nuevo Usuario
                </button>
                <a href="eliminados.php" class="btn btn-warning">
                    <i class="fa fa-trash"></i> Ver Usuarios Eliminados
                </a>
                <br><br>
                
                <table id="usuario_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 25%;">Nombre Completo</th>
                            <th style="width: 25%;">Correo</th>
                            <th style="width: 10%;">Rol</th>
                            <th style="width: 15%;">Fecha Creación</th>
                            <th style="width: 10%;">Editar</th>
                            <th style="width: 10%;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-labelledby="modalUsuarioLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h4>
                </div>
                <div class="modal-body">
                    <form id="usuario_form">
                        <input type="hidden" id="user_id" name="user_id">
                        
                        <div class="form-group">
                            <label for="user_nom">Nombre(s)*</label>
                            <input type="text" class="form-control" id="user_nom" name="user_nom" placeholder="Ingrese nombre(s)" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_ape">Apellidos*</label>
                            <input type="text" class="form-control" id="user_ape" name="user_ape" placeholder="Ingrese apellidos" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_correo">Correo Electrónico*</label>
                            <input type="email" class="form-control" id="user_correo" name="user_correo" placeholder="Ingrese correo" required>
                        </div>
                        
                        <div class="form-group" id="div_password">
                            <label for="user_pass">Contraseña*</label>
                            <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Ingrese contraseña">
                        </div>
                        
                        <div class="form-group">
                            <label for="rol_id">Rol*</label>
                            <select class="form-control" id="rol_id" name="rol_id" required>
                                <option value="">Seleccione</option>
                                <option value="1">Usuario</option>
                                <option value="2">Soporte</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnGuardar" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modalPasswordLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalPasswordLabel">Cambiar Contraseña</h4>
                </div>
                <div class="modal-body">
                    <form id="password_form">
                        <input type="hidden" id="user_id_pass" name="user_id">
                        
                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña*</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Ingrese nueva contraseña" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña*</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirme nueva contraseña" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnCambiarPass" class="btn btn-primary">Cambiar Contraseña</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once('../MainHead/js.php');
    ?>
    <script>
    var user_id = <?php echo $_SESSION['user_id']; ?>;
    var rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
    <script type="text/javascript" src="/ESTADIAS/view/GestionUsuarios/gestionUsuarios.js"></script>
</body>
</html>
