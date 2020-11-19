<?php 
global $post;
$thumbsize = !isset($thumbsize) ? homeo_get_config( 'blog_item_thumbsize', 'full' ) : $thumbsize;
$thumb = homeo_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-grid post-grid-v2'); ?>>
    <div class="list-inner">
        <?php
            if ( !empty($thumb) ) {
                ?>
                <div class="top-image">
                    <?php
                        echo trim($thumb);
                    ?>
                 </div>
                <?php
            }
        ?>
        <div class="col-content">
            <?php homeo_post_categories_first($post); ?>
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
        </div>
        <div class="info-bottom flex-middle">
            <div class="author-wrapper flex-middle">
                <div class="avatar-img">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ),40 ); ?>
                </div>
                <div class="right-inner">
                    <h4 class="author-title">
                        <a href="<?php the_permalink(); ?>">
                            <?php echo get_the_author(); ?>
                        </a>
                    </h4>
                </div>
            </div>
            <div class="ali-right">
                <div class="date">
                    <?php the_time( get_option('date_format', 'd M, Y') ); ?>
                </div>
            </div>
        </div>
    </div>
</article>