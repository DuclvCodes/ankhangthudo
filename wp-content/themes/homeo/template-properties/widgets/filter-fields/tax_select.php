<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



$output = WP_RealEstate_Abstract_Filter::hierarchical_tax_option_tree(0, 0, $name, $key, $field, $selected );

if ( !empty($output) ) {
    $placeholder = !empty($field['placeholder']) ? $field['placeholder'] : sprintf(esc_html__('Filter by %s', 'homeo'), $field['name']);
?>
    <div class="form-group form-group-<?php echo esc_attr($key); ?> tax-select-field">
        <?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
            <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" class="heading-label">
                <?php echo wp_kses_post($field['name']); ?>
            </label>
        <?php } ?>
        <div class="form-group-inner inner select-wrapper">
            <?php if ( !empty($field['icon']) ) { ?>
                <i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
            <?php } ?>
            <select name="<?php echo esc_attr($name); ?>" class="form-control" id="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" data-placeholder="<?php echo esc_attr($placeholder); ?>">
                <option value=""><?php echo esc_html($placeholder); ?></option>
                <?php echo trim($output); ?>
            </select>
        </div>
    </div><!-- /.form-group -->
<?php }