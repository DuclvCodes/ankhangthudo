<?php
/**
 * Template Loader
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
class WP_Private_Message_Template_Loader {
	
	/**
	 * Gets template path
	 *
	 * @access public
	 * @param $name
	 * @param $plugin_dir
	 * @return string
	 * @throws Exception
	 */
	public static function locate( $name, $plugin_dir = WP_PRIVATE_MESSAGE_PLUGIN_DIR ) {
		$template = '';

		$theme_folder_name = apply_filters( 'wp-private-message-theme-folder-name', 'wp-private-message' );
		// Current theme base dir
		if ( ! empty( $name ) ) {
			$template = locate_template( array("{$name}.php") );
		}

		// Child theme
		if ( ! $template && ! empty( $name ) && file_exists( get_stylesheet_directory() . "/".$theme_folder_name."/{$name}.php" ) ) {
			$template = get_stylesheet_directory() . "/".$theme_folder_name."/{$name}.php";
		}

		// Original theme
		if ( ! $template && ! empty( $name ) && file_exists( get_template_directory() . "/".$theme_folder_name."/{$name}.php" ) ) {
			$template = get_template_directory() . "/".$theme_folder_name."/{$name}.php";
		}

		// Plugin
		if ( ! $template && ! empty( $name ) && file_exists( $plugin_dir . "templates/{$name}.php" ) ) {
			$template = $plugin_dir . "/templates/{$name}.php";
		}

		// Nothing found
		if ( empty( $template ) ) {
			throw new Exception( "Template /templates/{$name}.php in plugin dir {$plugin_dir} not found." );
		}

		return $template;
	}

	
	/**
	 * Loads template content
	 *
	 * @param string $name
	 * @param array  $args
	 * @param string $plugin_dir
	 * @return string
	 * @throws Exception
	 */
	public static function get_template_part( $name, $args = array(), $plugin_dir = WP_PRIVATE_MESSAGE_PLUGIN_DIR ) {
		if ( is_array( $args ) && count( $args ) > 0 ) {
			extract( $args, EXTR_SKIP );
		}

		$path = self::locate( $name, $plugin_dir );
		ob_start();
		if ( $path ) {
			include $path;
		}
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}
