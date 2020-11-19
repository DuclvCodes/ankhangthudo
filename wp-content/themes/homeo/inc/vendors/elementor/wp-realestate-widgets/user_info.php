<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_User_Info extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_user_info';
    }

	public function get_title() {
        return esc_html__( 'Apus Header User Info', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-header-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout Type', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'popup' => esc_html__('Popup', 'homeo'),
                    'page' => esc_html__('Page', 'homeo'),
                ),
                'default' => 'popup'
            ]
        );

        $this->add_control(
            'login_title',
            [
                'label' => esc_html__( 'Login Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Enter title here', 'homeo' ),
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );

        $this->add_control(
            'login_img',
            [
                'label' => esc_html__( 'Login Image', 'homeo' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'homeo' ),
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );

        $this->add_control(
            'register_title',
            [
                'label' => esc_html__( 'Register Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'Enter title here', 'homeo' ),
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );
        
        $this->add_control(
            'register_img',
            [
                'label' => esc_html__( 'Register Image', 'homeo' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'homeo' ),
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'homeo' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'homeo' ),
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__( 'Alignment', 'homeo' ),
                'type' => Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'homeo' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'homeo' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'homeo' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Color', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Color Text', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .name-acount' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .not-login a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .not-login' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'text_hover_color',
            [
                'label' => esc_html__( 'Color Hover Link', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .not-login a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .not-login a:focus' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $userdata = get_userdata($user_id);
            $user_name = $userdata->display_name;
            
            $menu_nav = 'user-menu';

            if ( WP_RealEstate_User::is_agency($user_id) ) {
                $menu_nav = 'agency-menu';
                $agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
                $user_name = get_post_field('post_title', $agency_id);
                $post_thumbnail_id = get_post_thumbnail_id($agency_id);
                $avatar = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
            } elseif ( WP_RealEstate_User::is_agent($user_id) ) {
                $menu_nav = 'agent-menu';
                $agent_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
                $user_name = get_post_field('post_title', $agent_id);
                $post_thumbnail_id = get_post_thumbnail_id($agent_id);
                $avatar = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
            }
            ?>
            <div class="top-wrapper-menu author-verify <?php echo esc_attr($el_class); ?>">
                <a class="drop-dow" href="javascript:void(0);">
                    <div class="infor-account flex-middle">
                        <div class="avatar-wrapper">
                            <?php if ( !empty($avatar)) {
                                echo trim($avatar);
                            } else {
                                echo get_avatar($user_id, 54);
                            } ?>
                        </div>
                        <div class="name-acount"><?php echo esc_html($user_name); ?> 
                            <?php if ( !empty($menu_nav) && has_nav_menu( $menu_nav ) ) { ?>
                                <i class="fa fa-caret-down" aria-hidden="true"></i>
                            <?php } ?>
                        </div>
                    </div>
                </a>
                <?php
                    if ( !empty($menu_nav) && has_nav_menu( $menu_nav ) ) {
                        $args = array(
                            'theme_location' => $menu_nav,
                            'container_class' => 'inner-top-menu',
                            'menu_class' => 'nav navbar-nav topmenu-menu',
                            'fallback_cb' => '',
                            'menu_id' => '',
                            'walker' => new Homeo_Nav_Menu()
                        );
                        wp_nav_menu($args);
                    }
                ?>
            </div>
        <?php } else {
            $login_register_page_id = wp_realestate_get_option('login_register_page_id');
        ?>
            <div class="top-wrapper-menu not-login <?php echo esc_attr($el_class); ?>">
                <span class="login-icon"><i class="flaticon-user"></i></span>
                <?php if ( $layout_type == 'page' ) { ?>
                    <a class="btn-login" href="<?php echo esc_url( get_permalink( $login_register_page_id ) ); ?>" title="<?php esc_attr_e('Sign in','homeo'); ?>"><?php esc_html_e('Login / Register', 'homeo'); ?>
                    </a>
                <?php } else { ?>
                    <a class="btn-login apus-user-login" href="#apus_login_forgot_form" title="<?php esc_attr_e('Login','homeo'); ?>">
                        <?php esc_html_e('Login', 'homeo'); ?>
                    </a>

                    <span class="space text-link"> / </span>

                    <a class="btn-login register apus-user-register" href="#apus_register_form" title="<?php esc_attr_e('Register','homeo'); ?>">
                        <?php esc_html_e('Register', 'homeo'); ?>
                    </a>

                    <div id="apus_login_forgot_form" class="apus_login_register_form mfp-hide" data-effect="fadeIn">
                        <div class="form-login-register-inner">
                            <div class="row">
                                <?php
                                $bcol = 12;
                                if ( !empty($login_img['id']) ) {
                                    $bcol = 6;
                                ?>
                                    <div class="col-sm-<?php echo esc_attr($bcol); ?> hidden-xs banner-image">
                                        <?php echo homeo_get_attachment_thumbnail($login_img['id'], 'full'); ?>
                                    </div>
                                <?php } ?>
                                <div class="col-sm-<?php echo esc_attr($bcol); ?> col-xs-12">
                                    <div class="inner-right">
                                        <?php if ( !empty($login_title) ) { ?>
                                            <h3 class="title"><?php echo trim($login_title); ?></h3>
                                        <?php } ?>
                                        <?php echo do_shortcode( '[wp_realestate_login]' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="apus_register_form" class="apus_login_register_form mfp-hide" data-effect="fadeIn">
                        <div class="form-login-register-inner">
                            <div class="row">
                                <?php
                                $bcol = 12;
                                if ( !empty($register_img['id']) ) {
                                    $bcol = 6;
                                ?>
                                    <div class="col-sm-<?php echo esc_attr($bcol); ?> hidden-xs banner-image">
                                        <?php echo homeo_get_attachment_thumbnail($register_img['id'], 'full'); ?>
                                    </div>
                                <?php } ?>
                                <div class="col-sm-<?php echo esc_attr($bcol); ?> col-xs-12">
                                    <div class="inner-right">
                                        <?php if ( !empty($register_title) ) { ?>
                                            <h3 class="title"><?php echo trim($register_title); ?></h3>
                                        <?php } ?>
                                        <?php echo do_shortcode( '[wp_realestate_register]' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php } ?>
            </div>
        <?php }
    }
}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_User_Info );