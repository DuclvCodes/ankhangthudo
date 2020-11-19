<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$agents_display_mode = homeo_get_agents_display_mode();

$total = $agents->found_posts;
$per_page = $agents->query_vars['posts_per_page'];
$current = max( 1, $agents->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );
?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">
	
	<?php if ( $agents_display_mode == 'grid' ) {
		$columns = homeo_get_agents_columns();
		$bcol = $columns ? 12/$columns : 4;
		$i = 0;
	?>
			<?php while ( $agents->have_posts() ) : $agents->the_post(); ?>
				<div class="col-sm-<?php echo esc_attr($bcol); ?> col-xs-12">
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-grid' ); ?>
				</div>
			<?php endwhile; ?>
	<?php } else { ?>
		<?php while ( $agents->have_posts() ) : $agents->the_post(); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-list' ); ?>
		<?php endwhile; ?>
	<?php } ?>
	
</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $agents->max_num_pages ); ?></div>