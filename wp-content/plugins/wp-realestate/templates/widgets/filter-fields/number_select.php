<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$placeholder = !empty($field['placeholder']) ? $field['placeholder'] : sprintf(__('%s : Any', 'wp-realestate'), $field['name']);
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

            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <option value="<?php echo esc_attr( $i ); ?>" <?php selected($selected, $i); ?>>
                    <?php echo esc_attr( $i ); ?>+
                </option>
            <?php endfor; ?>
        </select>
    </div>
</div><!-- /.form-group -->