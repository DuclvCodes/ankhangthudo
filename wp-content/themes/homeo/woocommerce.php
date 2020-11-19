<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
get_header();

$sidebar_configs = homeo_get_woocommerce_layout_configs();
?>

<?php do_action( 'homeo_woo_template_main_before' ); ?>

<section id="main-container" class="main-content <?php echo apply_filters('homeo_woocommerce_content_class', 'container');?>">
	<?php homeo_before_content( $sidebar_configs ); ?>
	<div class="row">
		<?php homeo_display_sidebar_left( $sidebar_configs ); ?>

		<div id="main-content" class="archive-shop col-xs-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">

			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">

					<?php  woocommerce_content(); ?>

				</div><!-- #content -->
			</div><!-- #primary -->
		</div><!-- #main-content -->

		<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
		
	</div>
</section>
<?php
get_footer();