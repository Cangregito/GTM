<?php
session_start();
require_once(__DIR__ . '/../models/Notificaciones.php');

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["user_id"])) {
    $response = ["status" => "error", "message" => "No has iniciado sesión"];
    echo json_encode($response);
    exit;
}

// Verificar que el usuario pueda ver las notificaciones de inventario (admin o Mantenimiento planta)
$puedeVerNotificaciones = $_SESSION["rol_id"] == 2 && 
    (($_SESSION["user_nom"] == "Mantenimiento" && isset($_SESSION["user_ape"]) && $_SESSION["user_ape"] == "planta") || 
     ($_SESSION["user_nom"] == "admin"));

if (!$puedeVerNotificaciones) {
    $response = ["status" => "error", "message" => "No tienes permisos para ver notificaciones de inventario"];
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
            $datos = $notificaciones->getNotificacionesInventario($limit);
            
            // Formatear las notificaciones para mostrarlas adecuadamente
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
                
                $notificacionesFormateadas[] = $notificacion;
            }
            
            $response = [
                "status" => "success",
                "notificaciones" => $notificacionesFormateadas
            ];
            
            echo json_encode($response);
            break;
            
        // Contar el número de notificaciones para el badge
        case "contar":
            $total = $notificaciones->contarNotificacionesInventario();
            
            $response = [
                "status" => "success",
                "total" => $total
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
