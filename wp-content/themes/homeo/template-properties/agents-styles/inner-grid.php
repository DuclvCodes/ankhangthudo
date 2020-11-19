<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_realestate_before_agent_content', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="agent-grid agent-item">
        <div class="top-info">
        	<?php if ( has_post_thumbnail() ) { ?>
                <div class="member-thumbnail-wrapper flex-middle justify-content-center">
                    <?php homeo_agent_display_image( $post ,'homeo-agent-grid'); ?>
                    <?php homeo_agent_display_nb_properties($post); ?>
                </div>
            <?php } ?>

            <div class="agent-information">
            	
        		<?php the_title( sprintf( '<h2 class="agent-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                <?php homeo_agent_display_job( $post ); ?>
                <div class="metas">
                    <?php homeo_agent_display_phone($post, 'title'); ?>
                    <?php homeo_agent_display_fax($post, 'title'); ?>
                    <?php homeo_agent_display_email($post, 'title'); ?>
                    <?php homeo_agent_display_website($post, 'title'); ?>
                </div>

        	</div>
        </div>
        <div class="agent-information-bottom flex-middle">
            <?php homeo_agent_display_socials($post); ?>
            <div class="ali-right">
                <a href="<?php the_permalink(); ?>" class="view-my-listings text-theme"><?php esc_html_e('View My Listings', 'homeo'); ?><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agent_content', $post->ID ); ?>