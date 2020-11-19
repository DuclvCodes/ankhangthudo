<?php
/**
 * homeo functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.1.13
 */

define( 'HOMEO_THEME_VERSION', '1.1.13' );
define( 'HOMEO_DEMO_MODE', false );

if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

if ( ! function_exists( 'homeo_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Homeo 1.0
 */
function my_read_more_text() {
	return 'mon texte ici';
}
add_filter( 'ocean_post_readmore_link_text', 'my_read_more_text' );

function homeo_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on homeo, use a find and replace
	 * to change 'homeo' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'homeo', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'homeo-property-list', 260, 240, true );
	add_image_size( 'homeo-property-grid', 850, 550, true );
	add_image_size( 'homeo-property-grid1', 836, 950, true );
	add_image_size( 'homeo-agent-grid', 425, 325, true );
	add_image_size( 'homeo-slider', 1920, 870, true );
	add_image_size( 'homeo-gallery-v1', 1920, 600, true );
	add_image_size( 'homeo-gallery-v2-large', 960, 600, true );
	add_image_size( 'homeo-gallery-v2-small', 480, 600, true );
	add_image_size( 'homeo-gallery-v3', 640, 540, true );
	add_image_size( 'homeo-gallery-v4-large', 750, 450, true );
	add_image_size( 'homeo-gallery-v4-small', 165, 130, true );
	add_image_size( 'homeo-gallery-v5', 1920, 500, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'homeo' ),
		'agent-menu' => esc_html__( 'Agent Account Menu', 'homeo' ),
		'agency-menu' => esc_html__( 'Agency Account Menu', 'homeo' ),
		'user-menu' => esc_html__( 'User Account Menu', 'homeo' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	add_theme_support( "woocommerce", array('gallery_thumbnail_image_width' => 410) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	$color_scheme  = homeo_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'homeo_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for Block Styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Enqueue editor styles.
	add_editor_style( array( 'css/style-editor.css', homeo_get_fonts_url() ) );

	homeo_get_load_plugins();
}
endif; // homeo_setup
add_action( 'after_setup_theme', 'homeo_setup' );

/**
 * Load Google Front
 */

function homeo_get_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
    * supported by Montserrat, translate this to 'off'. Do not translate
    * into your own language.
    */
    $nunito = _x( 'on', 'Nunito font: on or off', 'homeo' );

    if ( 'off' !== $nunito ) {
        $font_families = array();

        if ( 'off' !== $nunito ) {
            $font_families[] = 'Nunito:300,400,600,700,800,900';
        }

        $query_args = array(
            'family' => ( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 		
 		$protocol = is_ssl() ? 'https:' : 'http:';
        $fonts_url = add_query_arg( $query_args, $protocol .'//fonts.googleapis.com/css' );
    }
 
    return esc_url( $fonts_url );
}

/**
 * Enqueue styles.
 *
 * @since Homeo 1.0
 */
function homeo_enqueue_styles() {
	
	// load font
	wp_enqueue_style( 'homeo-theme-fonts', homeo_get_fonts_url(), array(), null );

	//load font awesome
	wp_enqueue_style( 'all-awesome', get_template_directory_uri() . '/css/all-awesome.css', array(), '5.11.2' );

	//load font flaticon
	wp_enqueue_style( 'flaticon', get_template_directory_uri() . '/css/flaticon.css', array(), '1.0.0' );

	// load font themify icon
	wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons.css', array(), '1.0.0' );
			
	// load animate version 3.6.0
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array(), '3.6.0' );

	// load bootstrap style
	if( is_rtl() ){
		wp_enqueue_style( 'bootstrap-rtl', get_template_directory_uri() . '/css/bootstrap-rtl.css', array(), '3.2.0' );
	} else {
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '3.2.0' );
	}
	// slick
	wp_enqueue_style( 'slick', get_template_directory_uri() . '/css/slick.css', array(), '1.8.0' );
	// magnific-popup
	wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css', array(), '1.1.0' );
	// perfect scrollbar
	wp_enqueue_style( 'perfect-scrollbar', get_template_directory_uri() . '/css/perfect-scrollbar.css', array(), '0.6.12' );
	
	// mobile menu
	wp_enqueue_style( 'jquery-mmenu', get_template_directory_uri() . '/css/jquery.mmenu.css', array(), '0.6.12' );

	// main style
	wp_enqueue_style( 'homeo-template', get_template_directory_uri() . '/css/template.css', array(), '1.0' );
	
	$custom_style = homeo_custom_styles();
	if ( !empty($custom_style) ) {
		wp_add_inline_style( 'homeo-template', $custom_style );
	}
	wp_enqueue_style( 'homeo-style', get_template_directory_uri() . '/style.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'homeo_enqueue_styles', 100 );

function homeo_admin_enqueue_styles() {
	//load font awesome
	wp_enqueue_style( 'all-awesome', get_template_directory_uri() . '/css/all-awesome.css', array(), '5.11.2' );

	//load font flaticon
	wp_enqueue_style( 'flaticon', get_template_directory_uri() . '/css/flaticon.css', array(), '1.0.0' );

	// load font themify icon
	wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons.css', array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'homeo_admin_enqueue_styles', 100 );

function homeo_login_enqueue_styles() {
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array(), '4.5.0' );
	wp_enqueue_style( 'homeo-login-style', get_template_directory_uri() . '/css/login-style.css', array(), '1.0' );
}
add_action( 'login_enqueue_scripts', 'homeo_login_enqueue_styles', 10 );
/**
 * Enqueue scripts.
 *
 * @since Homeo 1.0
 */
function homeo_enqueue_scripts() {
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	// bootstrap
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '20150330', true );
	// slick
	wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '1.8.0', true );
	// countdown
	wp_register_script( 'countdown', get_template_directory_uri() . '/js/countdown.js', array( 'jquery' ), '20150315', true );
	wp_localize_script( 'countdown', 'homeo_countdown_opts', array(
		'days' => esc_html__('Days', 'homeo'),
		'hours' => esc_html__('Hrs', 'homeo'),
		'mins' => esc_html__('Mins', 'homeo'),
		'secs' => esc_html__('Secs', 'homeo'),
	));
	wp_enqueue_script( 'countdown' );
	// popup
	wp_enqueue_script( 'jquery-magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
	// unviel
	wp_enqueue_script( 'jquery-unveil', get_template_directory_uri() . '/js/jquery.unveil.js', array( 'jquery' ), '1.1.0', true );
	
	// perfect scrollbar
	wp_enqueue_script( 'perfect-scrollbar', get_template_directory_uri() . '/js/perfect-scrollbar.min.js', array( 'jquery' ), '1.5.0', true );
	
	// addthis
	wp_register_script('addthis', '//s7.addthis.com/js/250/addthis_widget.js', array(), '0.6.12', true);
	
	if ( homeo_get_config('keep_header') ) {
		wp_enqueue_script( 'sticky', get_template_directory_uri() . '/js/sticky.min.js', array( 'jquery', 'elementor-waypoints' ), '4.0.1', true );
	}

	// mobile menu script
	wp_enqueue_script( 'jquery-mmenu', get_template_directory_uri() . '/js/jquery.mmenu.js', array( 'jquery' ), '0.6.12', true );

	// main script
	wp_register_script( 'homeo-functions', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'homeo-functions', 'homeo_ajax', array(
		'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
		'previous' => esc_html__('Previous', 'homeo'),
		'next' => esc_html__('Next', 'homeo'),
		'mmenu_title' => esc_html__('Menu', 'homeo')
	));
	wp_enqueue_script( 'homeo-functions' );
	
	wp_add_inline_script( 'homeo-functions', "(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);" );
}
add_action( 'wp_enqueue_scripts', 'homeo_enqueue_scripts', 1 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Homeo 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function homeo_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'homeo_search_form_modify' );

/**
 * Function get opt_name
 *
 */
function homeo_get_opt_name() {
	return 'homeo_theme_options';
}
add_filter( 'apus_framework_get_opt_name', 'homeo_get_opt_name' );


function homeo_register_demo_mode() {
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		return true;
	}
	return false;
}
add_filter( 'apus_framework_register_demo_mode', 'homeo_register_demo_mode' );

function homeo_get_demo_preset() {
	$preset = '';
    if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
        if ( isset($_REQUEST['_preset']) && $_REQUEST['_preset'] ) {
            $presets = get_option( 'apus_framework_presets' );
            if ( is_array($presets) && isset($presets[$_REQUEST['_preset']]) ) {
                $preset = $_REQUEST['_preset'];
            }
        } else {
            $preset = get_option( 'apus_framework_preset_default' );
        }
    }
    return $preset;
}

