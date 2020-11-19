<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$selected_post = $post;
if ( $selected_post ) {
  update_post_meta($selected_post->ID, '_read_'.get_current_user_id(), 'yes');
  $post_id = $selected_post->ID;
  $user_id = get_current_user_id();
  $author = $selected_post->post_author;
  $sender = get_post_meta($post_id, '_sender', true);
  $recipient = get_post_meta($post_id, '_recipient', true);

  if ( $user_id == $sender ) {
    $recipient_id = $recipient;
  } else {
    $recipient_id = $sender;
  }
  $paged = isset($paged) ? $paged : 1;
  $args = array(
    'post_per_page' => wp_private_message_get_option('number_replied_per_page', 25),
    'paged' => $paged,
    'parent' => $selected_post->ID,
  );
  $reply_messages = WP_Private_Message_Message::get_list_reply_messages($args);
?>
  <div class="recipient-info clearfix">
    <div class="message-item pull-left">
        <?php $display_name = get_the_author_meta('display_name', $recipient_id); ?>
        <div class="avatar">
          <?php homeo_private_message_user_avatar( $recipient_id ); ?>
        </div>
        <div class="content">
          <h4 class="user-name"><?php echo esc_html($display_name); ?></h4>
          <div class="message-title"><?php echo esc_html($selected_post->post_title); ?></div>
        </div>
    </div>
    <a href="javascript:void(0);" class="delete-message-btn pull-right" data-id="<?php echo esc_attr($selected_post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-private-message-delete-message-nonce' )); ?>"><i class="flaticon-garbage"></i><?php esc_html_e('Delete Conversation', 'homeo'); ?></a>
  </div>
  <div class="content-box-white">
    <div class="list-replies-inner">
      <?php
      $next_page = $paged + 1;
      if ( $next_page <= $reply_messages->max_num_pages ) { ?>
        <div class="loadmore-action">
          <a href="javascript:void(0);" class="loadmore-replied-btn" data-paged="<?php echo esc_attr($next_page); ?>" data-parent="<?php echo esc_attr($selected_post->ID); ?>"><?php esc_html_e( 'Load more', 'homeo' ); ?></a>
        </div>
      <?php } ?>
      <ul class="list-replies">
        <?php
          if ( $next_page > $reply_messages->max_num_pages ) {
            echo WP_Private_Message_Template_Loader::get_template_part( 'reply-item', array( 'rpost' => $selected_post ) );
          }
        ?>
        
        <?php if ( $reply_messages->have_posts() ) {
          $posts = $reply_messages->posts;
          $posts = array_reverse($posts, true);
        ?>
          <?php foreach ($posts as $rpost) { ?>
            <?php
              echo WP_Private_Message_Template_Loader::get_template_part( 'reply-item', array( 'rpost' => $rpost ) );
            ?>
          <?php } ?>
        <?php } ?>
      </ul>
    </div>
    <?php
      echo WP_Private_Message_Template_Loader::get_template_part( 'reply-message-form', array( 'parent' => $post_id ) );
    } ?>
  </div>