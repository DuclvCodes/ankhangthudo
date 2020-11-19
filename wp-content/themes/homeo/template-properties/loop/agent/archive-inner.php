<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agents_display_mode = homeo_get_agents_display_mode();
?>
<div class="agents-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($agents_display_mode); ?>">
	<?php
	/**
	 * wp_realestate_before_agent_archive
	 */
	do_action( 'wp_realestate_before_agent_archive', $agents );
	?>

	<?php
	if ( !empty($agents) && !empty($agents->posts) ) {

		/**
		 * wp_realestate_before_loop_agent
		 */
		do_action( 'wp_realestate_before_loop_agent', $agents );
		?>

		<div class="agents-wrapper items-wrapper clearfix">
			
			<?php if ( $agents_display_mode == 'grid' ) {
				$columns = homeo_get_agents_columns();
				$bcol = $columns ? 12/$columns : 4;
				$i = 0;
			?>
				<div class="row">
					<?php while ( $agents->have_posts() ) : $agents->the_post(); ?>
						<div class="col-sm-6 col-md-<?php echo esc_attr($bcol); ?> col-xs-12 <?php echo esc_attr(($i%$columns == 0)?'lg-clearfix md-clearfix':'') ?> <?php echo esc_attr(($i%2 == 0)?'sm-clearfix':'') ?>">
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-grid' ); ?>
						</div>
					<?php $i++; endwhile; ?>
				</div>
			<?php } else { ?>
				<?php while ( $agents->have_posts() ) : $agents->the_post(); ?>
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-list' ); ?>
				<?php endwhile; ?>
			<?php } ?>
			

		</div>

		<?php
		/**
		 * wp_realestate_after_loop_agent
		 */
		do_action( 'wp_realestate_after_loop_agent', $agents );
		
		
		
		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found text-center"><?php esc_html_e('No agent found.', 'homeo'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_realestate_after_agent_archive
	 */
	do_action( 'wp_realestate_after_agent_archive', $agents );
	?>
</div>