function homeo_get_config($name, $default = '') {
	global $homeo_options;
    if ( isset($homeo_options[$name]) ) {
        return $homeo_options[$name];
    }
    return $default;
}

function homeo_set_exporter_ocdi_settings_option_keys($option_keys) {
	return array(
		'elementor_disable_color_schemes',
		'elementor_disable_typography_schemes',
		'elementor_allow_tracking',
		'elementor_cpt_support',
		'wp_realestate_settings',
		'wp_realestate_fields_data',
	);
}
add_filter( 'apus_exporter_ocdi_settings_option_keys', 'homeo_set_exporter_ocdi_settings_option_keys' );

function homeo_disable_one_click_import() {
	return false;
}
add_filter('apus_frammework_enable_one_click_import', 'homeo_disable_one_click_import');

function homeo_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Default', 'homeo' ),
		'id'            => 'sidebar-default',
		'description'   => esc_html__( 'Add widgets here to appear in your Sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Properties filter sidebar', 'homeo' ),
		'id'            => 'properties-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Properties filter sidebar fixed', 'homeo' ),
		'id'            => 'properties-filter-sidebar-fixed',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Properties filter Top sidebar', 'homeo' ),
		'id'            => 'properties-filter-top-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Properties filter Top Half Map', 'homeo' ),
		'id'            => 'properties-filter-top-half-map',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );


	register_sidebar( array(
		'name'          => esc_html__( 'Property single sidebar', 'homeo' ),
		'id'            => 'property-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Agents filter sidebar', 'homeo' ),
		'id'            => 'agents-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Agent single sidebar', 'homeo' ),
		'id'            => 'agent-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Agencies filter sidebar', 'homeo' ),
		'id'            => 'agencies-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Agency single sidebar', 'homeo' ),
		'id'            => 'agency-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'User Profile sidebar', 'homeo' ),
		'id'            => 'user-profile-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Blog sidebar', 'homeo' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop sidebar', 'homeo' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'homeo' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

}
add_action( 'widgets_init', 'homeo_widgets_init' );

