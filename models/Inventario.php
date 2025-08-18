<?php
require_once(__DIR__ . '/../config/conexion.php');

class Inventario extends Conectar
{
    /**
     * Listar todos los elementos del inventario
     * @return array Array de elementos
     */
    public function listar_inventario()
    {
        $conectar = $this->getConexion();
        $sql = "SELECT 
                    i.inv_id,
                    i.inv_nombre,
                    i.inv_descripcion,
                    i.inv_cantidad,
                    i.inv_estado,
                    i.inv_categoria,
                    i.inv_fecha_registro,
                    i.inv_fecha_actualizacion,
                    u.user_nom,
                    u.user_ape
                FROM 
                    tm_inventario i
                INNER JOIN 
                    tm_usuario u ON i.inv_usuario_id = u.user_id
                ORDER BY 
                    i.inv_fecha_registro DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un elemento del inventario por su ID
     * @param int $inv_id ID del elemento
     * @return array Datos del elemento
     */
    public function get_inventario_x_id($inv_id)
    {
        $conectar = $this->getConexion();
        $sql = "SELECT * FROM tm_inventario WHERE inv_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $inv_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insertar un nuevo elemento en el inventario
     * @param string $inv_nombre Nombre del elemento
     * @param string $inv_descripcion Descripción del elemento
     * @param int $inv_cantidad Cantidad disponible
     * @param string $inv_estado Estado (Activo/Inactivo/Descompuesto)
     * @param string $inv_categoria Categoría del elemento
     * @param int $inv_usuario_id ID del usuario que registra
     * @return bool Éxito o fracaso de la operación
     */
    public function insert_inventario($inv_nombre, $inv_descripcion, $inv_cantidad, $inv_estado, $inv_categoria, $inv_usuario_id)
    {
        $conectar = $this->getConexion();
        $sql = "INSERT INTO tm_inventario 
                (inv_nombre, inv_descripcion, inv_cantidad, inv_estado, inv_categoria, inv_usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $inv_nombre);
        $stmt->bindValue(2, $inv_descripcion);
        $stmt->bindValue(3, $inv_cantidad);
        $stmt->bindValue(4, $inv_estado);
        $stmt->bindValue(5, $inv_categoria);
        $stmt->bindValue(6, $inv_usuario_id);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar un elemento del inventario
     * @param int $inv_id ID del elemento
     * @param string $inv_nombre Nombre del elemento
     * @param string $inv_descripcion Descripción del elemento
     * @param int $inv_cantidad Cantidad disponible
     * @param string $inv_estado Estado (Activo/Inactivo/Descompuesto)
     * @param string $inv_categoria Categoría del elemento
     * @return bool Éxito o fracaso de la operación
     */
    public function update_inventario($inv_id, $inv_nombre, $inv_descripcion, $inv_cantidad, $inv_estado, $inv_categoria)
    {
        $conectar = $this->getConexion();
        $sql = "UPDATE tm_inventario 
                SET 
                    inv_nombre = ?, 
                    inv_descripcion = ?, 
                    inv_cantidad = ?, 
                    inv_estado = ?, 
                    inv_categoria = ?,
                    inv_fecha_actualizacion = NOW()
                WHERE 
                    inv_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $inv_nombre);
        $stmt->bindValue(2, $inv_descripcion);
        $stmt->bindValue(3, $inv_cantidad);
        $stmt->bindValue(4, $inv_estado);
        $stmt->bindValue(5, $inv_categoria);
        $stmt->bindValue(6, $inv_id);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar un elemento del inventario
     * @param int $inv_id ID del elemento
     * @return bool Éxito o fracaso de la operación
     */
    public function delete_inventario($inv_id)
    {
        $conectar = $this->getConexion();
        $sql = "DELETE FROM tm_inventario WHERE inv_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $inv_id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener resumen del inventario agrupado por categoría
     * @return array Estadísticas del inventario
     */
    public function get_inventario_stats()
    {
        $conectar = $this->getConexion();
        $sql = "SELECT 
                    inv_categoria,
                    COUNT(*) as total_items,
                    SUM(inv_cantidad) as total_cantidad,
                    SUM(CASE WHEN inv_estado = 'Activo' THEN inv_cantidad ELSE 0 END) as total_activos,
                    SUM(CASE WHEN inv_estado = 'Inactivo' THEN inv_cantidad ELSE 0 END) as total_inactivos,
                    SUM(CASE WHEN inv_estado = 'Descompuesto' THEN inv_cantidad ELSE 0 END) as total_descompuestos
                FROM 
                    tm_inventario
                GROUP BY 
                    inv_categoria
                ORDER BY 
                    total_items DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
