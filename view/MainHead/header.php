
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
                    <div class="dropdown dropdown-notification notif">
                        <a href="#" class="header-alarm dropdown-toggle active" id="dd-notification"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="font-icon-alarm"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-notif"
                            aria-labelledby="dd-notification">
                            <div class="dropdown-menu-notif-header">
                                Notifications
                                <span class="label label-pill label-danger">4</span>
                            </div>
                            <div class="dropdown-menu-notif-list">
                                <div class="dropdown-menu-notif-item">
                                    <div class="photo">
                                        <img src="/ESTADIAS/public/img/photo-64-1.jpg" alt="">
                                    </div>
                                    <div class="dot"></div>
                                    <a href="#">Morgan</a> was bothering about something
                                    <div class="color-blue-grey-lighter">7 hours ago</div>
                                </div>
                                <div class="dropdown-menu-notif-item">
                                    <div class="photo">
                                        <img src="/ESTADIAS/public/img/photo-64-2.jpg" alt="">                        
                                    </div>
                                    <div class="dot"></div>
                                    <a href="#">Lioneli</a> had commented on this <a href="#">Super Important Thing</a>
                                    <div class="color-blue-grey-lighter">7 hours ago</div>
                                </div>
                                <div class="dropdown-menu-notif-item">
                                    <div class="photo">
                                        <img src="/ESTADIAS/public/img/photo-64-3.jpg" alt="">
                                    </div>
                                    <div class="dot"></div>
                                    <a href="#">Xavier</a> had commented on the <a href="#">Movie title</a>
                                    <div class="color-blue-grey-lighter">7 hours ago</div>
                                </div>
                                <div class="dropdown-menu-notif-item">
                                    <div class="photo">
                                        <img src="/ESTADIAS/public/img/photo-64-4.jpg" alt="">
                                    </div>
                                    <a href="#">Lionely</a> wants to go to <a href="#">Cinema</a> with you to see <a
                                        href="#">This Movie</a>
                                    <div class="color-blue-grey-lighter">7 hours ago</div>
                                </div>
                            </div>
                            <div class="dropdown-menu-notif-more">
                                <a href="#">See more</a>
                            </div>
                        </div>
                    </div>
                    <!-- MENU DESPLEJABLE PRINCIPAL -->
                    <div class="dropdown user-menu">
                        <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <img src="/ESTADIAS/public/img/avatar-2-64.png" alt="">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                            <a class="dropdown-item" href="#"><span
                                    class="font-icon glyphicon glyphicon-user"></span>Perfil</a>
                            <a class="dropdown-item" href="#"><span
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