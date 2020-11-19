<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);
?>
<div class="property-detail-detail">
    <h3><?php esc_html_e('Details', 'wp-realestate'); ?></h3>
    <ul class="list">
        <?php if ( $meta_obj->check_post_meta_exist('property_id') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Property ID : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('property_id')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('lot_area') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Lot Area : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('lot_area')); ?> <?php echo wp_realestate_get_option('measurement_unit_area'); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('home_area') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Home Area : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('home_area')); ?> <?php echo wp_realestate_get_option('measurement_unit_area'); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('lot_dimensions') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Lot dimensions : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('lot_dimensions')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('rooms') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Rooms : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('rooms')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('beds') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Beds : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('beds')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('baths') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Baths : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('baths')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('garages') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Garages : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('garages')); ?></div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('price') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Price : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_price_html()); ?></div>
            </li>
        <?php } ?>

        <?php if ( $meta_obj->check_post_meta_exist('year_build') ) { ?>
            <li>
                <div class="text"><?php esc_html_e('Year built : ', 'wp-realestate'); ?></div>
                <div class="value"><?php echo trim($meta_obj->get_post_meta('year_build')); ?></div>
            </li>
        <?php } ?>

        <?php do_action('wp-realestate-single-property-details', $post); ?>
    </ul>
</div>