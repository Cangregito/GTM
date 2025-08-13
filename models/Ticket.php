<?php
require_once(__DIR__ . '/../config/conexion.php');

class Ticket extends Conectar {

    public function insert_ticket($user_id, $cat_id, $ticket_titulo, $ticket_descripcion) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "INSERT INTO tm_ticket (user_id, cat_id, ticket_titulo, ticket_descripcion, fech_crea, estado) 
                    VALUES (?, ?, ?, ?, now(), 1)";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $cat_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $ticket_titulo, PDO::PARAM_STR);
            $stmt->bindParam(4, $ticket_descripcion, PDO::PARAM_STR);
            $stmt->execute();
            return $conectar->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function listar_ticket_x_usu($user_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT
                tm_ticket.ticket_id,
                tm_ticket.cat_id,
                tm_ticket.user_id,
                tm_ticket.ticket_titulo,
                tm_ticket.ticket_descripcion,
                tm_ticket.fech_crea,
                tm_usuario.user_nom,
                tm_usuario.user_ape,
                tm_cat.cat_nomb
            FROM
                tm_ticket
            INNER JOIN tm_cat ON tm_ticket.cat_id = tm_cat.cat_id
            INNER JOIN tm_usuario ON tm_ticket.user_id = tm_usuario.user_id
            WHERE
                tm_ticket.estado = 1
                AND tm_usuario.user_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function listar_todos_tickets() {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT
                tm_ticket.ticket_id,
                tm_ticket.cat_id,
                tm_ticket.user_id,
                tm_ticket.ticket_titulo,
                tm_ticket.ticket_descripcion,
                tm_ticket.fech_crea,
                tm_usuario.user_nom,
                tm_usuario.user_ape,
                tm_cat.cat_nomb
            FROM
                tm_ticket
            INNER JOIN tm_cat ON tm_ticket.cat_id = tm_cat.cat_id
            INNER JOIN tm_usuario ON tm_ticket.user_id = tm_usuario.user_id
            WHERE
                tm_ticket.estado = 1";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
