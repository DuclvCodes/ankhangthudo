<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$rating = get_comment_meta( $comment->comment_ID, '_rating_avg', true );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="the-comment media">
		<div class="avatar media-left">
			<?php echo get_avatar( $comment->user_id, '80', '' ); ?>
		</div>
		<div class="comment-box media-body">

			<div class="star-rating clear pull-right" title="<?php echo sprintf( esc_attr__( 'Rated %d out of 5', 'wp-realestate' ), $rating ) ?>">
				<?php echo WP_RealEstate_Review::print_review($rating); ?>
				<span class="review-avg"><?php echo number_format((float)$rating, 1, '.', ''); ?></span>
			</div>
			
			<div class="meta">
				<div class="info-meta">
					<span class="commnet-author">
						<?php comment_author(); ?>
					</span>
					<?php if ( $comment->comment_approved == '0' ) : ?>
						<span class="meta"><em><?php esc_html_e( 'Your comment is awaiting approval', 'wp-realestate' ); ?></em></span>
					<?php else : ?>
						<span class="meta">
							<time><?php echo get_comment_date( get_option('date_format', 'd M, Y') ); ?></time>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<div itemprop="description" class="comment-text">
				<?php comment_text(); ?>
			</div>

		</div>
	</div>
</li>