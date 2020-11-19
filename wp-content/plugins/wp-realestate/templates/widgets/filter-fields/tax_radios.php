<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


$output = WP_RealEstate_Abstract_Filter::hierarchical_tax_tree(0, 0, $name, $key, $field, $selected, 'radio' );

if ( !empty($output) ) {
?>
    <div class="form-group form-group-<?php echo esc_attr($key); ?> tax-radios-field">
        <?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
            <label for="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" class="heading-label">
                <?php echo wp_kses_post($field['name']); ?>
            </label>

        <?php } ?>
        <div class="form-group-inner">
            <?php echo $output; ?>
        </div>
    </div><!-- /.form-group -->
<?php }