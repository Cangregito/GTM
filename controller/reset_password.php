<?php
session_start();
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Usuario.php');
require_once(__DIR__ . '/../libs/SimpleMailer.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$action = $_POST['action'] ?? '';
$usuario = new Usuario();

switch ($action) {
    case 'request_reset':
        handleRequestReset();
        break;
    
    case 'verify_code':
        handleVerifyCode();
        break;
    
    case 'reset_password':
        handleResetPassword();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

function handleRequestReset() {
    global $usuario;
    
    $email = trim($_POST['user_email'] ?? '');
    
    // Validaciones
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico es requerido']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de correo electrónico inválido']);
        return;
    }
    
    // Verificar si el usuario existe
    $userExists = $usuario->verificar_usuario_existe($email);
    
    if (!$userExists) {
        // Por seguridad, no revelamos si el email existe o no
        echo json_encode([
            'success' => true, 
            'message' => 'Si el correo existe en nuestro sistema, recibirás un código de verificación en unos minutos.'
        ]);
        return;
    }
    
    // Generar código de verificación de 6 dígitos
    $resetCode = sprintf('%06d', mt_rand(0, 999999));
    $resetExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Guardar código en la base de datos
    $result = $usuario->guardar_codigo_reset($email, $resetCode, $resetExpiry);
    
    if ($result) {
        // Enviar email con el código
        $emailSent = sendResetEmail($email, $resetCode);
        
        if ($emailSent) {
            echo json_encode([
                'success' => true,
                'message' => 'Código de verificación enviado. Revisa tu correo electrónico.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar el correo. Intenta nuevamente.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error interno. Intenta nuevamente.'
        ]);
    }
}

function handleVerifyCode() {
    global $usuario;
    
    $email = trim($_POST['user_email'] ?? '');
    $code = trim($_POST['reset_code'] ?? '');
    
    // Validaciones
    if (empty($email) || empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        return;
    }
    
    if (strlen($code) !== 6 || !ctype_digit($code)) {
        echo json_encode(['success' => false, 'message' => 'El código debe tener 6 dígitos']);
        return;
    }
    
    // Verificar código
    $isValid = $usuario->verificar_codigo_reset($email, $code);
    
    if ($isValid) {
        // Generar token temporal para el cambio de contraseña
        $resetToken = bin2hex(random_bytes(32));
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        $tokenSaved = $usuario->guardar_token_reset($email, $resetToken, $tokenExpiry);
        
        if ($tokenSaved) {
            echo json_encode([
                'success' => true,
                'message' => 'Código verificado correctamente',
                'reset_token' => $resetToken
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error interno. Intenta nuevamente.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Código inválido o expirado'
        ]);
    }
}

function handleResetPassword() {
    global $usuario;
    
    $email = trim($_POST['user_email'] ?? '');
    $token = trim($_POST['reset_token'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validaciones
    if (empty($email) || empty($token) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        return;
    }
    
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
        return;
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
        return;
    }
    
    // Verificar token
    $tokenValid = $usuario->verificar_token_reset($email, $token);
    
    if (!$tokenValid) {
        echo json_encode(['success' => false, 'message' => 'Token inválido o expirado']);
        return;
    }
    
    // Actualizar contraseña
    $passwordUpdated = $usuario->actualizar_password($email, $newPassword);
    
    if ($passwordUpdated) {
        // Limpiar códigos y tokens de reset
        $usuario->limpiar_reset_data($email);
        
        echo json_encode([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la contraseña. Intenta nuevamente.'
        ]);
    }
}

function sendResetEmail($email, $code) {
    try {
        $mailer = new SimpleMailer();
        
        $subject = 'Código de Recuperación de Contraseña - GTM';
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .code { font-size: 24px; font-weight: bold; color: #007bff; text-align: center; 
                        background: white; padding: 15px; border: 2px dashed #007bff; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>GTM - Recuperación de Contraseña</h1>
                </div>
                <div class='content'>
                    <h2>Código de Verificación</h2>
                    <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                    <p>Tu código de verificación es:</p>
                    <div class='code'>$code</div>
                    <p><strong>Este código expira en 15 minutos.</strong></p>
                    <p>Si no solicitaste este restablecimiento, puedes ignorar este correo.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 GTM - Gestión Total de Mantenimiento</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $mailer->sendMail($email, $subject, $message);
        
    } catch (Exception $e) {
        error_log("Error enviando email de reset: " . $e->getMessage());
        return false;
    }
}
?>
