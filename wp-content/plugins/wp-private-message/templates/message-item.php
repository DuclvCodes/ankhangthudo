<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$args = array(
	'post_per_page' => 1,
	'paged' => 1,
	'parent' => $post->ID,
);
$reply_messages = WP_Private_Message_Message::get_list_reply_messages($args);
$read = get_post_meta($post->ID, '_read_'.WP_Private_Message_Mixes::get_current_user_id(), true);
$yourself_id = WP_Private_Message_Mixes::get_current_user_id();
$sender = get_post_meta($post->ID, '_sender', true);
$recipient = get_post_meta($post->ID, '_recipient', true);
if ( $yourself_id == $sender ) {
	$recipient_id = $recipient;
} else {
	$recipient_id = $sender;
}
if ( $read ) {
	$classes .= ' read';
} else {
	$classes .= ' unread';
}
?>
<li id="message-id-<?php echo esc_attr($post->ID); ?>" class="<?php echo esc_attr($classes); ?>">
	<a class="message-item" href="javascript:void(0);" data-id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-private-message-choose-message-nonce' )); ?>">
		<div class="avatar">
			<?php echo get_avatar( $recipient_id, '75', '' ); ?>
		</div>
		<div class="content">
			<h4 class="user-name"><?php echo esc_html( get_the_author_meta('display_name', $recipient_id)); ?>
				<span class="message-time"> -
					<?php if ( $reply_messages->have_posts() ) { ?>
						<?php foreach ($reply_messages->posts as $rpost) {?>
								<?php echo sprintf(__('%s', 'wp-private-message'), human_time_diff(get_the_time('U', $rpost), current_time('timestamp')) ); ?>
						<?php } ?>
					<?php } else { ?>
							<?php echo sprintf(__('%s', 'wp-private-message'), human_time_diff(get_the_time('U', $post), current_time('timestamp')) ); ?>
					<?php } ?>
				</span>
			</h4>
			<div class="message-title"><?php echo esc_html($post->post_title); ?></div>
		</div>
	</a>
</li>