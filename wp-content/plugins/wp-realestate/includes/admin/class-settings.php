<?php
/**
 * Settings
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Settings {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'wp_realestate_settings';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 1.0
	 */
	public function __construct() {
	
		add_action( 'admin_menu', array( $this, 'admin_menu' ) , 10 );

		add_action( 'admin_init', array( $this, 'init' ) );

		//Custom CMB2 Settings Fields
		add_action( 'cmb2_render_wp_realestate_title', 'wp_realestate_title_callback', 10, 5 );

		add_action( "cmb2_save_options-page_fields", array( $this, 'settings_notices' ), 10, 3 );


		add_action( 'cmb2_render_api_keys', 'wp_realestate_api_keys_callback', 10, 5 );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-wp_realestate_properties_page_property-settings", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	public function admin_menu() {
		//Settings
	 	$wp_realestate_settings_page = add_submenu_page( 'edit.php?post_type=property', __( 'Settings', 'wp-realestate' ), __( 'Settings', 'wp-realestate' ), 'manage_options', 'property-settings',
	 		array( $this, 'admin_page_display' ) );
	}

	/**
	 * Register our setting to WP
	 * @since  1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Retrieve settings tabs
	 *
	 * @since 1.0
	 * @return array $tabs
	 */
	public function wp_realestate_get_settings_tabs() {
		$tabs             	  = array();
		$tabs['general']  	  = __( 'General', 'wp-realestate' );
		$tabs['property_submission']   = __( 'Property Submission', 'wp-realestate' );
		$tabs['pages']   = __( 'Pages', 'wp-realestate' );
		$tabs['compare_settings'] = __( 'Compare Settings', 'wp-realestate' );
		$tabs['review_settings'] = __( 'Review Settings', 'wp-realestate' );
	 	$tabs['api_settings'] = __( 'Social API', 'wp-realestate' );
	 	$tabs['yelp_walkscore_api_settings'] = __( 'Yelp | Walk Score API', 'wp-realestate' );
	 	$tabs['email_notification'] = __( 'Email Notification', 'wp-realestate' );

		return apply_filters( 'wp_realestate_settings_tabs', $tabs );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0
	 */
	public function admin_page_display() {

		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->wp_realestate_get_settings_tabs() ) ? $_GET['tab'] : 'general';

		?>

		<div class="wrap wp_realestate_settings_page cmb2_options_page <?php echo $this->key; ?>">
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->wp_realestate_get_settings_tabs() as $tab_id => $tab_name ) {

					$tab_url = esc_url( add_query_arg( array(
						'settings-updated' => false,
						'tab'              => $tab_id
					) ) );

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );

					echo '</a>';
				}
				?>
			</h2>
			
			<?php cmb2_metabox_form( $this->wp_realestate_settings( $active_tab ), $this->key ); ?>

		</div><!-- .wrap -->

		<?php
	}

	/**
	 * Define General Settings Metabox and field configurations.
	 *
	 * Filters are provided for each settings section to allow add-ons and other plugins to add their own settings
	 *
	 * @param $active_tab active tab settings; null returns full array
	 *
	 * @return array
	 */
	public function wp_realestate_settings( $active_tab ) {

		$pages = wp_realestate_cmb2_get_page_options( array(
			'post_type'   => 'page',
			'numberposts' => - 1
		) );

		$images_file_types = array();
		$mime_types = WP_RealEstate_Mixes::get_image_mime_types();
		foreach($mime_types as $key => $mine_type) {
			$images_file_types[$key] = $key;
		}

		$countries = array( '' => __('All Countries', 'wp-realestate') );
		$countries = array_merge( $countries, WP_RealEstate_Mixes::get_all_countries() );
		
		$wp_realestate_settings = array();
		// General
		$wp_realestate_settings['general'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'General Settings', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_general', array(
					array(
						'name' => __( 'General Settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_1',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Number properties per page', 'wp-realestate' ),
						'desc'    => __( 'Number of properties to display per page.', 'wp-realestate' ),
						'id'      => 'number_properties_per_page',
						'type'    => 'text',
						'default' => '10',
					),
					array(
						'name'    => __( 'Number agents per page', 'wp-realestate' ),
						'desc'    => __( 'Number of agents to display per page.', 'wp-realestate' ),
						'id'      => 'number_agents_per_page',
						'type'    => 'text',
						'default' => '10',
					),
					array(
						'name'    => __( 'Number agencies per page', 'wp-realestate' ),
						'desc'    => __( 'Number of agencies to display per page.', 'wp-realestate' ),
						'id'      => 'number_agencies_per_page',
						'type'    => 'text',
						'default' => '10',
					),
					array(
						'name' => __( 'User Settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_user',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Moderate New User', 'wp-realestate' ),
						'desc'    => __( 'Require admin approval of all new users', 'wp-realestate' ),
						'id'      => 'users_requires_approval',
						'type'    => 'pw_select',
						'options' => array(
							'auto' 	=> __( 'Auto Approve', 'wp-realestate' ),
							'email_approve' => __( 'Email Approve', 'wp-realestate' ),
							'admin_approve' => __( 'Administrator Approve', 'wp-realestate' ),
						),
						'default' => 'auto',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name' => __( 'Currency Settings', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_2',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),

					array(
						'name'    => __( 'Currency Symbol', 'wp-realestate' ),
						'desc'    => __( 'Enter your Currency Symbol. Default $', 'wp-realestate' ),
						'id'      => 'currency_symbol',
						'type'    => 'text',
						'default' => '$',
					),
					array(
						'name'    => __( 'Currency Code', 'wp-realestate' ),
						'desc'    => __( 'Enter your Currency Code. Default USD', 'wp-realestate' ),
						'id'      => 'currency_code',
						'type'    => 'text',
						'default' => 'USD',
					),
					array(
						'name'    => __( 'Currency Position', 'wp-realestate' ),
						'desc'    => 'Choose the position of the currency sign.',
						'id'      => 'currency_position',
						'type'    => 'pw_select',
						'options' => array(
							'before' => __( 'Before - $10', 'wp-realestate' ),
							'after'  => __( 'After - 10$', 'wp-realestate' )
						),
						'default' => 'before',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Decimal places', 'wp-realestate' ),
						'desc'    => __( 'This sets the number of decimal points shown in displayed prices.', 'wp-realestate' ),
						'id'      => 'money_decimals',
						'type'    => 'text_small',
						'attributes' 	    => array(
							'type' 				=> 'number',
							'min'				=> 0,
							'pattern' 			=> '\d*',
						)
					),
					array(
						'name'            => __( 'Decimal Separator', 'wp-realestate' ),
						'desc'            => __( 'The symbol (usually , or .) to separate decimal points', 'wp-realestate' ),
						'id'              => 'money_dec_point',
						'type'            => 'text_small',
						'default' 		=> '.',
					),
					array(
						'name'    => __( 'Thousands Separator', 'wp-realestate' ),
						'desc'    => __( 'If you need space, enter &nbsp;', 'wp-realestate' ),
						'id'      => 'money_thousands_separator',
						'type'    => 'text_small',
					),
					array(
						'name' => __( 'Measurement', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_measurement',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Area Unit', 'wp-realestate' ),
						'id'      => 'measurement_unit_area',
						'type'    => 'text_small',
						'default' => 'sqft',
					),
					array(
						'name'    => __( 'Distance Unit', 'wp-realestate' ),
						'id'      => 'measurement_distance_unit',
						'type'    => 'text_small',
						'default' => 'ft',
					),
					array(
						'name'    => __( 'Search Distance unit', 'wp-realestate' ),
						'id'      => 'search_distance_unit',
						'type'    => 'pw_select',
						'options' => array(
							'km' => __('Kilometers', 'wp-realestate'),
							'miles' => __('Miles', 'wp-realestate'),
						),
						'default' => 'miles',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),

					array(
						'name' => __( 'File Types', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_3',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Images File Types', 'wp-realestate' ),
						'id'      => 'image_file_types',
						'type'    => 'multicheck_inline',
						'options' => $images_file_types,
						'default' => array('jpg', 'jpeg', 'jpe', 'png')
					),
					array(
						'name' => __( 'Map API Settings', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_4',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Map Service', 'wp-realestate' ),
						'id'      => 'map_service',
						'type'    => 'pw_select',
						'options' => array(
							'mapbox' => __('Mapbox', 'wp-realestate'),
							'google-map' => __('Google Maps', 'wp-realestate'),
						),
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Google Map API', 'wp-realestate' ),
						'desc'    => __( 'Google requires an API key to retrieve location information for property listings. Acquire an API key from the <a href="https://developers.google.com/maps/documentation/geocoding/get-api-key">Google Maps API developer site.</a>', 'wp-realestate' ),
						'id'      => 'google_map_api_keys',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Google Map Type', 'wp-realestate' ),
						'id'      => 'googlemap_type',
						'type'    => 'pw_select',
						'options' => array(
							'roadmap' => __('ROADMAP', 'wp-realestate'),
							'satellite' => __('SATELLITE', 'wp-realestate'),
							'hybrid' => __('HYBRID', 'wp-realestate'),
							'terrain' => __('TERRAIN', 'wp-realestate'),
						),
						'default' => 'roadmap',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Google Maps Style', 'wp-realestate' ),
						'desc' 	  => wp_kses(__('<a href="//snazzymaps.com/">Get custom style</a> and paste it below. If there is nothing added, we will fallback to the Google Maps service.', 'wp-realestate'), array('a' => array('href' => array()))),
						'id'      => 'google_map_style',
						'type'    => 'textarea',
						'default' => '',
					),
					array(
						'name'    => __( 'Mapbox Token', 'wp-realestate' ),
						'desc' => wp_kses(__('<a href="//www.mapbox.com/help/create-api-access-token/">Get a FREE token</a> and paste it below. If there is nothing added, we will fallback to the Google Maps service.', 'wp-realestate'), array('a' => array('href' => array()))),
						'id'      => 'mapbox_token',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Mapbox Style', 'wp-realestate' ),
						'id'      => 'mapbox_style',
						'type'    => 'wp_realestate_image_select',
						'default' => 'streets-v11',
						'options' => array(
		                    'streets-v11' => array(
		                        'alt' => esc_html__('streets', 'wp-realestate'),
		                        'img' => WP_REALESTATE_PLUGIN_URL . '/assets/images/streets.png'
		                    ),
		                    'light-v10' => array(
		                        'alt' => esc_html__('light', 'wp-realestate'),
		                        'img' => WP_REALESTATE_PLUGIN_URL . '/assets/images/light.png'
		                    ),
		                    'dark-v10' => array(
		                        'alt' => esc_html__('dark', 'wp-realestate'),
		                        'img' => WP_REALESTATE_PLUGIN_URL . '/assets/images/dark.png'
		                    ),
		                    'outdoors-v11' => array(
		                        'alt' => esc_html__('outdoors', 'wp-realestate'),
		                        'img' => WP_REALESTATE_PLUGIN_URL . '/assets/images/outdoors.png'
		                    ),
		                    'satellite-v9' => array(
		                        'alt' => esc_html__('satellite', 'wp-realestate'),
		                        'img' => WP_REALESTATE_PLUGIN_URL . '/assets/images/satellite.png'
		                    ),
		                ),
					),
					array(
						'name'    => __( 'Geocoder Country', 'wp-realestate' ),
						'id'      => 'geocoder_country',
						'type'    => 'pw_select',
						'options' => $countries,
						'attributes'        => array(
		                    'data-allowclear' => 'true',
		                    'data-width'		=> '25em',
		                    'data-placeholder'	=> __( 'All Countries', 'wp-realestate' )
		                ),
					),
					array(
						'name' => __( 'Default maps location', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_general_settings_default_maps_location',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Latitude', 'wp-realestate' ),
						'desc'    => __( 'Enter your latitude', 'wp-realestate' ),
						'id'      => 'default_maps_location_latitude',
						'type'    => 'text_small',
						'default' => '43.6568',
					),
					array(
						'name'    => __( 'Longitude', 'wp-realestate' ),
						'desc'    => __( 'Enter your longitude', 'wp-realestate' ),
						'id'      => 'default_maps_location_longitude',
						'type'    => 'text_small',
						'default' => '-79.4512',
					),
					// location
					array(
						'name' => __( 'Location Settings', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_job_board_title',
						'id'   => 'wp_job_board_title_general_settings_location',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Location Multiple Fields', 'wp-realestate' ),
						'id'      => 'location_multiple_fields',
						'type'    => 'select',
						'options' => array(
							'yes' 	=> __( 'Yes', 'wp-realestate' ),
							'no'   => __( 'No', 'wp-realestate' ),
						),
						'default' => 'yes',
						'desc'    => __( 'You can set 4 fields for regions like: Country, State, City, District', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Number Fields', 'wp-realestate' ),
						'id'      => 'location_nb_fields',
						'type'    => 'select',
						'options' => array(
							'1' => __('1 Field', 'wp-realestate'),
							'2' => __('2 Fields', 'wp-realestate'),
							'3' => __('3 Fields', 'wp-realestate'),
							'4' => __('4 Fields', 'wp-realestate'),
						),
						'default' => '1',
						'desc'    => __( 'You can set 4 fields for regions like: Country, State, City, District', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'First Field Label', 'wp-realestate' ),
						'desc'    => __( 'First location field label', 'wp-realestate' ),
						'id'      => 'location_1_field_label',
						'type'    => 'text',
						'default' => 'Country',
					),
					array(
						'name'    => __( 'Second Field Label', 'wp-realestate' ),
						'desc'    => __( 'Second location field label', 'wp-realestate' ),
						'id'      => 'location_2_field_label',
						'type'    => 'text',
						'default' => 'State',
					),
					array(
						'name'    => __( 'Third Field Label', 'wp-realestate' ),
						'desc'    => __( 'Third location field label', 'wp-realestate' ),
						'id'      => 'location_3_field_label',
						'type'    => 'text',
						'default' => 'City',
					),
					array(
						'name'    => __( 'Fourth Field Label', 'wp-realestate' ),
						'desc'    => __( 'Fourth location field label', 'wp-realestate' ),
						'id'      => 'location_4_field_label',
						'type'    => 'text',
						'default' => 'District',
					),

				), $pages
			)		 
		);

		// Property Submission
		$wp_realestate_settings['property_submission'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Property Submission', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_property_submission', array(
					array(
						'name' => __( 'Property Submission', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_property_submission_settings_1',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Submit Property Form Page', 'wp-realestate' ),
						'desc'    => __( 'This is page to display form for submit property. The <code>[wp_realestate_submission]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'submit_property_form_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Moderate New Listings', 'wp-realestate' ),
						'desc'    => __( 'Require admin approval of all new listing submissions', 'wp-realestate' ),
						'id'      => 'submission_requires_approval',
						'type'    => 'pw_select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-realestate' ),
							'off'   => __( 'Disable', 'wp-realestate' ),
						),
						'default' => 'on',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Allow Published Edits', 'wp-realestate' ),
						'desc'    => __( 'Choose whether published property listings can be edited and if edits require admin approval. When moderation is required, the original property listings will be unpublished while edits await admin approval.', 'wp-realestate' ),
						'id'      => 'user_edit_published_submission',
						'type'    => 'pw_select',
						'options' => array(
							'no' 	=> __( 'Users cannot edit', 'wp-realestate' ),
							'yes'   => __( 'Users can edit without admin approval', 'wp-realestate' ),
							'yes_moderated'   => __( 'Users can edit, but edits require admin approval', 'wp-realestate' ),
						),
						'default' => 'yes',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'            => __( 'Listing Duration', 'wp-realestate' ),
						'desc'            => __( 'Listings will display for the set number of days, then expire. Leave this field blank if you don\'t want listings to have an expiration date.', 'wp-realestate' ),
						'id'              => 'submission_duration',
						'type'            => 'text_small',
						'default'         => 30,
					),
				), $pages
			)
		);

		// Property Submission
		$wp_realestate_settings['pages'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Pages', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_pages', array(
					array(
						'name'    => __( 'Properties Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the properties listing page. The <code>[wp_realestate_properties]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'properties_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Agents Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the agents listing page. The <code>[wp_realestate_agents]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'agents_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Agencies Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the agencies listing page. The <code>[wp_realestate_agencies]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'agencies_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Login/Register Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the property listings page. The <code>[wp_realestate_login]</code> <code>[wp_realestate_register]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'login_register_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Approve User Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the job listings page. The <code>[wp_realestate_approve_user]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'approve_user_page_id',
						'type'    => 'select',
						'options' => $pages,
					),
					array(
						'name'    => __( 'User Dashboard Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the user dashboard. The <code>[wp_realestate_user_dashboard]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'user_dashboard_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Edit Profile Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the user edit profile. The <code>[wp_realestate_change_profile]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'edit_profile_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Change Password Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the user change password. The <code>[wp_realestate_change_password]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'change_password_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'My Properties Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the my property page. The <code>[wp_realestate_my_properties]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'my_properties_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Favorite Properties Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the my favorite property page. The <code>[wp_realestate_my_property_favorite]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'favorite_properties_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					
					array(
						'name'    => __( 'Compare Properties Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the location of the property compare page. The <code>[wp_realestate_property_compare]</code> shortcode should be on this page.', 'wp-realestate' ),
						'id'      => 'compare_properties_page_id',
						'type'    => 'pw_select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
					array(
						'name'    => __( 'Terms and Conditions Page', 'wp-realestate' ),
						'desc'    => __( 'This lets the plugin know the Terms and Conditions page.', 'wp-realestate' ),
						'id'      => 'terms_conditions_page_id',
						'type'    => 'select',
						'options' => $pages,
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
				), $pages
			)
		);
		
		// Compare Settings
		$compare_fields = apply_filters( 'wp-realestate-default-property-compare-fields', array() );
		$setting_compare_fields = array();
		if ( !empty($compare_fields) ) {
			foreach ($compare_fields as $field) {
				$setting_compare_fields[] = array(
					'name'    => sprintf(__( 'Enable %s', 'wp-realestate' ), $field['name']),
					'id'      => sprintf('enable_compare_%s', $field['id']),
					'type'    => 'pw_select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-realestate' ),
						'off'   => __( 'Disable', 'wp-realestate' ),
					),
					'default' => 'on',
					'attributes'        => array(
	                    'data-allowclear' => 'false',
	                    'data-width'		=> '25em'
	                ),
				);
			}
		}
		$wp_realestate_settings['compare_settings'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Compare Fields Settings', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_compare_fields_settings', $setting_compare_fields, $pages, $compare_fields )
		);
		
		// Review Settings
		$wp_realestate_settings['review_settings'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Review Settings', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_review_settings', array(
				// review property
				array(
					'name' => __( 'Property review settings', 'wp-realestate' ),
					'type' => 'wp_realestate_title',
					'id'   => 'wp_realestate_title_property_review_settings_title',
					'before_row' => '<hr>',
					'after_row'  => '<hr>',
				),
				array(
					'name'    => __( 'Enable Property Review', 'wp-realestate' ),
					'id'      => 'enable_property_review',
					'type'    => 'pw_select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-realestate' ),
						'off'   => __( 'Disable', 'wp-realestate' ),
					),
					'default' => 'on',
					'attributes'        => array(
	                    'data-allowclear' => 'false',
	                    'data-width'		=> '25em'
	                ),
				),
				array(
					'id'          => 'property_review_category',
					'type'        => 'group',
					'name' => __( 'Property Review Category', 'wp-realestate' ),
					'repeatable'  => true,
					'options'     => array(
						'group_title'       => __( 'Category {#}', 'wp-realestate' ), // since version 1.1.4, {#} gets replaced by row number
						'add_button'        => __( 'Add Another Category', 'wp-realestate' ),
						'remove_button'     => __( 'Remove Category', 'wp-realestate' ),
						'sortable'          => true,
					),
					'fields'	=> array(
						array(
							'name'            => __( 'Category Key', 'wp-realestate' ),
							'desc'            => __( 'Enter category key.', 'wp-realestate' ),
							'id'              => 'key',
							'type'            => 'text',
							'attributes' 	    => array(
								'data-general-review-key' => 'property'
							),
						),
						array(
							'name'            => __( 'Category Name', 'wp-realestate' ),
							'desc'            => __( 'Enter category name.', 'wp-realestate' ),
							'id'              => 'name',
							'type'            => 'text',
						),
					)
				),
				
				// review agent
				array(
					'name' => __( 'Agent review settings', 'wp-realestate' ),
					'type' => 'wp_realestate_title',
					'id'   => 'wp_realestate_title_agent_review_settings_title',
					'before_row' => '<hr>',
					'after_row'  => '<hr>',
				),
				array(
					'name'    => __( 'Enable Agent Review', 'wp-realestate' ),
					'id'      => 'enable_agent_review',
					'type'    => 'pw_select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-realestate' ),
						'off'   => __( 'Disable', 'wp-realestate' ),
					),
					'default' => 'on',
					'attributes'        => array(
	                    'data-allowclear' => 'false',
	                    'data-width'		=> '25em'
	                ),
				),
				array(
					'id'          => 'agent_review_category',
					'type'        => 'group',
					'name' => __( 'Agent Review Category', 'wp-realestate' ),
					'repeatable'  => true,
					'options'     => array(
						'group_title'       => __( 'Category {#}', 'wp-realestate' ), // since version 1.1.4, {#} gets replaced by row number
						'add_button'        => __( 'Add Another Category', 'wp-realestate' ),
						'remove_button'     => __( 'Remove Category', 'wp-realestate' ),
						'sortable'          => true,
					),
					'fields'	=> array(
						array(
							'name'            => __( 'Category Key', 'wp-realestate' ),
							'desc'            => __( 'Enter category key.', 'wp-realestate' ),
							'id'              => 'key',
							'type'            => 'text',
							'attributes' 	    => array(
								'data-general-review-key' => 'agent'
							),
						),
						array(
							'name'            => __( 'Category Name', 'wp-realestate' ),
							'desc'            => __( 'Enter category name.', 'wp-realestate' ),
							'id'              => 'name',
							'type'            => 'text',
						),
					)
				),

				// review agency
				array(
					'name' => __( 'Agency review settings', 'wp-realestate' ),
					'type' => 'wp_realestate_title',
					'id'   => 'wp_realestate_title_agency_review_settings_title',
					'before_row' => '<hr>',
					'after_row'  => '<hr>',
				),
				array(
					'name'    => __( 'Enable Agency Review', 'wp-realestate' ),
					'id'      => 'enable_agency_review',
					'type'    => 'pw_select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-realestate' ),
						'off'   => __( 'Disable', 'wp-realestate' ),
					),
					'default' => 'on',
					'attributes'        => array(
	                    'data-allowclear' => 'false',
	                    'data-width'		=> '25em'
	                ),
				),
				array(
					'id'          => 'agency_review_category',
					'type'        => 'group',
					'name' => __( 'Agency Review Category', 'wp-realestate' ),
					'repeatable'  => true,
					'options'     => array(
						'group_title'       => __( 'Category {#}', 'wp-realestate' ), // since version 1.1.4, {#} gets replaced by row number
						'add_button'        => __( 'Add Another Category', 'wp-realestate' ),
						'remove_button'     => __( 'Remove Category', 'wp-realestate' ),
						'sortable'          => true,
					),
					'fields'	=> array(
						array(
							'name'            => __( 'Category Key', 'wp-realestate' ),
							'desc'            => __( 'Enter category key.', 'wp-realestate' ),
							'id'              => 'key',
							'type'            => 'text',
							'attributes' 	    => array(
								'data-general-review-key' => 'agency'
							),
						),
						array(
							'name'            => __( 'Category Name', 'wp-realestate' ),
							'desc'            => __( 'Enter category name.', 'wp-realestate' ),
							'id'              => 'name',
							'type'            => 'text',
						),
					)
				),

			) )		 
		);

		// ReCaptcha
		$wp_realestate_settings['api_settings'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Social API', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_api_settings', array(
					// Facebook
					array(
						'name' => __( 'Facebook API settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_api_settings_facebook_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-realestate'), admin_url('admin-ajax.php?action=wp_realestate_facebook_login')),
					),
					array(
						'name'            => __( 'App ID', 'wp-realestate' ),
						'desc'            => __( 'Please enter App ID of your Facebook account.', 'wp-realestate' ),
						'id'              => 'facebook_api_app_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'App Secret', 'wp-realestate' ),
						'desc'            => __( 'Please enter App Secret of your Facebook account.', 'wp-realestate' ),
						'id'              => 'facebook_api_app_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Facebook Login', 'wp-realestate' ),
						'id'      => 'enable_facebook_login',
						'type'    => 'checkbox',
					),

					// Linkedin
					array(
						'name' => __( 'Linkedin API settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_api_settings_linkedin_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-realestate'), home_url('/')),
					),
					array(
						'name'            => __( 'Client ID', 'wp-realestate' ),
						'desc'            => __( 'Please enter Client ID of your linkedin app.', 'wp-realestate' ),
						'id'              => 'linkedin_api_client_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Client Secret', 'wp-realestate' ),
						'desc'            => __( 'Please enter Client Secret of your linkedin app.', 'wp-realestate' ),
						'id'              => 'linkedin_api_client_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Linkedin Login', 'wp-realestate' ),
						'id'      => 'enable_linkedin_login',
						'type'    => 'checkbox',
					),

					// Twitter
					array(
						'name' => __( 'Twitter API settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_api_settings_twitter_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-realestate'), home_url('/')),
					),
					array(
						'name'            => __( 'Consumer Key', 'wp-realestate' ),
						'desc'            => __( 'Set Consumer Key for twitter.', 'wp-realestate' ),
						'id'              => 'twitter_api_consumer_key',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Consumer Secret', 'wp-realestate' ),
						'desc'            => __( 'Set Consumer Secret for twitter.', 'wp-realestate' ),
						'id'              => 'twitter_api_consumer_secret',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Access Token', 'wp-realestate' ),
						'desc'            => __( 'Set Access Token for twitter.', 'wp-realestate' ),
						'id'              => 'twitter_api_access_token',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Token Secret', 'wp-realestate' ),
						'desc'            => __( 'Set Token Secret for twitter.', 'wp-realestate' ),
						'id'              => 'twitter_api_token_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Twitter Login', 'wp-realestate' ),
						'id'      => 'enable_twitter_login',
						'type'    => 'checkbox',
					),

					// Google API
					array(
						'name' => __( 'Google API settings Settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_api_settings_google_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-realestate'), home_url('/')),
					),
					array(
						'name'            => __( 'API Key', 'wp-realestate' ),
						'desc'            => __( 'Please enter API key of your Google account.', 'wp-realestate' ),
						'id'              => 'google_api_key',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Client ID', 'wp-realestate' ),
						'desc'            => __( 'Please enter Client ID of your Google account.', 'wp-realestate' ),
						'id'              => 'google_api_client_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Client Secret', 'wp-realestate' ),
						'desc'            => __( 'Please enter Client secret of your Google account.', 'wp-realestate' ),
						'id'              => 'google_api_client_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Google Login', 'wp-realestate' ),
						'id'      => 'enable_google_login',
						'type'    => 'checkbox',
					),

					// Google Recaptcha
					array(
						'name' => __( 'Google reCAPTCHA API Settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_api_settings_google_recaptcha',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
						'desc' => __('The plugin use ReCaptcha v2', 'wp-realestate'),
					),
					array(
						'name'            => __( 'Site Key', 'wp-realestate' ),
						'desc'            => __( 'You can retrieve your site key from <a href="https://www.google.com/recaptcha/admin#list">Google\'s reCAPTCHA admin dashboard.</a>', 'wp-realestate' ),
						'id'              => 'recaptcha_site_key',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Secret Key', 'wp-realestate' ),
						'desc'            => __( 'You can retrieve your secret key from <a href="https://www.google.com/recaptcha/admin#list">Google\'s reCAPTCHA admin dashboard.</a>', 'wp-realestate' ),
						'id'              => 'recaptcha_secret_key',
						'type'            => 'text',
					),
				)
			)		 
		);
		
		// Yelp/Walkscore
		$yelp_url = 'https://www.yelp.com/developers/v3/manage_app';
		$wp_realestate_settings['yelp_walkscore_api_settings'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Yelp API', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_yelp_api_settings', array(
					// Yelp
					array(
						'name' => __( 'Yelp API settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_yelp_api_settings_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
					),
					array(
						'name'            => __( 'Yelp App ID', 'wp-realestate' ),
						'desc'            => sprintf(__( 'Add Yelp application ID. To get your Yelp Application ID, go to your Yelp Account. Register <a href="%s" target="_blank">here</a>', 'wp-realestate' ), $yelp_url),
						'id'              => 'api_settings_yelp_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Yelp App Secret', 'wp-realestate' ),
						'desc'            => sprintf(__( 'Put your Yelp App Secret here. You can find it in your Yelp Application Dashboard. Register <a href="%s" target="_blank">here</a>', 'wp-realestate' ), $yelp_url),
						'id'              => 'api_settings_yelp_app_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Distance unit', 'wp-realestate' ),
						'id'      => 'api_settings_yelp_distance_unit',
						'type'    => 'pw_select',
						'options' => array(
							'km' => __('Kilometers', 'wp-realestate'),
							'miles' => __('Miles', 'wp-realestate'),
						),
						'default' => 'miles',
						'attributes'        => array(
		                    'data-allowclear' => 'false',
		                    'data-width'		=> '25em'
		                ),
					),
                    array(
		                'name'              => __( 'Yelp Categories', 'wp-realestate' ),
		                'id'                =>'api_settings_yelp_categories',
		                'type'              => 'group',
		                'options'           => array(
		                    'group_title'       => __( 'Yelp Category {#}', 'wp-realestate' ),
		                    'add_button'        => __( 'Add Category', 'wp-realestate' ),
		                    'remove_button'     => __( 'Remove Category', 'wp-realestate' ),
		                    'sortable'          => true,
		                    'closed'         => true,
		                ),
		                'fields'            => array(
		                    array(
		                        'id'                => 'yelp_title',
		                        'name'              => __( 'Title', 'wp-realestate' ),
		                        'type'              => 'text',
		                    ),
		                    array(
								'name'    => __( 'Yelp Categories', 'wp-realestate' ),
								'id'      => 'yelp_category',
								'type'    => 'pw_select',
								'options' => WP_RealEstate_Property_Yelp::get_yelp_categories(),
								'attributes'        => array(
				                    'data-allowclear' => 'false',
				                    'data-width'		=> '25em'
				                ),
							),
		                    array(
		                        'name'              => __( 'Category Icon', 'wp-realestate' ),
		                        'id'                => 'yelp_icon',
		                        'type'              => 'file',
		                        'multiple_files'    => false,
		                    ),
		                ),
		            ),

		            // Walk Score
					array(
						'name' => __( 'Walk Score API settings', 'wp-realestate' ),
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_walk_score_api_settings_title',
						'before_row' => '<hr>',
						'after_row'  => '<hr>',
					),
					array(
						'name'            => __( 'Walk Score API Key', 'wp-realestate' ),
						'desc'            => __( 'Add Walk Score API key. To get your Walk Score API key, go to your Walk Score Account.', 'wp-realestate' ),
						'id'              => 'api_settings_walk_score_api_key',
						'type'            => 'text',
					),
				)
			)		 
		);

		// Email notification
		$wp_realestate_settings['email_notification'] = array(
			'id'         => 'options_page',
			'wp_realestate_title' => __( 'Email Notification', 'wp-realestate' ),
			'show_on'    => array(
				'key' => 'options-page',
				'value' => array( $this->key )
			),
			'fields'     => apply_filters( 'wp_realestate_settings_email_notification', array(
					
					array(
						'name'    => __( 'Admin Notice of New Property', 'wp-realestate' ),
						'id'      => 'admin_notice_add_new_listing',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new property is submitted on the frontend.', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_add_new_listing', 'subject') ),
						'id'      => 'admin_notice_add_new_listing_subject',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_add_new_listing', 'content') ),
						'id'      => 'admin_notice_add_new_listing_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					
					array(
						'name'    => __( 'Admin Notice of Updated Property', 'wp-realestate' ),
						'id'      => 'admin_notice_updated_listing',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a property is updated on the frontend.', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_updated_listing', 'subject') ),
						'id'      => 'admin_notice_updated_listing_subject',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_updated_listing', 'content') ),
						'id'      => 'admin_notice_updated_listing_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					
					array(
						'name'    => __( 'Admin Notice of Expiring Property Properties', 'wp-realestate' ),
						'id'      => 'admin_notice_expiring_listing',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to the site administrator before a property listing expires.', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-realestate' ),
						'desc'    => __( 'days', 'wp-realestate' ),
						'id'      => 'admin_notice_expiring_listing_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_expiring_listing', 'subject') ),
						'id'      => 'admin_notice_expiring_listing_subject',
						'type'    => 'text',
						'default' => 'Property Listing Expiring: {{property_title}}',
					),
					array(
						'name'    => __( 'Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('admin_notice_expiring_listing', 'content') ),
						'id'      => 'admin_notice_expiring_listing_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					
					array(
						'name'    => __( 'User Notice of Expiring Property Properties', 'wp-realestate' ),
						'id'      => 'user_notice_expiring_listing',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to user before a property listing expires.', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-realestate' ),
						'desc'    => __( 'days', 'wp-realestate' ),
						'id'      => 'user_notice_expiring_listing_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_notice_expiring_listing', 'subject') ),
						'id'      => 'user_notice_expiring_listing_subject',
						'type'    => 'text',
						'default' => 'Property Listing Expiring: {{property_title}}',
					),
					array(
						'name'    => __( 'Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_notice_expiring_listing', 'content') ),
						'id'      => 'user_notice_expiring_listing_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),


					array(
						'name' => __( 'Property Saved Search', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_saved_search',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Property Saved Search Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('saved_search_notice', 'subject') ),
						'id'      => 'saved_search_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Property Saved Search: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('saved_search_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Property Saved Search Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('saved_search_notice', 'content') ),
						'id'      => 'saved_search_notice_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					// contact form Property
					array(
						'name' => __( 'Property Contact Form', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_contact_form_property',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Property Contact Form Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('property_contact_form_notice', 'subject') ),
						'id'      => 'property_contact_form_notice_subject',
						'type'    => 'text',
						'default' => __( 'Contact Form', 'wp-realestate' ),
					),
					array(
						'name'    => __( 'Property Contact Form Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('property_contact_form_notice', 'content') ),
						'id'      => 'property_contact_form_notice_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					// contact form
					array(
						'name' => __( 'Contact Form', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_contact_form',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Contact Form Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('contact_form_notice', 'subject') ),
						'id'      => 'contact_form_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Contact Form: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('contact_form_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Contact Form Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('contact_form_notice', 'content') ),
						'id'      => 'contact_form_notice_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					// Approve new user register
					array(
						'name' => __( 'New user register (auto approve)', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_user_register_auto_approve',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'New user register Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_auto_approve', 'subject') ),
						'id'      => 'user_register_auto_approve_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New user register: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_auto_approve', 'subject') ),
					),
					array(
						'name'    => __( 'New user register Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_auto_approve', 'content') ),
						'id'      => 'user_register_auto_approve_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),
					// Approve new user register
					array(
						'name' => __( 'Approve new user register', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_user_register_need_approve',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Approve new user register Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_need_approve', 'subject') ),
						'id'      => 'user_register_need_approve_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Approve new user register: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_need_approve', 'subject') ),
					),
					array(
						'name'    => __( 'Approve new user register Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_need_approve', 'content') ),
						'id'      => 'user_register_need_approve_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),
					// Approved user register
					array(
						'name' => __( 'Approved user', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_user_register_approved',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Approved user Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_approved', 'subject') ),
						'id'      => 'user_register_approved_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Approve new user register: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_approved', 'subject') ),
					),
					array(
						'name'    => __( 'Approved user Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_approved', 'content') ),
						'id'      => 'user_register_approved_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),
					// Denied user register
					array(
						'name' => __( 'Denied user', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_user_register_denied',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Denied user Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_denied', 'subject') ),
						'id'      => 'user_register_denied_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Approve new user register: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_denied', 'subject') ),
					),
					array(
						'name'    => __( 'Denied user Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_register_denied', 'content') ),
						'id'      => 'user_register_denied_content',
						'type'    => 'wysiwyg',
						'default' => '',
					),

					// Reset Password
					array(
						'name' => __( 'Reset Password', 'wp-realestate' ),
						'desc' => '',
						'type' => 'wp_realestate_title',
						'id'   => 'wp_realestate_title_user_reset_password',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'Reset Password Subject', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_reset_password', 'subject') ),
						'id'      => 'user_reset_password_subject',
						'type'    => 'text',
						'default' => 'Your new password',
					),
					array(
						'name'    => __( 'Reset Password Content', 'wp-realestate' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-realestate' ), WP_RealEstate_Email::display_email_vars('user_reset_password', 'content') ),
						'id'      => 'user_reset_password_content',
						'type'    => 'wysiwyg',
						'default' => 'Your new password is: {{new_password}}',
					),
				)
			)		 
		);
		
		//Return all settings array if necessary
		if ( $active_tab === null   ) {  
			return apply_filters( 'wp_realestate_registered_settings', $wp_realestate_settings );
		}

		// Add other tabs and settings fields as needed
		return apply_filters( 'wp_realestate_registered_'.$active_tab.'_settings', isset($wp_realestate_settings[ $active_tab ])?$wp_realestate_settings[ $active_tab ]:array() );
	}

	/**
	 * Show Settings Notices
	 *
	 * @param $object_id
	 * @param $updated
	 * @param $cmb
	 */
	public function settings_notices( $object_id, $updated, $cmb ) {

		//Sanity check
		if ( $object_id !== $this->key ) {
			return;
		}

		if ( did_action( 'cmb2_save_options-page_fields' ) === 1 ) {
			settings_errors( 'wp_realestate-notices' );
		}

		add_settings_error( 'wp_realestate-notices', 'global-settings-updated', __( 'Settings updated.', 'wp-realestate' ), 'updated' );
	}


	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  1.0
	 *
	 * @param  string $field Field to retrieve
	 *
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {

		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'fields', 'wp_realestate_title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid property: ' . $field );
	}


}

