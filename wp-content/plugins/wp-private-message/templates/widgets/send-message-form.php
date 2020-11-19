<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $apus_author;
if ( empty($apus_author) ) {
	return false;
}

if ( !empty($args['before_widget']) ) {
	echo wp_kses_post( $args['before_widget'] );
}
?>
<div class="send-message-form-wrapper">
    <?php 
        if ( ! empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] );
            echo esc_attr( $instance['title'] );
            echo wp_kses_post( $args['after_title'] );
        }
        if ( is_user_logged_in() ) {
    ?>
        	<form id="send-message-form" class="send-message-form" action="?" method="post">
                <div class="form-group">
                    <input type="text" class="form-control style2" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'wp-private-message' ); ?>" required="required">
                </div><!-- /.form-group -->
                <div class="form-group space-30">
                    <textarea class="form-control message style2" name="message" placeholder="<?php esc_attr_e( 'Message', 'wp-private-message' ); ?>" required="required"></textarea>
                </div><!-- /.form-group -->

                <?php wp_nonce_field( 'wp-private-message-send-message', 'wp-private-message-send-message-nonce' ); ?>
              	<input type="hidden" name="recipient" value="<?php echo esc_attr($apus_author->ID); ?>">
              	<input type="hidden" name="action" value="wp_private_message_send_message">
                <button class="button btn btn-theme btn-block send-message-btn"><?php echo esc_html__( 'Send Message', 'wp-private-message' ); ?></button>
        	</form>
    <?php } else { ?>
        <a href="javascript:void(0);" class="login-form-popup-message"><?php esc_html_e('Login to send a private message', 'wp-private-message'); ?></a>
    <?php } ?>
</div>

<?php
if ( !empty($args['after_widget']) ) {
	echo wp_kses_post( $args['after_widget'] );
}
?>