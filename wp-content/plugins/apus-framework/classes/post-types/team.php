<?php
/**
 * mentor post type
 *
 * @package    apus-framework
 * @author     ApusTheme <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  13/06/2016 ApusTheme
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Apus_PostType_Team{

	/**
	 * init action and filter data to define resource post type
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ) );
		add_action( 'init', array( __CLASS__, 'definition_taxonomy' ) );
		add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'metaboxes' ) );
	}
	/**
	 *
	 */
	public static function definition() {
		
		$labels = array(
			'name'                  => __( 'Apus Teams', 'apus-framework' ),
			'singular_name'         => __( 'Team', 'apus-framework' ),
			'add_new'               => __( 'Add New Team', 'apus-framework' ),
			'add_new_item'          => __( 'Add New Team', 'apus-framework' ),
			'edit_item'             => __( 'Edit Team', 'apus-framework' ),
			'new_item'              => __( 'New Team', 'apus-framework' ),
			'all_items'             => __( 'All Teams', 'apus-framework' ),
			'view_item'             => __( 'View Team', 'apus-framework' ),
			'search_items'          => __( 'Search Team', 'apus-framework' ),
			'not_found'             => __( 'No Teams found', 'apus-framework' ),
			'not_found_in_trash'    => __( 'No Teams found in Trash', 'apus-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Apus Teams', 'apus-framework' ),
		);

		$labels = apply_filters( 'apus_framework_postype_mentor_labels' , $labels );

		register_post_type( 'apus_team',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'thumbnail' ),
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => array( 'slug' => __( 'mentor', 'apus-framework' ) ),
				'menu_position'     => 54,
				'categories'        => array(),
				'show_in_menu'  	=> true,
			)
		);
	}

	public static function definition_taxonomy() {
		$labels = array(
			'name'              => __( 'Team Categories', 'apus-framework' ),
			'singular_name'     => __( 'Team Category', 'apus-framework' ),
			'search_items'      => __( 'Search Team Categories', 'apus-framework' ),
			'all_items'         => __( 'All Team Categories', 'apus-framework' ),
			'parent_item'       => __( 'Parent Team Category', 'apus-framework' ),
			'parent_item_colon' => __( 'Parent Team Category:', 'apus-framework' ),
			'edit_item'         => __( 'Edit Team Category', 'apus-framework' ),
			'update_item'       => __( 'Update Team Category', 'apus-framework' ),
			'add_new_item'      => __( 'Add New Team Category', 'apus-framework' ),
			'new_item_name'     => __( 'New Team Category', 'apus-framework' ),
			'menu_name'         => __( 'Team Categories', 'apus-framework' ),
		);

		register_taxonomy( 'apus_team_category', 'apus_team', array(
			'labels'            => apply_filters( 'apus_framework_taxomony_team_category_labels', $labels ),
			'hierarchical'      => true,
			'query_var'         => 'team-category',
			'rewrite'           => array( 'slug' => __( 'team-category', 'apus-framework' ) ),
			'public'            => true,
			'show_ui'           => true,
		) );
	}

	/**
	 *
	 */
	public static function metaboxes( array $metaboxes ) {
		$prefix = 'apus_team_';
		
		$metaboxes[ $prefix . 'info' ] = array(
			'id'                        => $prefix . 'info',
			'title'                     => __( 'More Informations', 'apus-framework' ),
			'object_types'              => array( 'apus_team' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_info_fields()
		);
		
		return $metaboxes;
	}

	public static function metaboxes_info_fields() {
		$prefix = 'apus_team_';
		$fields = array(
			array(
				'name'              => __( 'Job', 'apus-framework' ),
				'id'                => $prefix . 'job',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Facebook', 'apus-framework' ),
				'id'                => $prefix . 'facebook',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Twitter', 'apus-framework' ),
				'id'                => $prefix . 'twitter',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Behance', 'apus-framework' ),
				'id'                => $prefix . 'behance',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Linkedin', 'apus-framework' ),
				'id'                => $prefix . 'linkedin',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Instagram', 'apus-framework' ),
				'id'                => $prefix . 'instagram',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Google Plus', 'apus-framework' ),
				'id'                => $prefix . 'google_plus',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Pinterest', 'apus-framework' ),
				'id'                => $prefix . 'pinterest',
				'type'              => 'text'
			),
		);

		return apply_filters( 'apus_framework_postype_apus_team_metaboxes_fields' , $fields );
	}

}

Apus_PostType_Team::init();