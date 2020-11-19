<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
$suffix = wp_realestate_get_option('measurement_unit_area');
$lot_area = homeo_property_display_meta($post, 'lot_area', '', '', $suffix.':');
$beds = homeo_property_display_meta($post, 'beds', '',esc_html__('Beds:','homeo') );
$baths = homeo_property_display_meta($post, 'baths', '',esc_html__('Baths:','homeo') );
?>
<article <?php post_class('property-list-simple'); ?>>
    <div class="flex-middle">
        <?php if ( has_post_thumbnail() ) { ?>
            <div class="property-thumbnail-wrapper flex-middle">
                <?php homeo_property_display_image( $post, 'thumbnail' ); ?>
            </div>
        <?php } ?>
        <div class="property-information">
            <?php the_title( sprintf( '<h2 class="entry-title property-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php homeo_property_display_price($post, 'no-icon-title', true); ?>
            <div class="property-metas">
                <?php 
                    echo trim($beds);
                    echo trim($baths);
                    echo trim($lot_area);
                ?>
            </div>
        </div>
    </div>
</article><!-- #post-## -->