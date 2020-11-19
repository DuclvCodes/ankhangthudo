<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$gallery = $obj_property_meta->get_post_meta( 'gallery' );
if ( has_post_thumbnail() || $gallery ) {
    $gallery_size = !empty($gallery_size) ? $gallery_size : 'homeo-gallery-v2-large';
    $first_class = 'col-xs-12';
    $second_class = 'col-sm-3 col-xs-6';
    if ( $gallery ) {
        $first_class = 'col-sm-6 col-xs-12';
        if ( count($gallery) == 1 ) {
            $second_class = 'col-sm-6 col-xs-12';
        } elseif ( count($gallery) == 2 ) {
            $second_class = 'col-sm-3 col-xs-6';
            $gallery_size = 'homeo-gallery-v2-small';
        }
    }
?>
<div class="property-detail-gallery v2">
    <div class="row list-gallery-property-v2">
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
                if ( $i > 4 ) {
                    $additional_class = 'hidden';
                }
            ?>
                <div class="<?php echo esc_attr($second_class.' '.$additional_class); ?>">
                    <a href="<?php echo esc_url( $src ); ?>" data-elementor-lightbox-slideshow="homeo-gallery" class="p-popup-image">
                        <?php
                        if ( $i <= 4 ) {
                            echo homeo_get_attachment_thumbnail( $id, $gallery_size );
                        }
                        ?>
                    </a>
                </div>
            <?php $i++; }
        } ?>
    </div>

    <div class="property-detail-gallery-actions">
        <div class="container">
            <div class="property-information flex-middle-sm">
                <div class="left-infor">
                    <a href="javascript:void(0);" class="btn btn-view-all-photos">
                        <i class="flaticon-photo-camera"></i>
                        <?php esc_html_e('View Photos', 'homeo'); ?>
                    </a>
                </div>
                <div class="property-action-detail v2 ali-right">
                    <?php
                        if ( homeo_get_config('listing_enable_compare', true) ) {
                            $args = array(
                                'added_icon_class' => 'flaticon-transfer-1',
                                'add_icon_class' => 'flaticon-transfer-1',
                            );
                            WP_RealEstate_Compare::display_compare_btn($post->ID, $args);
                        }
                        if ( homeo_get_config('listing_enable_favorite', true) ) {
                            WP_RealEstate_Favorite::display_favorite_btn($post->ID);
                        }
                        
                    ?>
                    <?php get_template_part('template-parts/sharebox-property'); ?>
                    <?php homeo_property_print_btn($post); ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php }