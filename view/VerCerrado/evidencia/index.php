<?php
session_start();
// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    header("Location: /ESTADIAS/index.php");
    exit;
}

require_once('../../MainHead/head.php');

// Verificar si se recibió el parámetro ID
if (!isset($_GET['ID'])) {
    header('Location: /ESTADIAS/view/VerCerrado/');
    exit;
}
$ticket_id = intval($_GET['ID']);
?>
<!DOCTYPE html>
<title>Subir Evidencia - Ticket #<?php echo $ticket_id; ?></title>
<html>

<head>
    <link rel="icon" href="/ESTADIAS/public/img/favicon.ico">
</head>

<body class="with-side-menu">
    <?php require_once('../../MainHead/header.php'); ?>
    <div class="mobile-menu-left-overlay"></div>
    <?php require_once('../../MainHead/nav.php'); ?>
    
    <div class="page-content">
        <div class="container-fluid">
            <header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Subir Evidencia - Ticket #<?php echo $ticket_id; ?></h3>
                            <ol class="breadcrumb breadcrumb-simple">
                                <li><a href="/ESTADIAS/view/Home/">Home</a></li>
                                <li><a href="/ESTADIAS/view/VerCerrado/">Tickets Cerrados</a></li>
                                <li class="active">Subir Evidencia</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            
            <div id="ticket-info" class="box-typical" style="margin-bottom: 30px;">
                <!-- Aquí se cargará la información del ticket -->
            </div>
            
            <div class="box-typical box-typical-padding">
                <h5 class="m-t-lg with-border">Subir Evidencia para el Ticket</h5>
                
                <div class="row">
                    <div class="col-lg-12">
                        <form id="evidencia-form" enctype="multipart/form-data">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                            
                            <div class="form-group">
                                <label class="form-label semibold" for="tipo_evidencia">Tipo de Evidencia</label>
                                <select class="form-control" id="tipo_evidencia" name="tipo_evidencia" required>
                                    <option value="">Seleccione...</option>
                                    <option value="factura">Factura</option>
                                    <option value="foto">Foto</option>
                                    <option value="recibo">Recibo de pago</option>
                                    <option value="documento">Documento</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label semibold" for="descripcion">Descripción de la Evidencia</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Describa brevemente esta evidencia..." required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label semibold" for="archivo">Seleccionar Archivo</label>
                                <div class="custom-file-upload">
                                    <input type="file" id="archivo" name="archivo" style="display: none;" required>
                                    <button id="file-selector" class="btn btn-default">
                                        <i class="fa fa-upload"></i> Seleccionar archivo
                                    </button>
                                    <div id="file-preview"></div>
                                </div>
                                <small class="text-muted">Formatos aceptados: JPG, PNG, PDF. Tamaño máximo: 5MB</small>
                            </div>
                            
                            <div class="form-group m-t-md">
                                <button type="submit" id="submit-btn" class="btn btn-success">Subir Evidencia</button>
                                <a href="/ESTADIAS/view/detalleTicket/?ID=<?php echo $ticket_id; ?>" class="btn btn-default">Volver al Ticket</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="row m-t-lg">
                    <div class="col-lg-12">
                        <h5 class="with-border">Evidencia Existente</h5>
                        <div id="evidencia-list">
                            <!-- Aquí se cargará la lista de evidencia existente -->
                            <div class="text-center" id="loading-evidencia">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>Cargando evidencia...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once('../../MainHead/js.php'); ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var user_id = <?php echo $_SESSION['user_id']; ?>;
        var rol_id = <?php echo $_SESSION['rol_id']; ?>;
        var ticket_id = <?php echo $ticket_id; ?>;
    </script>
    <script type="text/javascript" src="/ESTADIAS/view/VerCerrado/evidencia/evidencia.js"></script>
    
    <style>
        .custom-file-upload {
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        .custom-file-upload:hover {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }
        #file-preview {
            margin-top: 15px;
        }
        .file-preview-item {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .file-preview-item i {
            margin-right: 8px;
            font-size: 16px;
        }
        .evidencia-item {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
        }
        .evidencia-icon {
            font-size: 24px;
            margin-right: 15px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f1f1f1;
        }
        .evidencia-info {
            flex: 1;
        }
        .evidencia-actions {
            margin-left: 15px;
        }
        .evidencia-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .evidencia-meta {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }
    </style>
</body>
</html>
