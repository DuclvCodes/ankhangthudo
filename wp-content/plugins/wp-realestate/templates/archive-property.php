<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<section id="primary" class="content-area">
	<main id="main" class="site-main content" role="main">
		<header class="page-header">
			<?php
			if ( is_tax() ) {
				?>
					<h1 class="page-title"><?php echo single_cat_title(); ?></h1>
				<?php
			} else {
				?>
				<h1 class="page-title"><?php post_type_archive_title( '', true ); ?></h1>
				<?php
			}
			?>
			<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
		</header><!-- .page-header -->

		<?php
			global $wp_query;
			echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', array('properties' => $wp_query));
		?>

	</main><!-- .site-main -->
</section><!-- .content-area -->

<?php get_footer();
