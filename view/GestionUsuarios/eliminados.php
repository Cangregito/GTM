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
<title>Usuarios Eliminados</title>
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
                            <h3>Usuarios Eliminados</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li><a href="index.php">Gestión de Usuarios</a></li>
                                <li class="active">Usuarios Eliminados</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            <div class="box-typical box-typical-padding">
                <a href="index.php" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> Volver a Gestión de Usuarios
                </a>
                <br><br>
                
                <table id="usuarios_eliminados_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 20%;">Nombre Completo</th>
                            <th style="width: 20%;">Correo</th>
                            <th style="width: 10%;">Rol</th>
                            <th style="width: 15%;">Fecha Creación</th>
                            <th style="width: 15%;">Fecha Eliminación</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
    <script type="text/javascript" src="/ESTADIAS/view/GestionUsuarios/usuariosEliminados.js"></script>
</body>
</html>
