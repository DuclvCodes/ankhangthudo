<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = get_current_user_id();
?>
<li class="<?php echo esc_attr($rpost->post_author == $user_id ? 'yourself-reply' : 'user-reply'); ?> author-id-<?php echo esc_attr($rpost->post_author); ?>">
  <?php if ( $rpost->post_author != $user_id ) { ?>
    <div class="avatar">
      <?php homeo_private_message_user_avatar( $rpost->post_author ); ?>
    </div>
  <?php } ?>
  <div class="reply-content">
    <!-- date -->
    <?php
      $pdate = get_the_time( get_option('date_format'), $rpost );
      $current = strtotime(date("Y-m-d"));
      $date    = strtotime( get_the_time('Y-m-d', $rpost) );

      $datediff = $date - $current;
      $difference = floor($datediff/(60*60*24));
      if ( $difference == 0 ) {
        $date = esc_html__('Today', 'homeo');
      } elseif ( $difference == -1 ) {
        $date = esc_html__('Yesterday', 'homeo');
      }
    ?>
    <div class="post-date"><?php echo trim($pdate); ?>, <?php echo get_the_time( get_option('time_format'), $rpost ); ?></div>
    <div class="post-content"><?php echo esc_html($rpost->post_content); ?></div>
  </div>
  
</li>