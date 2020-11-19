<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.0
 */
/*
*Template Name: 404 Page
*/
get_header();
$icon = homeo_get_config('icon-img');
$bg = homeo_get_config('bg-img');
if( !empty($bg) && !empty($bg['url'])) {
	$style = " style='background-image: url(".esc_url($bg['url']).")' ";
}else{
	$style = '';
}
?>
<section class="page-404 justify-content-center flex-middle" <?php echo trim($style); ?>>
	<div id="main-container" class="inner">
		<div id="main-content" class="main-page">
			<section class="error-404 not-found clearfix">
				<div class="container">
					<div class="content-inner text-center">
						<div class="top-image">
							<?php if( !empty($icon) && !empty($icon['url'])) { ?>
								<img src="<?php echo esc_url( $icon['url']); ?>" alt="<?php bloginfo( 'name' ); ?>">
							<?php }else{ ?>
								<img src="<?php echo esc_url( get_template_directory_uri().'/images/error.png'); ?>" alt="<?php bloginfo( 'name' ); ?>">
							<?php } ?>
						</div>
						<div class="slogan">
							<h4 class="title-big">
								<?php
								$title = homeo_get_config('404_title');
								if ( !empty($title) ) {
									echo esc_html($title);
								} else {
									esc_html_e('Sorry About This, But it Seems We Are Unable to Find the Page You Are Looking for.', 'homeo');
								}
								?>
							</h4>
						</div>
						<div class="description">
							<?php
							$description = homeo_get_config('404_description');
							if ( !empty($description) ) {
								echo esc_html($description);
							} else {
								esc_html_e('Unfortunately the page you were looking for could not be found. It may be temporarily unavailable, moved or no longer exist. Check the Url you entered for any mistakes and try again.', 'homeo');
							}
							?>
						</div>
						<div class="page-content">
							<?php get_search_form(); ?>
							<div class="return">
								<a class="btn-theme btn btn-back-home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Back to Home','homeo') ?></a>
							</div>
						</div><!-- .page-content -->
					</div>
				</div>
			</section><!-- .error-404 -->
		</div><!-- .content-area -->
	</div>
</section>
<?php get_footer(); ?>