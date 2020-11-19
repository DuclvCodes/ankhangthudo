<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="properties-pagination-wrapper main-pagination-wrapper">
	<?php
		$pagination_type = homeo_get_properties_pagination();
		if ( $pagination_type == 'loadmore' || $pagination_type == 'infinite' ) {
			$next_link = get_next_posts_link( '&nbsp;', $properties->max_num_pages );
			if ( $next_link ) {
		?>
				<div class="ajax-pagination <?php echo trim($pagination_type == 'loadmore' ? 'loadmore-action' : 'infinite-action'); ?>">
					<div class="apus-pagination-next-link hidden"><?php echo wp_kses_post($next_link); ?></div>
					<a href="#" class="apus-loadmore-btn"><?php esc_html_e( 'Load more', 'homeo' ); ?></a>
					<span class="apus-allproducts"><?php esc_html_e( 'All properties loaded.', 'homeo' ); ?></span>
				</div>
		<?php
			}
		} else {
			WP_RealEstate_Mixes::custom_pagination( array(
				'max_num_pages' => $properties->max_num_pages,
				'prev_text'     => '<i class=" ti-angle-left"></i>',
				'next_text'     => '<i class=" ti-angle-right"></i>',
				'wp_query' => $properties
			));
		}
	?>
</div>
