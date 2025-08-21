<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html>

<head lang="es">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>GTM</title>
    <link rel="icon" href="/ESTADIAS/public/img/favicon.ico">
    <link href="public/img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
    <link href="public/img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
    <link href="public/img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
    <link href="public/img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">
    <link href="/ESTADIAS/docs/iconGTM.png" rel="icon" type="image/png">
    <link href="public/img/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="public/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/main.css">
    <style>
        body {
            background: #fff;
        }

        .sign-box {
            border: 1.5px solid #e0e0e0;
            box-shadow: 0 4px 24px rgba(60, 60, 60, 0.06);
            background: #fff;
        }

        .sign-title {
            color: #222;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sign-subtitle {
            color: #555;
            text-align: center;
            margin-bottom: 18px;
            font-size: 1rem;
        }

        .btn-minimal {
            background: #e3f0fa;
            color: #1a4c7a;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-minimal:hover {
            background: #b6d6f2;
            color: #163a56;
        }

        .sign-avatar img {
            border: 2px solid #e0e0e0;
            width: 100px;  /* Tamaño fijo de ancho */
            height: 100px; /* Tamaño fijo de alto */
            object-fit: contain; /* Mantiene la proporción sin distorsionar la imagen */
            transition: all 0.3s ease; /* Transición suave */
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            background: #fafbfc;
            color: #222;
        }

        .form-control:focus {
            border-color: #b6d6f2;
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 2px #e3f0fa;
        }
    </style>
</head>

<body class="with-side-menu">
    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <?php if (isset($_GET['m'])): ?>
                    <div class="alert alert-<?php 
                        if ($_GET['m'] == 1) echo 'danger';
                        elseif ($_GET['m'] == 2) echo 'warning';
                        elseif ($_GET['m'] == 'csrf') echo 'danger';
                        elseif ($_GET['m'] == 'password_reset_success') echo 'success';
                        else echo 'info';
                    ?>">
                        <?php
                        if ($_GET['m'] == 1)
                            echo "Correo o contraseña incorrectos.";
                        if ($_GET['m'] == 2)
                            echo "Debes completar todos los campos.";
                        if ($_GET['m'] == 'csrf')
                            echo "Token de seguridad inválido. Intenta de nuevo.";
                        if ($_GET['m'] == 'password_reset_success')
                            echo "<i class='fa fa-check-circle'></i> ¡Contraseña actualizada exitosamente! Ya puedes iniciar sesión con tu nueva contraseña.";
                        ?>
                    </div>
                <?php endif; ?>
                <form class="sign-box" method="POST" action="controller/usuario.php">
                    <input type="hidden" id="rol_id" name="rol_id" value="1">
                    <header class="sign-title">GTM</header>
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="sign-avatar" style="margin-bottom: 20px; width: 100px; height: 100px; margin-left: auto; margin-right: auto;">
                        <img src="public/img/Gerente.png" alt="Gerente de Tienda">
                    </div>
                    <header class="sign-title" id="lbltitulo">Acceso Gerente</header>
                    <div class="sign-subtitle">Gestion Total de Mantenimiento</div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="user_correo" name="user_correo"
                            placeholder="Correo corporativo" required />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="user_pass" name="user_pass"
                            placeholder="Contraseña" required />
                    </div>
                    <div class="form-group">
                        <div class="float-right reset">
                            <a href="reset-password.html">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="float-left reset">
                            <a href="#" id="btnsoporte">Acceso Soporte</a>
                        </div>
                        <button type="submit" class="btn btn-rounded btn-minimal" name="enviar">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="public/js/lib/jquery/jquery.min.js"></script>
    <script src="public/js/lib/tether/tether.min.js"></script>
    <script src="public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script src="public/js/plugins.js"></script>
    <script type="text/javascript" src="public/js/lib/match-height/jquery.matchHeight.min.js"></script>
    <script>
        $(function () {
            $('.page-center').matchHeight({
                target: $('html')
            });

            $(window).resize(function () {
                setTimeout(function () {
                    $('.page-center').matchHeight({ remove: true });
                    $('.page-center').matchHeight({
                        target: $('html')
                    });
                }, 100);
            });
        });
    </script>
    <script src="public/js/app.js"></script>
    <script type="text/javascript" src="index.js"></script>
</body>

</html>