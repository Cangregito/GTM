<?php
require_once("../config/conexion.php");
require_once("../models/Ticket.php");

$ticket = new Ticket();

switch($_GET["op"]){
    case "insert":
        $result = $ticket->insert_ticket(
            $_POST["user_id"],         
            $_POST["cat_id"],
            $_POST["ticket_titulo"],     
            $_POST["ticket_descripcion"],
            $_POST["prioridad"]
        );
        echo json_encode(['success' => $result ? true : false, 'ticket_id' => $result]);
        break;

    case "listar_x_usu":
        $rol_id = $_POST["rol_id"];
        $prioridad = isset($_POST["prioridad"]) ? $_POST["prioridad"] : '';
        
        if ($rol_id == 2) {
            // El administrador ve todos los tickets abiertos
            $datos = $ticket->listar_tickets_abiertos($prioridad);
        } else {
            // Usuarios normales solo ven sus tickets abiertos
            $datos = $ticket->listar_tickets_abiertos_x_usu($_POST["user_id"], $prioridad);
        }
        $data = array();
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = $row["ticket_id"];
            $sub_array[] = $row["cat_nomb"];
            $sub_array[] = $row["ticket_titulo"];
            $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
            
            // Añadir etiqueta de prioridad con color correspondiente
            $prioridad_color = "";
            switch($row["prioridad"]) {
                case "Urgente":
                    $prioridad_color = "danger";
                    break;
                case "Alto":
                case "Alta":
                    $prioridad_color = "warning";
                    break;
                case "Medio":
                case "Media":
                    $prioridad_color = "primary";
                    break;
                case "Bajo":
                case "Baja":
                    $prioridad_color = "info";
                    break;
                default:
                    $prioridad_color = "default";
            }
            $sub_array[] = '<span class="label label-pill label-'.$prioridad_color.'">'.$row["prioridad"].'</span>';
            
            $sub_array[] = date("d/m/Y", strtotime($row["fech_crea"]));
            $sub_array[] = '<button type="button" onClick="ver('.$row["ticket_id"].');" id="'.$row["ticket_id"].'" class="btn btn-warning btn-xs">Ver</button>';
            $data[] = $sub_array;
        }
        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        echo json_encode($results);
        break;
        
    case "listar_cerrados":
        $rol_id = $_POST["rol_id"];
        $prioridad = isset($_POST["prioridad"]) ? $_POST["prioridad"] : '';
        
        // Solo rol 2 (admin) puede ver todos los tickets cerrados
        if ($rol_id == 2) {
            $datos = $ticket->listar_tickets_cerrados($prioridad);
        } else {
            // Rol 1 (gerente) solo ve sus propios tickets cerrados
            $datos = $ticket->listar_tickets_cerrados_x_usu($_POST["user_id"], $prioridad);
        }
        $data = array();
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = $row["ticket_id"];
            $sub_array[] = $row["cat_nomb"];
            $sub_array[] = $row["ticket_titulo"];
            $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
            
            // Añadir etiqueta de prioridad con color correspondiente
            $prioridad_color = "";
            switch($row["prioridad"]) {
                case "Urgente":
                    $prioridad_color = "danger";
                    break;
                case "Alto":
                case "Alta":
                    $prioridad_color = "warning";
                    break;
                case "Medio":
                case "Media":
                    $prioridad_color = "primary";
                    break;
                case "Bajo":
                case "Baja":
                    $prioridad_color = "info";
                    break;
                default:
                    $prioridad_color = "default";
            }
            $sub_array[] = '<span class="label label-pill label-'.$prioridad_color.'">'.$row["prioridad"].'</span>';
            
            $sub_array[] = date("d/m/Y", strtotime($row["fech_crea"]));
            $sub_array[] = '
                <button type="button" onClick="ver('.$row["ticket_id"].');" id="'.$row["ticket_id"].'" class="btn btn-primary btn-sm">
                    <i class="fa fa-eye"></i> Ver
                </button>
                <button type="button" onClick="evidencia('.$row["ticket_id"].');" class="btn btn-info btn-sm">
                    <i class="fa fa-upload"></i> Evidencia
                </button>';
            $data[] = $sub_array;
        }
        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        echo json_encode($results);
        break;

    case "listardetalle":
        $datos = $ticket->listar_ticketdetalle_x_ticket($_POST["tick_id"]);
        // Si hay error, lo mostramos directamente
        if (isset($datos['error'])) {
            echo json_encode(['error' => $datos['error']]);
            break;
        }
        $data = array();
        if ($datos && is_array($datos)) {
            foreach($datos as $row){
                $sub_array = array();
                $sub_array['tickd_id'] = $row['tickd_id'];
                $sub_array['tickd_descrip'] = $row['ticket_descripcion'];
                $sub_array['fech_crea'] = $row['fech_crea'];
                $sub_array['user_nom'] = $row['user_nom'];
                $sub_array['user_ape'] = $row['user_ape'];
                $sub_array['rol_id'] = intval($row['rol_id']); // Convertir a entero explícitamente
                $data[] = $sub_array;
            }
        }
        echo json_encode($data);
        break;

    case "ver_ticket":
        $datos = $ticket->ver_ticket($_POST["tick_id"]);
        echo json_encode($datos);
        break;

    case "insertar_respuesta":
        $result = $ticket->insertar_respuesta($_POST["tick_id"], $_POST["user_id"], $_POST["respuesta"]);
        echo json_encode(['success' => $result ? true : false]);
        break;
        
    case "cerrar_ticket":
        $result = $ticket->cerrar_ticket($_POST["tick_id"]);
        echo json_encode(['success' => $result ? true : false]);
        break;
}
?>