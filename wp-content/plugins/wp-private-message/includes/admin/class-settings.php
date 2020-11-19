<?php
/**
 * Settings
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Settings {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'wp_private_message_settings';

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
		add_action( 'cmb2_render_wp_private_message_title', 'wp_private_message_title_callback', 10, 5 );

		add_action( "cmb2_save_options-page_fields", array( $this, 'settings_notices' ), 10, 3 );


		add_action( 'cmb2_render_api_keys', 'wp_private_message_api_keys_callback', 10, 5 );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-wp_private_message_properties_page_private_message-settings", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	public function admin_menu() {
		//Settings
	 	$wp_private_message_settings_page = add_submenu_page( 'edit.php?post_type=private_message', __( 'Settings', 'wp-private-message' ), __( 'Settings', 'wp-private-message' ), 'manage_options', 'private_message-settings',
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
	public function wp_private_message_get_settings_tabs() {
		$tabs             	  = array();
		$tabs['general']  	  = __( 'General', 'wp-private-message' );
	 	$tabs['email_notification'] = __( 'Email Notification', 'wp-private-message' );

		return apply_filters( 'wp_private_message_settings_tabs', $tabs );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0
	 */
	public function admin_page_display() {

		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->wp_private_message_get_settings_tabs() ) ? $_GET['tab'] : 'general';

		?>

		<div class="wrap wp_private_message_settings_page cmb2_options_page <?php echo $this->key; ?>">
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->wp_private_message_get_settings_tabs() as $tab_id => $tab_name ) {

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
			
			<?php cmb2_metabox_form( $this->wp_private_message_settings( $active_tab ), $this->key ); ?>

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
	public function wp_private_message_settings( $active_tab ) {

		$pages = wp_private_message_cmb2_get_post_options( array(
			'post_type'   => 'page',
			'numberposts' => - 1
		) );
		$wp_private_message_settings = array();
		// General
		$wp_private_message_settings['general'] = array(
			'id'         => 'options_page',
			'wp_private_message_title' => __( 'General Settings', 'wp-private-message' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key, ), ),
			'fields'     => apply_filters( 'wp_private_message_settings_general', array(
				array(
					'name' => __( 'General Settings', 'wp-private-message' ),
					'type' => 'wp_private_message_title',
					'id'   => 'wp_private_message_title_general_settings_1',
					'before_row' => '<hr>',
					'after_row'  => '<hr>'
				),
				array(
					'name'    => __( 'Messages Box Page', 'wp-private-message' ),
					'desc'    => __( 'This lets the plugin know the location of the Messages Box Page. The <code>[wp_private_message_dashboard]</code> shortcode should be on this page.', 'wp-private-message' ),
					'id'      => 'message_dashboard_page_id',
					'type'    => 'select',
					'options' => $pages,
				),
				
				array(
					'name'    => __( 'Number message per page', 'wp-private-message' ),
					'desc'    => __( 'Number of message to display per page.', 'wp-private-message' ),
					'id'      => 'number_message_per_page',
					'type'    => 'text',
					'default' => '25',
				),

				array(
					'name'    => __( 'Number replied per page', 'wp-private-message' ),
					'desc'    => __( 'Number of replied to display per page.', 'wp-private-message' ),
					'id'      => 'number_replied_per_page',
					'type'    => 'text',
					'default' => '25',
				),
			))		 
		);

		$wp_private_message_settings['email_notification'] = array(
			'id'         => 'options_page',
			'wp_private_message_title' => __( 'Email Notification', 'wp-private-message' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key, ), ),
			'fields'     => apply_filters( 'wp_private_message_settings_email_notification', array(
					array(
						'name' => __( 'Email New Message Settings', 'wp-private-message' ),
						'type' => 'wp_private_message_title',
						'id'   => 'wp_private_message_title_user_notice_add_new_message',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'User Notice of New Message', 'wp-private-message' ),
						'id'      => 'user_notice_add_new_message',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new message is submitted on the frontend.', 'wp-private-message' ),
					),
					array(
						'name'    => __( 'Subject', 'wp-private-message' ),
						'id'      => 'user_notice_add_new_message_subject',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Content', 'wp-private-message' ),
						'id'      => 'user_notice_add_new_message_content',
						'type'    => 'wysiwyg',
						'default' => '',
						'desc'    => __( 'Enter email content. You can add variables: {{user_name}}, {{message_dashboard}}, {{message_detail_url}}, {{message_subject}}, {{message_content}}, {{website_url}}, {{website_name}}', 'wp-private-message' ),
					),
					// reply
					array(
						'name' => __( 'Email Reply Message Settings', 'wp-private-message' ),
						'type' => 'wp_private_message_title',
						'id'   => 'wp_private_message_title_user_notice_add_reply_message',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name'    => __( 'User Notice of Reply Message', 'wp-private-message' ),
						'id'      => 'user_notice_replied_message',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new message is submitted on the frontend.', 'wp-private-message' ),
					),
					array(
						'name'    => __( 'Subject', 'wp-private-message' ),
						'id'      => 'user_notice_replied_message_subject',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __( 'Content', 'wp-private-message' ),
						'id'      => 'user_notice_replied_message_content',
						'type'    => 'wysiwyg',
						'default' => '',
						'desc'    => __( 'Enter email content. You can add variables: {{user_name}}, {{message_dashboard}}, {{message_detail_url}}, {{message_subject}}, {{message_content}}, {{website_url}}, {{website_name}}', 'wp-private-message' ),
					),
					
				)
			)		 
		);
		//Return all settings array if necessary

		if ( $active_tab === null   ) {  
			return apply_filters( 'wp_private_message_registered_settings', $wp_private_message_settings );
		}

		// Add other tabs and settings fields as needed
		return apply_filters( 'wp_private_message_registered_'.$active_tab.'_settings', isset($wp_private_message_settings[ $active_tab ])?$wp_private_message_settings[ $active_tab ]:array() );

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
			settings_errors( 'wp_private_message-notices' );
		}

		add_settings_error( 'wp_private_message-notices', 'global-settings-updated', __( 'Settings updated.', 'wp-private-message' ), 'updated' );

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
		if ( in_array( $field, array( 'key', 'fields', 'wp_private_message_title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid message: ' . $field );
	}


}

