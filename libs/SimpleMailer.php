<?php
/**
 * Clase simple para enviar correos usando SMTP (Gmail)
 * Esta es una versión simplificada para entornos de desarrollo
 */
class SimpleMailer {
    private $host = 'smtp.gmail.com';
    private $port = 587;
    private $username = ''; // Tu correo de Gmail
    private $password = ''; // Tu contraseña de aplicación de Gmail
    private $from_email = '';
    private $from_name = 'Sistema GTM';
    private $is_html = true;
    
    /**
     * Constructor
     * 
     * @param string $username Correo de Gmail
     * @param string $password Contraseña de aplicación de Gmail
     * @param string $from_name Nombre remitente (opcional)
     */
    public function __construct($username = '', $password = '', $from_name = 'Sistema GTM') {
        if (!empty($username)) {
            $this->username = $username;
            $this->from_email = $username;
        }
        if (!empty($password)) {
            $this->password = $password;
        }
        if (!empty($from_name)) {
            $this->from_name = $from_name;
        }
    }
    
    /**
     * Enviar un correo electrónico
     * 
     * @param string $to Correo del destinatario
     * @param string $subject Asunto del correo
     * @param string $message Mensaje (puede ser HTML)
     * @param string $reply_to Correo de respuesta (opcional)
     * @return bool Éxito o fracaso
     */
    public function send($to, $subject, $message, $reply_to = '') {
        if (empty($this->username) || empty($this->password)) {
            error_log('SimpleMailer: Faltan credenciales SMTP');
            return false;
        }
        
        // Headers básicos
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: " . ($this->is_html ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        
        if (!empty($reply_to)) {
            $headers .= "Reply-To: $reply_to\r\n";
        }
        
        // En entorno de desarrollo, guardamos en log en lugar de enviar
        $log_file = __DIR__ . "/../logs/emails.log";
        $log_dir = dirname($log_file);
        
        // Crear directorio si no existe
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        // Registrar el intento de envío
        $log_message = "\n\n" . str_repeat('-', 50) . "\n";
        $log_message .= "[" . date('Y-m-d H:i:s') . "] ENVÍO DE CORREO\n";
        $log_message .= "A: $to\n";
        $log_message .= "Asunto: $subject\n";
        $log_message .= "Headers: \n" . $headers . "\n";
        $log_message .= "Mensaje: \n" . $message . "\n";
        $log_message .= str_repeat('-', 50) . "\n";
        
        file_put_contents($log_file, $log_message, FILE_APPEND);
        
        /**
         * En un entorno real, aquí iría el código para conectarse al servidor SMTP y enviar el correo.
         * Como esto requiere una biblioteca completa como PHPMailer o configuración avanzada,
         * para fines de desarrollo simplemente simulamos un envío exitoso y registramos los detalles.
         */
        
        error_log("Correo registrado en $log_file");
        return true;
    }
    
    /**
     * Alias para el método send() para compatibilidad
     * 
     * @param string $to Correo del destinatario
     * @param string $subject Asunto del correo
     * @param string $message Mensaje (puede ser HTML)
     * @param string $reply_to Correo de respuesta (opcional)
     * @return bool Éxito o fracaso
     */
    public function sendMail($to, $subject, $message, $reply_to = '') {
        return $this->send($to, $subject, $message, $reply_to);
    }
}
?>
