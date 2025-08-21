<?php
require_once(__DIR__ . '/../config/conexion.php');

class Notificaciones extends Conectar
{
    /**
     * Obtiene todas las notificaciones de inventario para mostrar en el header
     * @param int $limit Número máximo de notificaciones a retornar
     * @return array Array de notificaciones
     */
    public function getNotificacionesInventario($limit = 5)
    {
        $conectar = $this->getConexion();
        
        // Obtener elementos con bajo stock (menos de 5 unidades)
        $sql = "SELECT 
                    'bajo_stock' as tipo,
                    inv_id,
                    inv_nombre,
                    inv_cantidad,
                    inv_categoria,
                    inv_fecha_actualizacion
                FROM 
                    tm_inventario
                WHERE 
                    inv_estado = 'Activo' 
                    AND inv_cantidad <= 5
                    AND inv_cantidad > 0
                ORDER BY 
                    inv_cantidad ASC,
                    inv_fecha_actualizacion DESC
                LIMIT " . intval($limit);
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $bajoStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener elementos descompuestos
        $sql = "SELECT 
                    'descompuesto' as tipo,
                    inv_id,
                    inv_nombre,
                    inv_cantidad,
                    inv_categoria,
                    inv_fecha_actualizacion
                FROM 
                    tm_inventario
                WHERE 
                    inv_estado = 'Descompuesto'
                ORDER BY 
                    inv_fecha_actualizacion DESC
                LIMIT " . intval($limit);
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $descompuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combinar y ordenar por fecha de actualización
        $notificaciones = array_merge($bajoStock, $descompuestos);
        
        usort($notificaciones, function($a, $b) {
            $dateA = strtotime($a['inv_fecha_actualizacion'] ?? date('Y-m-d H:i:s'));
            $dateB = strtotime($b['inv_fecha_actualizacion'] ?? date('Y-m-d H:i:s'));
            return $dateB - $dateA; // Ordenar de más reciente a más antiguo
        });
        
        // Limitar al número total de notificaciones
        return array_slice($notificaciones, 0, $limit);
    }
    
