<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
if ( !empty($post->post_type) && $post->post_type == 'property' ) {
	$author_id = $post->post_author;
	$avatar = $a_phone = '';
	if ( WP_RealEstate_User::is_agency($author_id) ) {
		$agency_id = WP_RealEstate_User::get_agency_by_user_id($author_id);
		$agency_post = get_post($agency_id);
		$author_email = homeo_agency_display_email($agency_post, 'no-title', false);
		
		$avatar = '';
		ob_start();
		homeo_agency_display_image($agency_post);
		$avatar = ob_get_clean();

		$a_title = get_the_title($agency_id);
		$a_title_html = '<a href="'.get_permalink($agency_id).'">'.get_the_title($agency_id).'</a>';
		$a_phone = homeo_agency_display_phone($agency_post, 'no-title', false);
	} elseif ( WP_RealEstate_User::is_agent($author_id) ) {
		$agent_id = WP_RealEstate_User::get_agent_by_user_id($author_id);
		$agent_post = get_post($agent_id);
		$author_email = homeo_agent_display_email($agent_post, 'no-title', false);

		$avatar = '';
		ob_start();
		homeo_agent_display_image($agent_post);
		$avatar = ob_get_clean();

		$a_title = get_the_title($agent_id);
		$a_title_html = '<a href="'.get_permalink($agent_id).'">'.get_the_title($agent_id).'</a>';
		$a_phone = homeo_agent_display_phone($agent_post, 'no-title', false);
	} else {
		$user_id = $post->post_author;
		$author_email = get_the_author_meta('user_email');
		$a_title = $a_title_html = get_the_author_meta('display_name');
		$a_phone = get_user_meta($user_id, '_phone', true);
		$a_phone = homeo_user_display_phone($a_phone, 'no-title', false);
	}

	if ( ! empty( $author_email ) ) :
		extract( $args );
		extract( $instance );
		$title = !empty($instance['title']) ? sprintf($instance['title'], $a_title) : '';
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
			<div class="agent-content-wrapper flex-middle">
				<div class="agent-thumbnail">
					<?php if ( !empty($avatar) ) {
						echo trim($avatar);
					} else {
				        echo get_avatar($post->post_author, 180);
					} ?>
				</div>
				<div class="agent-content">
					<h3><?php echo trim($a_title_html); ?></h3>
					<div class="phone"><?php echo trim($a_phone); ?></div>
					<div class="email"><?php echo trim($author_email); ?></div>
				</div>
			</div>
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
		        <button class="button btn btn-theme btn-block" name="contact-form"><?php echo esc_html__( 'Send Message', 'homeo' ); ?></button>
		    </form>
		    <?php //do_action('homeo_after_contact_form', $post); ?>
		</div>
	<?php endif;
}