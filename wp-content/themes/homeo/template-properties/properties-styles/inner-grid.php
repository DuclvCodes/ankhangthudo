<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>

<article <?php post_class('map-item property-grid property-item'); ?> <?php homeo_property_item_map_meta($post); ?> <?php homeo_property_display_gallery($post, 'homeo-property-grid'); ?>>
    <div class="top-info">
        <div class="property-thumbnail-wrapper flex-middle justify-content-center">
            <?php homeo_property_display_image( $post, 'homeo-property-grid' ); ?>
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
            <div class="bottom-label flex-middle">
                <?php homeo_property_display_price($post, 'no-icon-title', true); ?>
                <div class="ali-right">
                    <?php
                        if ( homeo_get_config('listing_enable_favorite', true) ) {
                            WP_RealEstate_Favorite::display_favorite_btn($post->ID);
                        }
                        if ( homeo_get_config('listing_enable_compare', true) ) {
                            $args = array(
                                'added_icon_class' => 'flaticon-transfer-1',
                                'add_icon_class' => 'flaticon-transfer-1',
                            );
                            WP_RealEstate_Compare::display_compare_btn($post->ID, $args);
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="property-information">
        	<?php homeo_property_display_type($post, 'no-icon-title', true); ?>

    		<?php the_title( sprintf( '<h2 class="property-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php homeo_property_display_full_location($post, 'icon'); ?>

            <?php
            $suffix = wp_realestate_get_option('measurement_unit_area');
            $lot_area = homeo_property_display_meta($post, 'lot_area', '', $suffix.':');
            $beds = homeo_property_display_meta($post, 'beds', '', esc_html__('Beds:', 'homeo'));
            $baths = homeo_property_display_meta($post, 'baths', '', esc_html__('Baths:', 'homeo'));

            if ( $lot_area || $beds || $baths ) {
            ?>
                <div class="property-metas flex-middle flex-wrap">
                    <?php
                        echo trim($beds);
                        echo trim($baths);
                        echo trim($lot_area);
                    ?>
                </div>
            <?php } ?>
    	</div>
    </div>
    <?php
        $postdate = homeo_property_display_postdate($post, 'no-icon-title', 'ago', false);
        $author = homeo_property_display_author($post, 'logo', false);
        if ( $postdate || $author ) {
    ?>
        <div class="property-metas-bottom flex-middle">
            <?php echo trim($author); ?>
            <div class="ali-right">
                <?php echo trim($postdate); ?>
            </div>
        </div>
    <?php } ?>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_property_content', $post->ID ); ?>