    /**
     * Contar el número total de notificaciones activas
     * @return int Número de notificaciones
     */
    public function contarNotificacionesInventario()
    {
        $conectar = $this->getConexion();
        
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM tm_inventario WHERE inv_estado = 'Activo' AND inv_cantidad <= 5 AND inv_cantidad > 0) +
                    (SELECT COUNT(*) FROM tm_inventario WHERE inv_estado = 'Descompuesto') as total_notificaciones";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return intval($result['total_notificaciones']);
    }
    
    /**
     * Calcular el tiempo transcurrido desde una fecha en formato amigable
     * @param string $datetime La fecha en formato Y-m-d H:i:s
     * @return string Tiempo transcurrido en formato legible
     */
    public function tiempoTranscurrido($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return "Justo ahora";
        } elseif ($diff < 3600) {
            $mins = round($diff / 60);
            return "Hace " . $mins . " minuto" . ($mins > 1 ? "s" : "");
        } elseif ($diff < 86400) {
            $hours = round($diff / 3600);
            return "Hace " . $hours . " hora" . ($hours > 1 ? "s" : "");
        } elseif ($diff < 604800) {
            $days = round($diff / 86400);
            return "Hace " . $days . " día" . ($days > 1 ? "s" : "");
        } else {
            return date("d/m/Y", $time);
        }
    }

    /**
     * Obtener notificaciones específicas para gerentes
     * @param int $limit Número máximo de notificaciones a retornar
     * @return array Array de notificaciones para gerente
     */
    public function getNotificacionesGerente($limit = 5)
    {
        $conectar = $this->getConexion();
        $notificaciones = [];
        
        // 1. Tickets de alta prioridad pendientes
        $sql = "SELECT 
                    'ticket_alta_prioridad' as tipo,
                    tick_id,
                    tick_titulo,
                    tick_descrip,
                    tick_prioridad,
                    tick_fechacreacion,
                    u.usu_nom,
                    u.usu_ape
                FROM 
                    tm_ticket t
                    INNER JOIN tm_usuario u ON t.usu_id = u.usu_id
                WHERE 
                    tick_estado = 'Abierto' 
                    AND tick_prioridad = 'Alta'
                ORDER BY 
                    tick_fechacreacion DESC
                LIMIT 3";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $ticketsAlta = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Tickets pendientes por mucho tiempo (más de 3 días)
        $sql = "SELECT 
                    'ticket_pendiente_largo' as tipo,
                    tick_id,
                    tick_titulo,
                    tick_descrip,
                    tick_prioridad,
                    tick_fechacreacion,
                    u.usu_nom,
                    u.usu_ape,
                    DATEDIFF(NOW(), tick_fechacreacion) as dias_pendiente
                FROM 
                    tm_ticket t
                    INNER JOIN tm_usuario u ON t.usu_id = u.usu_id
                WHERE 
                    tick_estado = 'Abierto' 
                    AND DATEDIFF(NOW(), tick_fechacreacion) >= 3
                ORDER BY 
                    tick_fechacreacion ASC
                LIMIT 2";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $ticketsPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 3. Inventario crítico que afecta operaciones
        $sql = "SELECT 
                    'inventario_critico' as tipo,
                    inv_id,
                    inv_nombre,
                    inv_cantidad,
                    inv_categoria,
                    inv_fecha_actualizacion
                FROM 
                    tm_inventario
                WHERE 
                    inv_estado = 'Activo' 
                    AND inv_cantidad <= 2
                    AND inv_categoria IN ('Equipos', 'Computadoras', 'Sistema POS')
                ORDER BY 
                    inv_cantidad ASC,
                    inv_fecha_actualizacion DESC
                LIMIT 2";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $inventarioCritico = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combinar todas las notificaciones
        $notificaciones = array_merge($ticketsAlta, $ticketsPendientes, $inventarioCritico);
        
        // Ordenar por relevancia (alta prioridad primero)
        usort($notificaciones, function($a, $b) {
            $prioridadA = $this->getPrioridadNotificacion($a['tipo']);
            $prioridadB = $this->getPrioridadNotificacion($b['tipo']);
            return $prioridadB - $prioridadA;
        });
        
        return array_slice($notificaciones, 0, $limit);
    }
    
    /**
     * Contar notificaciones para gerente
     * @return int Número total de notificaciones importantes
     */
    public function contarNotificacionesGerente()
    {
        $conectar = $this->getConexion();
        
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM tm_ticket WHERE tick_estado = 'Abierto' AND tick_prioridad = 'Alta') +
                    (SELECT COUNT(*) FROM tm_ticket WHERE tick_estado = 'Abierto' AND DATEDIFF(NOW(), tick_fechacreacion) >= 3) +
                    (SELECT COUNT(*) FROM tm_inventario WHERE inv_estado = 'Activo' AND inv_cantidad <= 2 AND inv_categoria IN ('Equipos', 'Computadoras', 'Sistema POS'))
                    as total_notificaciones";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return intval($result['total_notificaciones']);
    }
    
    /**
     * Obtener prioridad numérica para ordenar notificaciones
     * @param string $tipo Tipo de notificación
     * @return int Prioridad (mayor número = mayor prioridad)
     */
    private function getPrioridadNotificacion($tipo)
    {
        switch($tipo) {
            case 'ticket_alta_prioridad':
                return 3;
            case 'inventario_critico':
                return 2;
            case 'ticket_pendiente_largo':
                return 1;
            default:
                return 0;
        }
    }
    
    /**
     * Obtener estadísticas de rendimiento para el gerente
     * @return array Estadísticas del sistema
     */
    public function getEstadisticasGerente()
    {
        $conectar = $this->getConexion();
        
        // Tickets resueltos esta semana
        $sql = "SELECT COUNT(*) as tickets_resueltos_semana
                FROM tm_ticket 
                WHERE tick_estado = 'Cerrado' 
                AND WEEK(tick_fechacreacion) = WEEK(NOW())
                AND YEAR(tick_fechacreacion) = YEAR(NOW())";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $resueltosSemana = $stmt->fetch(PDO::FETCH_ASSOC)['tickets_resueltos_semana'];
        
        // Tickets pendientes total
        $sql = "SELECT COUNT(*) as tickets_pendientes
                FROM tm_ticket 
                WHERE tick_estado = 'Abierto'";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $pendientesTotal = $stmt->fetch(PDO::FETCH_ASSOC)['tickets_pendientes'];
        
        // Tiempo promedio de resolución (últimos 30 días)
        $sql = "SELECT AVG(DATEDIFF(tick_fecha_cierre, tick_fechacreacion)) as tiempo_promedio
                FROM tm_ticket 
                WHERE tick_estado = 'Cerrado' 
                AND tick_fecha_cierre >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $tiempoPromedio = $stmt->fetch(PDO::FETCH_ASSOC)['tiempo_promedio'] ?? 0;
        
        return [
            'tickets_resueltos_semana' => intval($resueltosSemana),
            'tickets_pendientes' => intval($pendientesTotal),
            'tiempo_promedio_resolucion' => round($tiempoPromedio, 1)
        ];
    }
}
