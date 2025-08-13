<?php
require_once(__DIR__ . '/../config/conexion.php');

class Categoria extends Conectar {
    public function get_categoria() {
        $conectar = $this->getConexion();
        $this->set_names();
        $sql = "SELECT * FROM tm_cat WHERE estado=1;";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>