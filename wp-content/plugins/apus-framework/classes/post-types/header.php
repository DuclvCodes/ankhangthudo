<?php
/**
 * Header manager for apus framework
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Apus_PostType_Header {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Header Builder', 'apus-framework' ),
			'singular_name'         => __( 'Header', 'apus-framework' ),
			'add_new'               => __( 'Add New Header', 'apus-framework' ),
			'add_new_item'          => __( 'Add New Header', 'apus-framework' ),
			'edit_item'             => __( 'Edit Header', 'apus-framework' ),
			'new_item'              => __( 'New Header', 'apus-framework' ),
			'all_items'             => __( 'All Headers', 'apus-framework' ),
			'view_item'             => __( 'View Header', 'apus-framework' ),
			'search_items'          => __( 'Search Header', 'apus-framework' ),
			'not_found'             => __( 'No Headers found', 'apus-framework' ),
			'not_found_in_trash'    => __( 'No Headers found in Trash', 'apus-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Headers Builder', 'apus-framework' ),
	    );

	    register_post_type( 'apus_header',
	      	array(
		        'labels'            => apply_filters( 'apus_postype_header_labels' , $labels ),
		        'supports'          => array( 'title', 'editor' ),
		        'public'            => true,
		        'has_archive'       => false,
		        'show_in_nav_menus' => false,
		        'menu_position'     => 51,
		        'menu_icon'         => 'dashicons-admin-post',
	      	)
	    );

  	}
  
}

Apus_PostType_Header::init();