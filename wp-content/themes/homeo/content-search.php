<?php 
global $post;
$thumbsize = !isset($thumbsize) ? homeo_get_config( 'blog_item_thumbsize', 'full' ) : $thumbsize;
$thumb = homeo_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-list-item'); ?>>
    <div class="list-inner">
        <?php
            if ( !empty($thumb) ) {
                ?>
                <div class="top-image">
                    <?php homeo_post_categories_first($post); ?>
                    <?php
                        echo trim($thumb);
                    ?>
                 </div>
                <?php
            }
        ?>
        <div class="col-content">
            <?php if ( empty($thumb) ){ ?>
                <div class="top-info-blog">
                    <div class="category">
                        <?php homeo_post_categories_first($post); ?>
                    </div>
                </div>
            <?php } ?>
            
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
                        <div class="stick-icon text-theme"><i class="ti-pin2"></i></div>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
            <div class="description"><?php echo homeo_substring( get_the_excerpt(),55, '...' ); ?></div>
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
            <div class="date">
                <i class="flaticon-calendar"></i>
                <?php the_time( get_option('date_format', 'd M, Y') ); ?>
            </div>
            <div class="ali-right">
                <a href="<?php the_permalink(); ?>" class="btn-readmore"><?php echo esc_html__( 'Read More', 'homeo' )?><i class="fas fa-angle-right"></i></a>
            </div>
        </div>
    </div>
</article>