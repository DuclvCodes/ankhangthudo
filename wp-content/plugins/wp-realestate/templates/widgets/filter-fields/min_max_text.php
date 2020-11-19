<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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

	    <input type="number" name="<?php echo esc_attr($min_name); ?>" class="form-control"
	           value="<?php echo esc_attr($min_selected); ?>"
	           placeholder="<?php echo esc_attr( sprintf(__('Min %s', 'wp-realestate'), $field['name']) ); ?>">


       <input type="number" name="<?php echo esc_attr($max_name); ?>" class="form-control"
	           value="<?php echo esc_attr($max_selected); ?>"
	           placeholder="<?php echo esc_attr( sprintf(__('Max %s', 'wp-realestate'), $field['name']) ); ?>">
	</div>


</div><!-- /.form-group -->
