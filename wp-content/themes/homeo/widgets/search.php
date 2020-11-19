<?php 
extract( $args );
extract( $instance );
$title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';

if ( $title ) {
    echo trim($before_title)  .trim( $title ) . $after_title;
}
?>
<div class="widget-search">
    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		
	  	<div class="input-group">
	  		<input type="text" placeholder="<?php esc_attr_e( 'Search', 'homeo' ); ?>" name="s" class="apus-search form-control"/>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-sm btn-search"><i class="flaticon-magnifying-glass"></i></button>
			</span>
	  	</div>
		<?php if ( isset($post_type) && $post_type ): ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" class="post_type" />
		<?php endif; ?>
	</form>
</div>