function homeo_get_load_plugins() {
	$plugins[] = array(
		'name'                     => esc_html__( 'Apus Framework For Themes', 'homeo' ),
        'slug'                     => 'apus-framework',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/apus-framework.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'Elementor Page Builder', 'homeo' ),
	    'slug'                     => 'elementor',
	    'required'                 => true,
	);
	
	$plugins[] = array(
		'name'                     => esc_html__( 'Revolution Slider', 'homeo' ),
        'slug'                     => 'revslider',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/revslider.zip'
	);
	
	$plugins[] = array(
		'name'                     => esc_html__( 'Cmb2', 'homeo' ),
	    'slug'                     => 'cmb2',
	    'required'                 => true,
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'MailChimp for WordPress', 'homeo' ),
	    'slug'                     => 'mailchimp-for-wp',
	    'required'                 =>  true
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'Contact Form 7', 'homeo' ),
	    'slug'                     => 'contact-form-7',
	    'required'                 => true,
	);

	// woocommerce plugins
	$plugins[] = array(
		'name'                     => esc_html__( 'Woocommerce', 'homeo' ),
	    'slug'                     => 'woocommerce',
	    'required'                 => true,
	);
	
	// Property plugins
	$plugins[] = array(
		'name'                     => esc_html__( 'WP RealEstate', 'homeo' ),
        'slug'                     => 'wp-realestate',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-realestate.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'WP RealEstate - WooCommerce Paid Listings', 'homeo' ),
        'slug'                     => 'wp-realestate-wc-paid-listings',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-realestate-wc-paid-listings.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'WP Private Message', 'homeo' ),
        'slug'                     => 'wp-private-message',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-private-message.zip'
	);
	
	$plugins[] = array(
        'name'                  => esc_html__( 'One Click Demo Import', 'homeo' ),
        'slug'                  => 'one-click-demo-import',
        'required'              => false,
    );

	tgmpa( $plugins );
}

