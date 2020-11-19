<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="agents-listing-wrapper">
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

		<div class="agents-wrapper">
			<?php while ( $agents->have_posts() ) : $agents->the_post(); ?>
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-grid' ); ?>
			<?php endwhile;?>
		</div>

		<?php
		/**
		 * wp_realestate_after_loop_agent
		 */
		do_action( 'wp_realestate_after_loop_agent', $agents );

		WP_RealEstate_Mixes::custom_pagination( array(
			'max_num_pages' => $agents->max_num_pages,
			'prev_text'          => __( 'Previous page', 'wp-realestate' ),
			'next_text'          => __( 'Next page', 'wp-realestate' ),
			'wp_query' 		=> $agents
		));

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No agent found.', 'wp-realestate'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_realestate_after_agent_archive
	 */
	do_action( 'wp_realestate_after_agent_archive', $agents );
	?>
</div>