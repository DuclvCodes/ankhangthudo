<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$input_name = isset($input_name) ? $input_name : '';
?>
<div class="wp-realestate-uploaded-file">
	<?php
	if ( !isset($file_url) ) {
		if ( is_numeric( $value ) ) {
			$image_src = wp_get_attachment_image_src( absint( $value ) );
			$file_url = $image_src ? $image_src[0] : '';
		} else {
			$file_url = $value;
			$value = WP_RealEstate_Image::get_attachment_id_from_url($value);
		}
	}
	$extension = ! empty( $extension ) ? $extension : substr( strrchr( $file_url, '.' ), 1 );
	if ( 'image' === wp_ext2type( $extension ) ) : ?>
		<span class="wp-realestate-uploaded-file-preview"><img src="<?php echo esc_url( $file_url ); ?>" /> <a class="wp-realestate-remove-uploaded-file" href="#">[<?php _e( 'remove', 'wp-realestate' ); ?>]</a></span>
	<?php else : ?>
		<span class="wp-realestate-uploaded-file-name"><code><?php echo esc_html( basename( $file_url ) ); ?></code> <a class="wp-realestate-remove-uploaded-file" href="#">[<?php _e( 'remove', 'wp-realestate' ); ?>]</a></span>
	<?php endif; ?>

	<input type="hidden" class="input-text" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
	
</div>