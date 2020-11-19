<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'properties' => $properties
);

$layout_type = homeo_get_properties_layout_type();
if ( $layout_type == 'top-map' ) {
	$properties_page = WP_RealEstate_Mixes::get_properties_page_url();
	$display_mode = homeo_get_properties_display_mode();
	?>
	<div class="properties-display-mode-wrapper-ajax">
		<?php echo homeo_display_mode_form($display_mode, $properties_page); ?>
	</div>
	<?php
}
?>

<?php
	echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', $args);
?>

<?php echo WP_RealEstate_Template_Loader::get_template_part('loop/property/pagination', array('properties' => $properties) ); ?>

