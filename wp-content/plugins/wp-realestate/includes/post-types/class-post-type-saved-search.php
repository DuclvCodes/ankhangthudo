<?php
/**
 * Post Type: Saved Search
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Post_Type_Saved_Search {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

	  	add_filter( 'manage_edit-saved_search_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_saved_search_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Saved Searches', 'wp-realestate' ),
			'singular_name'         => __( 'Saved Search', 'wp-realestate' ),
			'add_new'               => __( 'Add New Saved Search', 'wp-realestate' ),
			'add_new_item'          => __( 'Add New Saved Search', 'wp-realestate' ),
			'edit_item'             => __( 'Edit Saved Search', 'wp-realestate' ),
			'new_item'              => __( 'New Saved Search', 'wp-realestate' ),
			'all_items'             => __( 'Saved Searches', 'wp-realestate' ),
			'view_item'             => __( 'View Saved Search', 'wp-realestate' ),
			'search_items'          => __( 'Search Saved Search', 'wp-realestate' ),
			'not_found'             => __( 'No Saved Searches found', 'wp-realestate' ),
			'not_found_in_trash'    => __( 'No Saved Searches found in Trash', 'wp-realestate' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Saved Searches', 'wp-realestate' ),
		);

		register_post_type( 'saved_search',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_in_menu'		=> 'edit.php?post_type=property',
			)
		);
	}

	/**
	 * Defines custom fields
	 *
	 * @access public
	 * @param array $metaboxes
	 * @return array
	 */
	public static function fields( array $metaboxes ) {
		$email_frequency_default = WP_RealEstate_Saved_Search::get_email_frequency();
		$email_frequency = array();
		if ( $email_frequency_default && is_admin() ) {
			foreach ($email_frequency_default as $key => $value) {
				if ( !empty($value['label']) && !empty($value['days']) ) {
					$email_frequency[$key] = $value['label'];
				}
			}
		}
		$fields = array();
		if ( isset($_GET['post']) && $_GET['post'] && is_admin() ) {
			$post = get_post($_GET['post']);
			if ( $post && $post->post_type == 'saved_search' ) {
				$author_name = get_the_author_meta('display_name', $post->post_author);
				$author_email = get_the_author_meta('user_email', $post->post_author);
				$fields[] = array(
					'name' => sprintf( __('Author: %s (%s)', 'wp-realestate'), $author_name, $author_email ),
					'type' => 'title',
					'id'   => WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'author'
				);
			}
		}
		$fields[] = array(
			'name'              => __( 'Saved Search Query', 'wp-realestate' ),
			'id'                => WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'saved_search_query',
			'type'              => 'textarea',
		);
		$fields[] = array(
			'name'              => __( 'Email Frequency', 'wp-realestate' ),
			'id'                => WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'email_frequency',
			'type'              => 'select',
			'options'			=> $email_frequency
		);
		$metaboxes[ WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'general' ] = array(
			'id'                        => WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-realestate' ),
			'object_types'              => array( 'saved_search' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => $fields
		);
		return $metaboxes;
	}
	/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns() {
		$fields = array(
			'cb' 				=> '<input type="checkbox" />',
			'title' 			=> esc_html__( 'Title', 'wp-realestate' ),
			'email_frequency' 	=> esc_html__( 'Email Frequency', 'wp-realestate' ),
			'date' 				=> esc_html__( 'Date', 'wp-realestate' ),
			'author' 			=> esc_html__( 'Author', 'wp-realestate' ),
		);
		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		switch ( $column ) {
			case 'email_frequency':
					$email_frequency = get_post_meta( get_the_ID(), WP_REALESTATE_PROPERTY_SAVED_SEARCH_PREFIX . 'email_frequency', true );
					echo wp_kses_post($email_frequency);
				break;
		}
	}

}
WP_RealEstate_Post_Type_Saved_Search::init();