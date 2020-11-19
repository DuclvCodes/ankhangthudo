<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'wp_realestate_before_agent_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<!-- Main content -->

	<?php do_action( 'wp_realestate_before_agent_content', get_the_ID() ); ?>

		<div class="row">
			<div class="col-sm-9">

				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/detail' ); ?>
				
			</div>
			<div class="col-sm-3">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/contact-form' ); ?>
			</div>
		</div>

		<!-- agent description -->
		<div class="agent-detail-description">
			<h3><?php esc_html_e('Agent Description', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php the_content(); ?>
			</div>
		</div>

		<div class="agent-detail-description">
			<h3><?php esc_html_e('Agent Properties', 'wp-realestate'); ?></h3>
			<div class="inner">
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/properties' ); ?>
			</div>
		</div>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<!-- Review -->
			<?php comments_template(); ?>
		<?php endif; ?>
		
	<?php do_action( 'wp_realestate_after_agent_sidebar', get_the_ID() ); ?>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agent_detail', get_the_ID() ); ?>