<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$nb_columns = homeo_get_config('number_product_thumbs', 5);
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );

$thumbs_pos = homeo_get_config('product_thumbs_position', 'thumbnails-bottom');

$attachment_ids = $product->get_gallery_image_ids();
$count_thumbs = (!empty($attachment_ids) && has_post_thumbnail()) ? count($attachment_ids) + 1 : 1;
?>
<div class="apus-woocommerce-product-gallery-wrapper <?php echo esc_attr(($attachment_ids && has_post_thumbnail())?'':'full-width'); ?>">
    <?php
      $video = get_post_meta( $post->ID, 'apus_product_review_video', true );

      if (!empty($video)) {
        ?>
        <div class="video">
          <a href="<?php echo esc_url($video); ?>" class="popup-video">
            <i class="fa fa-play"></i>
            <span class="text-theme"><?php echo esc_html__('Watch video', 'homeo'); ?></span>
          </a>
        </div>
        <?php
      }
    ?>

	<div class="slick-carousel apus-woocommerce-product-gallery" data-carousel="slick" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="false" data-nav="false" data-slickparent="true">
		<?php
		
		if ( has_post_thumbnail() ) {
			$html  = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_attr__( 'Awaiting product image', 'homeo' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</div>
</div>
<?php if ( $attachment_ids && has_post_thumbnail() ) { ?>
	<div class="wrapper-thumbs <?php echo esc_attr($count_thumbs <= $nb_columns ? '' : ''); ?>">
		<div class="slick-carousel apus-woocommerce-product-gallery-thumbs <?php echo esc_attr($thumbs_pos == 'thumbnails-left' || $thumbs_pos == 'thumbnails-right' ? 'vertical' : ''); ?>" data-carousel="slick" data-items="<?php echo esc_attr($nb_columns); ?>" data-smallmedium="<?php echo esc_attr($nb_columns); ?>" data-extrasmall="<?php echo esc_attr($nb_columns); ?>" data-smallest="<?php echo esc_attr($nb_columns); ?>" data-pagination="false" data-nav="true" data-asnavfor=".apus-woocommerce-product-gallery" data-slidestoscroll="1" data-focusonselect="true" <?php echo trim($thumbs_pos == 'thumbnails-left' || $thumbs_pos == 'thumbnails-right' ? 'data-vertical="true"' : ''); ?>>
			<?php

			if ( has_post_thumbnail() ) {
				$html  = '<div class="woocommerce-product-gallery__image"><div class="thumbs-inner">';
				$html .= get_the_post_thumbnail( $post->ID, 'woocommerce_gallery_thumbnail' );
				$html .= '</div></div>';
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder"><div class="thumbs-inner">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_attr__( 'Awaiting product image', 'homeo' ) );
				$html .= '</div></div>';
			}

			echo apply_filters( 'homeo_woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

			
			foreach ( $attachment_ids as $attachment_id ) {
				$html  = '<div class="woocommerce-product-gallery__image"><div class="thumbs-inner">';
				$html .= wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false );
		 		$html .= '</div></div>';

				echo apply_filters( 'homeo_woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
			}

			?>
		</div>
	</div>
<?php } ?>