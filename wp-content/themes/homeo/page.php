<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.0
 */

get_header();
$sidebar_configs = homeo_get_page_layout_configs();

homeo_render_breadcrumbs();

?>

<section id="main-container" class="<?php echo apply_filters('homeo_page_content_class', 'container');?> inner">
	<?php homeo_before_content( $sidebar_configs ); ?>
	<div class="row">
		<?php homeo_display_sidebar_left( $sidebar_configs ); ?>
		<div id="main-content" class="main-page <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
			<div id="main" class="site-main clearfix" role="main">

				<?php
				// Start the loop.
				while ( have_posts() ) : the_post();
					
					// Include the page content template.
					the_content();

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				// End the loop.
				endwhile;
				?>
			</div><!-- .site-main -->
			<?php
    		wp_link_pages( array(
    			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'homeo' ) . '</span>',
    			'after'       => '</div>',
    			'link_before' => '<span>',
    			'link_after'  => '</span>',
    			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'homeo' ) . ' </span>%',
    			'separator'   => '',
    		) );
    		?>
		</div><!-- .content-area -->
		<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
	</div>
</section>
<?php get_footer(); ?>