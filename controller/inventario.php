<?php
session_start();
require_once(__DIR__ . '/../models/Inventario.php');

// Verificar que el usuario haya iniciado sesión y tenga permisos (usuario "Mantenimiento planta" o admin)
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["rol_id"]) || $_SESSION["rol_id"] != 2 || 
    !isset($_SESSION["user_nom"]) || 
    !(($_SESSION["user_nom"] == "Mantenimiento" && isset($_SESSION["user_ape"]) && $_SESSION["user_ape"] == "planta") || 
      ($_SESSION["user_nom"] == "admin"))) {
    $response = ["status" => "error", "message" => "No tienes permisos para acceder a esta función"];
    echo json_encode($response);
    exit;
}

// Para operaciones CRUD de inventario
if (isset($_GET["op"])) {
    $inventario = new Inventario();
    
    switch($_GET["op"]) {
        // Listar elementos del inventario
        case "listar":
            $datos = $inventario->listar_inventario();
            $data = [];
            
            foreach($datos as $row) {
                $sub_array = [];
                $sub_array[] = $row["inv_id"];
                $sub_array[] = $row["inv_nombre"];
                $sub_array[] = $row["inv_descripcion"] ? $row["inv_descripcion"] : "-";
                $sub_array[] = $row["inv_cantidad"];
                
                // Estado con etiqueta de color
                if($row["inv_estado"] == "Activo") {
                    $sub_array[] = '<span class="label label-pill label-success">Activo</span>';
                } elseif($row["inv_estado"] == "Inactivo") {
                    $sub_array[] = '<span class="label label-pill label-warning">Inactivo</span>';
                } else {
                    $sub_array[] = '<span class="label label-pill label-danger">Descompuesto</span>';
                }
                
                $sub_array[] = $row["inv_categoria"] ? $row["inv_categoria"] : "-";
                $sub_array[] = date("d/m/Y H:i", strtotime($row["inv_fecha_registro"]));
                
                // Botones de acción
                $sub_array[] = '<button type="button" onClick="editar('.$row["inv_id"].');" id="'.$row["inv_id"].'" class="btn btn-warning btn-xs">Editar</button>';
                $sub_array[] = '<button type="button" onClick="eliminar('.$row["inv_id"].');" id="'.$row["inv_id"].'" class="btn btn-danger btn-xs">Eliminar</button>';
                
                $data[] = $sub_array;
            }
            
            $results = [
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            ];
            
            echo json_encode($results);
            break;
            
        // Obtener elemento por ID para edición
        case "get_inventario_x_id":
            if(isset($_POST["inv_id"])) {
                $datos = $inventario->get_inventario_x_id($_POST["inv_id"]);
                if(is_array($datos) && count($datos) > 0) {
                    echo json_encode($datos);
                } else {
                    echo json_encode(["error" => "Elemento no encontrado"]);
                }
            } else {
                echo json_encode(["error" => "ID del elemento no proporcionado"]);
            }
            break;
            
        // Insertar nuevo elemento
        case "insert":
            if(isset($_POST["inv_nombre"]) && isset($_POST["inv_cantidad"]) && isset($_POST["inv_estado"])) {
                $inv_descripcion = isset($_POST["inv_descripcion"]) ? $_POST["inv_descripcion"] : "";
                $inv_categoria = isset($_POST["inv_categoria"]) ? $_POST["inv_categoria"] : "";
                $inv_usuario_id = $_SESSION["user_id"];
                
                $result = $inventario->insert_inventario(
                    $_POST["inv_nombre"],
                    $inv_descripcion,
                    $_POST["inv_cantidad"],
                    $_POST["inv_estado"],
                    $inv_categoria,
                    $inv_usuario_id
                );
                
                if($result) {
                    echo json_encode(["status" => "success", "message" => "Elemento agregado correctamente"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al registrar el elemento"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            }
            break;
            
        // Actualizar elemento existente
        case "update":
            if(isset($_POST["inv_id"]) && isset($_POST["inv_nombre"]) && isset($_POST["inv_cantidad"]) && isset($_POST["inv_estado"])) {
                $inv_descripcion = isset($_POST["inv_descripcion"]) ? $_POST["inv_descripcion"] : "";
                $inv_categoria = isset($_POST["inv_categoria"]) ? $_POST["inv_categoria"] : "";
                
                $result = $inventario->update_inventario(
                    $_POST["inv_id"],
                    $_POST["inv_nombre"],
                    $inv_descripcion,
                    $_POST["inv_cantidad"],
                    $_POST["inv_estado"],
                    $inv_categoria
                );
                
                if($result) {
                    echo json_encode(["status" => "success", "message" => "Elemento actualizado correctamente"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al actualizar el elemento"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            }
            break;
            
        // Eliminar elemento
        case "delete":
            if(isset($_POST["inv_id"])) {
                $result = $inventario->delete_inventario($_POST["inv_id"]);
                
                if($result) {
                    echo json_encode(["status" => "success", "message" => "Elemento eliminado correctamente"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al eliminar el elemento"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID del elemento no proporcionado"]);
            }
            break;
            
        // Obtener estadísticas del inventario
        case "stats":
            $datos = $inventario->get_inventario_stats();
            echo json_encode(["status" => "success", "data" => $datos]);
            break;
    }
}
?>
