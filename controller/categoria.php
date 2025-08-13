<?php
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Categoria.php');

$categoria = new Categoria();

if (isset($_GET["op"])) {
    switch($_GET["op"]){
        case "combo":
            $datos = $categoria->get_categoria();
            if(is_array($datos) && count($datos) > 0){
                $html = "<option value=''>Seleccione una categoría</option>";
                foreach($datos as $row){
                    $html .= '<option value="'.htmlspecialchars($row["cat_id"]).'">'.htmlspecialchars($row["cat_nomb"]).'</option>';
                }
                echo $html;
            }
            break;
    }
}
?>