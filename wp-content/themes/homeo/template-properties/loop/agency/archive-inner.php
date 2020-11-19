<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agencies_display_mode = homeo_get_agencies_display_mode();
?>
<div class="agencies-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($agencies_display_mode); ?>">
	<?php
	/**
	 * wp_realestate_before_agency_archive
	 */
	do_action( 'wp_realestate_before_agency_archive', $agencies );
	?>

	<?php
	if ( !empty($agencies) && !empty($agencies->posts) ) {

		/**
		 * wp_realestate_before_loop_agency
		 */
		do_action( 'wp_realestate_before_loop_agency', $agencies );
		?>

		<div class="agencies-wrapper items-wrapper clearfix">
			
			<?php if ( $agencies_display_mode == 'grid' ) {
				$columns = homeo_get_agencies_columns();
				$bcol = $columns ? 12/$columns : 4;
				$i = 0;
			?>
				<div class="row">
					<?php while ( $agencies->have_posts() ) : $agencies->the_post(); ?>
						<div class="col-sm-6 col-md-<?php echo esc_attr($bcol); ?> col-xs-12 <?php echo esc_attr(($i%$columns == 0)?'lg-clearfix md-clearfix':'') ?> <?php echo esc_attr(($i%2 == 0)?'sm-clearfix':'') ?>">
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agencies-styles/inner-grid' ); ?>
						</div>
					<?php $i++; endwhile; ?>
				</div>
			<?php } else { ?>
				<?php while ( $agencies->have_posts() ) : $agencies->the_post(); ?>
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agencies-styles/inner-list' ); ?>
				<?php endwhile; ?>
			<?php } ?>

		</div>

		<?php
		/**
		 * wp_realestate_after_loop_agency
		 */
		do_action( 'wp_realestate_after_loop_agency', $agencies );
		
		

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found text-center"><?php esc_html_e('No agency found.', 'homeo'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_realestate_after_agency_archive
	 */
	do_action( 'wp_realestate_after_agency_archive', $agencies );
	?>
</div>