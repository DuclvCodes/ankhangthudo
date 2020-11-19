<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="agents-pagination-wrapper">
	<?php
		WP_RealEstate_Mixes::custom_pagination( array(
			'max_num_pages' => $agents->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-realestate' ),
			'next_text'     => esc_html__( 'Next page', 'wp-realestate' ),
			'wp_query' 		=> $agents
		));
	?>
</div>
