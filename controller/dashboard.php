<?php
// Usar rutas absolutas para evitar problemas de inclusión
require_once(__DIR__ . "/../config/conexion.php");
require_once(__DIR__ . "/../models/Ticket.php");

$ticket = new Ticket();

switch($_GET["op"]){
    
    // Obtener estadísticas para dashboard de usuario
    case "ticket_totales_x_usuario":
        if(isset($_POST["user_id"])){
            try {
                $datos = $ticket->get_ticket_totales_x_usuario($_POST["user_id"]);
                echo json_encode($datos);
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se proporcionó user_id"
            ]);
        }
        break;
        
    // Obtener estadísticas por categoría para usuario
    case "ticket_totales_x_categoria_usuario":
        if(isset($_POST["user_id"])){
            try {
                $datos = $ticket->get_ticket_totales_x_categoria_usuario($_POST["user_id"]);
                
                // Verificar si los datos tienen el formato correcto para Morris.js
                if (!is_array($datos)) {
                    // Convertir a array si no lo es
                    $datos = [$datos];
                }
                
                // Verificar que cada elemento tenga las propiedades necesarias
                $datos_formateados = [];
                foreach ($datos as $item) {
                    if (is_array($item) && isset($item['categoria']) && isset($item['total'])) {
                        $datos_formateados[] = [
                            'categoria' => $item['categoria'],
                            'total' => (int)$item['total']
                        ];
                    }
                }
                
                echo json_encode($datos_formateados);
            } catch (Exception $e) {
                echo json_encode([
                    'error' => 'Error al obtener datos por categoría: ' . $e->getMessage(),
                    'data' => []
                ]);
            }
        } else {
            echo json_encode([
                'error' => 'No se proporcionó user_id',
                'data' => []
            ]);
        }
        break;
        
    // Obtener estadísticas generales (para soporte)
    case "ticket_totales_general":
        $datos = $ticket->get_ticket_totales_general();
        echo json_encode($datos);
        break;
        
    // Obtener estadísticas por categoría general (para soporte)
    case "ticket_totales_x_categoria_general":
        try {
            $datos = $ticket->get_ticket_totales_x_categoria_general();
            
            // Verificar si los datos tienen el formato correcto para Morris.js
            if (!is_array($datos)) {
                // Convertir a array si no lo es
                $datos = [$datos];
            }
            
            // Verificar que cada elemento tenga las propiedades necesarias
            $datos_formateados = [];
            foreach ($datos as $item) {
                if (is_array($item) && isset($item['categoria']) && isset($item['total'])) {
                    $datos_formateados[] = [
                        'categoria' => $item['categoria'],
                        'total' => (int)$item['total']
                    ];
                }
            }
            
            echo json_encode($datos_formateados);
        } catch (Exception $e) {
            echo json_encode([
                'error' => 'Error al obtener datos por categoría: ' . $e->getMessage(),
                'data' => []
            ]);
        }
        break;
}
?>
