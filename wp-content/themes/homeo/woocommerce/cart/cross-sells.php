<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>

	<div class="cross-sells products widget">
		<h2 class="widget-title"><?php esc_html_e( 'You may be interested in&hellip;', 'homeo' ) ?></h2>

		<div class="slick-carousel products" data-carousel="slick" data-items="3"
		    data-smallmedium="2"
		    data-extrasmall="1"
		    data-pagination="false" data-nav="true" data-rows="1">

		    <?php wc_set_loop_prop( 'loop', 0 ); ?>
		    
		    <?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
					$post_object = get_post( $cross_sell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );
				?>
				<div class="product clearfix">
	                <?php wc_get_template_part( 'item-product/inner' ); ?>
	            </div>
			<?php endforeach; ?>
		</div>
		
	</div>
	<?php
endif;

wp_reset_postdata();
