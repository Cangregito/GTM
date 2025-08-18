<?php
session_start();
require_once(__DIR__ . '/../models/Usuario.php');

// Manejo del login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: ../index.php?m=csrf");
        exit;
    }

    $correo = trim($_POST['user_correo'] ?? '');
    $pass = $_POST['user_pass'] ?? '';
    $rol_id = $_POST['rol_id'] ?? null; // <-- Recibe el rol_id

    if (empty($correo) || empty($pass)) {
        header("Location: ../index.php?m=2");
        exit;
    }

    $usuario = new Usuario();
    $resultado = $usuario->login($correo, $pass, $rol_id); // <-- Pásalo al modelo

    if ($resultado) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $resultado["user_id"];
        $_SESSION["user_nom"] = $resultado["user_nom"];
        $_SESSION["user_ape"] = $resultado["user_ape"];
        $_SESSION["rol_id"] = $resultado["rol_id"];
        header("Location: ../view/Home/");
        exit;
    } else {
        header("Location: ../index.php?m=1");
        exit;
    }
}

// Para operaciones CRUD de usuarios - Operaciones AJAX
if (isset($_GET["op"])) {
    $usuario = new Usuario();
    
    switch($_GET["op"]){
        // Listar todos los usuarios
        case "listar":
            $datos = $usuario->listar_usuarios();
            $data = array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["user_id"];
                $sub_array[] = $row["user_nom"] . " " . $row["user_ape"];
                $sub_array[] = $row["user_correo"];
                
                // Etiqueta para rol
                if($row["rol_id"] == 1){
                    $sub_array[] = '<span class="label label-pill label-primary">Usuario</span>';
                } else if($row["rol_id"] == 2){
                    $sub_array[] = '<span class="label label-pill label-success">Soporte</span>';
                } else {
                    $sub_array[] = '<span class="label label-pill label-default">Desconocido</span>';
                }
                
                $sub_array[] = date("d/m/Y", strtotime($row["user_crea"]));
                
                // Botones de acción
                $sub_array[] = '<button type="button" onClick="editar('.$row["user_id"].');" id="'.$row["user_id"].'" class="btn btn-warning btn-xs">Editar</button>';
                $sub_array[] = '<button type="button" onClick="eliminar('.$row["user_id"].');" id="'.$row["user_id"].'" class="btn btn-danger btn-xs">Eliminar</button>';
                
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
            
        // Obtener usuario por ID para edición
        case "get_usuario_x_id":
            if(isset($_POST["user_id"])){
                $datos = $usuario->get_usuario_x_id($_POST["user_id"]);
                if(is_array($datos) && count($datos) > 0){
                    echo json_encode($datos);
                } else {
                    echo json_encode(["error" => "Usuario no encontrado"]);
                }
            } else {
                echo json_encode(["error" => "ID de usuario no proporcionado"]);
            }
            break;
            
        // Insertar nuevo usuario
        case "insert":
            if(
                isset($_POST["user_nom"]) && 
                isset($_POST["user_ape"]) && 
                isset($_POST["user_correo"]) && 
                isset($_POST["user_pass"]) && 
                isset($_POST["rol_id"])
            ){
                $result = $usuario->insert_usuario(
                    $_POST["user_nom"],
                    $_POST["user_ape"],
                    $_POST["user_correo"],
                    $_POST["user_pass"],
                    $_POST["rol_id"]
                );
                
                if($result){
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "El correo ya está en uso"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            }
            break;
            
        // Actualizar usuario existente
        case "update":
            if(
                isset($_POST["user_id"]) && 
                isset($_POST["user_nom"]) && 
                isset($_POST["user_ape"]) && 
                isset($_POST["user_correo"]) && 
                isset($_POST["rol_id"])
            ){
                $result = $usuario->update_usuario(
                    $_POST["user_id"],
                    $_POST["user_nom"],
                    $_POST["user_ape"],
                    $_POST["user_correo"],
                    $_POST["rol_id"]
                );
                
                if($result){
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "El correo ya está en uso por otro usuario"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            }
            break;
            
        // Cambiar contraseña
        case "update_password":
            if(isset($_POST["user_id"]) && isset($_POST["new_password"])){
                $result = $usuario->update_password(
                    $_POST["user_id"],
                    $_POST["new_password"]
                );
                
                if($result){
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al actualizar la contraseña"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
            }
            break;
            
        // Eliminar usuario (borrado lógico)
        case "delete":
            if(isset($_POST["user_id"])){
                $result = $usuario->delete_usuario($_POST["user_id"]);
                
                if($result){
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al eliminar el usuario"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID de usuario no proporcionado"]);
            }
            break;
            
        // Listar usuarios eliminados
        case "listar_eliminados":
            $datos = $usuario->listar_usuarios_eliminados();
            $data = array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["user_id"];
                $sub_array[] = $row["user_nom"] . " " . $row["user_ape"];
                $sub_array[] = $row["user_correo"];
                
                // Etiqueta para rol
                if($row["rol_id"] == 1){
                    $sub_array[] = '<span class="label label-pill label-primary">Usuario</span>';
                } else if($row["rol_id"] == 2){
                    $sub_array[] = '<span class="label label-pill label-success">Soporte</span>';
                } else {
                    $sub_array[] = '<span class="label label-pill label-default">Desconocido</span>';
                }
                
                $sub_array[] = date("d/m/Y", strtotime($row["user_crea"]));
                $sub_array[] = date("d/m/Y", strtotime($row["fecha_elim"]));
                
                // Botón de restaurar
                $sub_array[] = '<button type="button" onClick="restaurar('.$row["user_id"].');" id="'.$row["user_id"].'" class="btn btn-success btn-xs">Restaurar</button>';
                
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
            
        // Restaurar usuario eliminado
        case "restaurar":
            if(isset($_POST["user_id"])){
                $result = $usuario->restaurar_usuario($_POST["user_id"]);
                
                if($result){
                    echo json_encode(["status" => "success"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al restaurar el usuario"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID de usuario no proporcionado"]);
            }
            break;
    }
}
?>