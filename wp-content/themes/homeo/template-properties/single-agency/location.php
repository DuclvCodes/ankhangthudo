<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$location = homeo_agency_display_full_location($post, 'no-icon-title', false);
$latitude = WP_RealEstate_Agency::get_post_meta( $post->ID, 'map_location_latitude', true );
$longitude = WP_RealEstate_Agency::get_post_meta( $post->ID, 'map_location_longitude', true );
?>
<div class="location-single-member">
	<div class="title-wrapper flex-middle">
    	<h3 class="title"><?php esc_html_e('Location', 'homeo'); ?></h3>
    	<?php if ( $location ) { ?>
    		<div class="location ali-right">
    			<?php echo trim($location); ?>
    		</div>
    	<?php } ?>
    </div>
    <?php if ( !empty($latitude) && !empty($longitude) ) { ?>
        <div class="location-inner">
            <div id="properties-google-maps" class="single-property-map single-map-agency"></div>
        </div>
    <?php } ?>
</div>