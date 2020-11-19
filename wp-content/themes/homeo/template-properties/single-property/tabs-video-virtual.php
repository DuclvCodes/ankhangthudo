<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

$virtual_tour = $obj_property_meta->get_post_meta('virtual_tour');
$video = $obj_property_meta->get_post_meta('video');

if ( ($obj_property_meta->check_post_meta_exist('video') && $video) || ($obj_property_meta->check_post_meta_exist('virtual_tour') && $virtual_tour) ) {
    $active = '';
    if ( $obj_property_meta->check_post_meta_exist('video') && $video ) {
        $active = 'video';
    } else {
       $active = 'virtual_tour'; 
    }
?>
<div class="tabs-video-virtual">
    <ul class="nav nav-tabs nav-member">
        <?php if ( $obj_property_meta->check_post_meta_exist('video') && $video ) { ?>
            <li class="<?php echo esc_attr($active == 'video' ? 'active' : ''); ?>">
                <a href="#tab-gallery-map-video" data-toggle="tab">
                    <?php echo esc_html__('Property video','homeo'); ?>
                </a>
            </li>
        <?php } ?>
        <?php if ( $obj_property_meta->check_post_meta_exist('virtual_tour') && $virtual_tour ) { ?>
            <li class="<?php echo esc_attr($active == 'virtual_tour' ? 'active' : ''); ?>">
                <a href="#tab-gallery-map-virtual_tour" data-toggle="tab">
                    <?php echo esc_html__('Virtual Tour','homeo'); ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content tab-content-descrip">
        <?php if ( $obj_property_meta->check_post_meta_exist('video') && $video ) { ?>
            <div id="tab-gallery-map-video" class="tab-pane <?php echo esc_attr($active == 'video' ? 'active' : ''); ?>">
                <?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/video' ); ?>
            </div>
        <?php } ?>
        <?php if ( $obj_property_meta->check_post_meta_exist('virtual_tour') && $virtual_tour ) { ?>
            <div id="tab-gallery-map-virtual_tour" class="tab-pane <?php echo esc_attr($active == 'virtual_tour' ? 'active' : ''); ?>">
                <?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/virtual_tour' ); ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php }