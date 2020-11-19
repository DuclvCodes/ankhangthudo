<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
$suffix = wp_realestate_get_option('measurement_unit_area');
$lot_area = homeo_property_display_meta($post, 'lot_area', '', '', $suffix);
$beds = homeo_property_display_meta($post, 'beds', '',esc_html__('Beds:','homeo') );
$baths = homeo_property_display_meta($post, 'baths', '',esc_html__('Baths:','homeo') );
$type = homeo_property_display_type($post,'',false);
?>
<div class="description inner">
	<?php if( !empty($type) || !empty($beds) || !empty($baths) || !empty($lot_area)  ){ ?>
		<div class="detail-metas-top">
	        <?php 
	            echo trim($type);
	            echo trim($beds);
	            echo trim($baths);
	            echo trim($lot_area);
	        ?>
	    </div>
    <?php } ?>
    <h3 class="title"><?php esc_html_e('Overview', 'homeo'); ?></h3>
    <div class="description-inner">
        <?php the_content(); ?>
        <?php do_action('wp-realestate-single-property-description', $post); ?>
    </div>
</div>