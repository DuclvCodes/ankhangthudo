<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>

<article <?php post_class('map-item property-grid-v1 property-item'); ?> <?php homeo_property_item_map_meta($post); ?> <?php homeo_property_display_gallery($post, 'homeo-property-grid1'); ?>>

        <div class="property-thumbnail-wrapper flex-middle justify-content-center">
            
            <?php homeo_property_display_image( $post, 'homeo-property-grid1' ); ?>

            <?php
                $featured = homeo_property_display_featured_icon($post, false);
                $status_label = homeo_property_display_status_label($post, false);
                $labels = homeo_property_display_label($post, false);
                if ( $featured || $status_label || $labels ) {
                    ?>
                    <div class="top-label">
                        <?php echo trim($status_label); ?>
                        <?php echo trim($featured); ?>
                        <?php echo trim($labels); ?>
                    </div>
                    <?php
                }
            ?>
            <div class="bottom-label">
                <?php homeo_property_display_price($post, 'no-icon-title', true); ?>
                <?php the_title( sprintf( '<h2 class="property-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                <?php 
                    $suffix = wp_realestate_get_option('measurement_unit_area');
                    $lot_area = homeo_property_display_meta($post, 'lot_area', '', $suffix.':');
                    $beds = homeo_property_display_meta($post, 'beds', '', esc_html__('Beds:', 'homeo'));
                    $baths = homeo_property_display_meta($post, 'baths', '', esc_html__('Baths:', 'homeo'));
                if ( $lot_area || $beds || $baths ) {
                    ?>
                    <div class="property-metas flex-middle">
                        <?php
                            echo trim($beds);
                            echo trim($baths);
                            echo trim($lot_area);
                        ?>
                    </div>
                <?php } ?>

            </div>
        </div>

</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_property_content', $post->ID ); ?>