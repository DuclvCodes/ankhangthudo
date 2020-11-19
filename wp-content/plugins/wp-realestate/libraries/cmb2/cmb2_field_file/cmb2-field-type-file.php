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

class WP_RealEstate_CMB2_Field_File {

	public static function init() {
		add_filter( 'cmb2_render_wp_realestate_file', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wp_realestate_file', array( __CLASS__, 'sanitize_map' ), 10, 4 );	
		add_filter( 'cmb2_types_esc_wp_realestate_file', array( __CLASS__, 'esc_escaped_value' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$files = $field->default() ? $field->default() : $field_escaped_value;
		$ajax = $field->args( 'ajax' ) ? true : false;

		$classes = array('hidden');
		if ( is_user_logged_in() && $ajax ) {
			wp_enqueue_script( 'wp-realestate-ajax-file-upload' );
			$classes[] = 'wp-realestate-file-upload';
		}
		$file_types = !empty($field->args( 'mime_types' )) ? $field->args( 'mime_types' ) : array();
		$allow_mime_types = !empty($field->args( 'allow_mime_types' )) ? $field->args( 'allow_mime_types' ) : array();
		$file_limit = !empty($field->args( 'file_limit' )) ? $field->args( 'file_limit' ) : 10;
		$multiple = $field->args( 'file_multiple' ) ? true : false;
		$current_name = 'current_' . $field->args( '_name' );
		if ( $multiple ) {
			$current_name = 'current_' . $field->args( '_name' ).'[]';
		}
		?>
		<div class="wp-realestate-uploaded-files">
			<?php if ( ! empty( $files ) ) : ?>
				<?php if ( is_array( $files ) ) :?>
					<?php foreach ( $files as $key => $value ) : ?>
						<?php
							echo WP_RealEstate_Template_Loader::get_template_part( 'misc/uploaded-file-html', array( 'input_name' => $current_name, 'value' => $key, 'file_url' => $value, 'field' => $field ) );
						?>
					<?php endforeach; ?>
				<?php elseif ( $value = $files ) : ?>
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'misc/uploaded-file-html', array( 'input_name' => $current_name, 'value' => $value, 'field' => $field ) ); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<input type="file" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-file_types="<?php echo esc_attr( implode( '|', $file_types ) ); ?>" data-mime_types="<?php echo esc_attr( implode( '|', $allow_mime_types ) ); ?>" <?php if ( ! empty( $multiple ) ) echo 'multiple'; ?> name="<?php echo esc_attr( $field->args( '_name' ) ); ?><?php if ( ! empty( $multiple ) ) echo '[]'; ?>" data-file_limit="<?php echo esc_attr($file_limit); ?>"/>

		<div class="label-can-drag">
			<div class="form-group group-upload">
		        <div class="upload-file-btn">
		            <?php if ( $multiple ) { ?>
		            	<span><?php esc_html_e('Upload Files', 'wp-realestate'); ?></span>
	            	<?php } else { ?>
		            	<span><?php esc_html_e('Upload File', 'wp-realestate'); ?></span>
	            	<?php } ?>
		            
		        </div>
		    </div>
		</div>

		<?php
		$field_type_object->_desc( true, true );
	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		
		$return = '';
		if ( !empty($field_args['ajax']) && $field_args['ajax'] ) {
			
			if ( !empty( $_POST['current_'.$field_args['_name']] ) ) {
				$values = $_POST['current_'.$field_args['_name']];

				if ( is_array($values) ) {
					$return = array();
					foreach ($values as $attachment_url) {
						if ( ! empty( $attachment_url ) && is_numeric( $attachment_url ) ) {
							$url = wp_get_attachment_url( $attachment_url );
							$return[$attachment_url] = $url;
						} elseif ( ! empty( $attachment_url ) ) {
							$attach_id = WP_RealEstate_Image::create_attachment($attachment_url, $object_id);
							if ( $attach_id ) {
								$url = wp_get_attachment_url( $attach_id );
								$return[$attach_id] = $url;
							}
						}
					}
				} else {
					if ( is_numeric( $values ) ) {
						$url = wp_get_attachment_url( $values );
						$return = $url;
					} else {
						$attach_id = WP_RealEstate_Image::create_attachment( $values, $object_id );
						$url = wp_get_attachment_url( $attach_id );
						$return = $url;
					}
				}
			}
		} else {
			if ( !empty($_FILES[$field_args['_name']]['name']) ) {
			    $files = $_FILES[$field_args['_name']];

			    foreach ($files['name'] as $key => $value) {            
		            if ($files['name'][$key]) { 
		                $file = array( 
		                    'name' => $files['name'][$key],
		                    'type' => $files['type'][$key], 
		                    'tmp_name' => $files['tmp_name'][$key], 
		                    'error' => $files['error'][$key],
		                    'size' => $files['size'][$key]
		                ); 
		                $_FILES = array( $field_args['_name'] => $file ); 
		                foreach ($_FILES as $file => $array) {              
		                    $attach_id = self::handle_attachment($file, $object_id); 
		                    if ( is_numeric($attach_id) ) {
		                    	$url = wp_get_attachment_url( $attach_id );
		                    	$return[$key] = array($attach_id => $url);
		                    }
		                }
		            } 
		        }
			}
		}
		
		
		update_post_meta($object_id, $field_args['_name'].'_img', $return);
		return $return;
	}

	public static function handle_attachment($file_handler,$post_id,$set_thu=false) {
		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		$attach_id = media_handle_upload( $file_handler, $post_id );
		return $attach_id;
	}

	public static function esc_escaped_value($return, $meta_value, $args, $obj) {
		return $meta_value;
	}
}

WP_RealEstate_CMB2_Field_File::init();