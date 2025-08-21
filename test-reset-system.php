<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prueba del Sistema de Reset - GTM</title>
    <link rel="stylesheet" href="public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/lib/font-awesome/font-awesome.min.css">
    <style>
        body { padding: 20px; }
        .test-container { max-width: 800px; margin: 0 auto; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1><i class="fa fa-test"></i> Prueba del Sistema de Reset de Password</h1>
        
        <div class="alert alert-info">
            <strong>Instrucciones:</strong>
            <ol>
                <li>Asegúrate de tener un usuario válido en la base de datos</li>
                <li>Prueba cada paso del proceso de reset</li>
                <li>Verifica que los correos se guarden en <code>logs/emails.log</code></li>
            </ol>
        </div>

        <h3>Enlaces de Prueba:</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-lock"></i> Paso 1: Solicitar Reset</h5>
                        <p class="card-text">Página inicial para solicitar el reset de password</p>
                        <a href="reset-password.html" class="btn btn-primary" target="_blank">
                            <i class="fa fa-external-link"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-key"></i> Paso 2: Verificar Código</h5>
                        <p class="card-text">Página para ingresar el código de verificación</p>
                        <a href="verify-reset.php?email=test@example.com" class="btn btn-warning" target="_blank">
                            <i class="fa fa-external-link"></i> Abrir (Demo)
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa fa-shield"></i> Paso 3: Nueva Contraseña</h5>
                        <p class="card-text">Página para establecer nueva contraseña</p>
                        <a href="new-password.php?email=test@example.com&token=demo123" class="btn btn-success" target="_blank">
                            <i class="fa fa-external-link"></i> Abrir (Demo)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mt-4">Estado de las Tablas:</h3>
        <?php
        require_once 'config/conexion.php';
        
        try {
            $conn = new Conectar();
            $db = $conn->getConexion();
            
            // Verificar tabla tm_reset_password
            $stmt = $db->query("SHOW TABLES LIKE 'tm_reset_password'");
            if ($stmt->rowCount() > 0) {
                echo '<div class="test-result success"><i class="fa fa-check"></i> Tabla tm_reset_password: <strong>Creada correctamente</strong></div>';
            } else {
                echo '<div class="test-result error"><i class="fa fa-times"></i> Tabla tm_reset_password: <strong>No encontrada</strong></div>';
            }
            
            // Verificar tabla tm_reset_tokens
            $stmt = $db->query("SHOW TABLES LIKE 'tm_reset_tokens'");
            if ($stmt->rowCount() > 0) {
                echo '<div class="test-result success"><i class="fa fa-check"></i> Tabla tm_reset_tokens: <strong>Creada correctamente</strong></div>';
            } else {
                echo '<div class="test-result error"><i class="fa fa-times"></i> Tabla tm_reset_tokens: <strong>No encontrada</strong></div>';
            }
            
            // Verificar columna user_modi
            $stmt = $db->query("SHOW COLUMNS FROM tm_usuario LIKE 'user_modi'");
            if ($stmt->rowCount() > 0) {
                echo '<div class="test-result success"><i class="fa fa-check"></i> Columna user_modi: <strong>Agregada correctamente</strong></div>';
            } else {
                echo '<div class="test-result error"><i class="fa fa-times"></i> Columna user_modi: <strong>No encontrada</strong></div>';
            }
            
            // Verificar usuarios disponibles
            $stmt = $db->query("SELECT COUNT(*) as total FROM tm_usuario WHERE estado = 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo '<div class="test-result info"><i class="fa fa-users"></i> Usuarios activos en el sistema: <strong>' . $result['total'] . '</strong></div>';
            
        } catch (Exception $e) {
            echo '<div class="test-result error"><i class="fa fa-exclamation-triangle"></i> Error de conexión: ' . $e->getMessage() . '</div>';
        }
        ?>

        <h3 class="mt-4">Archivos del Sistema:</h3>
        <?php
        $files = [
            'reset-password.html' => 'Página de solicitud',
            'verify-reset.php' => 'Página de verificación',
            'new-password.php' => 'Página de nueva contraseña',
            'controller/reset_password.php' => 'Controlador principal',
            'models/Usuario.php' => 'Modelo con métodos de reset',
            'libs/SimpleMailer.php' => 'Mailer para envío de correos'
        ];
        
        foreach ($files as $file => $description) {
            if (file_exists($file)) {
                echo '<div class="test-result success"><i class="fa fa-file"></i> ' . $file . ': <strong>' . $description . '</strong></div>';
            } else {
                echo '<div class="test-result error"><i class="fa fa-file-o"></i> ' . $file . ': <strong>No encontrado</strong></div>';
            }
        }
        ?>

        <div class="alert alert-success mt-4">
            <h4><i class="fa fa-check-circle"></i> ¡Sistema Listo!</h4>
            <p>Si todos los elementos muestran estado verde, el sistema de reset de password está completamente instalado y funcional.</p>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">
                <i class="fa fa-home"></i> Volver al Login
            </a>
        </div>
    </div>
</body>
</html>
