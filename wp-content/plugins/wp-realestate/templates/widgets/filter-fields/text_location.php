<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$latitude = isset($_GET['filter-center-latitude']) ? $_GET['filter-center-latitude'] : '';
$longitude = isset($_GET['filter-center-longitude']) ? $_GET['filter-center-longitude'] : '';
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
	    <div class="action-location">
		    <input type="text" name="<?php echo esc_attr($name); ?>" class="form-control" value="<?php echo esc_attr($selected); ?>" id="<?php echo esc_attr( $args['widget_id'] ); ?>_<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr(!empty($field['placeholder']) ? $field['placeholder'] : ''); ?>">
			<span class="find-me"></span>
			<span class="clear-location hidden"><i class="ti-close"></i></span>
		</div>
		<input type="hidden" name="filter-center-latitude" value="<?php echo esc_attr($latitude); ?>">
		<input type="hidden" name="filter-center-longitude" value="<?php echo esc_attr($longitude); ?>">
		
	</div>
</div><!-- /.form-group -->