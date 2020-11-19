<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$loop = WP_RealEstate_Query::get_agency_agents($post->ID, array(
    'post_per_page' => get_option('posts_per_page'),
    'paged' => $paged
));

if ( !empty($loop) && $loop->have_posts() ) {
?>
    <div class="agent-detail-properties">
        <?php
            while ( $loop->have_posts() ) : $loop->the_post();
                echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-list' );
            endwhile;

            wp_reset_postdata();
        ?>
    </div>
<?php }