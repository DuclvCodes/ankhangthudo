<?php
/**
 * Plugin Name: WP RealEstate
 * Plugin URI: http://apusthemes.com/wp-realestate/
 * Description: The latest plugins Real Estate you want. Completely all features, easy customize and override layout, functions. Supported global payment, build market, single, list property, single agent...etc. All fields are defined dynamic, they will help you can build any kind of Real Estate website.
 * Version: 1.2.12
 * Author: Habq
 * Author URI: http://apusthemes.com/
 * Requires at least: 3.8
 * Tested up to: 5.2
 *
 * Text Domain: wp-realestate
 * Domain Path: /languages/
 *
 * @package wp-realestate
 * @category Plugins
 * @author Habq
 */
if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

if ( !class_exists("WP_RealEstate") ) {
	
	final class WP_RealEstate {

		private static $instance;

		public static function getInstance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_RealEstate ) ) {
				self::$instance = new WP_RealEstate;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->plugin_update();
				
				add_action( 'activated_plugin', array( self::$instance, 'plugin_order' ) );
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
			define( 'WP_REALESTATE_PLUGIN_VERSION', '1.2.11' );

			define( 'WP_REALESTATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'WP_REALESTATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			define( 'WP_REALESTATE_PROPERTY_PREFIX', '_property_' );
			define( 'WP_REALESTATE_AGENT_PREFIX', '_agent_' );
			define( 'WP_REALESTATE_AGENCY_PREFIX', '_agency_' );
			
			define( 'WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX', '_saved_search_' );
		}

		public function includes() {
			global $wp_realestate_options;
			// Admin Settings
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/admin/class-settings.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/admin/class-permalink-settings.php';

			$wp_realestate_options = wp_realestate_get_settings();
			
			// post type
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/post-types/class-post-type-property.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/post-types/class-post-type-agency.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/post-types/class-post-type-agent.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/post-types/class-post-type-saved-search.php';

			// custom fields
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/custom-fields/class-fields-manager.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields-html.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields-display.php';
			
			// taxonomies
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-type.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-location.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-status.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-label.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-amenity.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-property-materials.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-agent-location.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-agent-category.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-agency-location.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-agency-category.php';

			//
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-scripts.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-template-loader.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-property.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-property-meta.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-property-yelp.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-agency.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-agent.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-review.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-price.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-query.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-shortcodes.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-abstract-form.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-submit-form.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-edit-form.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-user.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-image.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-recaptcha.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-email.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-abstract-filter.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-property-filter.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-agent-filter.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-agency-filter.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-saved-search.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-ajax.php';

			// social login
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-social-facebook.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-social-google.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-social-linkedin.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-social-twitter.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-favorite.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-compare.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-mixes.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/class-wpml.php';

			add_action('init', array( __CLASS__, 'register_post_statuses' ) );
		}

		public static function plugin_update() {
	        require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/plugin-update-checker/plugin-update-checker.php';
	        Puc_v4_Factory::buildUpdateChecker(
	            'https://www.apusthemes.com/themeplugins/wp-realestate.json',
	            __FILE__,
	            'wp-realestate'
	        );
	    }

		public static function register_post_statuses() {
			register_post_status(
				'expired',
				array(
					'label'                     => _x( 'Expired', 'post status', 'wp-realestate' ),
					'public'                    => false,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'wp-realestate' ),
				)
			);
			register_post_status(
				'preview',
				array(
					'label'                     => _x( 'Preview', 'post status', 'wp-realestate' ),
					'public'                    => false,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'wp-realestate' ),
				)
			);
			register_post_status(
				'pending_approve',
				array(
					'label'                     => _x( 'Pending Approval', 'post status', 'wp-realestate' ),
					'public'                    => false,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending Approve <span class="count">(%s)</span>', 'Pending Approve <span class="count">(%s)</span>', 'wp-realestate' ),
				)
			);
		}
		public static function register_widgets() {
			// widgets
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/widgets/class-widget-property-filter.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/widgets/class-widget-agent-filter.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'includes/widgets/class-widget-agency-filter.php';
		}
		/**
		 * Loads third party libraries
		 *
		 * @access public
		 * @return void
		 */
		public static function libraries() {
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_map/cmb-field-map.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_tags/cmb2-field-type-tags.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_file/cmb2-field-type-file.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_attached_user/cmb2-field-type-attached_user.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_image_select/cmb2-field-type-image-select.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_profile_url/cmb2-field-type-profile_url.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_ajax_search/cmb2-field-ajax-search.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb_field_select2/cmb-field-select2.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_select2/cmb-field-taxonomy-select2.php';
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_location/cmb-field-taxonomy-location.php';

			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/cmb2/cmb2-tabs/plugin.php';
			
			require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/class-tgm-plugin-activation.php';
		}

		/**
	     * Loads this plugin first
	     *
	     * @access public
	     * @return void
	     */
	    public static function plugin_order() {
		    $wp_path_to_this_file = preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR.'/$2', __FILE__ );
		    $this_plugin = plugin_basename( trim( $wp_path_to_this_file ) );
		    $active_plugins = get_option( 'active_plugins' );
		    $this_plugin_key = array_search( $this_plugin, $active_plugins );
			if ( $this_plugin_key ) {
				array_splice( $active_plugins, $this_plugin_key, 1 );
				array_unshift( $active_plugins, $this_plugin );
			    update_option( 'active_plugins', $active_plugins );
		    }
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

		public static function maybe_schedule_cron_properties() {
			if ( ! wp_next_scheduled( 'wp_realestate_check_for_expired_properties' ) ) {
				wp_schedule_event( time(), 'hourly', 'wp_realestate_check_for_expired_properties' );
			}
			if ( ! wp_next_scheduled( 'wp_realestate_delete_old_previews' ) ) {
				wp_schedule_event( time(), 'daily', 'wp_realestate_delete_old_previews' );
			}
			if ( ! wp_next_scheduled( 'wp_realestate_email_daily_notices' ) ) {
				wp_schedule_event( time(), 'daily', 'wp_realestate_email_daily_notices' );
			}
		}

		/**
		 * Unschedule cron properties. This is run on plugin deactivation.
		 */
		public static function unschedule_cron_properties() {
			wp_clear_scheduled_hook( 'wp_realestate_check_for_expired_properties' );
			wp_clear_scheduled_hook( 'wp_realestate_delete_old_previews' );
			wp_clear_scheduled_hook( 'wp_realestate_email_daily_notices' );
		}

		/**
		 *
		 */
		public function load_textdomain() {
			// Set filter for WP_RealEstate's languages directory
			$lang_dir = WP_REALESTATE_PLUGIN_DIR . 'languages/';
			$lang_dir = apply_filters( 'wp_realestate_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-realestate' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wp-realestate', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-realestate/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wp-realestate folder
				load_textdomain( 'wp-realestate', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wp-realestate/languages/ folder
				load_textdomain( 'wp-realestate', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wp-realestate', false, $lang_dir );
			}
		}
	}
}

register_activation_hook( __FILE__, array( 'WP_RealEstate', 'maybe_schedule_cron_properties' ) );
register_deactivation_hook( __FILE__, array( 'WP_RealEstate', 'unschedule_cron_properties' ) );

function WP_RealEstate() {
	return WP_RealEstate::getInstance();
}

add_action( 'plugins_loaded', 'WP_RealEstate' );