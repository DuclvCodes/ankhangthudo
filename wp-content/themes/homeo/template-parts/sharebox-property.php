<?php
global $post;
wp_enqueue_script('addthis');
if ( !homeo_get_config('show_property_social_share', true) ) {
	return;
}
?>
<div class="social-property">
	<a href="javascript:void(0);" class="btn-add-social">
		<i class="flaticon-share"></i>
		<span><?php esc_html_e('Share', 'homeo'); ?></span>
	</a>
	<div class="bo-social-icons">
		<?php if ( homeo_get_config('facebook_share', 1) ): ?>
 			<a href="javascript:void(0);" data-original-title="facebook" class="bo-social-facebook addthis_button_facebook" data-url="<?php echo esc_url(get_permalink($post)); ?>" data-title="<?php echo esc_attr(get_the_title($post)); ?>"><i class="fab fa-facebook-f"></i></a>
		<?php endif; ?>
		<?php if ( homeo_get_config('twitter_share', 1) ): ?>
 			<a href="javascript:void(0);" data-original-title="twitter" class="bo-social-twitter addthis_button_twitter"><i class="fab fa-twitter"></i></a>
		<?php endif; ?>
		<?php if ( homeo_get_config('linkedin_share', 1) ): ?>
 			<a href="javascript:void(0);" data-original-title="linkedin" class="bo-social-linkedin addthis_button_linkedin"><i class="fab fa-linkedin-in"></i></a>
		<?php endif; ?>
		
		<?php if ( homeo_get_config('pinterest_share', 1) ): ?>
 			<a href="javascript:void(0);" data-original-title="pinterest" class="bo-social-pinterest addthis_button_pinterest"><i class="fab fa-pinterest-p"></i></a>
		<?php endif; ?>

		<?php if ( homeo_get_config('more_share', 1) ): ?>
			<a href="javascript:void(0);" data-original-title="share_more" class="bo-social-pinterest addthis_button_compact"><i class="fas fa-ellipsis-h"></i></a>
		<?php endif; ?>
	</div>
</div>