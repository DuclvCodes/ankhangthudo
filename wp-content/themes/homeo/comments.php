<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<div class="box-comment">
	        <h3 class="comments-title"><?php comments_number( esc_html__('0 Comments', 'homeo'), esc_html__('1 Comment', 'homeo'), esc_html__('% Comments', 'homeo') ); ?></h3>
			<ol class="comment-list">
				<?php wp_list_comments('callback=homeo_comment_item'); ?>
			</ol><!-- .comment-list -->

			<?php homeo_comment_nav(); ?>
		</div>
	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'homeo' ); ?></p>
	<?php endif; ?>

	<?php
        $aria_req = ( $req ? " aria-required='true'" : '' );
        $comment_args = array(
                        'title_reply'=> '<h4 class="title">'.esc_html__('Để lại bình luận','homeo').'</h4>',
                        'comment_field' => '<div class="form-group space-comment">
                        						<label class="hidden">'.esc_html__('Bình luận', 'homeo').'</label>
                                                <textarea rows="7" id="comment" placeholder="'.esc_attr__('Write Your Comment', 'homeo').'" class="form-control"  name="comment"'.$aria_req.'></textarea>
                                            </div>',
                        'fields' => apply_filters(
                        	'comment_form_default_fields',
	                    		array(
	                                'author' => '<div class="row"><div class="col-sm-6 col-xs-12"><div class="form-group ">
	                                			<label class="hidden">'.esc_html__('Name', 'homeo').'</label>
	                                            <input type="text" name="author" placeholder="'.esc_attr__('Name', 'homeo').'" class="form-control" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' />
	                                            </div></div>',
	                                'email' => ' <div class="col-sm-6 col-xs-12"><div class="form-group ">
	                                			<label class="hidden">'.esc_html__('Email', 'homeo').'</label>
	                                            <input id="email"  name="email" placeholder="'.esc_attr__('Email', 'homeo').'" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" ' . $aria_req . ' />
	                                            </div></div>',
	                                'Website' => ' <div class="col-xs-12 col-sm-4 hidden"><div class="form-group ">
	                                            <input id="website" name="website" placeholder="'.esc_attr__('Website', 'homeo').'" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" ' . $aria_req . ' />
	                                            </div></div></div>',
	                            )
							),
	                        'label_submit' => esc_html__('Submit Comment', 'homeo'),
							'comment_notes_before' => '',
							'comment_notes_after' => '',
                        );
    ?>

	<?php homeo_comment_form($comment_args); ?>
</div><!-- .comments-area -->
