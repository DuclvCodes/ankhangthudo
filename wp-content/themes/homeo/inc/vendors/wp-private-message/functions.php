<?php

remove_action( 'widgets_init', array( WP_Private_Message::getInstance(), 'register_widgets' ) );

//add_action( 'homeo_after_contact_form', 'homeo_private_message_form', 10, 2 );
function homeo_private_message_form($post, $user_id) {
	?>
	<div class="send-private-wrapper">
		<a href="javascript:void(0);" class="send-private-message-btn"><i class="fa fa-hand-o-right" aria-hidden="true"></i><?php esc_html_e('Send Private Message', 'homeo'); ?></a>
	</div>
	<div class="send-private-message-wrapper-hidden hidden">
		<div class="send-private-message-wrapper">
			<h3 class="title"><?php echo sprintf(esc_html__('Send message to "%s"', 'homeo'), $post->post_title); ?></h3>
			<?php
			if ( is_user_logged_in() ) {
				?>
				<form id="send-message-form" class="send-message-form" action="?" method="post">
	                <div class="form-group">
	                    <input type="text" class="form-control style2" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'homeo' ); ?>" required="required">
	                </div><!-- /.form-group -->
	                <div class="form-group">
	                    <textarea class="form-control message style2" name="message" placeholder="<?php esc_attr_e( 'Enter text here...', 'homeo' ); ?>" required="required"></textarea>
	                </div><!-- /.form-group -->

	                <?php wp_nonce_field( 'wp-private-message-send-message', 'wp-private-message-send-message-nonce' ); ?>
	              	<input type="hidden" name="recipient" value="<?php echo esc_attr($user_id); ?>">
	              	<input type="hidden" name="action" value="wp_private_message_send_message">
	                <button class="button btn btn-theme btn-block send-message-btn"><?php echo esc_html__( 'Send Message', 'homeo' ); ?></button>
	        	</form>
				<?php
			} else {
				$login_url = '';
				if ( function_exists('wp_realestate_get_option') ) {
					$login_register_page_id = wp_realestate_get_option('login_register_page_id');
					$login_url = get_permalink( $login_register_page_id );
				}
				?>
				<a href="<?php echo esc_url($login_url); ?>" class="login"><?php esc_html_e('Please login to send a private message', 'homeo'); ?></a>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}

function homeo_private_message_user_avatar($user_id) {
	if ( class_exists('WP_RealEstate_User') && (WP_RealEstate_User::is_agency($user_id) || WP_RealEstate_User::is_agent($user_id)) ) {
	    if ( WP_RealEstate_User::is_agency($user_id) ) {
	        $agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
	        $post_thumbnail_id = get_post_thumbnail_id($agency_id);
            $avatar = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
	    } else {
	        $agent_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
	        $post_thumbnail_id = get_post_thumbnail_id($agent_id);
            $avatar = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
	    }
	}

	if ( !empty($avatar)) {
        echo trim($avatar);
    } else {
        echo get_avatar($user_id, 54);
    }
}