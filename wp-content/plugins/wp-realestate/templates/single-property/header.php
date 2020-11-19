<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$user_id = $post->post_author;
if ( WP_RealEstate_User::is_agent($user_id) ) {
    $agent_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
    $agent_title = get_the_title($agent_id);
} elseif ( WP_RealEstate_User::is_agency($user_id) ) {
    $agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
    $agent_title = get_the_title($agency_id);
} else {
    $agent_title = $userdata->display_name;
}
$address = WP_RealEstate_Property::get_post_meta( $post->ID, 'address', true );
$price = WP_RealEstate_Property::get_price_html($post->ID);

?>
<div class="property-detail-header">
    
    <div class="property-information">
        <?php WP_RealEstate_Property::get_property_types_html($post->ID); ?>

        <?php the_title( '<h1 class="entry-title property-title">', '</h1>' ); ?>

        <div class="property-date-author">
            <?php echo sprintf(__('posted %s ago', 'wp-realestate'), human_time_diff(get_the_time('U'), current_time('timestamp')) ); ?> 
            <?php
                echo $agent_title;
            ?>
        </div>
        <div class="property-metas">
            <?php if ( $address ) { ?>
                <div class="property-location"><?php echo wp_kses_post($address); ?></div>
            <?php } ?>
            <?php if ( $price ) { ?>
                <div class="property-price"><?php echo wp_kses_post($price); ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="property-detail-buttons">
        <?php WP_RealEstate_Property::display_favorite_btn($post->ID); ?>
    </div>
</div>