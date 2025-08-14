<?php
$dir = "../public/uploads/";
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $filepath = $dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Devuelve la URL absoluta para Summernote
        echo "/ESTADIAS/public/uploads/" . $filename;
    } else {
        http_response_code(400);
        echo "Error al subir";
    }
}
?>