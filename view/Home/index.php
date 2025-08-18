<?php
session_start();
// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: /ESTADIAS/index.php");
    exit;
}

require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>GTM - Dashboard</title>
    <!-- Morris Charts CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
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
                            <h3>Dashboard</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="#">Home</a></li>
                                <li class="active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Tarjetas de resumen -->
            <div class="row" id="cards-container">
                <div class="col-sm-4">
                    <div class="card-box bg-primary">
                        <div class="inner">
                            <h3 id="total-tickets">0</h3>
                            <p>Total de Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-ticket" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card-box bg-success">
                        <div class="inner">
                            <h3 id="tickets-abiertos">0</h3>
                            <p>Tickets Abiertos</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card-box bg-danger">
                        <div class="inner">
                            <h3 id="tickets-cerrados">0</h3>
                            <p>Tickets Cerrados</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-folder-o" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de categorías -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="box-typical box-typical-padding">
                        <h5 class="m-t-lg with-border">Tickets por Categoría</h5>
                        <div id="chart-categorias" style="height: 250px; min-height: 250px;"></div>
                        <button id="btnRecargar" class="btn btn-success mt-2">Recargar Gráficos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <?php
    require_once('../MainHead/js.php');
    ?>
    
    <!-- Morris Charts JS -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    
    <style>
    .card-box {
        position: relative;
        color: #fff;
        padding: 20px 10px 40px;
        margin: 20px 0px;
        border-radius: 5px;
    }
    .card-box:hover {
        text-decoration: none;
        color: #f1f1f1;
    }
    .card-box .inner {
        padding: 5px 10px 0 10px;
    }
    .card-box h3 {
        font-size: 27px;
        font-weight: bold;
        margin: 0 0 8px 0;
        white-space: nowrap;
        padding: 0;
        text-align: left;
    }
    .card-box p {
        font-size: 15px;
    }
    .card-box .icon {
        position: absolute;
        top: auto;
        bottom: 5px;
        right: 5px;
        z-index: 0;
        font-size: 72px;
        color: rgba(0, 0, 0, 0.15);
    }
    .bg-primary {
        background-color: #00a65a !important;
    }
    .bg-success {
        background-color: #3c8dbc !important;
    }
    .bg-danger {
        background-color: #dd4b39 !important;
    }
    </style>
    
    <script>
    var user_id = <?php echo $_SESSION['user_id']; ?>;
    var rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
    <script type="text/javascript" src="home.js"></script>
</body>
</html>