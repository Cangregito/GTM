// Función para listar tickets abiertos
function listar_tickets_abiertos() {
    try {
        $conectar = $this->getConexion();
        parent::set_names();
        $sql = "SELECT
            tm_ticket.ticket_id,
            tm_ticket.cat_id,
            tm_ticket.user_id,
            tm_ticket.ticket_titulo,
            tm_ticket.ticket_descripcion,
            tm_ticket.tik_estado,
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
            AND tm_ticket.tik_estado = 'Abierto'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Función para listar tickets abiertos por usuario
function listar_tickets_abiertos_x_usu($user_id) {
    try {
        $conectar = $this->getConexion();
        parent::set_names();
        $sql = "SELECT
            tm_ticket.ticket_id,
            tm_ticket.cat_id,
            tm_ticket.user_id,
            tm_ticket.ticket_titulo,
            tm_ticket.ticket_descripcion,
            tm_ticket.tik_estado,
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
            AND tm_usuario.user_id = ?
            AND tm_ticket.tik_estado = 'Abierto'";
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}
