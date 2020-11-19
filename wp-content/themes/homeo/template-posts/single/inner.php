<?php
$post_format = get_post_format();
global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="inner <?php echo esc_attr( !empty( get_the_content())?'has-content':''); ?>">
    	<div class="entry-content-detail <?php echo esc_attr((!has_post_thumbnail())?'not-img-featured':'' ); ?>">
            <?php homeo_post_categories_first($post); ?>
            <?php if (get_the_title()) { ?>
                <h1 class="entry-title">
                    <?php the_title(); ?>
                </h1>
            <?php } ?>

            <div class="top-detail-info clearfix">

                <div class="author-wrapper">
                    <div class="flex-middle">
                        <div class="avatar-img">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ),40 ); ?>
                        </div>
                        <div class="right-inner">
                            <h4 class="author-title">
                                <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                    <?php echo get_the_author(); ?>
                                </a>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="date">
                    <i class="flaticon-calendar"></i>
                    <?php the_time( get_option('date_format', 'd M, Y') ); ?>
                </div>

                <span class="comments"><i class="flaticon-chat"></i><?php comments_number( esc_html__('0 Comments', 'homeo'), esc_html__('1 Comment', 'homeo'), esc_html__('% Comments', 'homeo') ); ?></span>
            </div>

            <?php if(has_post_thumbnail()) { ?>
                <div class="entry-thumb">
                    <?php
                        $thumb = homeo_post_thumbnail();
                        echo trim($thumb);
                    ?>
                </div>
            <?php } ?>

        	<div class="single-info info-bottom">
                <div class="entry-description">
                    <?php
                        the_content();
                    ?>
                </div><!-- /entry-content -->
        		<?php
        		wp_link_pages( array(
        			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'homeo' ) . '</span>',
        			'after'       => '</div>',
        			'link_before' => '<span>',
        			'link_after'  => '</span>',
        			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'homeo' ) . ' </span>%',
        			'separator'   => '',
        		) );
        		?>
                <?php
                    $posttags = get_the_tags();
                ?>
                <?php if( !empty($posttags) || homeo_get_config('show_blog_social_share', false) ){ ?>
            		<div class="tag-social flex-middle-sm">

            			<?php if( homeo_get_config('show_blog_social_share', false) ) {
            				get_template_part( 'template-parts/sharebox' );
            			} ?>
                        <?php if(!empty($posttags)){ ?>
                            <div class="ali-right">
                                <?php homeo_post_tags(); ?>
                            </div>
                        <?php } ?>

            		</div>
                <?php } ?>
        	</div>

        </div>
    </div>
    <?php
        //Previous/next post navigation.
        the_post_navigation( array(
            'next_text' => '<span class="meta-nav"><i class="flaticon-next"></i></span> ' .
                '<div class="inner">'.
                '<div class="navi">' . esc_html__( 'Tiếp theo', 'homeo' ) . '</div>'.
                '<span class="title-direct">%title</span></div>',
            'prev_text' => '<span class="meta-nav"><i class="flaticon-back"></i></span> ' .
                '<div class="inner">'.
                '<div class="navi"> ' . esc_html__( 'Trước đó', 'homeo' ) . '</div>'.
                '<span class="title-direct">%title</span></div>',
        ) );
    ?>
</article>
