<?php
require_once(__DIR__ . '/../config/conexion.php');

class Evidencia extends Conectar {

    public function registrar_evidencia($ticket_id, $user_id, $tipo_evidencia, $descripcion, $archivo_nombre, $archivo_ruta, $archivo_extension) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "INSERT INTO tm_evidencia (ticket_id, user_id, tipo_evidencia, descripcion, archivo_nombre, archivo_ruta, archivo_extension, fecha_subida, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $tipo_evidencia, PDO::PARAM_STR);
            $stmt->bindParam(4, $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(5, $archivo_nombre, PDO::PARAM_STR);
            $stmt->bindParam(6, $archivo_ruta, PDO::PARAM_STR);
            $stmt->bindParam(7, $archivo_extension, PDO::PARAM_STR);
            $stmt->execute();
            return $conectar->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function listar_evidencia_x_ticket($ticket_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT 
                    e.evidencia_id,
                    e.ticket_id,
                    e.tipo_evidencia,
                    e.descripcion,
                    e.archivo_nombre,
                    e.archivo_ruta,
                    e.archivo_extension,
                    e.fecha_subida,
                    u.user_nom,
                    u.user_ape
                FROM 
                    tm_evidencia e
                INNER JOIN 
                    tm_usuario u ON e.user_id = u.user_id
                WHERE 
                    e.ticket_id = ? AND e.estado = 1
                ORDER BY 
                    e.fecha_subida DESC";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function obtener_evidencia($evidencia_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_evidencia WHERE evidencia_id = ? AND estado = 1";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $evidencia_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function eliminar_evidencia($evidencia_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "UPDATE tm_evidencia SET estado = 0 WHERE evidencia_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $evidencia_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
