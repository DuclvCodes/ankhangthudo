<?php
/**
 * Compare
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Compare {
	
	public static function init() {
        // Ajax endpoints.
        // add_property_compare
        add_action( 'wre_ajax_wp_realestate_ajax_add_property_compare',  array(__CLASS__,'process_add_property_compare') );

        // remove property compare
        add_action( 'wre_ajax_wp_realestate_ajax_remove_property_compare',  array(__CLASS__,'process_remove_property_compare') );

        // remove all property compare
        add_action( 'wre_ajax_wp_realestate_ajax_remove_all_property_compare',  array(__CLASS__,'process_remove_all_property_compare') );

        // compatible handlers.
		// add_property_compare
		add_action( 'wp_ajax_wp_realestate_ajax_add_property_compare',  array(__CLASS__,'process_add_property_compare') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_add_property_compare',  array(__CLASS__,'process_add_property_compare') );

		// remove property compare
		add_action( 'wp_ajax_wp_realestate_ajax_remove_property_compare',  array(__CLASS__,'process_remove_property_compare') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_remove_property_compare',  array(__CLASS__,'process_remove_property_compare') );

        // remove all property compare
        add_action( 'wp_ajax_wp_realestate_ajax_remove_all_property_compare',  array(__CLASS__,'process_remove_all_property_compare') );
        add_action( 'wp_ajax_nopriv_wp_realestate_ajax_remove_all_property_compare',  array(__CLASS__,'process_remove_all_property_compare') );
	}

	public static function compare_fields() {
		return apply_filters( 'wp-realestate-default-property-compare-fields', array() );
	}

	public static function process_add_property_compare() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-add-property-compare-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
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

		do_action('wp-realestate-process-add-property-compare', $_POST);


		$comapre = array();
        if ( isset($_COOKIE['realestate_compare']) ) {
            $compare = explode( ',', $_COOKIE['realestate_compare'] );
            if ( !self::check_added_compare($property_id, $compare) ) {
                $compare[] = $property_id;
            }
        } else {
            $compare = array( $property_id );
        }
		setcookie( 'realestate_compare', implode(',', $compare), time()+3600*24*10, '/' );
        $_COOKIE['realestate_compare'] = implode(',', $compare);


        $return = apply_filters( 'wp-realestate-process-add-property-compare-return', array(
            'status' => true,
            'nonce' => wp_create_nonce( 'wp-realestate-remove-property-compare-nonce' ),
            'msg' => esc_html__('Add compare successfully.', 'wp-realestate'),
        ));
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_remove_property_compare() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-remove-property-compare-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$property_id = !empty($_POST['property_id']) ? $_POST['property_id'] : '';

		if ( empty($property_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Property did not exists.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-realestate-process-remove-property-compare', $_POST);


		$newcomapre = array();
        if ( isset($_COOKIE['realestate_compare']) ) {
            $compare = explode( ',', $_COOKIE['realestate_compare'] );
            foreach ($compare as $key => $value) {
                if ( $property_id != $value ) {
                    unset($comapre[$key]);
                    $newcomapre[] = $value;
                }
            }
        }
        setcookie( 'realestate_compare', implode(',', $newcomapre) , time()+3600*24*10, '/' );
        $_COOKIE['realestate_compare'] = implode(',', $newcomapre);


        $return = apply_filters( 'wp-realestate-process-remove-property-compare-return', array(
            'status' => true,
        	'nonce' => wp_create_nonce( 'wp-realestate-add-property-compare-nonce' ),
        	'msg' => esc_html__('Remove property from compare successfully.', 'wp-realestate'),
        	'count' => count($newcomapre)
        ));
	   	echo wp_json_encode($return);
	   	exit;
	}

    public static function process_remove_all_property_compare() {
        $return = array();
        if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-remove-property-compare-nonce' ) ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
            echo wp_json_encode($return);
            exit;
        }


        do_action('wp-realestate-process-remove-property-compare', $_POST);


        $comapre = array();
        if ( isset($_COOKIE['realestate_compare']) ) {
            setcookie( 'realestate_compare', implode(',', $compare) , -100, '/' );
        }


        $return = apply_filters( 'wp-realestate-process-remove-all-property-compare-return', array(
            'status' => true,
            'nonce' => wp_create_nonce( 'wp-realestate-add-property-compare-nonce' ),
            'msg' => esc_html__('Remove all properties from compare successfully.', 'wp-realestate'),
            'count' => 0
        ));
        echo wp_json_encode($return);
        exit;
    }

	public static function get_compare_items() {
        if ( isset($_COOKIE['realestate_compare']) && !empty($_COOKIE['realestate_compare']) ) {
            return explode( ',', $_COOKIE['realestate_compare'] );
        }
        return array();
    }

	public static function check_added_compare($property_id) {
		if ( empty($property_id) ) {
			return false;
		}

		if ( empty($compares) && isset($_COOKIE['realestate_compare']) && !empty($_COOKIE['realestate_compare']) ) {
            $compares = explode( ',', $_COOKIE['realestate_compare'] );
            if ( in_array($property_id, $compares) ) {
	            return true;
	        }
        }
        return false;
	}

	public static function display_compare_btn($property_id, $args = array()) {
        $args = wp_parse_args( $args, array(
            'show_icon' => true,
            'show_text' => false,
            'echo' => true,
            'tooltip' => true,
            'added_classes' => 'btn-added-property-compare',
            'added_text' => esc_html__('Remove Compare', 'wp-realestate'),
            'added_tooltip_title' => esc_html__('Remove Compare', 'wp-realestate'),
            'added_icon_class' => 'flaticon-repeat',
            'add_classes' => 'btn-add-property-compare',
            'add_text' => esc_html__('Add Compare', 'wp-realestate'),
            'add_icon_class' => 'flaticon-repeat',
            'add_tooltip_title' => esc_html__('Add Compare', 'wp-realestate'),
        ));

		if ( self::check_added_compare($property_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-realestate-remove-property-compare-nonce' );
			$text = $args['added_text'];
            $icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-realestate-add-property-compare-nonce' );
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

	public static function get_data($key, $post_id, $field) {
		$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post_id);
		
        switch ($key) {
            case 'title':
                $value = '<h3><a href="'.esc_url(get_permalink($post_id)).'">'.get_the_title($post_id).'</a></h3>';
                break;
            case 'description':
                $value = get_post_field('post_content', $post_id);
                break;
            case 'baths':
            case 'beds':
            case 'garages':
            case 'id':
            case 'rooms':
            case 'year_built':
                $value = $obj_property_meta->get_post_meta( $key );
                break;
            // taxonomy
            case 'amenity':
                $terms = get_the_terms($post_id, 'property_amenity');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            case 'type':
                $terms = get_the_terms($post_id, 'property_type');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            case 'status':
                $terms = get_the_terms($post_id, 'property_status');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            case 'material':
                $terms = get_the_terms($post_id, 'property_material');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            case 'location':
                $terms = get_the_terms($post_id, 'property_location');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            case 'label':
                $terms = get_the_terms($post_id, 'property_label');
                $value = '';
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                    foreach ($terms as $term) {
                        $value .= ', ' . '<a href="'.esc_url(get_term_link($term)).'">'.$term->name.'</a>';
                    }
                }
                $value = ltrim($value, ', ');
                break;
            // <>
            case 'home_area':
            case 'lot_area':
                $value = $obj_property_meta->get_post_meta( $key );
                $value = $value.' '. wp_realestate_get_option( 'measurement_unit_area', 'sqft' );
                break;
            case 'price':
                $value = $obj_property_meta->get_price_html();
                break;
            // yes/no
            case 'featured':
                $value = $obj_property_meta->get_post_meta( $key );
                break;
            default:
                if ( !empty($field['custom_field_type']) && $field['custom_field_type'] == 'custom_field' ) {
                    $value = $obj_property_meta->get_custom_post_meta( $key );
                } else {
                    $value = $obj_property_meta->get_post_meta( $key );
                }
                break;
        }
        return apply_filters('wp-realestate-compare-field-value', $value, $key, $post_id);
    }
}

WP_RealEstate_Compare::init();