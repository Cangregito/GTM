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
}
?>