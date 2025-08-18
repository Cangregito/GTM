<?php
// Controlador para enviar mensajes desde el Centro de Ayuda
session_start();

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "No has iniciado sesión"]);
        exit;
    }
    
    // Obtener datos del formulario
    $subject = isset($_POST['subject']) ? $_POST['subject'] : "";
    $message = isset($_POST['message']) ? $_POST['message'] : "";
    $recipient = isset($_POST['recipient']) ? $_POST['recipient'] : "Jassiel.rr1502@gmail.com"; // Valor por defecto
    
    // Validar datos
    if (empty($subject) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios"]);
        exit;
    }
    
    // Obtener información del usuario que envía
    $user_name = $_SESSION["user_nom"] . " " . $_SESSION["user_ape"];
    $user_email = isset($_SESSION["user_correo"]) ? $_SESSION["user_correo"] : "usuario@gtm.com";
    $user_id = $_SESSION["user_id"];
    $rol_id = $_SESSION["rol_id"];
    $rol_name = ($rol_id == 1) ? "Usuario" : (($rol_id == 2) ? "Soporte" : "Administrador");
    
    // Construir el mensaje completo con la información del usuario
    $email_subject = "GTM - Mensaje de ayuda: " . $subject;
    $email_message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { padding: 20px; }
            .header { background-color: #3c8dbc; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { padding: 15px; text-align: center; font-size: 12px; color: #777; }
            .info { margin-bottom: 20px; padding: 10px; background-color: #eaf6ff; border-left: 5px solid #3c8dbc; }
            .message { padding: 15px; background-color: white; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Mensaje de Ayuda del Sistema GTM</h2>
            </div>
            <div class='content'>
                <div class='info'>
                    <p><strong>De:</strong> $user_name</p>
                    <p><strong>Email:</strong> $user_email</p>
                    <p><strong>ID de Usuario:</strong> $user_id</p>
                    <p><strong>Rol:</strong> $rol_name</p>
                    <p><strong>Asunto:</strong> $subject</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                </div>
                <div class='message'>
                    <h3>Mensaje:</h3>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
            </div>
            <div class='footer'>
                <p>Este mensaje fue enviado desde el Centro de Ayuda del Sistema GTM.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Incluir la clase SimpleMailer
    require_once(__DIR__ . '/../libs/SimpleMailer.php');
    
    // Crear instancia de SimpleMailer
    // En producción, reemplaza con tus credenciales reales
    $mailer = new SimpleMailer(
        '', // Tu correo de Gmail
        '', // Tu contraseña de aplicación de Gmail
        'Sistema GTM - Centro de Ayuda'
    );
    
    // Intentar enviar el correo
    if ($mailer->send($recipient, $email_subject, $email_message, $user_email)) {
        echo json_encode(["status" => "success"]);
    } else {
        // Si falla el envío, registrar el mensaje en un archivo de log como alternativa
        $log_file = __DIR__ . "/../logs/help_messages.log";
        $log_dir = dirname($log_file);
        
        // Crear directorio de logs si no existe
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        // Guardar mensaje en archivo de log
        $log_message = "[" . date('Y-m-d H:i:s') . "] Usuario: $user_name ($user_id) - Asunto: $subject - Mensaje: " . str_replace("\n", " ", $message) . "\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        // En entorno de producción, devolver éxito aunque haya fallado el mail
        // En desarrollo, mostrar error para depuración
        echo json_encode(["status" => "success"]);
        // echo json_encode(["status" => "error", "message" => "No se pudo enviar el correo"]);
    }
} else {
    // Si no es una petición POST, devolver error
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
?>
