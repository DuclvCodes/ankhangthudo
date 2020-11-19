<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$properties_display_mode = homeo_get_properties_display_mode();
$property_inner_style = homeo_get_properties_inner_style();
$layout_type = homeo_get_properties_layout_type();
?>
<div class="properties-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($properties_display_mode); ?>">
	<?php
	/**
	 * wp_realestate_before_property_archive
	 */
	do_action( 'wp_realestate_before_property_archive', $properties );
	?>

	<?php if ( !empty($properties) && !empty($properties->posts) ) : ?>
		<?php
		/**
		 * wp_realestate_before_loop_property
		 */
		do_action( 'wp_realestate_before_loop_property', $properties );
		?>
		<div class="properties-wrapper items-wrapper clearfix">
			<?php if ( $properties_display_mode == 'grid' ) {
				$columns = homeo_get_properties_columns();
				$bcol = $columns ? 12/$columns : 3;
				if($layout_type == 'half-map'){
					$ct = ($columns && $columns >= 2) ? 6 : 1;
				}else{
					$ct = '12';
				}
				$i = 0;
			?>
				<div class="row">
					<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
						<div class="col-sm-6 col-md-<?php echo esc_attr($bcol); ?> col-ct-<?php echo esc_attr($ct); ?> col-xs-12 <?php echo esc_attr(($i%$columns == 0)?'lg-clearfix md-clearfix':'') ?> <?php echo esc_attr(($i%2 == 0)?'sm-clearfix':'') ?>">
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-'.$property_inner_style ); ?>
						</div>
					<?php $i++; endwhile; ?>
				</div>
			<?php } else { ?>
				<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list' ); ?>
				<?php endwhile; ?>
			<?php } ?>
		</div>

		<?php
		/**
		 * wp_realestate_after_loop_property
		 */
		do_action( 'wp_realestate_after_loop_property', $properties );
		
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found text-center"><?php esc_html_e('No property found.', 'homeo'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_realestate_after_property_archive
	 */
	do_action( 'wp_realestate_after_property_archive', $properties );
	?>
</div>