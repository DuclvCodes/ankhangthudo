<?php
/**
 * Favorite
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Favorite {
	
	public static function init() {
		// Ajax endpoints.
		// add_property_favorite
		add_action( 'wre_ajax_wp_realestate_ajax_add_property_favorite',  array(__CLASS__,'process_add_property_favorite') );

		// remove property favorite
		add_action( 'wre_ajax_wp_realestate_ajax_remove_property_favorite',  array(__CLASS__,'process_remove_property_favorite') );


		// compatible handlers.
		// add_property_favorite
		add_action( 'wp_ajax_wp_realestate_ajax_add_property_favorite',  array(__CLASS__,'process_add_property_favorite') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_add_property_favorite',  array(__CLASS__,'process_add_property_favorite') );

		// remove property favorite
		add_action( 'wp_ajax_wp_realestate_ajax_remove_property_favorite',  array(__CLASS__,'process_remove_property_favorite') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_remove_property_favorite',  array(__CLASS__,'process_remove_property_favorite') );
	}

	public static function process_add_property_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-add-property-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$property_id = !empty($_POST['property_id']) ? $_POST['property_id'] : '';
		$post = get_post($property_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Property did not exists.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-realestate-process-add-property-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_property', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($property_id, $favorite) ) {
				$favorite[] = $property_id;
			}
		} else {
			$favorite = array( $property_id );
		}
		$result = update_user_meta( $user_id, '_favorite_property', $favorite );

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-realestate-remove-property-favorite-nonce' ), 'msg' => esc_html__('Add favorite successfully.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_property_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-remove-property-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$property_id = !empty($_POST['property_id']) ? $_POST['property_id'] : '';

		if ( empty($property_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Property did not exists.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-realestate-process-remove-property-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_property', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($property_id, $favorite) ) {
				$key = array_search( $property_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_property', $favorite );
			}
		}

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-realestate-add-property-favorite-nonce' ), 'msg' => esc_html__('Remove property from favorite successfully.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove property from favorite error.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_added_favorite($property_id) {
		if ( empty($property_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_property', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($property_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_property_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_property', true);
        return $data;
    }

	public static function display_favorite_btn($property_id, $args = array()) {
		$args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-property-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-realestate'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-realestate'),
			'added_icon_class' => 'flaticon-heart',
			'add_classes' => 'btn-add-property-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-realestate'),
			'add_icon_class' => 'flaticon-heart',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-realestate'),
		));

		if ( self::check_added_favorite($property_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-realestate-remove-property-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-realestate-add-property-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-property_id="<?php echo esc_attr($property_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}
}
WP_RealEstate_Favorite::init();