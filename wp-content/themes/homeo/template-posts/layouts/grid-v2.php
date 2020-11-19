<?php
	$columns = homeo_get_config('blog_columns', 1);
	$bcol = floor( 12 / $columns );
	$count = 1;
?>
<div class="layout-blog">
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-md-<?php echo esc_attr($bcol); ?> col-xs-12 col-sm-6 <?php echo esc_attr(($count%$columns)==1?' md-clearfix':''); ?> <?php echo esc_attr(($count%2)==1?' sm-clearfix ':''); ?>">
                <?php get_template_part( 'template-posts/loop/inner-grid-v2' ); ?>
            </div>
        <?php $count++; endwhile; ?>
    </div>
</div>