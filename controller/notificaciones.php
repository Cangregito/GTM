<?php
session_start();
require_once(__DIR__ . '/../models/Notificaciones.php');

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    $response = ["status" => "error", "message" => "No has iniciado sesión"];
    echo json_encode($response);
    exit;
}

// Verificar permisos según el rol
$esGerente = $_SESSION["rol_id"] == 1;
$puedeVerNotificacionesInventario = $_SESSION["rol_id"] == 2 && 
    (($_SESSION["user_nom"] == "Mantenimiento" && isset($_SESSION["user_ape"]) && $_SESSION["user_ape"] == "planta") || 
     ($_SESSION["user_nom"] == "admin"));

if (!$esGerente && !$puedeVerNotificacionesInventario) {
    $response = ["status" => "error", "message" => "No tienes permisos para ver notificaciones"];
    echo json_encode($response);
    exit;
}

// Procesamos las solicitudes
if (isset($_GET["op"])) {
    $notificaciones = new Notificaciones();
    
    switch($_GET["op"]) {
        // Obtener notificaciones para el panel desplegable
        case "listar":
            $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 5;
            
            if ($esGerente) {
                // Notificaciones específicas para gerente
                $datos = $notificaciones->getNotificacionesGerente($limit);
                $notificacionesFormateadas = [];
                
                foreach($datos as $item) {
                    $notificacion = [];
                    
                    if ($item["tipo"] == "ticket_alta_prioridad") {
                        $tiempoTranscurrido = $notificaciones->tiempoTranscurrido($item["tick_fechacreacion"]);
                        $notificacion = [
                            "id" => $item["tick_id"],
                            "tipo" => "ticket_alta_prioridad",
                            "mensaje" => "🚨 <strong>URGENTE:</strong> " . $item["tick_titulo"] . " - " . $item["usu_nom"] . " " . $item["usu_ape"],
                            "categoria" => "Ticket Crítico",
                            "tiempo" => $tiempoTranscurrido,
                            "icono" => "font-icon-fire",
                            "color" => "label-danger"
                        ];
                    } else if ($item["tipo"] == "ticket_pendiente_largo") {
                        $tiempoTranscurrido = $notificaciones->tiempoTranscurrido($item["tick_fechacreacion"]);
                        $diasPendiente = $item["dias_pendiente"] ?? 0;
                        $notificacion = [
                            "id" => $item["tick_id"],
                            "tipo" => "ticket_pendiente_largo",
                            "mensaje" => "⏰ Ticket pendiente <strong>{$diasPendiente} días:</strong> " . $item["tick_titulo"],
                            "categoria" => "Gestión",
                            "tiempo" => $tiempoTranscurrido,
                            "icono" => "font-icon-clock",
                            "color" => "label-warning"
                        ];
                    } else if ($item["tipo"] == "inventario_critico") {
                        $tiempoTranscurrido = $notificaciones->tiempoTranscurrido($item["inv_fecha_actualizacion"]);
                        $notificacion = [
                            "id" => $item["inv_id"],
                            "tipo" => "inventario_critico",
                            "mensaje" => "⚠️ <strong>CRÍTICO:</strong> " . $item["inv_nombre"] . " - Solo {$item['inv_cantidad']} disponibles",
                            "categoria" => $item["inv_categoria"],
                            "tiempo" => $tiempoTranscurrido,
                            "icono" => "font-icon-warning",
                            "color" => "label-danger"
                        ];
                    }
                    
                    if (!empty($notificacion)) {
                        $notificacionesFormateadas[] = $notificacion;
                    }
                }
                
            } else {
                // Notificaciones de inventario para admin/mantenimiento
                $datos = $notificaciones->getNotificacionesInventario($limit);
                $notificacionesFormateadas = [];
                
                foreach($datos as $item) {
                    $notificacion = [];
                    
                    // Formatear el tiempo transcurrido
                    $tiempoTranscurrido = $notificaciones->tiempoTranscurrido($item["inv_fecha_actualizacion"]);
                    
                    if ($item["tipo"] == "bajo_stock") {
                        $notificacion = [
                            "id" => $item["inv_id"],
                            "tipo" => "bajo_stock",
                            "mensaje" => "Bajo stock: <strong>{$item['inv_nombre']}</strong> - Solo quedan <strong>{$item['inv_cantidad']}</strong> unidades",
                            "categoria" => $item["inv_categoria"],
                            "tiempo" => $tiempoTranscurrido,
                            "icono" => "font-icon-warning",
                            "color" => "label-warning"
                        ];
                    } else if ($item["tipo"] == "descompuesto") {
                        $notificacion = [
                            "id" => $item["inv_id"],
                            "tipo" => "descompuesto",
                            "mensaje" => "Equipo descompuesto: <strong>{$item['inv_nombre']}</strong> - Requiere mantenimiento",
                            "categoria" => $item["inv_categoria"],
                            "tiempo" => $tiempoTranscurrido,
                            "icono" => "font-icon-fire",
                            "color" => "label-danger"
                        ];
                    }
                    
                    if (!empty($notificacion)) {
                        $notificacionesFormateadas[] = $notificacion;
                    }
                }
            }
            
            $response = [
                "status" => "success",
                "notificaciones" => $notificacionesFormateadas
            ];
            
            echo json_encode($response);
            break;
            
        // Contar el número de notificaciones para el badge
        case "contar":
            if ($esGerente) {
                $total = $notificaciones->contarNotificacionesGerente();
            } else {
                $total = $notificaciones->contarNotificacionesInventario();
            }
            
            $response = [
                "status" => "success",
                "total" => $total
            ];
            
            echo json_encode($response);
            break;
            
        // Obtener estadísticas para gerente
        case "estadisticas":
            if (!$esGerente) {
                $response = ["status" => "error", "message" => "Solo disponible para gerentes"];
                echo json_encode($response);
                break;
            }
            
            $estadisticas = $notificaciones->getEstadisticasGerente();
            
            $response = [
                "status" => "success",
                "estadisticas" => $estadisticas
            ];
            
            echo json_encode($response);
            break;
            
        default:
            $response = ["status" => "error", "message" => "Operación no válida"];
            echo json_encode($response);
            break;
    }
} else {
    $response = ["status" => "error", "message" => "No se ha especificado una operación"];
    echo json_encode($response);
}
?>
