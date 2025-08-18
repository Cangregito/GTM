<?php
require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Tickets Cerrados</title>
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
                            <h3>Tickets Cerrados</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="#">Home</a></li>
                                <li class="active">Tickets Cerrados</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            <div class="box-typical box-typical-padding">
                <!-- Filtros -->
                <div class="row mb-3" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            
                        </div>
                    </div>
                </div>
                
                <table id="ticket_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th style="width: 10%;">No.Ticket</th>
                            <th style="width: 15%;">Categoria</th>
                            <th class="d-none d-sm-table-cell" style="width: 25;">Titulo</th>
                            <th style="width: 5%;">Estado</th>
                            <th style="width: 10%;">Prioridad</th>
                            <th style="width: 10;">Fecha Creación</th>
                            <th class="text-center" style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="ticket-info" class="box-typical" style="margin-bottom: 30px;">
                <!-- Aquí se mostrará la información del ticket -->
            </div>
            <section class="activity-line" id="detalle-lista">
                <!-- Aquí se imprimen los detalles/conversación -->
            </section>

        </div><!--.container-fluid-->
    </div><!--.page-content-->
    

    
    <?php
    require_once('../MainHead/js.php');
    ?>
    <script>
    var user_id = <?php echo $_SESSION['user_id']; ?>;
    var rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
    <!-- Usa ruta absoluta para asegurar que el JS se cargue correctamente -->
    <script type="text/javascript" src="/ESTADIAS/view/VerCerrado/cerradosTicket.js?v=<?php echo time(); ?>"></script>
</body>
</body>

</html>