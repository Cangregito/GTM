<script src="/ESTADIAS/public/js/lib/jquery/jquery.min.js"></script>
<script src="/ESTADIAS/public/js/lib/tether/tether.min.js"></script>
<script src="/ESTADIAS/public/js/lib/bootstrap/bootstrap.min.js"></script>
<script src="/ESTADIAS/public/js/plugins.js"></script>
<script src="/ESTADIAS/public/js/lib/summernote/summernote.min.js"></script>
<script src="/ESTADIAS/public/js/lib/bootstrap-sweetalert/sweetalert.min.js"></script>
<script src="/ESTADIAS/public/js/app.js"></script>
<script src="/ESTADIAS/public/js/lib/datatables-net/datatables.min.js"></script>
<script src="/ESTADIAS/public/js/lib/fancybox/jquery.fancybox.pack.js"></script>
<script src="/ESTADIAS/public/js/profile-images.js"></script>
<script src="/ESTADIAS/public/js/notificaciones.js"></script>
<script src="/ESTADIAS/public/js/gerente-notificaciones.js"></script>

<script>
// Variables globales disponibles para todos los scripts
var userRole = <?php echo isset($_SESSION["rol_id"]) ? $_SESSION["rol_id"] : 'null'; ?>;
var userId = <?php echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 'null'; ?>;
var userName = "<?php echo isset($_SESSION["user_nom"]) ? $_SESSION["user_nom"] : ''; ?>";
</script>