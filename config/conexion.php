<?php
// Configuración de la conexión a la base de datos

class Conectar {
    protected $dbh;

    public function __construct() {
        try {
            // Usar variables de entorno si están disponibles, sino valores por defecto
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? 'GTM_DB';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASS'] ?? '';
            
            $this->dbh = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (Exception $e) {
            error_log("Error de conexión a BD: " . $e->getMessage());
            die("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }

    public function getConexion() {
        return $this->dbh;
    }
    public function set_names() {
        return $this->dbh->query("SET NAMES 'utf8'");
    }
    public function ruta() {
        return "http://localhost/ESTADIAS/";
    }
}
?>