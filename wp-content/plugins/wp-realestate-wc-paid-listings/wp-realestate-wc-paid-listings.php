<?php
/**
 * Plugin Name: WP RealEstate - WooCommerce Paid Listings
 * Plugin URI: http://apusthemes.com/wp-realestate-wc-paid-listings/
 * Description: Add paid listing functionality via WooCommerce
 * Version: 2.2.2
 * Author: Habq
 * Author URI: http://apusthemes.com
 * Requires at least: 3.8
 * Tested up to: 5.2
 *
 * Text Domain: wp-realestate-wc-paid-listings
 * Domain Path: /languages/
 *
 * @package wp-realestate-wc-paid-listings
 * @category Plugins
 * @author Habq
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists("WP_RealEstate_Wc_Paid_Listings") ) {
	
	final class WP_RealEstate_Wc_Paid_Listings {

		private static $instance;

		public static function getInstance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_RealEstate_Wc_Paid_Listings ) ) {
				self::$instance = new WP_RealEstate_Wc_Paid_Listings;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->plugin_update();
				
				add_action( 'tgmpa_register', array( self::$instance, 'register_plugins' ) );

				self::$instance->libraries();
				self::$instance->includes();
			}

			return self::$instance;
		}

		/**
		 *
		 */
		public function setup_constants() {
			
			define( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_VERSION', '2.2.2' );

			// Plugin Folder Path
			if ( ! defined( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR' ) ) {
				define( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_URL' ) ) {
				define( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_FILE' ) ) {
				define( 'WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_FILE', __FILE__ );
			}

			// Prefix
			if ( ! defined( 'WP_REALESTATE_WC_PAID_LISTINGS_PREFIX' ) ) {
				define( 'WP_REALESTATE_WC_PAID_LISTINGS_PREFIX', '_property_package_' );
			}
		}

		public function includes() {
			// post type
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/post-types/class-post-type-property_package.php';
			
			// class
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-mixes.php';
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-submit-form.php';
			if ( class_exists('WC_Product_Simple') ) {
				require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-product-type-package.php';
				
				require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-wc-cart.php';
				require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-wc-order.php';
			}
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-property-package.php';
			
			if ( class_exists( 'WC_Subscriptions' ) ) {
				require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-property-package-subscription.php';
			}
			
			// template loader
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'includes/class-template-loader.php';

			add_action('init', array( __CLASS__, 'register_post_statuses' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'style' ) );
		}

		public static function plugin_update() {
	        require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'libraries/plugin-update-checker/plugin-update-checker.php';
	        Puc_v4_Factory::buildUpdateChecker(
	            'https://www.apusthemes.com/themeplugins/wp-realestate-wc-paid-listings.json',
	            __FILE__,
	            'wp-realestate-wc-paid-listings'
	        );
	    }

		public static function style() {
			wp_enqueue_style('wp-realestate-wc-paid-listings-style', WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_URL . 'assets/style.css');
			wp_enqueue_script('wp-realestate-wc-paid-listings-script', WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_URL . 'assets/admin-main.js', array( 'jquery' ), '5', true);
		}

		/**
		 * Loads third party libraries
		 *
		 * @access public
		 * @return void
		 */
		public static function libraries() {
			require_once WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_DIR . 'libraries/class-tgm-plugin-activation.php';
		}

		public static function register_post_statuses() {
			register_post_status(
				'pending_payment',
				array(
					'label'                     => _x( 'Pending Payment', 'post status', 'wp-realestate-wc-paid-listings' ),
					'public'                    => false,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-realestate-wc-paid-listings' ),
				)
			);
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
	            ),
	            array(
		            'name'      => 'WP RealEstate',
		            'slug'      => 'wp-realestate',
		            'required'  => true,
	            )
			);

			tgmpa( $plugins );
		}
		/**
		 *
		 */
		public function load_textdomain() {
			// Set filter for WP_RealEstate_Wc_Paid_Listings's languages directory
			$lang_dir = dirname( plugin_basename( WP_REALESTATE_WC_PAID_LISTINGS_PLUGIN_FILE ) ) . '/languages/';
			$lang_dir = apply_filters( 'wp_realestate_wc_paid_listings_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-realestate-wc-paid-listings' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wp-realestate-wc-paid-listings', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-realestate-wc-paid-listings/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wp-realestate-wc-paid-listings folder
				load_textdomain( 'wp-realestate-wc-paid-listings', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wp-realestate-wc-paid-listings/languages/ folder
				load_textdomain( 'wp-realestate-wc-paid-listings', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wp-realestate-wc-paid-listings', false, $lang_dir );
			}
		}
	}
}

function WP_RealEstate_Wc_Paid_Listings() {
	return WP_RealEstate_Wc_Paid_Listings::getInstance();
}

add_action( 'plugins_loaded', 'WP_RealEstate_Wc_Paid_Listings' );