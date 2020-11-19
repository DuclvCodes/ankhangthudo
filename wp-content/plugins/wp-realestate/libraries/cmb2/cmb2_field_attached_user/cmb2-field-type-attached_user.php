<?php
/**
 * CMB2 File
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_CMB2_Field_Attached_User {

	public static function init() {
		add_filter( 'cmb2_render_wp_realestate_attached_user', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wp_realestate_attached_user', array( __CLASS__, 'sanitize_map' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		if ( $field_object_id ) {
			if ( get_post_type($field_object_id) == 'agent' ) {
				$prefix = WP_REALESTATE_AGENT_PREFIX;
			} elseif ( get_post_type($field_object_id) == 'agency' ) {
				$prefix = WP_REALESTATE_AGENCY_PREFIX;
			}
			if ( !empty($prefix) ) {
				$user_id = get_post_meta( $field_object_id, $prefix.'user_id', true );
				$display_name = get_post_meta( $field_object_id, $prefix.'display_name', true );
				$email = get_post_meta( $field_object_id, $prefix.'email', true );
				if ( $user_id ) {
					$html = '<div><strong>'.$display_name.'</strong></div>';
					$html .= __('User email: ', 'wp-realestate').$email;
				}
				if ( !empty($html) ) {
					echo wp_kses_post($html);
				}
			}
		}

	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

}

WP_RealEstate_CMB2_Field_Attached_User::init();