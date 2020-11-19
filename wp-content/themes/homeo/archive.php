<?php
get_header();
$sidebar_configs = homeo_get_blog_layout_configs();

homeo_render_breadcrumbs();
?>
<section id="main-container" class="main-content  <?php echo apply_filters('homeo_blog_content_class', 'container');?> inner">
	<?php homeo_before_content( $sidebar_configs ); ?>
	<div class="row">
		<?php homeo_display_sidebar_left( $sidebar_configs ); ?>

		<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<div id="main" class="site-main layout-blog" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header hidden">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<?php
				$layout = homeo_get_config( 'blog_display_mode', 'list' );
				get_template_part( 'template-posts/layouts/'.$layout );

				// Previous/next page navigation.
				homeo_paging_nav();

			// If no content, include the "No posts found" template.
			else :
				get_template_part( 'template-posts/content', 'none' );

			endif;
			?>

			</div><!-- .site-main -->
		</div><!-- .content-area -->
		
		<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
		
	</div>
</section>
<?php get_footer(); ?>