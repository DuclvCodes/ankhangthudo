<?php
extract( $args );
extract( $instance );
global $post;
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}
$args = array(
	'post_type' => 'post',
	'posts_per_page' => $number_post
);
$query = new WP_Query($args);
if($query->have_posts()):
?>
<div class="post-widget">
<ul class="posts-list">
<?php
	while($query->have_posts()):$query->the_post();
?>
	<li>
		<article class="post">
            <div class="flex-middle">
                <?php
                    if(has_post_thumbnail()){
                ?>
                    <div class="image" >
                        <div class="image-inner">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'thumbnail'); ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
                <div class="inner">
                    <?php if (get_the_title()) { ?>
                        <h4 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    <?php } ?>
        			<div class="top-info-blog">
                        <div class="date">
                            <?php the_time( get_option('date_format', 'd M, Y') ); ?>
                        </div>
                    </div>
                </div>
            </div>
		</article>
	</li>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
</ul>
</div>
<?php endif; ?>