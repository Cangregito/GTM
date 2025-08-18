<?php
require_once("../config/conexion.php");
require_once("../models/Evidencia.php");

$evidencia = new Evidencia();

switch($_GET["op"]) {
    case "subir":
        // Verificar si se recibieron los datos necesarios
        if (!isset($_POST["ticket_id"]) || !isset($_POST["tipo_evidencia"]) || !isset($_POST["descripcion"]) || !isset($_POST["user_id"]) || !isset($_FILES["archivo"])) {
            echo json_encode(["error" => "Datos incompletos"]);
            break;
        }
        
        $ticket_id = intval($_POST["ticket_id"]);
        $tipo_evidencia = $_POST["tipo_evidencia"];
        $descripcion = $_POST["descripcion"];
        $user_id = intval($_POST["user_id"]);
        $archivo = $_FILES["archivo"];
        
        // Validar el archivo
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if ($archivo["size"] > $max_size) {
            echo json_encode(["error" => "El archivo es demasiado grande. El tamaño máximo permitido es 5MB."]);
            break;
        }
        
        if (!in_array($archivo["type"], $allowed_types)) {
            echo json_encode(["error" => "Tipo de archivo no permitido. Por favor, seleccione una imagen (JPG, PNG) o un PDF."]);
            break;
        }
        
        // Procesar el archivo
        $file_ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
        $file_name = "evidencia_" . time() . "_" . uniqid() . "." . $file_ext;
        $upload_path = "../public/uploads/evidencia/" . $file_name;
        
        if (move_uploaded_file($archivo["tmp_name"], $upload_path)) {
            // Guardar en la base de datos
            $result = $evidencia->registrar_evidencia(
                $ticket_id,
                $user_id,
                $tipo_evidencia,
                $descripcion,
                $archivo["name"],
                $file_name,
                $file_ext
            );
            
            if ($result) {
                echo json_encode(["success" => true, "evidencia_id" => $result]);
            } else {
                // Si falla la inserción en BD, eliminar el archivo
                if (file_exists($upload_path)) {
                    unlink($upload_path);
                }
                echo json_encode(["error" => "No se pudo registrar la evidencia en la base de datos"]);
            }
        } else {
            echo json_encode(["error" => "No se pudo subir el archivo. Por favor, inténtelo de nuevo."]);
        }
        break;
        
    case "listar":
        if (!isset($_POST["ticket_id"])) {
            echo json_encode(["error" => "ID de ticket no proporcionado"]);
            break;
        }
        
        $ticket_id = intval($_POST["ticket_id"]);
        $datos = $evidencia->listar_evidencia_x_ticket($ticket_id);
        echo json_encode($datos);
        break;
        
    case "eliminar":
        if (!isset($_POST["evidencia_id"])) {
            echo json_encode(["error" => "ID de evidencia no proporcionado"]);
            break;
        }
        
        $evidencia_id = intval($_POST["evidencia_id"]);
        
        // Obtener información de la evidencia
        $info = $evidencia->obtener_evidencia($evidencia_id);
        
        if ($info) {
            $file_path = "../public/uploads/evidencia/" . $info["archivo_ruta"];
            
            // Eliminar de la base de datos
            $result = $evidencia->eliminar_evidencia($evidencia_id);
            
            if ($result) {
                // Eliminar archivo físico
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["error" => "No se pudo eliminar la evidencia de la base de datos"]);
            }
        } else {
            echo json_encode(["error" => "No se encontró la evidencia especificada"]);
        }
        break;
}
?>
