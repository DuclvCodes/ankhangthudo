<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agencies_display_mode = homeo_get_agencies_display_mode();

$total = $agencies->found_posts;
$per_page = $agencies->query_vars['posts_per_page'];
$current = max( 1, $agencies->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );
?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">

	<?php if ( $agencies_display_mode == 'grid' ) {
		$columns = homeo_get_agencies_columns();
		$bcol = $columns ? 12/$columns : 4;
		$i = 0;
	?>
			<?php while ( $agencies->have_posts() ) : $agencies->the_post(); ?>
				<div class="col-sm-<?php echo esc_attr($bcol); ?> col-xs-12">
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agencies-styles/inner-grid' ); ?>
				</div>
			<?php endwhile; ?>
	<?php } else { ?>
		<?php while ( $agencies->have_posts() ) : $agencies->the_post(); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agencies-styles/inner-list' ); ?>
		<?php endwhile; ?>
	<?php } ?>

</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $agencies->max_num_pages ); ?></div>