<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="form-group form-group-<?php echo esc_attr($key); ?>">
	
    <div class="form-group-inner inner">
	    
		<?php
			$distance_type = apply_filters( 'wp_realestate_filter_distance_type', 'miles' );
		?>
		<div class="search_distance_wrapper clearfix">
			<div class="search-distance-label">
				<?php
					$placeholder = !empty($field['placeholder']) ? $field['placeholder'] : esc_html__('Radius', 'wp-realestate');
					echo sprintf(wp_kses(__('%s: <span class="text-distance">%s</span> %s', 'wp-realestate'), array('span' => array('class' => array()))), $placeholder, $selected, $distance_type);
				?>
			</div>
			<div class="search-distance-wrapper">
				<input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_html($selected); ?>" />
				<div class="search-distance-slider"><div class="ui-slider-handle distance-custom-handle"></div></div>
			</div>
		</div>
	</div>
</div><!-- /.form-group -->