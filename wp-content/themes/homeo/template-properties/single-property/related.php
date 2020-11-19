<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$relate_count = apply_filters('wp_realestate_number_property_releated', homeo_get_config('property_related_number', 2));

$tax_query = array();
$terms = WP_RealEstate_Property::get_property_taxs( $post->ID, 'property_type' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'property_type',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}

$terms = WP_RealEstate_Property::get_property_taxs( $post->ID, 'property_status' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}

if ( empty($tax_query) ) {
    return;
}
$args = array(
    'post_type' => 'property',
    'posts_per_page' => $relate_count,
    'post__not_in' => array( $post->ID ),
    'tax_query' => array_merge(array( 'relation' => 'AND' ), $tax_query)
);
$relates = new WP_Query( $args );
if( $relates->have_posts() ):
?>
    <div class="widget releated-properties">
        <h4 class="widget-title">
            <span><?php esc_html_e( 'Related Properties', 'homeo' ); ?></span>
        </h4>
        <div class="widget-content">
            <div class="row">
            <?php
                while ( $relates->have_posts() ) : $relates->the_post(); ?>
                    <div class="col-xs-12 col-sm-6">
                        <?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-grid' ); ?>
                    </div>
                <?php endwhile;
            ?>
            </div>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>