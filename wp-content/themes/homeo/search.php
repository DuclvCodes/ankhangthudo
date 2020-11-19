<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.0
 */

get_header();
$sidebar_configs = homeo_get_blog_layout_configs();

$columns = homeo_get_config('blog_columns', 1);
$bscol = floor( 12 / $columns );
$_count  = 0;

homeo_render_breadcrumbs();
?>
<section id="main-container" class="main-content  <?php echo apply_filters('homeo_blog_content_class', 'container');?> inner">
		
	<a href="javascript:void(0)" class="mobile-sidebar-btn hidden-lg hidden-md"> <i class="fa fa-bars"></i></a>
	<div class="mobile-sidebar-panel-overlay"></div>
	<div class="row">
		<div id="main-content" class="col-xs-12 <?php echo esc_attr( is_active_sidebar( 'sidebar-default' ) ? 'col-md-8' : 'col-md-12'); ?>">
			<main id="main" class="site-main layout-blog" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header hidden">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->
				<div class="layout-posts-list">
					<?php
					// Start the Loop.
					while ( have_posts() ) : the_post();

						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						?>
							<?php get_template_part( 'content', 'search' ); ?>
						<?php
						$_count++;
					// End the loop.
					endwhile; ?>
				</div>
				<?php 
				// Previous/next page navigation.
				homeo_paging_nav();

			// If no content, include the "No posts found" template.
			else :
				get_template_part( 'template-posts/content', 'none' );

			endif;
			?>

			</main><!-- .site-main -->
		</div><!-- .content-area -->
		<?php if ( is_active_sidebar( 'sidebar-default' ) ): ?>
			<div class="col-md-4 col-sm-12 col-xs-12 ">
				<div class="sidebar sidebar-right">
		        	
		   			<?php dynamic_sidebar('sidebar-default'); ?>
			   		
	           	</div>
	        </div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>