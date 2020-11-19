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
class Apus_PostType_Portfolio{

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
			'name'                  => __( 'Apus Portfolios', 'apus-framework' ),
			'singular_name'         => __( 'Portfolio', 'apus-framework' ),
			'add_new'               => __( 'Add New Portfolio', 'apus-framework' ),
			'add_new_item'          => __( 'Add New Portfolio', 'apus-framework' ),
			'edit_item'             => __( 'Edit Portfolio', 'apus-framework' ),
			'new_item'              => __( 'New Portfolio', 'apus-framework' ),
			'all_items'             => __( 'All Portfolios', 'apus-framework' ),
			'view_item'             => __( 'View Portfolio', 'apus-framework' ),
			'search_items'          => __( 'Search Portfolio', 'apus-framework' ),
			'not_found'             => __( 'No Portfolios found', 'apus-framework' ),
			'not_found_in_trash'    => __( 'No Portfolios found in Trash', 'apus-framework' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Apus Portfolios', 'apus-framework' ),
		);

		$labels = apply_filters( 'apus_framework_postype_mentor_labels' , $labels );

		register_post_type( 'apus_portfolio',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'public'            => true,
				'has_archive'       => true,
				'rewrite'           => array( 'slug' => __( 'portfolio', 'apus-framework' ) ),
				'menu_position'     => 54,
				'categories'        => array(),
				'show_in_menu'  	=> true,
			)
		);
	}

	public static function definition_taxonomy() {
		$labels = array(
			'name'              => __( 'Portfolio Categories', 'apus-framework' ),
			'singular_name'     => __( 'Portfolio Category', 'apus-framework' ),
			'search_items'      => __( 'Search Portfolio Categories', 'apus-framework' ),
			'all_items'         => __( 'All Portfolio Categories', 'apus-framework' ),
			'parent_item'       => __( 'Parent Portfolio Category', 'apus-framework' ),
			'parent_item_colon' => __( 'Parent Portfolio Category:', 'apus-framework' ),
			'edit_item'         => __( 'Edit Portfolio Category', 'apus-framework' ),
			'update_item'       => __( 'Update Portfolio Category', 'apus-framework' ),
			'add_new_item'      => __( 'Add New Portfolio Category', 'apus-framework' ),
			'new_item_name'     => __( 'New Portfolio Category', 'apus-framework' ),
			'menu_name'         => __( 'Portfolio Categories', 'apus-framework' ),
		);

		register_taxonomy( 'apus_portfolio_category', 'apus_portfolio', array(
			'labels'            => apply_filters( 'apus_framework_taxomony_portfolio_category_labels', $labels ),
			'hierarchical'      => true,
			'query_var'         => 'portfolio-category',
			'rewrite'           => array( 'slug' => __( 'portfolio-category', 'apus-framework' ) ),
			'public'            => true,
			'show_ui'           => true,
		) );
	}

	/**
	 *
	 */
	public static function metaboxes( array $metaboxes ) {
		$prefix = 'apus_portfolio_';
		
		$metaboxes[ $prefix . 'info' ] = array(
			'id'                        => $prefix . 'info',
			'title'                     => __( 'More Informations', 'apus-framework' ),
			'object_types'              => array( 'apus_portfolio' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_info_fields()
		);
		
		$metaboxes[ $prefix . 'social' ] = array(
			'id'                        => $prefix . 'social',
			'title'                     => __( 'Social Informations', 'apus-framework' ),
			'object_types'              => array( 'apus_portfolio' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_social_fields()
		);

		$metaboxes[ $prefix . 'team' ] = array(
			'id'                        => $prefix . 'team',
			'title'                     => __( 'Team Informations', 'apus-framework' ),
			'object_types'              => array( 'apus_portfolio' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => self::metaboxes_team_fields()
		);

		return $metaboxes;
	}

	public static function metaboxes_info_fields() {
		$prefix = 'apus_portfolio_';
		$fields = array(
			array(
				'name'              => __( 'Client', 'apus-framework' ),
				'id'                => $prefix . 'client',
				'type'              => 'text'
			),
			array(
				'name'              => __( 'Date', 'apus-framework' ),
				'id'                => $prefix . 'date',
				'type'              => 'text_date'
			),
			array(
				'name'              => __( 'Service', 'apus-framework' ),
				'id'                => $prefix . 'service',
				'type'              => 'textarea'
			),
			array(
				'name' => __( 'Gallery', 'apus-framework' ),
				'id'   => $prefix . 'gallery',
				'type' => 'file_list',
				'query_args' => array( 'type' => 'image' ),
			)
		);

		return apply_filters( 'apus_framework_postype_apus_portfolio_metaboxes_fields' , $fields );
	}

	public static function metaboxes_social_fields() {
		$prefix = 'apus_portfolio_';
		$fields = array(
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

		return apply_filters( 'apus_framework_postype_apus_portfolio_metaboxes_social_fields' , $fields );
	}

	public static function metaboxes_team_fields() {
		$prefix = 'apus_portfolio_';
		$fields =  array(
			array(
				'id'                => $prefix . 'teams',
				'type'              => 'group',
				'options'     => array(
					'group_title'   => esc_html__( 'Member {#}', 'apus-framework' ),
					'add_button'    => esc_html__( 'Add Another Member', 'apus-framework' ),
					'remove_button' => esc_html__( 'Remove Member', 'apus-framework' ),
					'sortable'      => true,
				),
				'fields'            => array(
					array(
						'id'                => 'value',
						'name'              => esc_html__( 'Name', 'apus-framework' ),
						'type'              => 'text',
					),
					array(
						'id'                => 'text',
						'name'              => esc_html__( 'Job', 'apus-framework' ),
						'type'              => 'text',
					),
				),
			),
		);  

		return apply_filters( 'apus_framework_postype_apus_portfolio_metaboxes_team_fields' , $fields );
	}
}

Apus_PostType_Portfolio::init();