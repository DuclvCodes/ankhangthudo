<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
if ( !empty($post->post_type) && ($post->post_type == 'agent' || $post->post_type == 'agency' || $post->post_type == 'property' ) ) {
	$user_id = $post->post_author;
	if ( $post->post_type == 'agency' ) {
		$author_name = $post->post_title;
	} elseif ( $post->post_type == 'agent' ) {
		$author_name = $post->post_title;
	} else {
		if ( WP_RealEstate_User::is_agency($user_id) ) {
			$agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);

			$author_name = get_the_title($agency_id);
		} elseif ( WP_RealEstate_User::is_agent($user_id) ) {
			$agent_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
			
			$author_name = get_the_title($agent_id);
		} else {
			$author_name = get_the_author_meta('display_name');
		}
	}

	extract( $args );
	extract( $instance );
	$title = !empty($instance['title']) ? sprintf($instance['title'], $author_name) : '';
	$title = apply_filters('widget_title', $title);

	if ( $title ) {
	    echo trim($before_title)  . trim( $title ) . $after_title;
	}
	?>
		<div class="contact-form-agent">
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
	<?php
}

?>