<?php
/**
 * Scripts
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Scripts {
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
		wp_enqueue_style( 'perfect-scrollbar-jquery', WP_PRIVATE_MESSAGE_PLUGIN_URL . 'assets/css/perfect-scrollbar.css' );
		wp_register_script( 'perfect-scrollbar-jquery', WP_PRIVATE_MESSAGE_PLUGIN_URL . 'assets/js/perfect-scrollbar.jquery.min.js', array( 'jquery' ), '0.6.10', true );
		wp_register_script( 'wp-private-message-main', WP_PRIVATE_MESSAGE_PLUGIN_URL . 'assets/js/main.js', array( 'jquery', 'jquery-ui-slider', 'perfect-scrollbar-jquery' ), '20131022', true );
		wp_localize_script( 'wp-private-message-main', 'wp_private_message_opts', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		));
		wp_enqueue_script( 'wp-private-message-main' );
	}

	/**
	 * Loads backend files
	 *
	 * @access public
	 * @return void
	 */
	public static function enqueue_backend() {
		wp_enqueue_style( 'wp-private-message-style-admin', WP_PRIVATE_MESSAGE_PLUGIN_URL . 'assets/css/style-admin.css' );

		wp_enqueue_script( 'wp-private-message-admin-main', WP_PRIVATE_MESSAGE_PLUGIN_URL . 'assets/js/admin-main.js', array( 'jquery' ), '1.0.0', true );
	}

}

WP_Private_Message_Scripts::init();
