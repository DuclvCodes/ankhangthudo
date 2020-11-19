<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

$args = array(
    'limit' => $number_post,
    'get_properties_by' => $get_properties_by,
    'orderby' => $orderby,
    'order' => $order,
);

$loop = homeo_get_properties($args);
if ( $loop->have_posts() ):
?>
<div class="properties-sidebar">
	<?php
		while ( $loop->have_posts() ): $loop->the_post();
			get_template_part( 'template-properties/properties-styles/inner', 'list-simple');
	    endwhile;
    	wp_reset_postdata();
    ?>
</div>
<?php endif; ?>