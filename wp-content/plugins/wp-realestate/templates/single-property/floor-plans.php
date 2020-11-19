<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);
if ( $meta_obj->check_post_meta_exist('floor_plans_group') && ($floor_plans = $meta_obj->get_post_meta('floor_plans_group')) ) {
?>
    <div class="property-detail-floor-plans">
        <h3><?php esc_html_e('Floor Plans', 'wp-realestate'); ?></h3>
        <div class="panel-group" id="accordion-floor_plans">
        <?php $i = 1; foreach ($floor_plans as $floor_plan) { ?>
            <div class="panel panel-default floor-item">
                <div class="panel-heading <?php if ( $i == 1 ) echo esc_attr('active'); ?>">
                    <?php if ( !empty($floor_plan['name']) ) { ?>
                        <h3>
                            <a data-toggle="collapse" data-parent="#accordion-floor_plans" href="#collapse-floor_plan<?php echo esc_attr($i); ?>">
                                <?php echo trim($floor_plan['name']); ?>
                            </a>
                        </h3>
                    <?php } ?>
                    <div class="metas">
                        <?php if ( !empty($floor_plan['rooms']) ) { ?>
                            <div class="rooms"><span class="subtitle"><?php esc_html_e('Rooms:', 'wp-realestate'); ?></span> <?php echo trim($floor_plan['rooms']); ?></div>
                        <?php } ?>
                        <?php if ( !empty($floor_plan['baths']) ) { ?>
                            <div class="baths"><span class="subtitle"><?php esc_html_e('Baths:', 'wp-realestate'); ?></span> <?php echo trim($floor_plan['baths']); ?></div>
                        <?php } ?>
                        <?php if ( !empty($floor_plan['size']) ) { ?>
                            <div class="size"><span class="subtitle"><?php esc_html_e('Size:', 'wp-realestate'); ?></span> <?php echo trim($floor_plan['size']); ?></div>
                        <?php } ?>
                    </div>
                </div>
                <div id="collapse-floor_plan<?php echo esc_attr($i); ?>" class="panel-collapse content-item collapse <?php echo esc_attr($i == 1 ? 'in' : ''); ?>">
                    <?php if ( !empty($floor_plan['image_id']) ) { ?>
                        <div class="image">
                            <a href="<?php echo esc_url($floor_plan['image']); ?>">
                                <?php echo wp_get_attachment_image($floor_plan['image_id'], 'large'); ?>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if ( !empty($floor_plan['content']) ) { ?>
                        <div class="content"><?php echo trim($floor_plan['content']); ?></div>
                    <?php } ?>
                </div>
            </div>

        <?php $i++; } ?>
        </div>

        <?php do_action('wp-realestate-single-property-floor-plans', $post); ?>
    </div>
<?php }