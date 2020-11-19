<?php
/**
 * Post Type: Private Message
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Post_Type_Private_Message {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	
	  	add_filter( 'manage_edit-messages_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_messages_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Messages', 'wp-private-message' ),
			'singular_name'         => __( 'Message', 'wp-private-message' ),
			'add_new'               => __( 'Add New Message', 'wp-private-message' ),
			'add_new_item'          => __( 'Add New Message', 'wp-private-message' ),
			'edit_item'             => __( 'Edit Message', 'wp-private-message' ),
			'new_item'              => __( 'New Message', 'wp-private-message' ),
			'all_items'             => __( 'All Messages', 'wp-private-message' ),
			'view_item'             => __( 'View Message', 'wp-private-message' ),
			'search_items'          => __( 'Search Message', 'wp-private-message' ),
			'not_found'             => __( 'No Messages found', 'wp-private-message' ),
			'not_found_in_trash'    => __( 'No Messages found in Trash', 'wp-private-message' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Messages', 'wp-private-message' ),
		);
		
		register_post_type( 'private_message',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'author' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'menu_icon'         => 'dashicons-megaphone',
				'show_in_rest'		=> false,
			)
		);
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
			'title' 			=> __( 'Title', 'wp-private-message' ),
			'sender' 			=> __( 'Sender', 'wp-private-message' ),
			'recipients' 		=> __( 'Recipients', 'wp-private-message' ),
			'sent' 				=> __( 'Sent', 'wp-private-message' ),
			'message_status' 	=> __( 'Status', 'wp-private-message' ),
			'parent' 			=> __( 'Parent', 'wp-private-message' ),
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
		global $post;
		switch ( $column ) {
			case 'sender':
				echo ( empty( $post->post_author ) ? esc_html__( '-', 'wp-private-message' ) : sprintf( esc_html__( '%s', 'wp-private-message' ), '<a href="' . esc_url( add_query_arg( 'author', $post->post_author ) ) . '">' . esc_html( get_the_author() ) . '</a>' ) ) . '</span>';
				break;
			case 'recipients':
				
				break;
			
			case 'sent':
				echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</strong><span>';
				break;
			
			case 'message_status':
				$status   = $post->post_status;
				$statuses = WP_Private_Message_Message_Listing::message_statuses();

				$status_text = $status;
				if ( !empty($statuses[$status]) ) {
					$status_text = $statuses[$status];
				}
				echo sprintf( '<a href="?post_type=messages&post_status=%s">%s</a>', esc_attr( $post->post_status ), '<span class="status-' . esc_attr( $post->post_status ) . '">' . esc_html( $status_text ) . '</span>' );
				break;
			case 'parent':
				echo $post->parent;
				break;
		}
	}

}
WP_Private_Message_Post_Type_Private_Message::init();


