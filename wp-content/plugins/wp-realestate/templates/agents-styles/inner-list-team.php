<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$address = get_post_meta( $post->ID, WP_REALESTATE_AGENT_PREFIX . 'address', true );

?>

<?php do_action( 'wp_realestate_before_agent_content', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('agent-team'); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
        <div class="agent-thumbnail">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </div>
    <?php } ?>
    <div class="agent-information">
    	
		<?php the_title( sprintf( '<h2 class="entry-title agent-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

        <?php if ( $address ) { ?>
            <div class="agent-location"><?php echo $address; ?></div>
        <?php } ?>

	</div>
    
    <a href="javascript:void(0);" class="btn btn-agency-remove-agent" data-agent_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-agency-remove-agent-nonce' )); ?>"><?php esc_html_e('Remove', 'wp-realestate'); ?></a>

</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agent_content', $post->ID ); ?>