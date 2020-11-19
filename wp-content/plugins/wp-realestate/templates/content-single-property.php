<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_property_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- heading -->
	<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/header' ); ?>

	<!-- Main content -->
	<div class="row">
		<div class="col-sm-9">

			<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>

			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/description' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/energy' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/detail' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/amenities' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/floor-plans' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/subproperties' ); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/releated' ); ?>

			
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<!-- Review -->
				<?php comments_template(); ?>
			<?php endif; ?>

			<?php do_action( 'wp_realestate_after_property_content', $post->ID ); ?>
		</div>
		<div class="col-sm-3">
			<?php do_action( 'wp_realestate_before_property_sidebar', $post->ID ); ?>
			<!-- property detail agent -->
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/agent-detail' ); ?>
			<!-- property detail -->
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/map-location' ); ?>

			<?php do_action( 'wp_realestate_after_property_sidebar', $post->ID ); ?>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_property_detail', $post->ID ); ?>