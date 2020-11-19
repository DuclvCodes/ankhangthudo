<?php
global $post;
$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);
$facilities = $meta_obj->get_post_meta('public_facilities_group');
if ( $meta_obj->check_post_meta_exist('public_facilities_group') && is_array( $facilities ) && count( $facilities[0] ) > 0 ) {
?>
    <div class="property-section property-public-facilities">
        <h3><?php echo esc_html__('Facilities', 'wp-realestate'); ?></h3>
        <div class="clearfix">
            <?php foreach ( $facilities as $facility ) : ?>
                <div class="property-public-facility-wrapper">
                    <div class="property-public-facility">
                        <div class="property-public-facility-title">
                            <span><?php echo empty( $facility['public_facilities_key'] ) ? '' : esc_attr( $facility['public_facilities_key'] ); ?></span>
                        </div>

                        <div class="property-public-facility-info">
    						<?php echo empty( $facility['public_facilities_value'] ) ? '' : esc_attr( $facility['public_facilities_value'] ); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php do_action('wp-realestate-single-property-facilities', $post); ?>
        </div>
    </div>
<?php }