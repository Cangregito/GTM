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
}