require get_template_directory() . '/inc/plugins/class-tgm-plugin-activation.php';
require get_template_directory() . '/inc/functions-helper.php';
require get_template_directory() . '/inc/functions-frontend.php';

/**
 * Implement the Custom Header feature.
 *
 */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/classes/megamenu.php';
require get_template_directory() . '/inc/classes/mobilemenu.php';

/**
 * Custom template tags for this theme.
 *
 */
require get_template_directory() . '/inc/template-tags.php';

if ( defined( 'APUS_FRAMEWORK_REDUX_ACTIVED' ) ) {
	require get_template_directory() . '/inc/vendors/redux-framework/redux-config.php';
	define( 'HOMEO_REDUX_FRAMEWORK_ACTIVED', true );
}
if( homeo_is_cmb2_activated() ) {
	require get_template_directory() . '/inc/vendors/cmb2/page.php';
	define( 'HOMEO_CMB2_ACTIVED', true );
}
if( homeo_is_woocommerce_activated() ) {
	require get_template_directory() . '/inc/vendors/woocommerce/functions.php';
	require get_template_directory() . '/inc/vendors/woocommerce/functions-redux-configs.php';
	define( 'HOMEO_WOOCOMMERCE_ACTIVED', true );
}

if( homeo_is_wp_realestate_activated() ) {
	require get_template_directory() . '/inc/vendors/wp-realestate/functions-redux-configs.php';
	require get_template_directory() . '/inc/vendors/wp-realestate/functions.php';
	require get_template_directory() . '/inc/vendors/wp-realestate/functions-agent.php';
	require get_template_directory() . '/inc/vendors/wp-realestate/functions-agency.php';

	require get_template_directory() . '/inc/vendors/wp-realestate/functions-property-display.php';
	require get_template_directory() . '/inc/vendors/wp-realestate/functions-agent-display.php';
	require get_template_directory() . '/inc/vendors/wp-realestate/functions-agency-display.php';
}

if ( homeo_is_wp_realestate_wc_paid_listings_activated() ) {
	require get_template_directory() . '/inc/vendors/wp-realestate-wc-paid-listings/functions.php';
}

if( homeo_is_apus_framework_activated() ) {
	require get_template_directory() . '/inc/widgets/custom_menu.php';
	require get_template_directory() . '/inc/widgets/recent_post.php';
	require get_template_directory() . '/inc/widgets/search.php';
	require get_template_directory() . '/inc/widgets/socials.php';
	
	require get_template_directory() . '/inc/widgets/elementor-template.php';
	
	if ( homeo_is_wp_realestate_activated() ) {
		require get_template_directory() . '/inc/widgets/mortgage_calculator.php';
		require get_template_directory() . '/inc/widgets/contact-form.php';
		require get_template_directory() . '/inc/widgets/property-contact-form.php';
		
		require get_template_directory() . '/inc/widgets/property-list.php';

		require get_template_directory() . '/inc/widgets/user-short-profile.php';
		
		if ( homeo_is_wp_private_message() ) {
			require get_template_directory() . '/inc/widgets/private-message-form.php';
		}
	}

	define( 'HOMEO_FRAMEWORK_ACTIVED', true );
}
if ( homeo_is_wp_private_message() ) {
	require get_template_directory() . '/inc/vendors/wp-private-message/functions.php';
}

require get_template_directory() . '/inc/vendors/elementor/functions.php';
require get_template_directory() . '/inc/vendors/one-click-demo-import/functions.php';

function homeo_register_post_types($post_types) {
	foreach ($post_types as $key => $post_type) {
		if ( $post_type == 'brand' || $post_type == 'testimonial' ) {
			unset($post_types[$key]);
		}
	}
	if ( !in_array('header', $post_types) ) {
		$post_types[] = 'header';
	}
	return $post_types;
}
add_filter( 'apus_framework_register_post_types', 'homeo_register_post_types' );


/**
 * Customizer additions.
 *
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Styles
 *
 */
require get_template_directory() . '/inc/custom-styles.php';