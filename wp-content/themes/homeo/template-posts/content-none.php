<?php
/**
 *
 * If no content, include the "No posts found" template.
 * @since 1.0
 * @version 1.0.0
 *
 */
?>
<article id="post-0" class="post no-results not-found">
	<div class="entry-content e-entry-content">
		<h2 class="title-no-results">
			<?php esc_html_e( 'Nothing Found', 'homeo' ) ?>
		</h2>
		<div><?php esc_html_e( 'Try again please, use the search form below.', 'homeo' ); ?></div>
		<?php get_search_form(); ?>
	</div>
	<!-- entry-content -->
</article><!-- /article -->