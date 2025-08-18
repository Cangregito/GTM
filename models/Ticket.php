<?php
require_once(__DIR__ . '/../config/conexion.php');

class Ticket extends Conectar {

    public function insert_ticket($user_id, $cat_id, $ticket_titulo, $ticket_descripcion, $prioridad = 'Medio') {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            
            // Obtener el apellido del usuario
            $sql_user = "SELECT user_ape FROM tm_usuario WHERE user_id = ?";
            $stmt_user = $conectar->prepare($sql_user);
            $stmt_user->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt_user->execute();
            $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
            
            // Añadir el apellido al título del ticket
            if ($user && isset($user['user_ape'])) {
                $ticket_titulo = $ticket_titulo . " (" . $user['user_ape'] . ")";
            }
            
            $sql = "INSERT INTO tm_ticket (user_id, cat_id, ticket_titulo, ticket_descripcion, tik_estado, prioridad, fech_crea, estado) 
                    VALUES (?, ?, ?, ?, 'Abierto', ?, now(), 1)";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $cat_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $ticket_titulo, PDO::PARAM_STR);
            $stmt->bindParam(4, $ticket_descripcion, PDO::PARAM_STR);
            $stmt->bindParam(5, $prioridad, PDO::PARAM_STR);
            $stmt->execute();
            return $conectar->lastInsertId();
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
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
                AND tm_usuario.user_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
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
                tm_ticket.estado = 1";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listar_ticketdetalle_x_ticket($ticket_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT
                    td_ticketdetalle.tickd_id,
                    td_ticketdetalle.ticket_descripcion,
                    td_ticketdetalle.fech_crea,
                    tm_usuario.user_nom,
                    tm_usuario.user_ape,
                    tm_usuario.rol_id
                FROM
                    td_ticketdetalle
                INNER JOIN tm_usuario ON td_ticketdetalle.user_id = tm_usuario.user_id
                WHERE
                    ticket_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function ver_ticket($ticket_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "SELECT
                    tm_ticket.ticket_id,
                    tm_ticket.ticket_titulo,
                    tm_ticket.ticket_descripcion,
                    tm_ticket.tik_estado,
                    tm_ticket.fech_crea,
                    tm_cat.cat_nomb,
                    tm_usuario.user_nom,
                    tm_usuario.user_ape,
                    tm_usuario.rol_id
                FROM
                    tm_ticket
                INNER JOIN tm_cat ON tm_ticket.cat_id = tm_cat.cat_id
                INNER JOIN tm_usuario ON tm_ticket.user_id = tm_usuario.user_id
                WHERE
                    tm_ticket.ticket_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function insertar_respuesta($ticket_id, $user_id, $respuesta) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "INSERT INTO td_ticketdetalle (ticket_id, user_id, ticket_descripcion, fech_crea, estado) VALUES (?, ?, ?, NOW(), 1)";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $respuesta, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function listar_tickets_cerrados($prioridad = '') {
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
                tm_ticket.prioridad,
                tm_ticket.fech_crea,
                tm_usuario.user_nom,
                tm_usuario.user_ape,
                tm_cat.cat_nomb
            FROM
                tm_ticket
            INNER JOIN tm_cat ON tm_ticket.cat_id = tm_cat.cat_id
            INNER JOIN tm_usuario ON tm_ticket.user_id = tm_usuario.user_id
            WHERE
                tm_ticket.estado = 0
                AND tm_ticket.tik_estado = 'Cerrado'";
                
            // Si hay filtro por prioridad
            if (!empty($prioridad)) {
                $sql .= " AND tm_ticket.prioridad = '" . $prioridad . "'";
            }
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listar_tickets_cerrados_x_usu($user_id, $prioridad = '') {
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
                tm_ticket.prioridad,
                tm_ticket.fech_crea,
                tm_usuario.user_nom,
                tm_usuario.user_ape,
                tm_cat.cat_nomb
            FROM
                tm_ticket
            INNER JOIN tm_cat ON tm_ticket.cat_id = tm_cat.cat_id
            INNER JOIN tm_usuario ON tm_ticket.user_id = tm_usuario.user_id
            WHERE
                tm_ticket.estado = 0
                AND tm_usuario.user_id = ?
                AND tm_ticket.tik_estado = 'Cerrado'";
                
            // Si hay filtro por prioridad
            if (!empty($prioridad)) {
                $sql .= " AND tm_ticket.prioridad = '" . $prioridad . "'";
            }
                
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function cerrar_ticket($ticket_id) {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            $sql = "UPDATE tm_ticket SET tik_estado = 'Cerrado', estado = 0 WHERE ticket_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $ticket_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function listar_tickets_abiertos($prioridad = '') {
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
                tm_ticket.prioridad,
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
                
            // Si hay filtro por prioridad
            if (!empty($prioridad)) {
                $sql .= " AND tm_ticket.prioridad = '" . $prioridad . "'";
            }
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function listar_tickets_abiertos_x_usu($user_id, $prioridad = '') {
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
                tm_ticket.prioridad,
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
                
            // Si hay filtro por prioridad
            if (!empty($prioridad)) {
                $sql .= " AND tm_ticket.prioridad = '" . $prioridad . "'";
            }
                
            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /* Métodos para estadísticas del dashboard */
    
    // Obtener total de tickets, abiertos y cerrados para un usuario específico
    public function get_ticket_totales_x_usuario($user_id)
    {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            
            // Convertir a entero para asegurar que sea un número
            $user_id = intval($user_id);
            
            // Verificar que haya un user_id válido
            if ($user_id <= 0) {
                return [
                    'total' => 0,
                    'abiertos' => 0,
                    'cerrados' => 0
                ];
            }
            
            $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN tik_estado = 'Abierto' AND estado = 1 THEN 1 END) as abiertos,
                    COUNT(CASE WHEN tik_estado = 'Cerrado' OR estado = 0 THEN 1 END) as cerrados
                FROM 
                    tm_ticket 
                WHERE 
                    user_id = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $user_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si no hay resultados, devolver valores por defecto
            if (!$result) {
                return [
                    'total' => 0,
                    'abiertos' => 0,
                    'cerrados' => 0
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Error en get_ticket_totales_x_usuario: ' . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'total' => 0,
                'abiertos' => 0,
                'cerrados' => 0
            ];
        }
    }
    
    // Obtener total de tickets por categoría para un usuario específico
    public function get_ticket_totales_x_categoria_usuario($user_id)
    {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            
            // Convertir a entero para asegurar que sea un número
            $user_id = intval($user_id);
            
            // Verificar que haya un user_id válido
            if ($user_id <= 0) {
                return $this->get_datos_ejemplo_categorias();
            }
            
            $sql = "SELECT 
                    c.cat_nomb as categoria,
                    COUNT(*) as total
                FROM 
                    tm_ticket t
                INNER JOIN 
                    tm_cat c ON t.cat_id = c.cat_id
                WHERE 
                    t.user_id = ?
                GROUP BY 
                    c.cat_id, c.cat_nomb
                ORDER BY 
                    total DESC";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $user_id);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si no hay resultados, devolver datos de ejemplo
            if (empty($result)) {
                return $this->get_datos_ejemplo_categorias();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Error en get_ticket_totales_x_categoria_usuario: ' . $e->getMessage());
            return $this->get_datos_ejemplo_categorias();
        }
    }
    
    // Obtener total de tickets, abiertos y cerrados de todo el sistema (para soporte)
    public function get_ticket_totales_general()
    {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            
            $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN tik_estado = 'Abierto' AND estado = 1 THEN 1 END) as abiertos,
                    COUNT(CASE WHEN tik_estado = 'Cerrado' OR estado = 0 THEN 1 END) as cerrados
                FROM 
                    tm_ticket";
            
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    // Obtener total de tickets por categoría de todo el sistema (para soporte)
    public function get_ticket_totales_x_categoria_general()
    {
        try {
            $conectar = $this->getConexion();
            parent::set_names();
            
            $sql = "SELECT 
                    c.cat_nomb as categoria,
                    COUNT(*) as total
                FROM 
                    tm_ticket t
                INNER JOIN 
                    tm_cat c ON t.cat_id = c.cat_id
                GROUP BY 
                    c.cat_id, c.cat_nomb
                ORDER BY 
                    total DESC";
            
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si no hay resultados, devolver datos de ejemplo
            if (empty($result)) {
                return $this->get_datos_ejemplo_categorias();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Error en get_ticket_totales_x_categoria_general: ' . $e->getMessage());
            return $this->get_datos_ejemplo_categorias();
        }
    }
    
    // Función para generar datos de ejemplo para gráficos cuando no hay datos reales
    private function get_datos_ejemplo_categorias()
    {
        return [
            [
                'categoria' => 'Mantenimiento Correctivo',
                'total' => 0
            ],
            [
                'categoria' => 'Mantenimiento Preventivo',
                'total' => 0
            ],
            [
                'categoria' => 'Infraestructura y edificio',
                'total' => 0
            ],
            [
                'categoria' => 'Sistemas y Redes',
                'total' => 0
            ]
        ];
    }
}
