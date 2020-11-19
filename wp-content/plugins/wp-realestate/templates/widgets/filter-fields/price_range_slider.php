<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="clearfix form-group form-group-<?php echo esc_attr($key); ?>">
	
    <div class="form-group-inner">
		<?php
			$min_val = (!empty( $_GET[$name.'-from'] ) && $_GET[$name.'-from'] >= $min) ? $_GET[$name.'-from'] : $min;
			$max_val = (!empty( $_GET[$name.'-to'] ) && $_GET[$name.'-to'] <= $max) ? $_GET[$name.'-to'] : $max;
		?>
	  	<div class="from-to-wrapper">
	  		<?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
		    	<label for="<?php echo esc_attr($name); ?>" class="heading-label">
		    		<?php echo wp_kses_post($field['name']); ?>
		    	</label>
		    <?php } ?>
		    
			<span class="inner">
				<?php echo esc_html__('From','wp-realestate'); ?>
				<span class="from-text"><?php echo WP_RealEstate_Price::format_price($min_val, true); ?></span>
				<span class="space"><?php echo esc_html__('to','wp-realestate'); ?></span>
				<span class="to-text"><?php echo WP_RealEstate_Price::format_price($max_val, true); ?></span>
			</span>
		</div>
		<div class="price-range-slider" data-max="<?php echo esc_attr($max); ?>" data-min="<?php echo esc_attr($min); ?>"></div>
	  	<input type="hidden" name="<?php echo esc_attr($name.'-from'); ?>" class="filter-from" value="<?php echo esc_attr($min_val); ?>">
	  	<input type="hidden" name="<?php echo esc_attr($name.'-to'); ?>" class="filter-to" value="<?php echo esc_attr($max_val); ?>">
	  </div>
</div><!-- /.form-group -->