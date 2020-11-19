<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;

if ( ! comments_open() ) {
	return;
}

?>

<?php if ( have_comments() ) :
	$nb_reviews = WP_RealEstate_Review::get_total_reviews($post->ID);
	$rating = get_post_meta( $post->ID, '_average_rating', true );
?>
	<div id="comments">
		<div class="box-comment">
			<div class="review-title-wrapper flex-middle-sm">
				<h3 class="title"><?php echo sprintf(esc_html__('%d Reviews', 'homeo'), $nb_reviews); ?></h3>
				<div class="rating-wrapper ali-right">
					<?php WP_RealEstate_Review::print_review($rating, 'list', $nb_reviews); ?>
				</div>
			</div>

			<ol class="comment-list">
				<?php wp_list_comments( array( 'callback' => array( 'WP_RealEstate_Review', 'agent_comments' ) ) ); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="apus-pagination">';
				paginate_comments_links( apply_filters( 'wp_realestate_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>
		</div>
	</div>
<?php endif; ?>

<div id="reviews">
	<?php $commenter = wp_get_current_commenter(); ?>
	<div id="review_form_wrapper" class="commentform commentform-detail-agent">
		<div id="review_form">
			<?php
				$comment_form = array(
					'title_reply'          => have_comments() ? esc_html__( 'Add a review', 'homeo' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'homeo' ), get_the_title() ),
					'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'homeo' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<div class="row"><div class="col-xs-12 col-sm-6"><div class="form-group"><label>'.esc_html__( 'Name', 'homeo' ).'</label>'.
						            '<input id="author" placeholder="'.esc_attr__( 'Your Name', 'homeo' ).'" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></div></div>',
						'email'  => '<div class="col-xs-12 col-sm-6"><div class="form-group"><label>'.esc_html__( 'Email', 'homeo' ).'</label>' .
						            '<input id="email" placeholder="'.esc_attr__( 'your@mail.com', 'homeo' ).'" class="form-control" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></div></div></div>',
					),
					'label_submit'  => esc_html__( 'Submit Review', 'homeo' ),
					'logged_in_as'  => '',
					'comment_field' => ''
				);

				$comment_form['must_log_in'] = '<div class="must-log-in">' . esc_html__( 'You must be <a href="">logged in</a> to post a review.', 'homeo' ) . '</div>';
				
				$comment_form['comment_field'] .= '<div class="form-group"><label>'.esc_html__( 'Review', 'homeo' ).'</label><textarea id="comment" class="form-control" placeholder="'.esc_attr__( 'Write Comment', 'homeo' ).'" name="comment" cols="45" rows="5" aria-required="true"></textarea></div>';
				
				homeo_comment_form($comment_form);
			?>
		</div>
	</div>
</div>