<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$gallery = $obj_property_meta->get_post_meta( 'gallery' );
$latitude = $obj_property_meta->get_post_meta( 'map_location_latitude' );
$longitude = $obj_property_meta->get_post_meta( 'map_location_longitude' );

$active = homeo_get_config('property_header_active_tab', 'gallery');
$map_service = wp_realestate_get_option('map_service', 'mapbox');

if ( has_post_thumbnail() || !empty( $gallery ) || (!empty($latitude) && !empty($longitude)) ) {
?>
    <div class="tabs-gallery-map no-padding">
        <div class="container p-relative">
            <ul class="nav nav-tabs nav-table">
                <?php if ( has_post_thumbnail() || !empty( $gallery ) ) : ?>
                    <li class="<?php echo esc_attr($active == 'gallery' ? 'active' : ''); ?>">
                        <a href="#tab-gallery-map-gallery" data-toggle="tab">
                            <i class="flaticon-photo-camera"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ( !empty($latitude) && !empty($longitude) ) : ?>
                    <li class="<?php echo esc_attr($active == 'map' || ( !has_post_thumbnail() && empty($gallery) && $active == 'gallery' ) ? 'active' : ''); ?>">
                        <a class="tab-google-map" href="#tab-gallery-map-map" data-toggle="tab">
                            <i class="flaticon-pin"></i>
                        </a>
                    </li>
                    <?php if ( $map_service == 'google-map' ) { ?>
                        <li>
                            <a class="tab-google-street-view-map <?php echo esc_attr($active == 'mapview' ? 'active' : ''); ?>" href="#tab-gallery-map-mapview" data-toggle="tab">
                                <i class="flaticon-street-view"></i>
                            </a>
                        </li>
                    <?php } ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="tab-content tab-content-descrip">

            <?php if ( has_post_thumbnail() || !empty( $gallery ) ) : ?>
                <div id="tab-gallery-map-gallery" class="tab-pane <?php echo esc_attr($active == 'gallery' ? 'active' : ''); ?>">
                    <?php
                    $args = array();
                    if ( !empty($gallery_size) ) {
                        $args = array('gallery_size' => $gallery_size);
                    }
                    echo WP_RealEstate_Template_Loader::get_template_part('single-property/gallery', $args);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ( !empty($latitude) && !empty($longitude) ) : ?>
                <div id="tab-gallery-map-map" class="tab-pane <?php echo esc_attr($active == 'map' || ( empty($gallery) && $active == 'gallery' ) ? 'active' : ''); ?>">
                    <div id="properties-google-maps" class="single-property-map"></div>
                </div>
                <?php if ( $map_service == 'google-map' ) { ?>
                    <div id="tab-gallery-map-mapview" class="tab-pane <?php echo esc_attr($active == 'mapview' ? 'active' : ''); ?>">
                        <div id="single-tab-property-street-view-map"></div>
                    </div>
                <?php } ?>
            <?php endif; ?>

        </div>
    </div>
<?php } ?>