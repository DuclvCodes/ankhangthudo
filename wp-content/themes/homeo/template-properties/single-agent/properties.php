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

$loop = WP_RealEstate_Query::get_agents_properties(array(
    'agent_ids' => array($post->ID),
    'post_per_page' => get_option('posts_per_page'),
    'paged' => $paged
));

if ( !empty($loop) && $loop->have_posts() ) {
?>
    <div class="agent-detail-properties agent-agency-detail-properties">
        
        <div class="row">
            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <div class="col-xs-12 list-item">
                    <?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list-member' ); ?>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        
        if ( $loop->max_num_pages > 1 ) {
        ?>
            <div class="ajax-properties-pagination">
                <a href="#" class="apus-loadmore-btn" data-paged="<?php echo esc_attr($paged + 1); ?>" data-post_id="<?php echo esc_attr($post->ID); ?>" data-type="agent"><?php esc_html_e( 'Load more', 'homeo' ); ?></a>
                <span class="apus-allproducts"><?php esc_html_e( 'All properties loaded.', 'homeo' ); ?></span>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="agent-detail-properties">
        <?php esc_html_e('No properties found', 'homeo'); ?>
    </div>
    <?php
}
