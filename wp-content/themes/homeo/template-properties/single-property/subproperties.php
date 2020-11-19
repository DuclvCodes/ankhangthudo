<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
$subproperties_columns = homeo_get_config('property_subproperties_number',2);

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
    <div class="widget property-subproperties">
        <h3 class="widget-title"><?php esc_html_e('Subproperties', 'homeo'); ?></h3>
        <div class="row">
            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <div class="col-xs-12 col-sm-6 col-md-<?php echo esc_attr(12 / $subproperties_columns); ?>">
                    <?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-grid' ); ?>
                </div>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    </div>
<?php }