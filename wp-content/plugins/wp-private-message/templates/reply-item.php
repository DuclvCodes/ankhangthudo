<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = WP_Private_Message_Mixes::get_current_user_id();
?>
<li class="<?php echo esc_attr($rpost->post_author == $user_id ? 'yourself-reply' : 'user-reply'); ?> author-id-<?php echo esc_attr($rpost->post_author); ?>">
  <?php if ( $rpost->post_author != $user_id ) { ?>
    <div class="avatar">
      <?php echo get_avatar( $rpost->post_author, '75', '' ); ?>
    </div>
  <?php } ?>
  <div class="reply-content">
    <!-- date -->
    <?php
      
      $current = strtotime(date("Y-m-d"));
      $date    = strtotime( get_the_time('Y-m-d', $rpost) );

      $datediff = $date - $current;
      $difference = floor($datediff/(60*60*24));
      if ( $difference == 0 ) {
        $date = esc_html__('Today', 'wp-private-message');
      } elseif ( $difference == -1 ) {
        $date = esc_html__('Yesterday', 'wp-private-message');
      } else {
        $date = get_the_time( get_option('date_format'), $rpost );
      }
    ?>
    <div class="post-date"><?php echo trim($date); ?>, <?php echo get_the_time( get_option('time_format'), $rpost ); ?></div>
    <div class="post-content"><?php echo esc_html($rpost->post_content); ?></div>
  </div>
  
</li>