<?php
/**
 * Brand manager for apus framework
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Apus_PostType_Brand {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Apus Brand', 'apus-framework' ),
			'singular_name'         => __( 'Brand', 'apus-framework' ),
			'add_new'               => __( 'Add New Brand', 'apus-framework' ),
			'add_new_item'          => __( 'Add New Brand', 'apus-framework' ),
			'edit_item'             => __( 'Edit Brand', 'apus-framework' ),
			'new_item'              => __( 'New Brand', 'apus-framework' ),
			'all_items'             => __( 'All Brands', 'apus-framework' ),
			'view_item'             => __( 'View Brand', 'apus-framework' ),
			'search_items'          => __( 'Search Brand', 'apus-framework' ),
			'not_found'             => __( 'No Brands found', 'apus-framework' ),
			'not_found_in_trash'    => __( 'No Brands found in Trash', 'apus-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Apus Brands', 'apus-framework' ),
	    );

	    register_post_type( 'apus_brand',
	      	array(
		        'labels'            => apply_filters( 'apus_postype_brand_labels' , $labels ),
		        'supports'          => array( 'title', 'thumbnail' ),
		        'public'            => true,
		        'has_archive'       => true,
		        'menu_position'     => 54
	      	)
	    );

  	}
  	
  	public static function metaboxes(array $metaboxes){
		$prefix = 'apus_brand_';
	    
	    $metaboxes[ $prefix . 'settings' ] = array(
			'id'                        => $prefix . 'settings',
			'title'                     => __( 'Brand Information', 'apus-framework' ),
			'object_types'              => array( 'apus_brand' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_fields()
		);

	    return $metaboxes;
	}

	public static function metaboxes_fields() {
		$prefix = 'apus_brand_';
	
		$fields =  array(
			array(
				'name' => __( 'Brand Link', 'apus-framework' ),
				'id'   => $prefix."link",
				'type' => 'text'
			)
		);  
		
		return apply_filters( 'apus_framework_postype_apus_brand_metaboxes_fields' , $fields );
	}
}

Apus_PostType_Brand::init();