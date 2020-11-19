<?php
/**
 * Plugin Name: WP Private Message
 * Plugin URI: http://apusthemes.com/wp-private-message/
 * Description: Powerful plugin to create a private message on your website.
 * Version: 1.0.4
 * Author: Habq
 * Author URI: http://apusthemes.com/
 * Requires at least: 3.8
 * Tested up to: 5.2
 *
 * Text Domain: wp-private-message
 * Domain Path: /languages/
 *
 * @package wp-private-message
 * @category Plugins
 * @author Habq
 */
if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

if ( !class_exists("WP_Private_Message") ) {
	
	final class WP_Private_Message {

		private static $instance;

		public static function getInstance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Private_Message ) ) {
				self::$instance = new WP_Private_Message;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				
				add_action( 'tgmpa_register', array( self::$instance, 'register_plugins' ) );
				add_action( 'widgets_init', array( self::$instance, 'register_widgets' ) );

				self::$instance->libraries();
				self::$instance->includes();
			}

			return self::$instance;
		}
		/**
		 *
		 */
		public function setup_constants(){
			define( 'WP_PRIVATE_MESSAGE_PLUGIN_VERSION', '1.0.4' );

			define( 'WP_PRIVATE_MESSAGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'WP_PRIVATE_MESSAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		public function includes() {
			global $wp_private_message_options;
			// Admin Settings
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/admin/class-settings.php';

			$wp_private_message_options = wp_private_message_get_settings();
			
			// post type
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/post-types/class-post-type-private-message.php';

			//
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-scripts.php';
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-template-loader.php';
			
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-private-message.php';
			
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-shortcodes.php';
			
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-recaptcha.php';
			
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/class-mixes.php';
		}

		/**
		 * Loads third party libraries
		 *
		 * @access public
		 * @return void
		 */
		public static function libraries() {
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'libraries/class-tgm-plugin-activation.php';
		}

		/**
		 * Install plugins
		 *
		 * @access public
		 * @return void
		 */
		public static function register_plugins() {
			$plugins = array(
	            array(
		            'name'      => 'CMB2',
		            'slug'      => 'cmb2',
		            'required'  => true,
	            )
			);

			tgmpa( $plugins );
		}

		public static function register_widgets() {
			// widgets
			require_once WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'includes/widgets/class-widget-send-message.php';
		}

		/**
		 *
		 */
		public function load_textdomain() {
			// Set filter for WP_Private_Message's languages directory
			$lang_dir = WP_PRIVATE_MESSAGE_PLUGIN_DIR . 'languages/';
			$lang_dir = apply_filters( 'wp_private_message_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-private-message' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wp-private-message', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-private-message/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wp-private-message folder
				load_textdomain( 'wp-private-message', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wp-private-message/languages/ folder
				load_textdomain( 'wp-private-message', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wp-private-message', false, $lang_dir );
			}
		}
	}
}

function WP_Private_Message() {
	return WP_Private_Message::getInstance();
}

add_action( 'plugins_loaded', 'WP_Private_Message' );