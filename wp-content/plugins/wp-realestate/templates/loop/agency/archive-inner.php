<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="agencies-listing-wrapper">
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

		<div class="agencies-wrapper">
			<?php while ( $agencies->have_posts() ) : $agencies->the_post(); ?>
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agencies-styles/inner-grid' ); ?>
			<?php endwhile;?>
		</div>

		<?php
		/**
		 * wp_realestate_after_loop_agency
		 */
		do_action( 'wp_realestate_after_loop_agency', $agencies );

		WP_RealEstate_Mixes::custom_pagination( array(
			'max_num_pages' => $agencies->max_num_pages,
			'prev_text'          => __( 'Previous page', 'wp-realestate' ),
			'next_text'          => __( 'Next page', 'wp-realestate' ),
			'wp_query' 		=> $agencies
		));

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No agency found.', 'wp-realestate'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_realestate_after_agency_archive
	 */
	do_action( 'wp_realestate_after_agency_archive', $agencies );
	?>
</div>