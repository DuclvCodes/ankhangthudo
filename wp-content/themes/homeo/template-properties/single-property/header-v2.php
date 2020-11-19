<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>
<div class="property-detail-header top-header-detail-property v2">
    <div class="property-information flex-sm">
        <div class="left-infor">
            <div class="title-wrapper">
                <?php the_title( '<h1 class="property-title">', '</h1>' ); ?>
                <?php homeo_property_single_display_featured_icon($post); ?>
            </div>
            <?php homeo_property_display_full_location($post,'no-icon-title',true); ?>
        </div>
        <div class="property-action-detail ali-right">
            <?php homeo_property_display_price($post); ?>
        </div>
    </div>
</div>