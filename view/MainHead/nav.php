<?php
if ($_SESSION["rol_id"] == 1) {
?>
<nav class="side-menu">
    <ul class="side-menu-list">
        <li class="blue-dirty">
            <a href="../Home/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Inicio</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="../NuevoTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Nuevo ticket</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="../ConsultarTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar ticket</span>
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
            <a href="../Home/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Inicio</span>
            </a>
        </li>
        <li class="blue-dirty">
            <a href="../ConsultarTicket/index.php">
                <span class="glyphicon glyphicon-th"></span>
                <span class="lbl">Consultar ticket</span>
            </a>
        </li>
    </ul>
</nav>
<?php
}
?>