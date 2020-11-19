<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function homeo_ocdi_import_files() {
    $demos = array();
    $demos[] = array(
        'import_file_name'             => 'Home',
        //'categories'                   => array( 'Property Board' ),
        'local_import_file'            => trailingslashit( get_template_directory() ) . 'inc/vendors/one-click-demo-import/data/dummy-data.xml',
        'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'inc/vendors/one-click-demo-import/data/widgets.wie',
        'local_import_redux'           => array(
            array(
                'file_path'   => trailingslashit( get_template_directory() ) . 'inc/vendors/one-click-demo-import/data/redux-options.json',
                'option_name' => 'homeo_theme_options',
            ),
        ),
        'import_preview_image_url'     => trailingslashit( get_template_directory_uri() ) . 'screenshot.png',
        'import_notice'                => esc_html__( 'Import process may take 5-10 minutes. If you facing any issues please contact our support.', 'homeo' ),
        'preview_url'                  => 'https://demoapus.com/homeo/',
    );
    return apply_filters( 'homeo_ocdi_files_args', $demos );
}
add_filter( 'pt-ocdi/import_files', 'homeo_ocdi_import_files' );

function homeo_ocdi_after_import_setup( $selected_import ) {
    // Assign menus to their locations.
    $main_menu       = get_term_by( 'name', 'Main Menu', 'nav_menu' );
    $agent_menu      = get_term_by( 'name', 'Agent Dashboard', 'nav_menu' );
    $agency_menu   = get_term_by( 'name', 'Agency Dashboard', 'nav_menu' );
    $user_menu   = get_term_by( 'name', 'User', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
            'primary' => $main_menu->term_id,
            'agent-menu' => $agent_menu->term_id,
            'agency-menu' => $agency_menu->term_id,
            'user-menu' => $user_menu->term_id,
        )
    );

    // Assign front page and posts page (blog page) and other WooCommerce pages
    $blog_page_id       = get_page_by_title( 'Blog' );
    $shop_page_id       = get_page_by_title( 'Shop' );
    $cart_page_id       = get_page_by_title( 'Cart' );
    $checkout_page_id   = get_page_by_title( 'Checkout' );
    $myaccount_page_id  = get_page_by_title( 'My Account' );

    update_option( 'show_on_front', 'page' );
    
    update_option( 'page_for_posts', $blog_page_id->ID );
    update_option( 'woocommerce_shop_page_id', $shop_page_id->ID );
    update_option( 'woocommerce_cart_page_id', $cart_page_id->ID );
    update_option( 'woocommerce_checkout_page_id', $checkout_page_id->ID );
    update_option( 'woocommerce_enable_myaccount_registration', 'yes' );

    // elementor
    update_option( 'elementor_global_image_lightbox', 'yes' );
    update_option( 'elementor_disable_color_schemes', 'yes' );
    update_option( 'elementor_disable_typography_schemes', 'yes' );
    update_option( 'elementor_container_width', 1170 );

    
    $front_page_id = get_page_by_title( 'Home 1' );
    update_option( 'page_on_front', $front_page_id->ID );

    $file = trailingslashit( get_template_directory() ) . 'inc/vendors/one-click-demo-import/data/settings.json';
    if ( file_exists($file) ) {
        homeo_ocdi_import_settings($file);
    }

    if ( homeo_is_revslider_activated() ) {
        require_once( ABSPATH . 'wp-load.php' );
        require_once( ABSPATH . 'wp-includes/functions.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $slider_array = array(
            trailingslashit( get_template_directory() ) . 'inc/vendors/one-click-demo-import/data/slider-1.zip'
        );
        $slider = new RevSlider();

        foreach( $slider_array as $filepath ) {
            $slider->importSliderFromPost( true, true, $filepath );
        }
    }
}
add_action( 'pt-ocdi/after_import', 'homeo_ocdi_after_import_setup' );

function homeo_ocdi_import_settings($file) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    $file_obj = new WP_Filesystem_Direct( array() );
    $datas = $file_obj->get_contents($file);
    $datas = json_decode( $datas, true );

    if ( count( array_filter( $datas ) ) < 1 ) {
        return;
    }

    if ( !empty($datas['page_options']) ) {
        homeo_ocdi_import_page_options($datas['page_options']);
    }
    if ( !empty($datas['metadata']) ) {
        homeo_ocdi_import_some_metadatas($datas['metadata']);
    }
}

function homeo_ocdi_import_page_options($datas) {
    if ( $datas ) {
        foreach ($datas as $option_name => $page_id) {
            update_option( $option_name, $page_id);
        }
    }
}

function homeo_ocdi_import_some_metadatas($datas) {
    if ( $datas ) {
        foreach ($datas as $slug => $post_types) {
            if ( $post_types ) {
                foreach ($post_types as $post_type => $metas) {
                    if ( $metas ) {
                        $args = array(
                            'name'        => $slug,
                            'post_type'   => $post_type,
                            'post_status' => 'publish',
                            'numberposts' => 1
                        );
                        $posts = get_posts($args);
                        if ( $posts && isset($posts[0]) ) {
                            foreach ($metas as $meta) {
                                update_post_meta( $posts[0]->ID, $meta['meta_key'], $meta['meta_value'] );
                                if ( $meta['meta_key'] == '_mc4wp_settings' ) {
                                    update_option( 'mc4wp_default_form_id', $posts[0]->ID );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function homeo_ocdi_before_widgets_import() {

    $sidebars_widgets = get_option('sidebars_widgets');
    $all_widgets = array();

    array_walk_recursive( $sidebars_widgets, function ($item, $key) use ( &$all_widgets ) {
        if( ! isset( $all_widgets[$key] ) ) {
            $all_widgets[$key] = $item;
        } else {
            $all_widgets[] = $item;
        }
    } );

    if( isset( $all_widgets['array_version'] ) ) {
        $array_version = $all_widgets['array_version'];
        unset( $all_widgets['array_version'] );
    }

    $new_sidebars_widgets = array_fill_keys( array_keys( $sidebars_widgets ), array() );

    $new_sidebars_widgets['wp_inactive_widgets'] = $all_widgets;
    if( isset( $array_version ) ) {
        $new_sidebars_widgets['array_version'] = $array_version;
    }

    update_option( 'sidebars_widgets', $new_sidebars_widgets );
}
add_action( 'pt-ocdi/before_widgets_import', 'homeo_ocdi_before_widgets_import' );