
<header class="site-header">
    <div class="container-fluid">

        <a href="#" class="site-logo">
            <img class="hidden-md-down" src="/ESTADIAS/docs/logo-GTM.png" alt="">
        </a>

        <button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
            <span>toggle menu</span>
        </button>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>
        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="site-header-shown">
                    <!-- Notificaciones -->
                    <div class="dropdown dropdown-notification notif">
                        <a href="#" class="header-alarm dropdown-toggle" id="dd-notification"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="font-icon-alarm"></i>
                            <span class="label label-danger" style="display: none;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-notif"
                            aria-labelledby="dd-notification">
                            <div class="dropdown-menu-notif-header">
                                <?php if(isset($_SESSION["rol_id"]) && $_SESSION["rol_id"] == 1): ?>
                                    <span class="font-icon font-icon-star"></span>
                                    Notificaciones Gerenciales
                                <?php else: ?>
                                    <span class="font-icon font-icon-warning"></span>
                                    Notificaciones de Sistema
                                <?php endif; ?>
                                <div class="dropdown-menu-notif-header-create">
                                    <button class="create" onclick="window.gerenteNotificaciones?.cargarNotificaciones()">
                                        <i class="font-icon font-icon-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="jscroll" style="max-height: 350px; overflow-y: auto;">
                                <div class="dropdown-item text-center">
                                    <p class="color-blue-grey-lighter">
                                        <i class="font-icon font-icon-refresh spinning"></i>
                                        Cargando notificaciones...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(isset($_SESSION["rol_id"]) && $_SESSION["rol_id"] == 1): ?>
                    <!-- Indicador de rendimiento para gerente -->
                    <div class="header-performance-indicator">
                        <i class="font-icon-check-circle text-success"></i>
                        <span class="text-success">Óptimo</span>
                    </div>
                    <?php endif; ?>
                    <!-- MENU DESPLEJABLE PRINCIPAL -->
                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <?php if(isset($_SESSION["rol_id"]) && $_SESSION["rol_id"] == 1): ?>
                                <img src="/ESTADIAS/public/img/Gerente.png" alt="Gerente de Tienda" class="profile-image profile-gerente">
                            <?php else: ?>
                                <img src="/ESTADIAS/public/img/Soporte.png" alt="Soporte" class="profile-image profile-soporte">
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="/ESTADIAS/view/Perfil/"><span
                                    class="font-icon glyphicon glyphicon-user"></span>Perfil</a>
                            <a class="dropdown-item" href="/ESTADIAS/view/Ayuda/"><span
                                    class="font-icon glyphicon glyphicon-question-sign"></span>Ayuda</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/ESTADIAS/view/Home/logout.php"><span
                                    class="font-icon glyphicon glyphicon-log-out"></span>Cerrar sesion</a>
                        </div>
                    </div>

                </div><!--.site-header-shown-->
                <input type="hidden" id="user_idx" value="<?php echo isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : '' ?>"> <!-- ID del Usuario -->
                <input type="hidden" id="rol_idx" value="<?php echo isset($_SESSION["rol_id"]) ? $_SESSION["rol_id"] : '' ?>"> <!-- ID del Rol -->
                <div class="dropdown dropdown-typical">
                    <a href="#" class="dropdown-toggle no-arr">
                        <span class="font-icon font-icon-user"></span>
                        <span class="lblcontactonomx">
                            <?php 
                                echo isset($_SESSION["user_nom"]) ? $_SESSION["user_nom"] : '';
                                echo " ";
                                echo isset($_SESSION["user_ape"]) ? $_SESSION["user_ape"] : '';
                            ?>
                        </span>
                    </a>
                </div>
            </div><!-- .site-header-content-in -->
        </div><!-- .site-header-content -->
    </div><!-- .container-fluid -->
</header><!--.site-header-->