// Get it started
$WP_Private_Message_Settings = new WP_Private_Message_Settings();

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function wp_private_message_get_option( $key = '', $default = false ) {
	global $wp_private_message_options;
	$value = ! empty( $wp_private_message_options[ $key ] ) ? $wp_private_message_options[ $key ] : $default;
	$value = apply_filters( 'wp_private_message_get_option', $value, $key, $default );

	return apply_filters( 'wp_private_message_get_option_' . $key, $value, $key, $default );
}



/**
 * Get Settings
 *
 * Retrieves all WP_Private_Message plugin settings
 *
 * @since 1.0
 * @return array WP_Private_Message settings
 */
function wp_private_message_get_settings() {
	return apply_filters( 'wp_private_message_get_settings', get_option( 'wp_private_message_settings' ) );
}


/**
 * WP_Private_Message Title
 *
 * Renders custom section titles output; Really only an <hr> because CMB2's output is a bit funky
 *
 * @since 1.0
 *
 * @param       $field_object , $escaped_value, $object_id, $object_type, $field_type_object
 *
 * @return void
 */
function wp_private_message_title_callback( $field_object, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$id                = $field_type_object->field->args['id'];
	$title             = $field_type_object->field->args['name'];
	$field_description = $field_type_object->field->args['desc'];

	echo '<hr>';

}


/**
 * Gets a number of posts and displays them as options
 *
 * @param  array $query_args Optional. Overrides defaults.
 * @param  bool  $force      Force the pages to be loaded even if not on settings
 *
 * @see: https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types
 * @return array An array of options that matches the CMB2 options array
 */
function wp_private_message_cmb2_get_post_options( $query_args, $force = false ) {

	$post_options = array( '' => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'private_message-settings' != $_GET['page'] ) && ! $force ) {
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


/**
 * Modify CMB2 Default Form Output
 *
 * @param string @args
 *
 * @since 1.0
 */

add_filter( 'cmb2_get_metabox_form_format', 'wp_private_message_modify_cmb2_form_output', 10, 3 );

function wp_private_message_modify_cmb2_form_output( $form_format, $object_id, $cmb ) {

	//only modify the wp_private_message settings form
	if ( 'wp_private_message_settings' == $object_id && 'options_page' == $cmb->cmb_id ) {

		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="wp_private_message-submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'wp-private-message' ) . '" class="button-primary"></div></form>';
	}

	return $form_format;

}
