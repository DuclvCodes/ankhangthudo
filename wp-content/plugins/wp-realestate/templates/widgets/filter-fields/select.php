<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


ob_start();
if ( !empty($options) ) {
    $i = 1;
    foreach ($options as $option) {
        if ( $option['value'] ) {
            ?>
            <option value="<?php echo esc_attr($option['value']); ?>" <?php selected($selected, $option['value']); ?>>
                <?php echo esc_attr($option['text']); ?>
            </option>
            <?php
            $i++;
        }
    }
}
$output = ob_get_clean();

if ( !empty($output) ) {
    $placeholder = !empty($field['placeholder']) ? $field['placeholder'] : sprintf(__('Filter by %s', 'wp-realestate'), $field['label']);
?>
    <div class="form-group form-group-<?php echo esc_attr($key); ?>">
        <?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
            <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" class="heading-label">
                <?php echo wp_kses_post($field['name']); ?>
            </label>
        <?php } ?>
        <div class="form-group-inner inner">
            <?php if ( !empty($field['icon']) ) { ?>
                <i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
            <?php } ?>
            <select name="<?php echo esc_attr($name); ?>" class="form-control" id="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" data-placeholder="<?php echo esc_attr($placeholder); ?>">
                <option value=""><?php echo esc_html($placeholder); ?></option>
                <?php echo $output; ?>
            </select>
        </div>
    </div><!-- /.form-group -->
<?php }