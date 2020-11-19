<?php
/**
 * Scripts
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Scripts {
	/**
	 * Initialize scripts
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_backend' ) );
	}

	/**
	 * Loads front files
	 *
	 * @access public
	 * @return void
	 */
	public static function enqueue_frontend() {
		if ( is_user_logged_in() ) {
			wp_register_script( 'jquery-iframe-transport', WP_REALESTATE_PLUGIN_URL . 'assets/js/jquery-fileupload/jquery.iframe-transport.js', array( 'jquery' ), '1.8.3', true );
			wp_register_script( 'jquery-fileupload', WP_REALESTATE_PLUGIN_URL . 'assets/js/jquery-fileupload/jquery.fileupload.js', array( 'jquery', 'jquery-iframe-transport', 'jquery-ui-widget' ), '9.11.2', true );
			wp_register_script( 'wp-realestate-ajax-file-upload', WP_REALESTATE_PLUGIN_URL . 'assets/js/ajax-file-upload.js', array( 'jquery', 'jquery-fileupload' ), WP_REALESTATE_PLUGIN_VERSION, true );

			$js_field_html_img = WP_RealEstate_Template_Loader::get_template_part('misc/uploaded-file-html', array( 'input_name'  => '', 'value' => '', 'extension' => 'jpg' ));
			$js_field_html = WP_RealEstate_Template_Loader::get_template_part('misc/uploaded-file-html', array( 'input_name'  => '', 'value' => '', 'extension' => 'zip' ));

			wp_localize_script(
				'wp-realestate-ajax-file-upload',
				'wp_realestate_file_upload',
				array(
					'ajax_url'               => admin_url( 'admin-ajax.php' ),
					'ajax_url_endpoint'      => WP_RealEstate_Ajax::get_endpoint(),
					'js_field_html_img'      => esc_js( str_replace( "\n", '', $js_field_html_img ) ),
					'js_field_html'          => esc_js( str_replace( "\n", '', $js_field_html ) ),
					'i18n_invalid_file_type' => __( 'Invalid file type. Accepted types:', 'wp-realestate' ),
					'i18n_over_upload_limit' => __( 'You are only allowed to upload a maximum of %d files.', 'wp-realestate' ),
				)
			);
		}

		$select2_args = array( 'width' => '100%' );
		if ( is_rtl() ) {
			$select2_args['dir'] = 'rtl';
		}
		$select2_args['language_result'] = __( 'No results found', 'wp-realestate' );

		wp_register_script( 'select2', WP_REALESTATE_PLUGIN_URL . 'assets/js/select2/select2.full.min.js', array( 'jquery'  ), '4.0.5', true );
		wp_localize_script( 'select2', 'wp_realestate_select2_opts', $select2_args);
		wp_register_style( 'select2', WP_REALESTATE_PLUGIN_URL . 'assets/js/select2/select2.min.css', array(), '4.0.5' );

		wp_enqueue_style( 'magnific', WP_REALESTATE_PLUGIN_URL . 'assets/js/magnific/magnific-popup.css', array(), '1.1.0' );
		wp_enqueue_script( 'magnific', WP_REALESTATE_PLUGIN_URL . 'assets/js/magnific/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );

		wp_register_script( 'jquery-ui-touch-punch', WP_REALESTATE_PLUGIN_URL . 'assets/js/jquery.ui.touch-punch.min.js', array( 'jquery' ), '20150330', true );

		$browser_key = wp_realestate_get_option('google_map_api_keys');
		$key = empty( $browser_key ) ? '' : 'key='. $browser_key . '&';
		wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?'. $key .'libraries=weather,geometry,visualization,places,drawing' );
		
		if ( wp_realestate_get_option('map_service') == 'google-map' ) {
			wp_enqueue_script( 'google-maps' );
		}
		
		wp_register_style( 'leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.css', array(), '1.5.1' );
		wp_register_script( 'jquery-highlight', WP_REALESTATE_PLUGIN_URL . 'assets/js/jquery.highlight.js', array( 'jquery' ), '5', true );
	    wp_register_script( 'leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-GoogleMutant', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/Leaflet.GoogleMutant.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'control-geocoder', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/Control.Geocoder.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'esri-leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/esri-leaflet.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'esri-leaflet-geocoder', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/esri-leaflet-geocoder.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-markercluster', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.markercluster.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-HtmlIcon', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/LeafletHtmlIcon.js', array( 'jquery' ), '1.5.1', true );

	    wp_enqueue_script('chart', WP_REALESTATE_PLUGIN_URL . 'assets/js/chart.min.js', array('jquery'), '1.0', false);

		$dashboard_page_url = get_permalink( wp_realestate_get_option('user_dashboard_page_id') );
		$login_register_url = get_permalink( wp_realestate_get_option('login_register_page_id') );
		
		wp_register_script( 'wp-realestate-main', WP_REALESTATE_PLUGIN_URL . 'assets/js/main.js', array( 'jquery', 'jquery-ui-slider', 'jquery-ui-touch-punch' ), '20131022', true );
		wp_localize_script( 'wp-realestate-main', 'wp_realestate_opts', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajaxurl_endpoint'      => WP_RealEstate_Ajax::get_endpoint(),
			'dashboard_url' => esc_url( $dashboard_page_url ),
			'login_register_url' => esc_url( $login_register_url ),
			'home_url' => esc_url( home_url( '/' ) ),


            'money_decimals' => wp_realestate_get_option('money_decimals', 0),
			'money_dec_point' => wp_realestate_get_option('money_dec_point', 0),
			'money_thousands_separator' => wp_realestate_get_option('money_thousands_separator') ? wp_realestate_get_option('money_thousands_separator') : '',

			'show_more' => esc_html__('Show more +', 'wp-realestate'),
			'show_more_icon' => '',
			'show_less' => esc_html__('Show less -', 'wp-realestate'),
			'show_less_icon' => '',

			'geocoder_country' => wp_realestate_get_option('geocoder_country', ''),
			'rm_item_txt' => esc_html__('Are you sure?', 'wp-realestate'),
			'ajax_nonce' => wp_create_nonce( 'wpre-ajax-nonce' ),
		));
		wp_enqueue_script( 'wp-realestate-main' );
	}

	/**
	 * Loads backend files
	 *
	 * @access public
	 * @return void
	 */
	public static function enqueue_backend() {
		
		wp_register_style( 'leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.css', array(), '1.5.1' );
		wp_register_script( 'jquery-highlight', WP_REALESTATE_PLUGIN_URL . 'assets/js/jquery.highlight.js', array( 'jquery' ), '5', true );
	    wp_register_script( 'leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-GoogleMutant', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/Leaflet.GoogleMutant.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'control-geocoder', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/Control.Geocoder.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'esri-leaflet', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/esri-leaflet.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'esri-leaflet-geocoder', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/esri-leaflet-geocoder.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-markercluster', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/leaflet.markercluster.js', array( 'jquery' ), '1.5.1', true );
	    wp_register_script( 'leaflet-HtmlIcon', WP_REALESTATE_PLUGIN_URL . 'assets/js/leaflet/LeafletHtmlIcon.js', array( 'jquery' ), '1.5.1', true );

		$browser_key = wp_realestate_get_option('google_map_api_keys');
		$key = empty( $browser_key ) ? '' : 'key='. $browser_key . '&';
		wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?'. $key .'libraries=weather,geometry,visualization,places,drawing' );
			    
		wp_enqueue_style( 'wp-realestate-style-admin', WP_REALESTATE_PLUGIN_URL . 'assets/css/style-admin.css' );

		// select2
		$select2_args = array( 'width' => '100%' );
		if ( is_rtl() ) {
			$select2_args['dir'] = 'rtl';
		}
		wp_register_script( 'select2', WP_REALESTATE_PLUGIN_URL . 'assets/js/select2/select2.full.min.js', array( 'jquery'  ), '4.0.5', true );
		wp_localize_script( 'select2', 'wp_realestate_select2_opts', $select2_args);
		wp_enqueue_style( 'select2', WP_REALESTATE_PLUGIN_URL . 'assets/js/select2/select2.min.css', array(), '4.0.5' );
		wp_enqueue_script( 'select2' );
		//
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_script( 'wp-realestate-admin-main', WP_REALESTATE_PLUGIN_URL . 'assets/admin/admin-main.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'wp-realestate-admin-main', 'wp_realestate_opts', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		));
		wp_enqueue_script( 'wp-realestate-admin-main' );
	}

}

WP_RealEstate_Scripts::init();
