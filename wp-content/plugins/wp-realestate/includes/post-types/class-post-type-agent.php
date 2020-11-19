<?php
/**
 * Post Type: Agent
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Post_Type_Agent {

	public static $prefix = WP_REALESTATE_AGENT_PREFIX;

	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );

	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields_front' ) );

	  	add_filter( 'manage_edit-agent_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_agent_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );

		add_action( 'denied_to_publish', array( __CLASS__, 'process_denied_to_publish' ) );
		add_action( 'pending_to_publish', array( __CLASS__, 'process_pending_to_publish' ) );
	}

	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Agents', 'wp-realestate' ),
			'singular_name'         => __( 'Agent', 'wp-realestate' ),
			'add_new'               => __( 'Add New Agent', 'wp-realestate' ),
			'add_new_item'          => __( 'Add New Agent', 'wp-realestate' ),
			'edit_item'             => __( 'Edit Agent', 'wp-realestate' ),
			'new_item'              => __( 'New Agent', 'wp-realestate' ),
			'all_items'             => __( 'All Agents', 'wp-realestate' ),
			'view_item'             => __( 'View Agent', 'wp-realestate' ),
			'search_items'          => __( 'Search Agent', 'wp-realestate' ),
			'not_found'             => __( 'No Agents found', 'wp-realestate' ),
			'not_found_in_trash'    => __( 'No Agents found in Trash', 'wp-realestate' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Agents', 'wp-realestate' ),
		);
		$has_archive = true;
		$agent_archive = get_option('wp_realestate_agent_archive_slug');
		if ( $agent_archive ) {
			$has_archive = $agent_archive;
		}
		$rewrite_slug = get_option('wp_realestate_agent_base_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'agent', 'Agent slug - resave permalinks after changing this', 'wp-realestate' );
		}
		$rewrite = array(
			'slug'       => $rewrite_slug,
			'with_front' => false
		);
		register_post_type( 'agent',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments' ),
				'public'            => true,
				'has_archive'       => $has_archive,
				'rewrite'           => $rewrite,
				'menu_position'     => 51,
				'categories'        => array(),
				'menu_icon'         => 'dashicons-admin-post',
				'show_in_rest'		=> true,
			)
		);
	}

	public static function save_post($post_id, $post) {
		if ( $post->post_type === 'agent' ) {
			$arg = array( 'ID' => $post_id );
			if ( !empty($_POST[self::$prefix . 'featured']) ) {
				$arg['menu_order'] = -1;
			} else {
				$arg['menu_order'] = 0;
			}
			
			remove_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
			wp_update_post( $arg );
			add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );

			clean_post_cache( $post_id );
		}
	}

	public static function process_denied_to_publish($post) {
		if ( $post->post_type === 'agent' ) {
			$user_id = WP_RealEstate_User::get_user_by_agent_id($post->ID);
			remove_action('denied_to_publish', array( __CLASS__, 'process_denied_to_publish' ) );
			do_action( 'wp_realestate_new_user_approve_approve_user', $user_id );
			add_action( 'denied_to_publish', array( __CLASS__, 'process_denied_to_publish' ) );
		}
	}

	public static function process_pending_to_publish($post) {
		if ( $post->post_type === 'agent' ) {
			$user_id = WP_RealEstate_User::get_user_by_agent_id($post->ID);
			remove_action('pending_to_publish', array( __CLASS__, 'process_pending_to_publish' ) );
			do_action( 'wp_realestate_new_user_approve_approve_user', $user_id );
			add_action( 'pending_to_publish', array( __CLASS__, 'process_pending_to_publish' ) );
		}
	}

	/**
	 * Defines custom fields
	 *
	 * @access public
	 * @param array $metaboxes
	 * @return array
	 */
	public static function fields( array $metaboxes ) {
		$metaboxes[ self::$prefix . 'contact_details' ] = array(
			'id'              	=> self::$prefix . 'contact_details',
			'title'           	=> __( 'Contact details', 'wp-realestate' ),
			'object_types'    	=> array( 'agent' ),
			'context'         	=> 'normal',
			'priority'        	=> 'high',
			'show_names'      	=> true,
			'show_in_rest'		=> true,
			'fields'          	=> array(
				array(
					'name'              => __( 'Attached User', 'wp-realestate' ),
					'id'                => self::$prefix . 'attached_user',
					'type'              => 'wp_realestate_attached_user',
				),
				array(
					'name'              => __( 'Featured Agent', 'wp-realestate' ),
					'id'                => self::$prefix . 'featured',
					'type'              => 'checkbox',
					'description'		=> __( 'Featured agents will be sticky during searches, and can be styled differently.', 'wp-realestate' )
				),
				array(
					'id'                => self::$prefix . 'job',
					'name'              => __( 'Job', 'wp-realestate' ),
					'type'              => 'text',
				),
				array(
					'id'                => self::$prefix . 'email',
					'name'              => __( 'E-mail', 'wp-realestate' ),
					'type'              => 'text',
				),
				array(
					'id'                => self::$prefix . 'website',
					'name'              => __( 'Web', 'wp-realestate' ),
					'type'              => 'text',
				),
				array(
					'id'                => self::$prefix . 'phone',
					'name'              => __( 'Phone', 'wp-realestate' ),
					'type'              => 'text',
				),
			),
		);

		$metaboxes[ self::$prefix . 'socials' ] = array(
			'id'                        => self::$prefix . 'socials',
			'title'                     => __( 'Socials', 'wp-realestate' ),
			'object_types'              => array( 'agent' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Socials', 'wp-realestate' ),
					'id'                => self::$prefix . 'socials',
					'type'              => 'group',
					'options'     		=> array(
						'group_title'       => __( 'Network {#}', 'wp-realestate' ),
						'add_button'        => __( 'Add Another Network', 'wp-realestate' ),
						'remove_button'     => __( 'Remove Network', 'wp-realestate' ),
						'sortable'          => false,
						'closed'         => true,
					),
					'fields'			=> array(
						array(
							'name'      => __( 'Network', 'wp-realestate' ),
							'id'        => 'network',
							'type'      => 'select',
							'options'   => WP_RealEstate_Mixes::get_socials_network()
						),
						array(
							'name'      => __( 'Url', 'wp-realestate' ),
							'id'        => 'url',
							'type'      => 'text',
						),
					),
				),
			),
		);

		$metaboxes[ self::$prefix . 'location' ] = array(
			'id'                        => self::$prefix . 'location',
			'title'                     => __( 'Location', 'wp-realestate' ),
			'object_types'              => array( 'agent' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Friendly Address', 'wp-realestate' ),
					'id'                => self::$prefix . 'address',
					'type'              => 'text',
				),
				array(
					'id'                => self::$prefix . 'map_location',
					'name'              => __( 'Location', 'wp-realestate' ),
					'desc'              => __( 'Drag the marker to set the exact location', 'wp-realestate' ),
					'type'              => 'pw_map',
					'sanitization_cb'   => 'pw_map_sanitise',
					'split_values'      => true,
				),
			),
		);

		return $metaboxes;
	}

	public static function fields_front( array $metaboxes ) {
		if ( is_admin() ) {
			return $metaboxes;
		}

		$user_id = get_current_user_id();
		if ( WP_RealEstate_User::is_agent($user_id) ) {
			$post_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
			if ( !empty($post_id) ) {
				$post = get_post( $post_id );
				$featured_image = get_post_thumbnail_id( $post_id );
			}
		}

		$fields = apply_filters( 'wp-realestate-agent-fields-front', array(
				array(
					'id'                => self::$prefix . 'post_type',
					'type'              => 'hidden',
					'default'           => 'agent',
					'priority'           => 0,
				),
				array(
					'name'              => __( 'Profile url', 'wp-realestate' ),
					'id'                => self::$prefix . 'profile_url',
					'type'              => 'wp_realestate_profile_url',
					'priority'           => 1,
				),
				array(
					'name'              => __( 'Featured Image', 'wp-realestate' ),
					'id'                => self::$prefix . 'featured_image',
					'type'              => 'wp_realestate_file',
					'multiple'			=> false,
					'default'           => ! empty( $featured_image ) ? $featured_image : '',
					'ajax'				=> true,
					'mime_types' => array( 'gif', 'jpeg', 'jpg', 'png' ),
					'priority'           => 2,
				),

				array(
					'name'              => __( 'Full Name', 'wp-realestate' ),
					'id'                => self::$prefix . 'title',
					'type'              => 'text',
					'default'           => ! empty( $post ) ? $post->post_title : '',
					'attributes'		=> array(
						'required'			=> 'required'
					),
					'priority'           => 3,
				),
				array(
					'name'              => __( 'Description', 'wp-realestate' ),
					'id'                => self::$prefix . 'description',
					'type'              => 'wysiwyg',
					'default'           => ! empty( $post ) ? $post->post_content : '',
					'priority'           => 4,
					'options' => array(
					    'media_buttons' => false,
					    'textarea_rows' => 8,
					    'tinymce'       => array(
							'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
							'paste_as_text'                 => true,
							'paste_auto_cleanup_on_paste'   => true,
							'paste_remove_spans'            => true,
							'paste_remove_styles'           => true,
							'paste_remove_styles_if_webkit' => true,
							'paste_strip_class_attributes'  => true,
							'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
							'toolbar2'                      => '',
							'toolbar3'                      => '',
							'toolbar4'                      => ''
						),
					    'quicktags' => false
					),
				),

				array(
					'id'                => self::$prefix . 'job',
					'name'              => __( 'Job', 'wp-realestate' ),
					'type'              => 'text',
					'priority'           => 5,
				),
				array(
					'id'                => self::$prefix . 'email',
					'name'              => __( 'E-mail', 'wp-realestate' ),
					'type'              => 'text',
					'priority'           => 6,
				),
				array(
					'id'                => self::$prefix . 'website',
					'name'              => __( 'Web', 'wp-realestate' ),
					'type'              => 'text',
					'priority'           => 7,
				),
				array(
					'id'                => self::$prefix . 'phone',
					'name'              => __( 'Phone', 'wp-realestate' ),
					'type'              => 'text',
					'priority'           => 8,
				),

				array(
					'name'      		=> __( 'Location', 'wp-realestate' ),
					'id'        		=> self::$prefix . 'location',
					'type'      		=> 'wpre_taxonomy_location',
					'taxonomy'  		=> 'agent_location',
					'priority'           => 22,
					'attributes'		=> array(
						'placeholder' 	=> __( 'Select %s', 'wp-realestate' ),
					),
				),
				array(
					'name'              => __( 'Friendly Address', 'wp-realestate' ),
					'id'                => self::$prefix . 'address',
					'type'              => 'text',
					'priority'           => 9,
				),
				array(
					'id'                => self::$prefix . 'map_location',
					'name'              => __( 'Map Location', 'wp-realestate' ),
					'type'              => 'pw_map',
					'sanitization_cb'   => 'pw_map_sanitise',
					'split_values'      => true,
					'priority'           => 10,
				),

				// socials
				array(
					'name'              => __( 'Socials', 'wp-realestate' ),
					'id'                => self::$prefix . 'socials',
					'type'              => 'group',
					'options'     		=> array(
						'group_title'       => __( 'Network {#}', 'wp-realestate' ),
						'add_button'        => __( 'Add Another Network', 'wp-realestate' ),
						'remove_button'     => __( 'Remove Network', 'wp-realestate' ),
						'sortable'          => false,
						'closed'         => true,
					),
					'fields'			=> array(
						array(
							'name'      => __( 'Network', 'wp-realestate' ),
							'id'        => 'network',
							'type'      => 'select',
							'options'   => WP_RealEstate_Mixes::get_socials_network()
						),
						array(
							'name'      => __( 'Url', 'wp-realestate' ),
							'id'        => 'url',
							'type'      => 'text',
						),
					),
					'priority'           => 11,
				),
				
			)
		);

		uasort( $fields, array( 'WP_RealEstate_Mixes', 'sort_array_by_priority') );

		$metaboxes[ self::$prefix . 'fields_front' ] = array(
			'id'                        => self::$prefix . 'fields_front',
			'title'                     => __( 'General Options', 'wp-realestate' ),
			'object_types'              => array( 'agent' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
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
	public static function custom_columns($columns) {
		if ( isset($columns['comments']) ) {
			unset($columns['comments']);
		}
		if ( isset($columns['date']) ) {
			unset($columns['date']);
		}
		$fields = array_merge($columns, array(
			'thumbnail' 		=> __( 'Thumbnail', 'wp-realestate' ),
			'email' 			=> __( 'E-mail', 'wp-realestate' ),
			'website' 			=> __( 'Website', 'wp-realestate' ),
			'phone' 			=> __( 'Phone', 'wp-realestate' ),
			'properties' 		=> __( 'Properties', 'wp-realestate' ),
			'featured' 			=> __( 'Featured', 'wp-realestate' ),
			'author'			=> __( 'Author', 'wp-realestate' ),
			'date' 				=> __( 'Date', 'wp-realestate' ),
		));
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
			case 'thumbnail':
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'thumbnail', array(
						'class' => 'attachment-thumbnail attachment-thumbnail-small ',
					) );
				} else {
					echo '-';
				}
				break;
			case 'email':
				$email = get_post_meta( get_the_ID(), self::$prefix . 'email', true );

				if ( ! empty( $email ) ) {
					echo esc_attr( $email );
				} else {
					echo '-';
				}
				break;
			case 'website':
				$website = get_post_meta( get_the_ID(), self::$prefix  . 'website', true );

				if ( ! empty( $website ) ) {
					echo esc_attr( $website );
				} else {
					echo '-';
				}
				break;
			case 'phone':
				$phone = get_post_meta( get_the_ID(), self::$prefix  . 'phone', true );

				if ( ! empty( $phone ) ) {
					echo esc_attr( $phone );
				} else {
					echo '-';
				}
				break;
			case 'properties':
				$query_args = array(
					'agent_ids' => array(get_the_ID()),
					'fields' => 'ids'
				);
				$properties_count = WP_RealEstate_Query::get_agents_properties( $query_args )->post_count;
				echo esc_attr( $properties_count );
				break;
			case 'featured':
				$featured = get_post_meta( get_the_ID(), self::$prefix . 'featured', true );
				if ( ! empty( $featured ) ) {
					echo '<div class="dashicons dashicons-star-filled"></div>';
				} else {
					echo '<div class="dashicons dashicons-star-empty"></div>';
				}
				break;
		}
	}

}
WP_RealEstate_Post_Type_Agent::init();