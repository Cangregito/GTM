<?php
session_start();
session_unset();
session_destroy();
header("Location: /ESTADIAS/index.php");
exit;
?>