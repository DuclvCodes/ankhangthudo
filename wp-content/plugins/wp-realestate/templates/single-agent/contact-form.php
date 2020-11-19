<?php
global $post;
// get our custom meta

$author_email = WP_RealEstate_Agent::get_post_meta( $post->ID, 'email', true );
if ( empty($author_email) && !empty($post->post_author) ) {
	$userdata = get_userdata( $post->post_author );
	$author_email = $userdata->user_email;
}
$email = $phone = '';
if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$userdata = get_userdata( $user_id );
	$email = $userdata->user_email;
}
?>

<div class="contact-form widget">
	<h2 class="widget-title">
		<span><?php echo sprintf( esc_html__('Contact %s', 'wp-realestate'), get_the_title() ); ?></span>
	</h2>
	
	<?php if ( ! empty( $author_email ) ) : ?>
	    <form method="post" action="?" class="contact-form-wrapper">
	    	<div class="row">
		        <div class="col-sm-12">
			        <div class="form-group">
			            <input type="text" class="form-control style2" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'wp-realestate' ); ?>" required="required">
			        </div><!-- /.form-group -->
			    </div>
			    <div class="col-sm-12">
			        <div class="form-group">
			            <input type="email" class="form-control style2" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'wp-realestate' ); ?>" required="required" value="<?php echo esc_attr($email); ?>">
			        </div><!-- /.form-group -->
			    </div>
			    <div class="col-sm-12">
			        <div class="form-group">
			            <input type="text" class="form-control style2" name="phone" placeholder="<?php esc_attr_e( 'Phone', 'wp-realestate' ); ?>" required="required" value="<?php echo esc_attr($phone); ?>">
			        </div><!-- /.form-group -->
			    </div>
	        </div>
	        <div class="form-group space-30">
	            <textarea class="form-control style2" name="message" placeholder="<?php esc_attr_e( 'Message', 'wp-realestate' ); ?>" required="required"></textarea>
	        </div><!-- /.form-group -->

	        <?php do_action('wp-realestate-send-contact'); ?>

	        <?php if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) { ?>
                <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_realestate_get_option( 'recaptcha_site_key' )); ?>"></div>
          	<?php } ?>
          	
          	<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
	        <button class="button btn btn-theme btn-block" name="contact-form"><?php echo esc_html__( 'Send Message', 'wp-realestate' ); ?></button>
	    </form>
	<?php endif; ?>
</div>
