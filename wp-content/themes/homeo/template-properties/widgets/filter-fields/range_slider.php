<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$suffix = wp_realestate_get_option('measurement_unit_area');
?>
<div class="form-group form-group-<?php echo esc_attr($key); ?>">
	
    <div class="form-group-inner">
		<?php
			$min_val = ! empty( $_GET[$name.'-from'] ) ? esc_attr( $_GET[$name.'-from'] ) : $min;
			$max_val = ! empty( $_GET[$name.'-to'] ) ? esc_attr( $_GET[$name.'-to'] ) : $max;
		?>
		<div class="from-to-wrapper">
			<?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
		    	<label for="<?php echo esc_attr($name); ?>" class="heading-label">
		    		<?php echo wp_kses_post($field['name']); ?>
		    	</label>
		    <?php } ?>
			<span class="inner">
				<?php echo esc_html__('From','homeo'); ?>
				<span class="from-text"><?php echo esc_html($min_val); ?></span>
				<span class="space"><?php echo esc_html__('to','homeo'); ?></span>
				<span class="to-text"><?php echo esc_html($max_val); ?></span>
				<?php echo trim(!empty($suffix) ? '<span class="suffix">'.$suffix.'</span>' :''); ?>
			</span>
		</div>
	  	<div class="main-range-slider" data-max="<?php echo esc_attr($max); ?>" data-min="<?php echo esc_attr($min); ?>"></div>

	  	<input type="hidden" name="<?php echo esc_attr($name.'-from'); ?>" class="filter-from" value="<?php echo esc_attr($min_val); ?>">
	  	<input type="hidden" name="<?php echo esc_attr($name.'-to'); ?>" class="filter-to" value="<?php echo esc_attr($max_val); ?>">
	</div>
</div><!-- /.form-group -->