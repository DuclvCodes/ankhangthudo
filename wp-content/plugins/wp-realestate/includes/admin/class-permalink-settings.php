<?php
/**
 * Permalink Settings
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_RealEstate_Permalink_Settings {
	
	public static function init() {
		add_action('admin_init', array( __CLASS__, 'setup_fields') );
		add_action('admin_init', array( __CLASS__, 'settings_save') );
	}

	public static function setup_fields() {
		add_settings_field(
			'wp_realestate_property_base_slug',
			__( 'Property base', 'wp-realestate' ),
			array( __CLASS__, 'property_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_type_slug',
			__( 'Property type base', 'wp-realestate' ),
			array( __CLASS__, 'property_type_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_location_slug',
			__( 'Property location base', 'wp-realestate' ),
			array( __CLASS__, 'property_location_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_amenity_slug',
			__( 'Property amenity base', 'wp-realestate' ),
			array( __CLASS__, 'property_amenity_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_status_slug',
			__( 'Property status base', 'wp-realestate' ),
			array( __CLASS__, 'property_status_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_label_slug',
			__( 'Property label base', 'wp-realestate' ),
			array( __CLASS__, 'property_label_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_property_material_slug',
			__( 'Property material base', 'wp-realestate' ),
			array( __CLASS__, 'property_material_slug_input' ),
			'permalink',
			'optional'
		);
		//
		add_settings_field(
			'wp_realestate_property_archive_slug',
			__( 'Property archive page', 'wp-realestate' ),
			array( __CLASS__, 'property_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// agent
		add_settings_field(
			'wp_realestate_agent_base_slug',
			__( 'Agent base', 'wp-realestate' ),
			array( __CLASS__, 'agent_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agent_category_slug',
			__( 'Agent category base', 'wp-realestate' ),
			array( __CLASS__, 'agent_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agent_location_slug',
			__( 'Agent location base', 'wp-realestate' ),
			array( __CLASS__, 'agent_location_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agent_archive_slug',
			__( 'Agent archive page', 'wp-realestate' ),
			array( __CLASS__, 'agent_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// agency
		add_settings_field(
			'wp_realestate_agency_base_slug',
			__( 'Agency base', 'wp-realestate' ),
			array( __CLASS__, 'agency_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agency_category_slug',
			__( 'Agency category base', 'wp-realestate' ),
			array( __CLASS__, 'agency_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agency_location_slug',
			__( 'Agency location base', 'wp-realestate' ),
			array( __CLASS__, 'agency_location_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_realestate_agency_archive_slug',
			__( 'Agency archive page', 'wp-realestate' ),
			array( __CLASS__, 'agency_archive_slug_input' ),
			'permalink',
			'optional'
		);
	}

	public static function property_base_slug_input() {
		$value = get_option('wp_realestate_property_base_slug');
		?>
		<input name="wp_realestate_property_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_amenity_slug_input() {
		$value = get_option('wp_realestate_property_amenity_slug');
		?>
		<input name="wp_realestate_property_amenity_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-amenity', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_status_slug_input() {
		$value = get_option('wp_realestate_property_status_slug');
		?>
		<input name="wp_realestate_property_status_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-status', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_label_slug_input() {
		$value = get_option('wp_realestate_property_label_slug');
		?>
		<input name="wp_realestate_property_label_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-label', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_material_slug_input() {
		$value = get_option('wp_realestate_property_material_slug');
		?>
		<input name="wp_realestate_property_material_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-material', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_type_slug_input() {
		$value = get_option('wp_realestate_property_type_slug');
		?>
		<input name="wp_realestate_property_type_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-type', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_location_slug_input() {
		$value = get_option('wp_realestate_property_location_slug');
		?>
		<input name="wp_realestate_property_location_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'property-location', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function property_archive_slug_input() {
		$value = get_option('wp_realestate_property_archive_slug');
		?>
		<input name="wp_realestate_property_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'properties', 'wp-realestate' ); ?>" />
		<?php
	}

	// agent
	public static function agent_base_slug_input() {
		$value = get_option('wp_realestate_agent_base_slug');
		?>
		<input name="wp_realestate_agent_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agent', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agent_category_slug_input() {
		$value = get_option('wp_realestate_agent_category_slug');
		?>
		<input name="wp_realestate_agent_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agent-category', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agent_location_slug_input() {
		$value = get_option('wp_realestate_agent_location_slug');
		?>
		<input name="wp_realestate_agent_location_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agent-location', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agent_archive_slug_input() {
		$value = get_option('wp_realestate_agent_archive_slug');
		?>
		<input name="wp_realestate_agent_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agents', 'wp-realestate' ); ?>" />
		<?php
	}

	// agency
	public static function agency_base_slug_input() {
		$value = get_option('wp_realestate_agency_base_slug');
		?>
		<input name="wp_realestate_agency_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agency', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agency_category_slug_input() {
		$value = get_option('wp_realestate_agency_category_slug');
		?>
		<input name="wp_realestate_agency_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agency-category', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agency_location_slug_input() {
		$value = get_option('wp_realestate_agency_location_slug');
		?>
		<input name="wp_realestate_agency_location_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agency-location', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function agency_archive_slug_input() {
		$value = get_option('wp_realestate_agency_archive_slug');
		?>
		<input name="wp_realestate_agency_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'agencies', 'wp-realestate' ); ?>" />
		<?php
	}

	public static function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		if ( isset( $_POST['permalink_structure'] ) ) {
			if ( function_exists( 'switch_to_locale' ) ) {
				switch_to_locale( get_locale() );
			}
			if ( isset($_POST['wp_realestate_property_base_slug']) ) {
				update_option( 'wp_realestate_property_base_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_base_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_amenity_slug']) ) {
				update_option( 'wp_realestate_property_amenity_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_amenity_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_status_slug']) ) {
				update_option( 'wp_realestate_property_status_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_status_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_label_slug']) ) {
				update_option( 'wp_realestate_property_label_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_label_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_material_slug']) ) {
				update_option( 'wp_realestate_property_material_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_material_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_type_slug']) ) {
				update_option( 'wp_realestate_property_type_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_type_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_location_slug']) ) {
				update_option( 'wp_realestate_property_location_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_location_slug']) );
			}
			if ( isset($_POST['wp_realestate_property_archive_slug']) ) {
				update_option( 'wp_realestate_property_archive_slug', sanitize_title_with_dashes($_POST['wp_realestate_property_archive_slug']) );
			}

			// agent
			if ( isset($_POST['wp_realestate_agent_base_slug']) ) {
				update_option( 'wp_realestate_agent_base_slug', sanitize_title_with_dashes($_POST['wp_realestate_agent_base_slug']) );
			}
			if ( isset($_POST['wp_realestate_agent_category_slug']) ) {
				update_option( 'wp_realestate_agent_category_slug', sanitize_title_with_dashes($_POST['wp_realestate_agent_category_slug']) );
			}
			if ( isset($_POST['wp_realestate_agent_location_slug']) ) {
				update_option( 'wp_realestate_agent_location_slug', sanitize_title_with_dashes($_POST['wp_realestate_agent_location_slug']) );
			}
			if ( isset($_POST['wp_realestate_agent_archive_slug']) ) {
				update_option( 'wp_realestate_agent_archive_slug', sanitize_title_with_dashes($_POST['wp_realestate_agent_archive_slug']) );
			}

			// agency
			if ( isset($_POST['wp_realestate_agency_base_slug']) ) {
				update_option( 'wp_realestate_agency_base_slug', sanitize_title_with_dashes($_POST['wp_realestate_agency_base_slug']) );
			}
			if ( isset($_POST['wp_realestate_agency_category_slug']) ) {
				update_option( 'wp_realestate_agency_category_slug', sanitize_title_with_dashes($_POST['wp_realestate_agency_category_slug']) );
			}
			if ( isset($_POST['wp_realestate_agency_location_slug']) ) {
				update_option( 'wp_realestate_agency_location_slug', sanitize_title_with_dashes($_POST['wp_realestate_agency_location_slug']) );
			}
			if ( isset($_POST['wp_realestate_agency_archive_slug']) ) {
				update_option( 'wp_realestate_agency_archive_slug', sanitize_title_with_dashes($_POST['wp_realestate_agency_archive_slug']) );
			}
		}
	}
}

WP_RealEstate_Permalink_Settings::init();
