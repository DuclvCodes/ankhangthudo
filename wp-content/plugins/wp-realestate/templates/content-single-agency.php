<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_agent_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<!-- Main content -->

	<?php do_action( 'wp_realestate_before_agent_content', get_the_ID() ); ?>

		<div class="row">
			<div class="col-sm-9">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/detail' ); ?>
			</div>
			<div class="col-sm-3">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/contact-form' ); ?>
			</div>
		</div>

		<!-- agency description -->
		<div class="agency-detail-description">
			<h3><?php esc_html_e('Description', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php the_content(); ?>
			</div>
		</div>

		<div class="agency-detail-properties">
			<h3><?php esc_html_e('Properties', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/properties' ); ?>
			</div>
		</div>

		<div class="agency-detail-agents">
			<h3><?php esc_html_e('Agents', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/agents' ); ?>
			</div>
		</div>

		<div class="agency-detail-location">
			<h3><?php esc_html_e('Location', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php
					$latitude = WP_RealEstate_Agency::get_post_meta( $post->ID, 'map_location_latitude', true );
					$longitude = WP_RealEstate_Agency::get_post_meta( $post->ID, 'map_location_longitude', true );
				?>
				<div class="location-maps-wrapper" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>"></div>
			</div>
		</div>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<!-- Review -->
			<?php comments_template(); ?>
		<?php endif; ?>
		
	<?php do_action( 'wp_realestate_after_agent_sidebar', get_the_ID() ); ?>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agent_detail', get_the_ID() ); ?>