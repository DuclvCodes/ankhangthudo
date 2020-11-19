<div id="apus-header-mobile" class="header-mobile hidden-lg clearfix">    
    <div class="container">
        <div class="row">
            <div class="flex-middle">
                <div class="col-xs-3">
                    <?php if ( homeo_get_config('header_mobile_menu', true) ) { ?>
                        <a href="#navbar-offcanvas" class="btn btn-showmenu btn-theme">
                            <i class="fas fa-bars"></i>
                        </a>
                    <?php } ?>
                </div>
                <div class="col-xs-6 text-center">
                    <?php
                        $logo = homeo_get_config('media-mobile-logo');
                    ?>
                    <?php if( isset($logo['url']) && !empty($logo['url']) ): ?>
                        <div class="logo">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="logo logo-theme">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <img src="<?php echo esc_url( get_template_directory_uri().'/images/logo.svg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-xs-3">
                        <?php
                            if ( homeo_get_config('header_mobile_login', true) && homeo_is_wp_realestate_activated() ) {
                                if ( is_user_logged_in() ) {
                                    $user_id = get_current_user_id();
                                    $menu_nav = 'user-menu';
                                    if ( WP_RealEstate_User::is_agency($user_id) ) {
                                        $menu_nav = 'agency-menu';
                                    } elseif ( WP_RealEstate_User::is_agent($user_id) ) {
                                        $menu_nav = 'agent-menu';
                                    }
                                    
                                    if ( !empty($menu_nav) && has_nav_menu( $menu_nav ) ) {
                                    ?>
                                        <div class="top-wrapper-menu pull-right">
                                            <a class="drop-dow btn-menu-account" href="javascript:void(0);">
                                                <i class="fas fa-user"></i>
                                            </a>
                                            <?php
                                                
                                                $args = array(
                                                    'theme_location' => $menu_nav,
                                                    'container_class' => 'inner-top-menu',
                                                    'menu_class' => 'nav navbar-nav topmenu-menu',
                                                    'fallback_cb' => '',
                                                    'menu_id' => '',
                                                    'walker' => new Homeo_Nav_Menu()
                                                );
                                                wp_nav_menu($args);
                                                
                                            ?>
                                        </div>
                                        <?php } ?>
                            <?php } else {
                                $login_register_page_id = wp_realestate_get_option('login_register_page_id');
                            ?>
                                    <div class="top-wrapper-menu pull-right">
                                        <a class="drop-dow btn-menu-account" href="<?php echo esc_url( get_permalink( $login_register_page_id ) ); ?>">
                                            <i class="fa fa-user"></i>
                                        </a>
                                    </div>
                            <?php }
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>