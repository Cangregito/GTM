<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])) {
    header("Location: /ESTADIAS/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head lang="es">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link href="/ESTADIAS/public/img/favicon.144x144.png" rel="apple-touch-icon" type="image/png" sizes="144x144">
	<link href="/ESTADIAS/public/img/favicon.114x114.png" rel="apple-touch-icon" type="image/png" sizes="114x114">
	<link href="/ESTADIAS/public/img/favicon.72x72.png" rel="apple-touch-icon" type="image/png" sizes="72x72">
	<link href="/ESTADIAS/public/img/favicon.57x57.png" rel="apple-touch-icon" type="image/png">
	<link href="/ESTADIAS/docs/iconGTM.png" rel="icon" type="image/png">
	<link href="/ESTADIAS/public/img/favicon.ico" rel="shortcut icon">
	<link rel="stylesheet" href="/ESTADIAS/public/css/lib/summernote/summernote.css" />
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/pages/editor.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/lib/font-awesome/font-awesome.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/lib/bootstrap-sweetalert/sweetalert.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/vendor/sweet-alert-animations.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/lib/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/lib/datatables-net/datatables.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/vendor/datatables-net.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/vendor/fancybox.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/pages/activity.min.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/profile-images.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/separate/notificaciones.css">
	<link rel="stylesheet" href="/ESTADIAS/public/css/main.css">
</head>