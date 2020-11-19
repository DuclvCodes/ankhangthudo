<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


?>
<div class="property-detail-header top-header-detail-property v3">
    <div class="property-information flex-middle-sm">
        <div class="left-infor">
            <div class="title-wrapper">
                <?php the_title( '<h1 class="property-title">', '</h1>' ); ?>
                <?php homeo_property_single_display_featured_icon($post); ?>
            </div>
            <?php homeo_property_display_full_location($post,'no-icon-title',true); ?>
        </div>
        <div class="property-action-detail ali-right">
            <?php
            if ( homeo_get_config('listing_enable_compare', true) ) {
                $args = array(
                    'added_icon_class' => 'flaticon-transfer-1',
                    'add_icon_class' => 'flaticon-transfer-1',
                );
                WP_RealEstate_Compare::display_compare_btn($post->ID, $args);
            }
            if ( homeo_get_config('listing_enable_favorite', true) ) {
                WP_RealEstate_Favorite::display_favorite_btn($post->ID);
            }
            ?>
            <?php get_template_part('template-parts/sharebox-property'); ?>
            <?php homeo_property_print_btn($post); ?>

            <?php homeo_property_display_price($post); ?>
        </div>
    </div>
</div>