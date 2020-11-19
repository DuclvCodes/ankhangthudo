<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);

$video = $meta_obj->get_post_meta('video');
?>
<?php if ( ! empty( $video ) ) : ?>
	<div class="property-section property-video">
		<h3><?php echo esc_html__( 'Video', 'wp-realestate' ); ?></h3>
		<div class="video-embed-wrapper">
			<?php
			if ( strpos($video, 'www.aparat.com') !== false ) {
			    $path = parse_url($video, PHP_URL_PATH);
				$matches = preg_split("/\/v\//", $path);
				
				if ( !empty($matches[1]) ) {
				    $output = '<iframe src="http://www.aparat.com/video/video/embed/videohash/'. $matches[1] . '/vt/frame"
				                allowFullScreen="true"
				                webkitallowfullscreen="true"
				                mozallowfullscreen="true"
				                height="720"
				                width="1280" >
				                </iframe>';

				    echo $output;
				}
		   	} else {
				echo apply_filters( 'the_content', '[embed width="1280" height="720"]' . esc_attr( $video ) . '[/embed]' );
			}

			?>
		</div>

		<?php do_action('wp-realestate-single-property-video', $post); ?>
	</div>
<?php endif; ?>