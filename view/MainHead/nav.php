<?php
if ($_SESSION["rol_id"] == 1) {
?>
<nav class="side-menu">
    <ul class="side-menu-list">
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/Home/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Inicio</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/NuevoTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Nuevo ticket</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/ConsultarTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar ticket</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/VerCerrado/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar ticket Cerrado</span>
            </a>
        </li>
    </ul>
</nav>
<?php
} else {
?>
<nav class="side-menu">
    <ul class="side-menu-list">
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/Home/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Inicio</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/ConsultarTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar Ticket</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/VerCerrado/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar Ticket Cerrado</span>
            </a>
        </li>
        
        <?php
        // Solo mostrar la opción de Gestión de Usuarios para el usuario "admin" y rol de soporte (rol_id == 2)
        if ($_SESSION["rol_id"] == 2 && $_SESSION["user_nom"] == "admin") {
        ?>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/GestionUsuarios/index.php">
                <span class="glyphicon glyphicon-user"></span>
                <span class="lbl">Gestión de Usuarios</span>
            </a>
        </li>
        <?php
        }
        
        // Mostrar la opción de Gestión de Inventario para el usuario con nombre "Mantenimiento" y apellido "planta" 
        // o para el usuario "admin"
        if ($_SESSION["rol_id"] == 2 && 
            (($_SESSION["user_nom"] == "Mantenimiento" && $_SESSION["user_ape"] == "planta") || 
             ($_SESSION["user_nom"] == "admin"))) {
        ?>
        <li class="blue-dirty">
            <a href="/ESTADIAS/view/Inventario/index.php">
                <span class="glyphicon glyphicon-list"></span>
                <span class="lbl">Gestión de Inventario</span>
            </a>
        </li>
        <?php
        }
        ?>
    </ul>
</nav>
<?php
}
?>