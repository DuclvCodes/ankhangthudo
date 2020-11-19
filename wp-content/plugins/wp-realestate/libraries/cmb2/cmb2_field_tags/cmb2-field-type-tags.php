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

class WP_RealEstate_CMB2_Field_Tags {

	public static function init() {
		add_filter( 'cmb2_render_wp_realestate_tags', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wp_realestate_tags', array( __CLASS__, 'sanitize_map' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$value = $field->args( 'default' );

		echo $field_type_object->input( array(
			'type'       => 'text',
			'name'       => $field->args( '_name' ),
			'value'      => $value,
		) );
		
	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		
		if ( is_array( $value ) ) {
			$tags = array_map( 'absint', $value );
		} else {
			$raw_tags = array_filter( array_map( 'sanitize_text_field', explode( ',', $value ) ) );
			$tags = array();

			foreach ( $raw_tags as $tag ) {
				$tags[] = self::format_property_amenity( $tag );
			}
		}

		if ( ! empty( $tags ) ) {
			wp_set_object_terms( $object_id, $tags, $field_args['taxonomy'], false );
		}

		return $value;
	}

	public static function format_property_amenity( $tag ) {
		if ( strlen( $tag ) <= 3 ) {
			$tag = strtoupper( $tag );
		} else {
			$tag = strtolower( $tag );
		}
		return $tag;
	}

}

WP_RealEstate_CMB2_Field_Tags::init();