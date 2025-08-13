<?php
session_start();
require_once(__DIR__ . '/../models/Usuario.php');

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
?>