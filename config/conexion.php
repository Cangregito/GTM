<?php
// Configuración de la conexión a la base de datos

class Conectar {
    protected $dbh;

    public function __construct() {
        try {
            $this->dbh = new PDO(
                "mysql:host=localhost;dbname=GTM_DB",
                "root",
                ""
            );
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die("Error de conexión: " . $e->getMessage());
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