<?php
/**
 * Categories
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WP_RealEstate_Taxonomy_Agent_Category{

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 1 );
	}

	/**
	 *
	 */
	public static function definition() {
		$labels = array(
			'name'              => __( 'Categories', 'wp-realestate' ),
			'singular_name'     => __( 'Category', 'wp-realestate' ),
			'search_items'      => __( 'Search Categories', 'wp-realestate' ),
			'all_items'         => __( 'All Categories', 'wp-realestate' ),
			'parent_item'       => __( 'Parent Category', 'wp-realestate' ),
			'parent_item_colon' => __( 'Parent Category:', 'wp-realestate' ),
			'edit_item'         => __( 'Edit', 'wp-realestate' ),
			'update_item'       => __( 'Update', 'wp-realestate' ),
			'add_new_item'      => __( 'Add New', 'wp-realestate' ),
			'new_item_name'     => __( 'New Category', 'wp-realestate' ),
			'menu_name'         => __( 'Categories', 'wp-realestate' ),
		);

		$rewrite_slug = get_option('wp_realestate_agent_category_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'agent-category', 'Agent category slug - resave permalinks after changing this', 'wp-realestate' );
		}
		$rewrite = array(
			'slug'         => $rewrite_slug,
			'with_front'   => false,
			'hierarchical' => false,
		);
		register_taxonomy( 'agent_category', 'agent', array(
			'labels'            => apply_filters( 'wp_realestate_taxomony_agent_category_labels', $labels ),
			'hierarchical'      => true,
			'rewrite'           => $rewrite,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'		=> true
		) );
	}

}

WP_RealEstate_Taxonomy_Agent_Category::init();