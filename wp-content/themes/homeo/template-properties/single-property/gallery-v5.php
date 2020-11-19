<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$gallery = $obj_property_meta->get_post_meta( 'gallery' );
?>
<div class="property-detail-gallery v5">
<?php
if ( has_post_thumbnail() || $gallery ) {
    $gallery_size = !empty($gallery_size) ? $gallery_size : 'full';
?>

    <div class="slick-carousel" data-carousel="slick" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="true" data-nav="true" data-autoplay="true">
        <?php if ( has_post_thumbnail() ) {
            $thumbnail_id = get_post_thumbnail_id($post);
        ?>
            <a href="<?php echo esc_url( get_the_post_thumbnail_url($post, 'full') ); ?>" data-elementor-lightbox-slideshow="homeo-gallery" class="p-popup-image">
                <?php echo homeo_get_attachment_thumbnail($thumbnail_id, $gallery_size);?>
            </a>
        <?php } ?>

        <?php
        if ( $gallery ) {
            foreach ( $gallery as $id => $src ) { ?>
                <a href="<?php echo esc_url( $src ); ?>" data-elementor-lightbox-slideshow="homeo-gallery" class="p-popup-image">
                    <?php echo homeo_get_attachment_thumbnail( $id, $gallery_size );?>
                </a>
            <?php }
        } ?>
    </div>
    
<?php } ?>

    <div class="property-detail-header top-header-detail-property">
        <div class="container">
            <div class="property-information flex-middle-sm">
                <div class="left-infor">
                    <div class="title-wrapper">
                        <?php the_title( '<h1 class="property-title">', '</h1>' ); ?>
                        <?php homeo_property_display_featured_icon($post); ?>
                    </div>
                    <?php homeo_property_display_full_location($post,'no-icon-title',true); ?>
                </div>
                <div class="property-action-detail ali-right">
                    <?php homeo_property_display_price($post); ?>
                </div>
            </div>
        </div>
    </div>

</div>