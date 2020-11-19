<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);
if ( $meta_obj->check_post_meta_exist('energy_class') && ($energy_class = $meta_obj->get_post_meta('energy_class')) ) {
    $options = array(
        'A+' => esc_html__('A+', 'homeo'),
        'A' => esc_html__('A', 'homeo'),
        'B' => esc_html__('B', 'homeo'),
        'C' => esc_html__('C', 'homeo'),
        'D' => esc_html__('D', 'homeo'),
        'E' => esc_html__('E', 'homeo'),
        'F' => esc_html__('F', 'homeo'),
        'G' => esc_html__('G', 'homeo'),
        'H' => esc_html__('H', 'homeo'),
    );
?>
    <div class="property-detail-energy">
        <h3 class="title"><?php esc_html_e('Energy', 'homeo'); ?></h3>
        <div class="energy-inner flex-middle">
            <?php foreach ($options as $key => $title) {
                $classs = 'energy-'. strtolower($key);
                if ( $key == 'A+' ) {
                    $classs = 'energy-aplus';
                }
            ?>
                <div class="energy-group <?php echo esc_attr($classs); ?>">
                    <?php echo esc_html($title); ?>
                    <?php if ( $energy_class == $key ) {
                        $energy_index = $meta_obj->get_post_meta('energy_index');
                        $energy_index_text = '';
                        if ( !empty($energy_index) ) {
                            $energy_index_text = $energy_index.' '.esc_html__('kWh/m²a', 'homeo'). ' |';
                        }
                    ?>
                        <div class="indicator-energy">
                            <?php echo sprintf(esc_html__('%s Your energy class is %s', 'homeo'), $energy_index_text, $title); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php do_action('wp-realestate-single-property-energy', $post); ?>
    </div>
<?php }