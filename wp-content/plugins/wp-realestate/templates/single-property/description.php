<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


?>
<div class="description inner">
    <h3 class="title"><?php esc_html_e('Overview', 'wp-realestate'); ?></h3>
    <div class="description-inner">
        <?php the_content(); ?>

        <?php do_action('wp-realestate-single-property-description', $post); ?>
    </div>
</div>