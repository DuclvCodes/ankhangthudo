<?php
$product_item = isset($product_item) ? $product_item : 'inner';

wc_set_loop_prop( 'loop', 0 );
wc_set_loop_prop( 'columns', $columns );

$classes = array();
if ( $columns == 5 ) {
	$bcol = 'cus-5';
} else {
	$bcol = 12/$columns;
}
if ($columns > 4) {
	$classes[] = 'col-desktop-4 col-lg-'.$bcol.' col-md-3'.( $columns > 1 ? ' col-sm-6 col-xs-6' : '');
} else {
	$classes[] = 'col-lg-'.$bcol.' col-md-'.$bcol.( $columns > 1 ? ' col-sm-6 col-xs-6' : '');
}
?>
<div class="products products-grid">
	<div class="row row-products">
		
		<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
			<div <?php post_class( $classes ); ?>>
			 	<?php wc_get_template_part( 'item-product/'.$product_item ); ?>
			</div>
		<?php endwhile; ?>

	</div>
</div>
<?php wp_reset_postdata(); ?>