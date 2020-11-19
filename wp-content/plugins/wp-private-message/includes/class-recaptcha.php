<?php
/**
 * Recaptcha
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Private_Message_Recaptcha {
	
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function enqueue() {

		if ( self::is_recaptcha_enabled() ) {
			wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js?render=explicit', array( 'jquery' ), false, true );
		}
	}

	public static function validate_fields( $return ) {
		if ( self::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? self::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				return new WP_Error( 'validation-error', esc_html__( 'reCAPTCHA is a required field', 'procity' ) );
			}
		}
		return $return;
	}
	/**
	 * Checks if reCAPTCHA is enabled
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_recaptcha_enabled() {
		$site_key = wp_private_message_get_option( 'recaptcha_site_key' );
		$secret_key = wp_private_message_get_option( 'recaptcha_secret_key' );

		if ( ! empty( $site_key ) && ! empty( $secret_key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if reCAPTCHA is valid
	 *
	 * @access public
	 * @param $recaptcha_response string
	 * @return bool
	 */
	public static function is_recaptcha_valid( $recaptcha_response ) {
		$response = wp_remote_get(
			add_query_arg(
				array(
					'secret'   => wp_private_message_get_option( 'recaptcha_secret_key' ),
					'response' => $recaptcha_response
				),
				'https://www.google.com/recaptcha/api/siteverify'
			)
		);
		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			return false;
		}

		$json = json_decode( $response['body'] );
		if ( ! $json || ! $json->success ) {
			return false;
		}

		return true;
	}

}

