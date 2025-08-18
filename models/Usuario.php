<?php
require_once(__DIR__ . '/../config/conexion.php');

class Usuario extends Conectar
{

    public function login($correo, $pass, $rol_id)
    {
        $conectare = $this->getConexion();
        $sql = "SELECT * FROM tm_usuario WHERE user_correo = ? AND estado = 1 AND rol_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $correo);
        $stmt->bindValue(2, $rol_id);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Migración automática: si la contraseña no está hasheada
            if (strlen($resultado['user_pass']) < 60) { // Un hash bcrypt tiene 60 caracteres
                if ($pass === $resultado['user_pass']) {
                    $nuevo_hash = password_hash($pass, PASSWORD_DEFAULT);
                    $update = $conectare->prepare("UPDATE tm_usuario SET user_pass = ? WHERE user_id = ?");
                    $update->execute([$nuevo_hash, $resultado['user_id']]);
                    $resultado['user_pass'] = $nuevo_hash;
                } else {
                    return false;
                }
            }
            if (password_verify($pass, $resultado['user_pass'])) {
                return $resultado;
            }
        }
        return false;
    }
    
    /* NUEVAS FUNCIONES PARA LA GESTIÓN DE USUARIOS */
    
    // Listar todos los usuarios (solo activos)
    public function listar_usuarios()
    {
        $conectare = $this->getConexion();
        $sql = "SELECT 
                    u.user_id,
                    u.user_nom,
                    u.user_ape,
                    u.user_correo,
                    u.rol_id,
                    u.estado,
                    u.user_crea
                FROM 
                    tm_usuario u
                WHERE 
                    u.estado = 1 AND
                    u.fecha_elim IS NULL
                ORDER BY 
                    u.user_id";
        $stmt = $conectare->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Listar usuarios eliminados
    public function listar_usuarios_eliminados()
    {
        $conectare = $this->getConexion();
        $sql = "SELECT 
                    u.user_id,
                    u.user_nom,
                    u.user_ape,
                    u.user_correo,
                    u.rol_id,
                    u.estado,
                    u.user_crea,
                    u.fecha_elim
                FROM 
                    tm_usuario u
                WHERE 
                    u.estado = 0 AND
                    u.fecha_elim IS NOT NULL
                ORDER BY 
                    u.fecha_elim DESC";
        $stmt = $conectare->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Restaurar usuario eliminado
    public function restaurar_usuario($user_id)
    {
        $conectare = $this->getConexion();
        $sql = "UPDATE tm_usuario 
                SET 
                    estado = 1,
                    fecha_elim = NULL,
                    user_modi = NOW()
                WHERE 
                    user_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
    
    // Obtener detalles de un usuario por su ID
    public function get_usuario_x_id($user_id)
    {
        $conectare = $this->getConexion();
        $sql = "SELECT 
                    user_id,
                    user_nom,
                    user_ape,
                    user_correo,
                    rol_id,
                    estado
                FROM 
                    tm_usuario 
                WHERE 
                    user_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Insertar un nuevo usuario
    public function insert_usuario($user_nom, $user_ape, $user_correo, $user_pass, $rol_id)
    {
        $conectare = $this->getConexion();
        
        // Verificar si el correo ya existe
        $check = $conectare->prepare("SELECT COUNT(*) FROM tm_usuario WHERE user_correo = ?");
        $check->bindValue(1, $user_correo);
        $check->execute();
        
        if ($check->fetchColumn() > 0) {
            return false; // Correo ya existe
        }
        
        // Encriptar contraseña
        $pass_hash = password_hash($user_pass, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO tm_usuario 
                (user_nom, user_ape, user_correo, user_pass, rol_id, user_crea, estado) 
                VALUES 
                (?, ?, ?, ?, ?, NOW(), 1)";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $user_nom);
        $stmt->bindValue(2, $user_ape);
        $stmt->bindValue(3, $user_correo);
        $stmt->bindValue(4, $pass_hash);
        $stmt->bindValue(5, $rol_id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
    
    // Actualizar usuario existente
    public function update_usuario($user_id, $user_nom, $user_ape, $user_correo, $rol_id)
    {
        $conectare = $this->getConexion();
        
        // Verificar si el correo ya existe en otro usuario
        $check = $conectare->prepare("SELECT COUNT(*) FROM tm_usuario WHERE user_correo = ? AND user_id != ?");
        $check->bindValue(1, $user_correo);
        $check->bindValue(2, $user_id);
        $check->execute();
        
        if ($check->fetchColumn() > 0) {
            return false; // Correo ya existe en otro usuario
        }
        
        $sql = "UPDATE tm_usuario 
                SET 
                    user_nom = ?, 
                    user_ape = ?, 
                    user_correo = ?, 
                    rol_id = ?,
                    user_modi = NOW()
                WHERE 
                    user_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $user_nom);
        $stmt->bindValue(2, $user_ape);
        $stmt->bindValue(3, $user_correo);
        $stmt->bindValue(4, $rol_id);
        $stmt->bindValue(5, $user_id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
    
    // Cambiar contraseña de un usuario
    public function update_password($user_id, $new_password)
    {
        $conectare = $this->getConexion();
        $pass_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE tm_usuario 
                SET 
                    user_pass = ?,
                    user_modi = NOW() 
                WHERE 
                    user_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $pass_hash);
        $stmt->bindValue(2, $user_id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
    
    // Eliminar usuario (borrado lógico)
    public function delete_usuario($user_id)
    {
        $conectare = $this->getConexion();
        $sql = "UPDATE tm_usuario 
                SET 
                    estado = 0,
                    fecha_elim = NOW()
                WHERE 
                    user_id = ?";
        $stmt = $conectare->prepare($sql);
        $stmt->bindValue(1, $user_id);
        $resultado = $stmt->execute();
        
        return $resultado;
    }
}
?>