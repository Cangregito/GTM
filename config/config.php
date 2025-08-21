<?php
/**
 * Archivo de configuración de rutas
 * Define las rutas base del proyecto para evitar hardcodeo
 */

class Config {
    // Configuración de rutas
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $path = '/ESTADIAS';
        return $protocol . "://" . $host . $path;
    }
    
    public static function getBasePath() {
        return '/ESTADIAS';
    }
    
    public static function getControllerPath() {
        return self::getBasePath() . '/controller';
    }
    
    public static function getViewPath() {
        return self::getBasePath() . '/view';
    }
    
    public static function getPublicPath() {
        return self::getBasePath() . '/public';
    }
    
    public static function getUploadsPath() {
        return self::getPublicPath() . '/uploads';
    }
    
    // Configuración de aplicación
    public static function getAppName() {
        return 'GTM - Gestión Total de Mantenimiento';
    }
    
    public static function getVersion() {
        return '1.0.0';
    }
    
    // Configuración de archivos
    public static function getMaxFileSize() {
        return 5 * 1024 * 1024; // 5MB
    }
    
    public static function getAllowedFileTypes() {
        return ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    }
}
?>
