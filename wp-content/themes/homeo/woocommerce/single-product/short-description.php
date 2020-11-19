<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
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
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}
$layout = homeo_get_config('product_single_version', 'v1');
?>
<div class="woocommerce-product-details__short-description-wrapper <?php echo esc_attr($layout == 'v2' ? 'v2' : ''); ?>">
	<div class="woocommerce-product-details__short-description <?php echo esc_attr($layout == 'v2' ? 'hideContent' : ''); ?>">
	    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
	</div>
	<?php if ( $layout == 'v2' ) { ?>
		<a href="javascript:void(0);" class="view-more-desc view-more"><span><?php esc_html_e('View More', 'homeo'); ?></span> <i class="fa fa-angle-double-right"></i></a>
	<?php } ?>
</div>