<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$properties_display_mode = homeo_get_properties_display_mode();
$property_inner_style = homeo_get_properties_inner_style();


$total = $properties->found_posts;
$per_page = $properties->query_vars['posts_per_page'];
$current = max( 1, $properties->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );
?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">
	<?php if ( $properties_display_mode == 'grid' ) {
		$columns = homeo_get_properties_columns();
		$bcol = $columns ? 12/$columns : 4;
	?>
			<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
				<div class="col-sm-<?php echo esc_attr($bcol); ?> col-xs-12">
					<?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-'.$property_inner_style ); ?>
				</div>
			<?php endwhile; ?>
	<?php } else { ?>
		<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
			<?php echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list' ); ?>
		<?php endwhile; ?>
	<?php } ?>
</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $properties->max_num_pages ); ?></div>