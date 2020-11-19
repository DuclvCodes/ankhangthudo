<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="properties-listing-wrapper">
	<?php
	/**
	 * wp_realestate_before_property_archive
	 */
	do_action( 'wp_realestate_before_property_archive', $properties );
	?>

	<?php if ( $properties->have_posts() ) : ?>
		<?php
		/**
		 * wp_realestate_before_loop_property
		 */
		do_action( 'wp_realestate_before_loop_property', $properties );
		?>

		<div class="properties-wrapper">
			<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list' ); ?>
			<?php endwhile; ?>
		</div>

		<?php
		/**
		 * wp_realestate_after_loop_property
		 */
		do_action( 'wp_realestate_after_loop_property', $properties );

		WP_RealEstate_Mixes::custom_pagination( array(
			'max_num_pages' => $properties->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-realestate' ),
			'next_text'     => esc_html__( 'Next page', 'wp-realestate' ),
			'wp_query' 		=> $properties
		));
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No property found.', 'wp-realestate'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_realestate_before_property_archive
	 */
	do_action( 'wp_realestate_before_property_archive', $properties );
	?>
</div>