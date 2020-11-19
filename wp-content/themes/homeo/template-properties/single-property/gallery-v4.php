<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$gallery = $obj_property_meta->get_post_meta( 'gallery' );
if ( has_post_thumbnail() || $gallery ) {
    $gallery_size = !empty($gallery_size) ? $gallery_size : 'homeo-gallery-v4-large';
    $second_size = 'homeo-gallery-v4-small';
    $first_class = 'col-xs-12';
    $second_class = 'col-sm-2 col-xs-4';
    if ( $gallery ) {
        $first_class = 'col-sm-8 col-xs-12';
        if ( count($gallery) == 1 ) {
            $second_class = 'col-sm-4 col-xs-12';
            $second_size = '360x450';
        } elseif ( count($gallery) == 2 ) {
            $second_class = 'col-sm-4 col-xs-12';
            $second_size = '360x210';
        } elseif ( count($gallery) == 3 ) {
            $second_class = 'col-sm-4 col-xs-12';
            $second_size = '360x130';
        } elseif ( count($gallery) == 4 ) {
            $second_class = 'col-sm-2 col-xs-4';
            $second_size = '165x210';
        }
    } else {
        $gallery_size = '1170x450';
    }
?>
<div class="property-detail-gallery">
    <div class="row list-gallery-property-v4">
        <?php if ( has_post_thumbnail() ) {
            $thumbnail_id = get_post_thumbnail_id($post);
        ?>
            <div class="<?php echo esc_attr($first_class); ?>">
                <a href="<?php echo esc_url( get_the_post_thumbnail_url($post, 'full') ); ?>" data-elementor-lightbox-slideshow="homeo-gallery" class="p-popup-image">
                    <?php echo homeo_get_attachment_thumbnail($thumbnail_id, $gallery_size);?>
                </a>
            </div>
        <?php } ?>

        <?php
        if ( $gallery ) {
            $i=1; foreach ( $gallery as $id => $src ) {
                $additional_class = '';
                if ( $i > 6 ) {
                    $additional_class = 'hidden';
                }
                $more_image_class = $more_image_html = '';
                if ( $i == 6 && count($gallery) > 6 ) {
                    $more_image_html = '<div class="view-more-gallery">+'.(count($gallery) - 6).'</div>';
                    $more_image_class = 'view-more-image';
                }
            ?>
                <div class="<?php echo esc_attr($second_class.' '.$additional_class); ?>">
                    <a href="<?php echo esc_url( $src ); ?>" data-elementor-lightbox-slideshow="homeo-gallery" class="p-popup-image <?php echo esc_attr($more_image_class); ?>">
                        <?php
                        if ( $i <= 6 ) {
                            echo homeo_get_attachment_thumbnail( $id, $second_size );
                            echo trim($more_image_html);
                        }
                        ?>
                    </a>
                </div>
            <?php $i++; }
        } ?>
    </div>
    
</div>
<?php }