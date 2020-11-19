<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script( 'jquery-datetimepicker', WP_REALESTATE_PLUGIN_URL . '/assets/js/jquery.datetimepicker.full.min.js', array( 'jquery' ), '20150330', true );
wp_enqueue_style( 'jquery-datetimepicker', WP_REALESTATE_PLUGIN_URL . '/assets/css/jquery.datetimepicker.min.css' );

$selected_form = !empty($selected['from']) ? $selected['from'] : '';
$selected_to = !empty($selected['to']) ? $selected['to'] : '';
?>
<div class="form-group form-group-<?php echo esc_attr($key); ?>">
	<?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
    	<label for="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" class="heading-label">
    		<?php echo wp_kses_post($field['name']); ?>
    	</label>
    <?php } ?>
    <div class="form-group-inner inner">
    	<div class="row row-20">
    		<div class="col-xs-6">
			    <div class="date-from-wrapper">
				    <?php if ( !empty($field['icon']) ) { ?>
				    	<i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
				    <?php } ?>

				    <input type="text" name="<?php echo esc_attr($name); ?>[from]" class="form-control field-datetimepicker"
				           value="<?php echo esc_attr($selected_form); ?>"
				           id="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr(!empty($field['placeholder']) ? $field['placeholder'] : ''); ?> <?php esc_attr_e('Form', 'homeo'); ?>">
				</div>
			</div>
			<div class="col-xs-6">
				<div class="date-to-wrapper">
				    <?php if ( !empty($field['icon']) ) { ?>
				    	<i class="<?php echo esc_attr( $field['icon'] ); ?>"></i>
				    <?php } ?>
				    <input type="text" name="<?php echo esc_attr($name); ?>[to]" class="form-control field-datetimepicker"
				           value="<?php echo esc_attr($selected_to); ?>" placeholder="<?php echo esc_attr(!empty($field['placeholder']) ? $field['placeholder'] : ''); ?> <?php esc_attr_e('To', 'homeo'); ?>">
				</div>
			</div>
		</div>
	</div>

</div><!-- /.form-group -->