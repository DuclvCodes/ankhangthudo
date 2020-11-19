<?php
/**
 * Shortcodes
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Shortcodes {
	/**
	 * Initialize shortcodes
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
	    add_shortcode( 'wp_private_message_dashboard', array( __CLASS__, 'dashboard' ) );
	}

	public static function dashboard( $atts ) {
		if ( is_user_logged_in() ) {
			$user_id = WP_Private_Message_Mixes::get_current_user_id();
		    return WP_Private_Message_Template_Loader::get_template_part( 'dashboard', array( 'user_id' => $user_id ) );
	    } else {
	    	return WP_Private_Message_Template_Loader::get_template_part( 'not-allowed' );
	    }
	}

	public static function send_message( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_Private_Message_Template_Loader::get_template_part( 'send-message-form');
	    } else {
	    	return WP_Private_Message_Template_Loader::get_template_part( 'not-allowed' );
	    }
	}
}

WP_Private_Message_Shortcodes::init();
