<?php
/**
 * CMB2 File
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_CMB2_Field_Image_Select {

	public static function init() {
		add_filter( 'cmb2_render_wp_realestate_image_select', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wp_realestate_image_select', array( __CLASS__, 'sanitize_map' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		self::setup_admin_scripts();
		$value = !empty($field_escaped_value) ? $field_escaped_value : $field->default();
		$options = $field->args( 'options' );
		if ( $options ) {
			?>
			<ul class="image-select-wrapper">
				<?php
				$i = 1;
				foreach ($options as $key => $opt) {
					?>
					<li class="image-select-item">
						<label class="<?php echo esc_attr($value == $key ? 'selected' : ''); ?>" for="<?php echo esc_attr($field->args( 'id' ).'-'.$i);?>">
							<input id="<?php echo esc_attr($field->args( 'id' ).'-'.$i);?>" type="radio" name="<?php echo esc_attr($field->args( '_name' )); ?>" value="<?php echo esc_attr($key); ?>" <?php checked($value, $key); ?> class="hidden">
							<?php if ( !empty($opt['img']) ) { ?>
								<img src="<?php echo esc_url($opt['img']); ?>" alt="<?php echo esc_attr(!empty($opt['alt']) ? $opt['alt'] : ''); ?>">
							<?php } ?>
							<?php if ( !empty($opt['title']) ) { ?>
								<h5 class="title"><?php echo esc_html($opt['title']); ?></h5>
							<?php } ?>
						</label>
					</li>
					<?php
					$i++;
				}
				?>
			</ul>
			<?php
		}
	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		
		return $value;
	}

	public static function setup_admin_scripts() {
		wp_enqueue_script( 'image-select-script', plugins_url( 'js/script.js', __FILE__ ), array(), '1.0' );
		wp_enqueue_style( 'image-select-style', plugins_url( 'css/style.css', __FILE__ ), array(), '1.0' );
	}
}

WP_RealEstate_CMB2_Field_Image_Select::init();