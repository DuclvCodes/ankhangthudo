<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$args = array(
    'post_per_page' => -1,
    'meta_query' => array(
        array(
            'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'parent_property',
            'value'     => $post->ID,
            'compare'   => '==',
        )
    )
);
$loop = WP_RealEstate_Query::get_posts($args);
if ( $loop->have_posts() ) {
?>
    <div class="property-subproperties">
        <h3 class="title"><?php esc_html_e('Subproperties', 'wp-realestate'); ?></h3>
        <?php while ( $loop->have_posts() ) : $loop->the_post();
            echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list' );
        endwhile;
        ?>
        <?php wp_reset_postdata(); ?>
    </div>
<?php }