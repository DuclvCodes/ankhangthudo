<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="submission-form-wrapper">
	<?php
		do_action( 'wp_realestate_property_submit_done_content_after', sanitize_title( $property->post_status ), $property );

		switch ( $property->post_status ) :
			case 'publish' :
				echo wp_kses_post(sprintf(__( 'Property listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-realestate' ), get_permalink( $property->ID ) ));
			break;
			case 'pending' :
				echo wp_kses_post(sprintf(esc_html__( 'Property submitted successfully. Your listing will be visible once approved.', 'wp-realestate' ), get_permalink( $property->ID )));
			break;
			default :
				do_action( 'wp_realestate_property_submit_done_content_' . str_replace( '-', '_', sanitize_title( $property->post_status ) ), $property );
			break;
		endswitch;

		do_action( 'wp_realestate_property_submit_done_content_after', sanitize_title( $property->post_status ), $property );
	?>
</div>