// Get it started
$WP_RealEstate_Settings = new WP_RealEstate_Settings();

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function wp_realestate_get_option( $key = '', $default = false ) {
	global $wp_realestate_options;
	$value = ! empty( $wp_realestate_options[ $key ] ) ? $wp_realestate_options[ $key ] : $default;
	$value = apply_filters( 'wp_realestate_get_option', $value, $key, $default );

	return apply_filters( 'wp_realestate_get_option_' . $key, $value, $key, $default );
}



/**
 * Get Settings
 *
 * Retrieves all WP_RealEstate plugin settings
 *
 * @since 1.0
 * @return array WP_RealEstate settings
 */
function wp_realestate_get_settings() {
	return apply_filters( 'wp_realestate_get_settings', get_option( 'wp_realestate_settings' ) );
}


/**
 * WP_RealEstate Title
 *
 * Renders custom section titles output; Really only an <hr> because CMB2's output is a bit funky
 *
 * @since 1.0
 *
 * @param       $field_object , $escaped_value, $object_id, $object_type, $field_type_object
 *
 * @return void
 */
function wp_realestate_title_callback( $field_object, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$id                = $field_type_object->field->args['id'];
	$title             = $field_type_object->field->args['name'];
	$field_description = $field_type_object->field->args['desc'];
	if ( $field_description ) {
		echo '<div class="desc">'.$field_description.'</div>';
	}
}

function wp_realestate_cmb2_get_page_options( $query_args, $force = false ) {
	$post_options = array( '' => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'property-settings' != $_GET['page'] ) && ! $force ) {
		return $post_options;
	}

	$args = wp_parse_args( $query_args, array(
		'post_type'   => 'page',
		'numberposts' => 10,
	) );

	$posts = get_posts( $args );

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}

add_filter( 'cmb2_get_metabox_form_format', 'wp_realestate_modify_cmb2_form_output', 10, 3 );
function wp_realestate_modify_cmb2_form_output( $form_format, $object_id, $cmb ) {
	//only modify the wp_realestate settings form
	if ( 'wp_realestate_settings' == $object_id && 'options_page' == $cmb->cmb_id ) {

		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="wp_realestate-submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'wp-realestate' ) . '" class="button-primary"></div></form>';
	}

	return $form_format;

}
