<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

ob_start();
if ( !empty($options) ) {
    $i = 1;
    foreach ($options as $option) {
        ?>
        <li class="list-item"><input id="<?php echo esc_attr($option['text']); ?>" type="radio" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($option['value']); ?>"  <?php checked($selected, $option['value']); ?>><label for="<?php echo esc_attr($option['text']); ?>"><?php echo wp_kses_post($option['text']); ?></label>
        </li>
        <?php
        $i++;
    }
}
$output = ob_get_clean();

if ( !empty($output) ) {
?>
    <div class="form-group form-group-<?php echo esc_attr($key); ?>">
        <?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
            <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" class="heading-label">
                <?php echo wp_kses_post($field['name']); ?>
            </label>

        <?php } ?>
        <div class="form-group-inner">
            <ul class="terms-list circle-check">
                <?php echo $output; ?>
            </ul>
        </div>
    </div><!-- /.form-group -->
<?php }