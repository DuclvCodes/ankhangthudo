<?php
/**
 * Apus Framework Plugin
 *
 * A simple, truly extensible and fully responsive options framework
 * for WordPress themes and plugins. Developed with WordPress coding
 * standards and PHP best practices in mind.
 *
 * Plugin Name:     Apus Framework
 * Plugin URI:      http://apusthemes.com
 * Description:     Apus framework for wordpress theme
 * Author:          Team ApusTheme
 * Author URI:      http://apusthemes.com
 * Version:         2.1
 * Text Domain:     apus-framework
 * License:         GPL3+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     languages
 */

define( 'APUS_FRAMEWORK_VERSION', '2.1');
define( 'APUS_FRAMEWORK_URL', plugin_dir_url( __FILE__ ) );
define( 'APUS_FRAMEWORK_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Redux Framework
 *
 */
if ( !class_exists( 'ReduxFramework' ) && file_exists( APUS_FRAMEWORK_DIR . 'libs/redux/ReduxCore/framework.php' ) ) {
    require_once( APUS_FRAMEWORK_DIR . 'libs/redux/ReduxCore/framework.php' );
    require_once( APUS_FRAMEWORK_DIR . 'libs/loader.php' );
    define( 'APUS_FRAMEWORK_REDUX_ACTIVED', true );
} else {
	define( 'APUS_FRAMEWORK_REDUX_ACTIVED', true );
}
/**
 * Custom Post type
 *
 */
add_action( 'init', 'apus_framework_register_post_types', 1 );
/**
 * Import data sample
 *
 */
require APUS_FRAMEWORK_DIR . 'importer/import.php';
/**
 * functions
 *
 */
require APUS_FRAMEWORK_DIR . 'functions.php';
require APUS_FRAMEWORK_DIR . 'functions-preset.php';
/**
 * Widgets Core
 *
 */
require APUS_FRAMEWORK_DIR . 'classes/class-apus-widgets.php';
add_action( 'widgets_init',  'apus_framework_widget_init' );

require APUS_FRAMEWORK_DIR . 'classes/createplaceholder.php';
/**
 * Init
 *
 */
function apus_framework_init() {
	$demo_mode = apply_filters( 'apus_framework_register_demo_mode', false );
	if ( $demo_mode ) {
		apus_framework_init_redux();
	}
	$enable_tax_fields = apply_filters( 'apus_framework_enable_tax_fields', false );
	if ( $enable_tax_fields ) {
		if ( !class_exists( 'Taxonomy_MetaData_CMB2' ) ) {
			require_once APUS_FRAMEWORK_DIR . 'libs/cmb2/taxonomy/Taxonomy_MetaData_CMB2.php';
		}
	}
}
add_action( 'init', 'apus_framework_init', 100 );

function apus_framework_load_textdomain() {

	$lang_dir = APUS_FRAMEWORK_DIR . 'languages/';
	$lang_dir = apply_filters( 'apus-framework_languages_directory', $lang_dir );

	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'plugin_locale', get_locale(), 'apus-framework' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'apus-framework', $locale );

	// Setup paths to current locale file
	$mofile_local  = $lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/apus-framework/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/apus-framework folder
		load_textdomain( 'apus-framework', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/apus-framework/languages/ folder
		load_textdomain( 'apus-framework', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'apus-framework', false, $lang_dir );
	}
}

add_action( 'plugins_loaded', 'apus_framework_load_textdomain' );