<?php
require_once('../MainHead/head.php');
?>
<!DOCTYPE html>
<title>Nuevo Tiket</title>
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

                        <!-- Sección 3: Descripción -->
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
                            <button type="submit" name="action" value="add" class="btn btn-rounded btn-inline btn-primary">Guardar</button>
                        </div>
                        </form>
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