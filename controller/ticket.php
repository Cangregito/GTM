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
            $_POST["ticket_descripcion"] 
        );
        echo json_encode(['success' => $result ? true : false, 'ticket_id' => $result]);
        break;

    case "listar_x_usu":
        $rol_id = $_POST["rol_id"];
        if ($rol_id == 2) {
            $datos = $ticket->listar_todos_tickets();
        } else {
            $datos = $ticket->listar_ticket_x_usu($_POST["user_id"]);
        }
        $data = array();
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = $row["ticket_id"];
            $sub_array[] = $row["cat_nomb"];
            $sub_array[] = $row["ticket_titulo"];
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
}
?>