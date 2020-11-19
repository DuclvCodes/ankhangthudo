<?php
/**
 * Testimonial manager for apus framework
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Framework
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Apus_PostType_Testimonial {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Apus Testimonial', 'apus-framework' ),
			'singular_name'         => __( 'Testimonial', 'apus-framework' ),
			'add_new'               => __( 'Add New Testimonial', 'apus-framework' ),
			'add_new_item'          => __( 'Add New Testimonial', 'apus-framework' ),
			'edit_item'             => __( 'Edit Testimonial', 'apus-framework' ),
			'new_item'              => __( 'New Testimonial', 'apus-framework' ),
			'all_items'             => __( 'All Testimonials', 'apus-framework' ),
			'view_item'             => __( 'View Testimonial', 'apus-framework' ),
			'search_items'          => __( 'Search Testimonial', 'apus-framework' ),
			'not_found'             => __( 'No Testimonials found', 'apus-framework' ),
			'not_found_in_trash'    => __( 'No Testimonials found in Trash', 'apus-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Apus Testimonials', 'apus-framework' ),
	    );

	    register_post_type( 'apus_testimonial',
	      	array(
		        'labels'            => apply_filters( 'apus_postype_testimonial_labels' , $labels ),
		        'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		        'public'            => true,
		        'has_archive'       => true,
		        'menu_position'     => 54
	      	)
	    );

  	}
  	
  	public static function metaboxes(array $metaboxes){
		$prefix = 'apus_testimonial_';
	    
	    $metaboxes[ $prefix . 'settings' ] = array(
			'id'                        => $prefix . 'settings',
			'title'                     => __( 'Testimonial Information', 'apus-framework' ),
			'object_types'              => array( 'apus_testimonial' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_fields()
		);

	    return $metaboxes;
	}

	public static function metaboxes_fields() {
		$prefix = 'apus_testimonial_';
	
		$fields =  array(
			array(
	            'name' => __( 'Job', 'apus-framework' ),
	            'id'   => "{$prefix}job",
	            'type' => 'text',
	            'description' => __('Enter Job example CEO, CTO','apus-framework')
          	), 
			array(
				'name' => __( 'Testimonial Link', 'apus-framework' ),
				'id'   => $prefix."link",
				'type' => 'text'
			)
		);  
		
		return apply_filters( 'apus_framework_postype_apus_testimonial_metaboxes_fields' , $fields );
	}
}

Apus_PostType_Testimonial::init();