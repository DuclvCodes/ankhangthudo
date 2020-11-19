<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
if ( !empty($post->post_type) && ($post->post_type == 'agent' || $post->post_type == 'agency') ) {

	if ( $post->post_type == 'agent' ) {
		$author_email = WP_RealEstate_Agent::get_post_meta( $post->ID, 'email' );
	} else {
		$author_email = WP_RealEstate_Agency::get_post_meta( $post->ID, 'email' );
	}

	if ( ! empty( $author_email ) ) :
		extract( $args );
		extract( $instance );
		$title = !empty($instance['title']) ? sprintf($instance['title'], $post->post_title) : '';
		$title = apply_filters('widget_title', $title);

		if ( $title ) {
		    echo trim($before_title)  . trim( $title ) . $after_title;
		}

		$email = $phone = '';
		if ( is_user_logged_in() ) {
			$current_user_id = get_current_user_id();
			$userdata = get_userdata( $current_user_id );
			$email = $userdata->user_email;
		}
	?>	

		<div class="contact-form-agent">
		    <form method="post" action="?" class="contact-form-wrapper">
		    	<div class="row">
			        <div class="col-sm-12">
				        <div class="form-group">
				            <input type="text" class="form-control" name="name" placeholder="<?php esc_attr_e( 'Name', 'homeo' ); ?>" required="required">
				        </div><!-- /.form-group -->
				    </div>
				    <div class="col-sm-12">
				        <div class="form-group">
				            <input type="email" class="form-control" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'homeo' ); ?>" required="required" value="<?php echo esc_attr($email); ?>">
				        </div><!-- /.form-group -->
				    </div>
				    <div class="col-sm-12">
				        <div class="form-group">
				            <input type="text" class="form-control style2" name="phone" placeholder="<?php esc_attr_e( 'Phone', 'homeo' ); ?>" required="required" value="<?php echo esc_attr($phone); ?>">
				        </div><!-- /.form-group -->
				    </div>
		        </div>
		        <div class="form-group">
		            <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'homeo' ); ?>" required="required"></textarea>
		        </div><!-- /.form-group -->

		        <?php if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) { ?>
		            <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_realestate_get_option( 'recaptcha_site_key' )); ?>"></div>
		      	<?php } ?>

		      	<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
		        <button class="button btn btn-theme-second btn-block" name="contact-form"><?php echo esc_html__( 'Send Message', 'homeo' ); ?></button>
		    </form>
		    <?php //do_action('homeo_after_contact_form', $post); ?>
		</div>
	<?php endif;
}

?>