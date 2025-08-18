<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: /ESTADIAS/index.php");
    exit;
}

// Verificar que el usuario sea de soporte con nombre "Mantenimiento" y apellido "planta" o sea "admin"
if ($_SESSION["rol_id"] != 2 || 
    !(($_SESSION["user_nom"] == "Mantenimiento" && $_SESSION["user_ape"] == "planta") || 
      ($_SESSION["user_nom"] == "admin"))) {
    header("Location: /ESTADIAS/view/Home/");
    exit;
}

require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Gestión de Inventario</title>
<html>
<head>
    <style>
        .inventario-header {
            margin-bottom: 30px;
        }
        
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-title {
            font-size: 14px;
            color: #334155;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 24px;
            color: #3c8dbc;
            font-weight: 700;
        }
        
        .box-tools {
            margin-bottom: 15px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #3c8dbc 0%, #2d6284 100%);
            color: white;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #334155;
        }
        
        .form-control {
            border-radius: 4px;
            border: 1px solid #dce6f1;
        }
        
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
        }
        
        .btn-primary {
            background-color: #3c8dbc;
            border-color: #3c8dbc;
        }
        
        .btn-primary:hover {
            background-color: #2d6284;
            border-color: #2d6284;
        }
        
        .label-success {
            background-color: #5cb85c;
        }
        
        .label-warning {
            background-color: #f0ad4e;
        }
        
        .label-danger {
            background-color: #d9534f;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        
        .category-tag {
            display: inline-block;
            padding: 2px 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 12px;
            color: #495057;
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
                            <h3>Gestión de Inventario</h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="../Home/">Home</a></li>
                                <li class="active">Inventario</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>

            <div class="box-typical box-typical-padding">
                <div class="inventario-header">
                    <h5 class="m-t-lg with-border">Herramientas y Equipos Disponibles</h5>
                    <p>Gestione el inventario de herramientas y equipos de mantenimiento.</p>
                </div>
                
                <div class="row" id="stats-container">
                    <!-- Aquí se mostrarán las estadísticas del inventario -->
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-title">Total Elementos</div>
                            <div class="stat-value" id="total-items">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-title">Activos</div>
                            <div class="stat-value" id="total-activos">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-title">Inactivos</div>
                            <div class="stat-value" id="total-inactivos">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-title">Descompuestos</div>
                            <div class="stat-value" id="total-descompuestos">0</div>
                        </div>
                    </div>
                </div>
                
                <div class="box-tools">
                    <button type="button" class="btn btn-primary" id="btnnuevo">
                        <i class="fa fa-plus"></i> Nuevo Elemento
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table id="inventario_data" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Estado</th>
                                <th>Categoría</th>
                                <th>Fecha de Registro</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para CRUD -->
    <div id="modalInventario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="inventario_form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="inv_id" name="inv_id">
                        
                        <div class="form-group">
                            <label for="inv_nombre" class="form-control-label">Nombre del Elemento*</label>
                            <input type="text" class="form-control" id="inv_nombre" name="inv_nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="inv_descripcion" class="form-control-label">Descripción</label>
                            <textarea class="form-control" id="inv_descripcion" name="inv_descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="inv_cantidad" class="form-control-label">Cantidad*</label>
                            <input type="number" class="form-control" id="inv_cantidad" name="inv_cantidad" min="0" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="inv_estado" class="form-control-label">Estado*</label>
                            <select class="form-control" id="inv_estado" name="inv_estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Descompuesto">Descompuesto</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="inv_categoria" class="form-control-label">Categoría</label>
                            <select class="form-control" id="inv_categoria" name="inv_categoria">
                                <option value="">Seleccione una categoría</option>
                                <option value="Herramientas Manuales">Herramientas Manuales</option>
                                <option value="Herramientas Eléctricas">Herramientas Eléctricas</option>
                                <option value="Maquinaria">Maquinaria</option>
                                <option value="Equipos de Seguridad">Equipos de Seguridad</option>
                                <option value="Consumibles">Consumibles</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGuardar" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    require_once('../MainHead/js.php');
    ?>
    <script src="/ESTADIAS/view/Inventario/inventario.js"></script>
</body>
</html>
