<?php
/**
 * Post Type: Property Listing
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Post_Type_Property {
	public static $prefix = WP_REALESTATE_PROPERTY_PREFIX;
	public static function init() {
		add_filter('use_block_editor_for_post_type', array( __CLASS__, 'disable_gutenberg' ), 10, 2);
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_action( 'admin_menu', array( __CLASS__, 'add_pending_count_to_menu' ) );
	  	//add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	add_filter( 'cmb2_admin_init', array( __CLASS__, 'metaboxes' ) );
  		

	  	add_filter( 'manage_edit-property_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_property_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'filter_property_by_type' ) );
		add_action( 'parse_query', array( __CLASS__, 'filter_property_by_type_in_query' ) );

		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );

		add_action( 'pending_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'pending_payment_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'preview_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'draft_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'auto-draft_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'expired_to_publish', array( __CLASS__, 'set_expiry_date' ) );

		add_action( 'wp_realestate_check_for_expired_properties', array('WP_RealEstate_Property', 'check_for_expired_properties') );
		add_action( 'wp_realestate_delete_old_previews', array('WP_RealEstate_Property', 'delete_old_previews') );

		add_action( 'wp_realestate_email_daily_notices', array( 'WP_RealEstate_Property', 'send_admin_expiring_notice' ) );
		add_action( 'wp_realestate_email_daily_notices', array( 'WP_RealEstate_Property', 'send_agent_expiring_notice' ) );
		add_action( 'template_redirect', array( 'WP_RealEstate_Property', 'track_property_view' ), 20 );

		add_action( "cmb2_save_field_".self::$prefix."expiry_date", array( __CLASS__, 'save_expiry_date' ), 10, 3 );

		// Ajax endpoints.
		add_action( 'wre_ajax_wp_realestate_ajax_remove_property',  array(__CLASS__,'process_remove_property') );
		
		// compatible handlers.
		add_action( 'wp_ajax_wp_realestate_ajax_remove_property',  array(__CLASS__,'process_remove_property') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_remove_property',  array(__CLASS__,'process_remove_property') );
	}

	public static function disable_gutenberg($current_status, $post_type) {
	    if ($post_type === 'property') {
	    	return false;
	    }
	    return $current_status;
	}

	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Properties', 'wp-realestate' ),
			'singular_name'         => __( 'Property', 'wp-realestate' ),
			'add_new'               => __( 'Add New Property', 'wp-realestate' ),
			'add_new_item'          => __( 'Add New Property', 'wp-realestate' ),
			'edit_item'             => __( 'Edit Property', 'wp-realestate' ),
			'new_item'              => __( 'New Property', 'wp-realestate' ),
			'all_items'             => __( 'All Properties', 'wp-realestate' ),
			'view_item'             => __( 'View Property', 'wp-realestate' ),
			'search_items'          => __( 'Search Property', 'wp-realestate' ),
			'not_found'             => __( 'No Properties found', 'wp-realestate' ),
			'not_found_in_trash'    => __( 'No Properties found in Trash', 'wp-realestate' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Properties', 'wp-realestate' ),
		);
		$has_archive = true;
		$property_archive = get_option('wp_realestate_property_archive_slug');
		if ( $property_archive ) {
			$has_archive = $property_archive;
		}
		$property_rewrite_slug = get_option('wp_realestate_property_base_slug');
		if ( empty($property_rewrite_slug) ) {
			$property_rewrite_slug = _x( 'property', 'Property slug - resave permalinks after changing this', 'wp-realestate' );
		}
		$rewrite = array(
			'slug'       => $property_rewrite_slug,
			'with_front' => false
		);
		register_post_type( 'property',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments' ),
				'public'            => true,
				'has_archive'       => $has_archive,
				'rewrite'           => $rewrite,
				'menu_position'     => 51,
				'categories'        => array(),
				'menu_icon'         => 'dashicons-admin-home',
				'show_in_rest'		=> true,
			)
		);
	}

	/**
	 * Adds pending count to WP admin menu label
	 *
	 * @access public
	 * @return void
	 */
	public static function add_pending_count_to_menu() {
		global $menu;
		$menu_item_index = null;

		foreach( $menu as $index => $menu_item ) {
			if ( ! empty( $menu_item[5] ) && $menu_item[5] == 'menu-posts-property' ) {
				$menu_item_index = $index;
				break;
			}
		}

		if ( $menu_item_index ) {
			$pending = wp_count_posts( 'property' )->pending;
			$count = $pending;

			if ( $count > 0 ) {
				$menu_title = $menu[ $menu_item_index ][0];
				$menu_title = sprintf('%s <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $menu_title, $count );
				$menu[ $menu_item_index ][0] = $menu_title;
			}
		}
	}

	public static function save_expiry_date($updated, $action, $obj) {
		if ( $action != 'disabled' ) {
			$key = self::$prefix.'expiry_date';
			$data_to_save = $obj->data_to_save;
			$post_id = !empty($data_to_save['post_ID']) ? $data_to_save['post_ID'] : '';
			$expiry_date = isset($data_to_save[$key]) ? $data_to_save[$key] : '';
			if ( empty( $expiry_date ) ) {
				if ( wp_realestate_get_option( 'submission_duration' ) ) {
					$expires = WP_RealEstate_Property::calculate_property_expiry( $post_id );
					update_post_meta( $post_id, $key, $expires );
				} else {
					delete_post_meta( $post_id, $key );
				}
			} else {
				update_post_meta( $post_id, self::$prefix.'expiry_date', date( 'Y-m-d', strtotime( sanitize_text_field( $expiry_date ) ) ) );
			}

		}
	}

	public static function save_post($post_id, $post) {
		if ( $post->post_type === 'property' ) {
			$post_args = array();
			if ( !empty($_POST[self::$prefix . 'posted_by']) ) {
				$post_args['post_author'] = $_POST[self::$prefix . 'posted_by'];
			}

			if ( !empty($_POST[self::$prefix . 'featured']) ) {
				$post_args['menu_order'] = -1;
			} else {
				$post_args['menu_order'] = 0;
			}

			$expiry_date = get_post_meta( $post_id, self::$prefix.'expiry_date', true );
			$today_date = date( 'Y-m-d', current_time( 'timestamp' ) );
			$is_property_expired = $expiry_date && $today_date > $expiry_date;

			if ( $is_property_expired && ! WP_RealEstate_Property::is_property_status_changing( null, 'draft' ) ) {

				if ( !empty($_POST) ) {
					if ( WP_RealEstate_Property::is_property_status_changing( 'expired', 'publish' ) ) {
						if ( empty($_POST[self::$prefix.'expiry_date']) || strtotime( $_POST[self::$prefix.'expiry_date'] ) < current_time( 'timestamp' ) ) {
							$expires = WP_RealEstate_Property::calculate_property_expiry( $post_id );
							update_post_meta( $post_id, self::$prefix.'expiry_date', WP_RealEstate_Property::calculate_property_expiry( $post_id ) );
							if ( isset( $_POST[self::$prefix.'expiry_date'] ) ) {
								$_POST[self::$prefix.'expiry_date'] = $expires;
							}
						}
					} else {
						$post_args['post_status'] = 'expired';
					}
				}
			}
			if ( !empty($post_args) ) {
				$post_args['ID'] = $post_id;

				remove_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
				wp_update_post( $post_args );
				add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
			}

			delete_transient( 'wp_realestate_filter_counts' );
			
			clean_post_cache( $post_id );
		}
	}

	public static function set_expiry_date( $post ) {

		if ( $post->post_type === 'property' ) {

			// See if it is already set.
			if ( metadata_exists( 'post', $post->ID, self::$prefix.'expiry_date' ) ) {
				$expires = get_post_meta( $post->ID, self::$prefix.'expiry_date', true );

				// if ( $expires && strtotime( $expires ) < current_time( 'timestamp' ) ) {
				// 	update_post_meta( $post->ID, self::$prefix.'expiry_date', '' );
				// }
			}

			// See if the user has set the expiry manually.
			if ( ! empty( $_POST[self::$prefix.'expiry_date'] ) ) {
				update_post_meta( $post->ID, self::$prefix.'expiry_date', date( 'Y-m-d', strtotime( sanitize_text_field( $_POST[self::$prefix.'expiry_date'] ) ) ) );
			} elseif ( ! isset( $expires ) ) {
				// No manual setting? Lets generate a date if there isn't already one.
				$expires = WP_RealEstate_Property::calculate_property_expiry( $post->ID );
				update_post_meta( $post->ID, self::$prefix.'expiry_date', $expires );

				// In case we are saving a post, ensure post data is updated so the field is not overridden.
				if ( isset( $_POST[self::$prefix.'expiry_date'] ) ) {
					$_POST[self::$prefix.'expiry_date'] = $expires;
				}
			}
		}
	}

	public static function submission_validate( $data ) {
		$error = array();
		if ( empty($data['post_author']) ) {
			$error[] = array( 'danger', __( 'Please login to submit property', 'wp-realestate' ) );
		}
		if ( empty($data['post_title']) ) {
			$error[] = array( 'danger', __( 'Title is required.', 'wp-realestate' ) );
		}
		if ( empty($data['post_content']) ) {
			$error[] = array( 'danger', __( 'Description is required.', 'wp-realestate' ) );
		}
		return $error;
	}

	public static function process_remove_property() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-delete-property-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to remove this property', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$property_id = empty( $_POST['property_id'] ) ? false : intval( $_POST['property_id'] );
		if ( !$property_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Property not found', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$is_allowed = WP_RealEstate_Mixes::is_allowed_to_remove( get_current_user_id(), $property_id );

		if ( ! $is_allowed ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this property.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action( 'wp-realestate-process-remove-before-save', $property_id );

		if ( wp_delete_post( $property_id ) ) {
			$return = array( 'status' => true, 'msg' => esc_html__('Property has been successfully removed.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('An error occured when removing an item.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function metaboxes() {
		global $pagenow;
		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {
			
			do_action('wp-realestate-property-fields-admin');

			//return WP_RealEstate_Custom_Fields::meta_admin_custom_fields();
		}
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
		if ( isset($columns['author']) ) {
			unset($columns['author']);
		}
		$c_fields = array();
		foreach ($columns as $key => $column) {
			if ( $key == 'title' ) {
				$c_fields['thumbnail'] = __( 'Thumbnail', 'wp-realestate' );
			}
			$c_fields[$key] = $column;
		}
		$fields = array_merge($c_fields, array(
			'posted' 			=> __( 'Posted', 'wp-realestate' ),
			'expires' 			=> __( 'Expires', 'wp-realestate' ),
			'price' 			=> __( 'Price', 'wp-realestate' ),
			'type' 				=> __( 'Type', 'wp-realestate' ),
			'location' 			=> __( 'Location', 'wp-realestate' ),
			'status' 			=> __( 'Status', 'wp-realestate' ),
			'featured' 			=> __( 'Featured', 'wp-realestate' ),
			'property_status' 	=> __( 'Status', 'wp-realestate' ),
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
		global $post;
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
			case 'type':
				$terms = get_the_terms( get_the_ID(), 'property_type' );
				if ( is_array( $terms ) ) {
					$property_type = array_shift( $terms );
					$color_value = get_term_meta( $property_type->term_id, '_color', true );
					$style = '';
					if ( $color_value ) {
						$style = 'style="background-color: '.$color_value.'; color: #fff;"';
					}
					echo sprintf( '<a href="?post_type=property&property_type=%s" class="property-type-bg" '.$style.'>%s</a>', $property_type->slug, $property_type->name );
				} else {
					echo '-';
				}
				break;
			case 'price':
				$obj = WP_RealEstate_Property_Meta::get_instance($post->ID);
				echo $obj->get_price_html();
				break;
			case 'location':
				$terms = get_the_terms( get_the_ID(), 'property_location' );
				if ( ! empty( $terms ) ) {
					$location = array_shift( $terms );
					echo sprintf( '<a href="?post_type=property&property_location=%s">%s</a>', $location->slug, $location->name );
				} else {
					echo '-';
				}
				break;
			case 'status':
				$terms = get_the_terms( get_the_ID(), 'property_status' );
				if ( ! empty( $terms ) ) {
					$status = array_shift( $terms );
					echo sprintf( '<a href="?post_type=property&property_status=%s">%s</a>', $status->slug, $status->name );
				} else {
					echo '-';
				}
				break;
			case 'posted':
				echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</strong><span><br>';
				echo ( empty( $post->post_author ) ? esc_html__( 'by a guest', 'wp-realestate' ) : sprintf( esc_html__( 'by %s', 'wp-realestate' ), '<a href="' . esc_url( add_query_arg( 'author', $post->post_author ) ) . '">' . esc_html( get_the_author() ) . '</a>' ) ) . '</span>';
				break;
			case 'expires':
				$expires = get_post_meta( $post->ID, self::$prefix.'expiry_date', true);
				if ( $expires ) {
					echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) ) . '</strong>';
				} else {
					echo '&ndash;';
				}
				break;
			case 'featured':
				$featured = get_post_meta( get_the_ID(), self::$prefix . 'featured', true );

				if ( ! empty( $featured ) ) {
					echo '<div class="dashicons dashicons-star-filled"></div>';
				} else {
					echo '<div class="dashicons dashicons-star-empty"></div>';
				}
				break;
			case 'property_status':

				$post_status = get_post_status_object( $post->post_status );
				if ( !empty($post_status->label) ) {
					$status_text = $post_status->label;
				} else {
					$status_text = $post->post_status;
				}

				echo sprintf( '<a href="?post_type=property&post_status=%s" class="post-status %s">%s</a>', esc_attr( $post->post_status ), esc_attr( $post->post_status ), '<span class="status-' . esc_attr( $post->post_status ) . '">' . esc_html( $status_text ) . '</span>' );
				break;
		}
	}

	public static function filter_property_by_type() {
		global $typenow;
		if ($typenow == 'property') {
			$selected = isset($_GET['property_type']) ? $_GET['property_type'] : '';
			$terms = get_terms( 'property_type', array('hide_empty' => false,) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				?>
				<select name="property_type">
					<option value=""><?php esc_html_e('All types', 'wp-realestate'); ?></option>
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->slug); ?>" <?php echo trim($term->slug == $selected ? ' selected="selected"' : '') ; ?>><?php echo esc_html($term->name); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
			// locations
			$selected = isset($_GET['property_location']) ? $_GET['property_location'] : '';
			$terms = get_terms( 'property_location', array('hide_empty' => false,) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				?>
				<select name="property_location">
					<option value=""><?php esc_html_e('All locations', 'wp-realestate'); ?></option>
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->slug); ?>" <?php echo trim($term->slug == $selected ? ' selected="selected"' : '') ; ?>><?php echo esc_html($term->name); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
			// statuses
			$selected = isset($_GET['property_status']) ? $_GET['property_status'] : '';
			$terms = get_terms( 'property_status', array('hide_empty' => false,) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				?>
				<select name="property_status">
					<option value=""><?php esc_html_e('All statuses', 'wp-realestate'); ?></option>
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->slug); ?>" <?php echo trim($term->slug == $selected ? ' selected="selected"' : '') ; ?>><?php echo esc_html($term->name); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
		}
	}

	public static function filter_property_by_type_in_query($query) {
		global $pagenow;

		$post_author = isset($_GET['post_author']) ? $_GET['post_author'] : '';
		$q_vars    = &$query->query_vars;

		if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == 'property' ) {
			if ( !empty($post_author) ) {
				$q_vars['author'] = $post_author;
			}
		}
		
	}

	/**
	 * Returns user properties
	 *
	 * @access public
	 * @param $user_id
	 * @param $status
	 * @param $parent_property_id
	 * @return array
	 */
	public static function get_properties( $user_id = null, $status = 'any', $parent_property_id = null ) {
		$args = array(
			'post_type'     	=> 'property',
			'post_status'   	=> $status,
			'posts_per_page' 	=> -1,
		);

		if ( $user_id ) {
			$args['author'] = $user_id;
		}

		if ( !empty($_REQUEST['search']) ) {
			$args['s'] = $_REQUEST['search'];
		}
		if ( $parent_property_id ) {
			$args['meta_key'] = self::$prefix . 'parent_property';
			$args['meta_value'] = $parent_property_id;
		}

		$query = new WP_Query( $args );

		return $query->get_posts();
	}
}
WP_RealEstate_Post_Type_Property::init();


