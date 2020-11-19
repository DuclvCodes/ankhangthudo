<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$latitude = $obj_property_meta->get_post_meta( 'map_location_latitude' );
$longitude = $obj_property_meta->get_post_meta( 'map_location_longitude' );

$map_service = wp_realestate_get_option('map_service', 'mapbox');

?>
<?php if ( !empty($latitude) && !empty($longitude) ) : ?>
	<div class="property-detail-map-street">
		<div class="widget-title-wrapper flex-middle-sm">
    		<h3><?php esc_html_e('Location', 'homeo'); ?></h3>
    		<div class="ali-right">
    			<?php homeo_property_display_full_location($post, 'icon',true); ?>
    		</div>
    	</div>

    	<div class="single-property-google-maps-wrapper">
		    <div id="single-property-google-maps" class="single-property-map"></div>
		    <?php if ( $map_service == 'google-map' ) { ?>
                <div id="single-property-street-view-map"></div>
                <a href="#maps-street" class="btn location-street-view"><?php esc_html_e('Street View', 'homeo'); ?></a>
	        <?php } ?>
			<a href="#maps" class="btn location-map-view hidden"><?php esc_html_e('Map View', 'homeo'); ?></a>
		</div>
	</div>
<?php endif; ?>