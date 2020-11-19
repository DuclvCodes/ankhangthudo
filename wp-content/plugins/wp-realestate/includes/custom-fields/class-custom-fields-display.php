<?php
/**
 * template loader
 *
 * @package    wp-realestate
 * @author     Habq
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_RealEstate_Custom_Fields_Display {
	
	public static function init() {
		add_action('init', array(__CLASS__, 'hooks'));
	}

	public static function hooks() {
		$hooks = WP_RealEstate_Fields_Manager::get_display_hooks();
        
		foreach ($hooks as $hook => $title) {
			if ( !empty($hook) ) {
				add_action( $hook, function($post) use ( $hook ) {
					self::display_hook($post, $hook);
				}, 100 );
			}
		}
	}

	public static function display_hook($post, $current_hook) {
		$custom_fields = WP_RealEstate_Fields_Manager::get_custom_fields_data();

		if (is_array($custom_fields) && sizeof($custom_fields) > 0) {
			foreach ($custom_fields as $key => $custom_field) {
				$hook_display = !empty($custom_field['hook_display']) ? $custom_field['hook_display'] : '';
				if ( !empty($hook_display) && $hook_display == $current_hook ) {
					echo self::display_field_data($custom_field, $post, $current_hook);
				}
			}
		}
	}

	public static function display_field_data($custom_field, $post, $current_hook) {
		$field_type = !empty($custom_field['type']) ? $custom_field['type'] : '';
		$field_id = !empty($custom_field['id']) ? $custom_field['id'] : '';
		$field_name = !empty($custom_field['name']) ? $custom_field['name'] : '';
		$value = get_post_meta( $post->ID, $field_id, true );
        if ( empty($value) ) {
            return;
        }
		$output_value = '';

		switch ( $field_type ) {
            case 'text':
            case 'textarea':
            case 'wp-editor':
            case 'number':
            case 'url':
            case 'email':
            case 'select':
            case 'radio':
                $output_value = $value;
                break;
            case 'date':
            	$output_value = strtotime($value);
            	$output_value = date(get_option('date_format'), $output_value);
                break;
            case 'checkbox':
            	$output_value = $value ? esc_html__('Yes', 'wp-realestate') : esc_html__('No', 'wp-realestate');
            	break;
            case 'multiselect':
                if ( is_array($value) ) {
                	$output_value = implode(', ', $value);
                }
                break;
            case 'file':
                $return = '';
                if ( is_array($value) ) {
                	foreach ($value as $file) {
                		if ( self::check_image_mime_type($file) ) {
                			$return .= '<img src="'.esc_url($file).'">';
                		} else {
                			$return .= '<a href="'.esc_url($file).'">'.esc_html__('Download file', 'wp-realestate').'</a>';
                		}
                	}
                } elseif ( !empty($value) ) {
                	if ( self::check_image_mime_type($value) ) {
            			$return .= '<img src="'.esc_url($value).'">';
            		} else {
            			$return .= '<a href="'.esc_url($value).'">'.esc_html__('Download file', 'wp-realestate').'</a>';
            		}
                }
                $output_value = $return;
            break;
        }
        ob_start();
        if ( $current_hook === 'wp-realestate-single-property-details' ) {
            ?>
            <li>
                <?php if ( $field_name ) { ?>
                    <div class="text"><?php echo trim($field_name); ?>:</div>
                <?php } ?>
                <div class="value"><?php echo trim($output_value); ?></div>
            </li>
            <?php
        } else {
            ?>
            <div class="custom-field-data">
            	<?php if ( $field_name ) { ?>
    	        	<h5><?php echo trim($field_name); ?></h5>
    	        <?php } ?>
    	        <div class="content"><?php echo trim($output_value); ?></div>
            </div>
            <?php
        }
        $html = ob_get_clean();
        return apply_filters( 'wp_realestate_display_field_data', $html, $custom_field, $post, $field_name, $output_value, $current_hook );
	}

	public static function check_image_mime_type($image_path) {
		$filetype = strtolower(substr(strstr($image_path, '.'), 1));
	    $mimes  = array( "gif", "jpg", "png", "ico");

	    if ( in_array($filetype, $mimes) ) {
	        return true;
	    } else {
	        return false;
	    }
	}
}

WP_RealEstate_Custom_Fields_Display::init();