<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_property_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('property-single-layout property-single-v2'); ?>>
	<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/gallery-v2', array('gallery_size' => 'homeo-gallery-v2-large') ); ?>
	<div class="<?php echo apply_filters('homeo_property_content_class', 'container');?>">
		<!-- Main content -->
		<div class="content-property-detail content-property-detail-v2">
			
			<?php if ( is_active_sidebar( 'property-single-sidebar' ) ): ?>
				<a href="javascript:void(0)" class="mobile-sidebar-btn space-10 hidden-lg hidden-md btn-right"><i class="ti-menu-alt"></i> </a>
				<div class="mobile-sidebar-panel-overlay"></div>
			<?php endif; ?>

			<div class="row">
				<div class="property-detail-main col-xs-12 col-md-<?php echo esc_attr( is_active_sidebar( 'property-single-sidebar' ) ? 8 : 12); ?>">

					<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>
					<div id="property-single-details">
						<?php
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/header-v2' );

						if ( homeo_get_config('show_property_description', true) ) {
							echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/description' );
						}
						
						if ( homeo_get_config('show_property_energy', true) ) {
							echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/energy' );
						}
						?>

						<?php
						if ( homeo_get_config('show_property_detail', true) ) {
							echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/detail' );
						}
						?>

						<?php
						if ( homeo_get_config('show_property_attachments', true) ) {
							echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/attachments' );
						}
						?>
					</div>

					<?php
					if ( homeo_get_config('show_property_amenities', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/amenities' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_materials', false) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/materials' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_location', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/location' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_floor-plans', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/floor-plans' );
					}
					?>
					
					<?php
					if ( homeo_get_config('show_property_tabs-video-virtual', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/tabs-video-virtual' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_facilities', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/facilities' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_valuation', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/valuation' );
					}
					?>
					
					<?php
					if ( homeo_get_config('show_property_stats_graph', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/stats_graph' );
					}
					?>
					
					<?php
					if ( homeo_get_config('show_property_nearby_yelp', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/nearby_yelp' );
					}
					?>

					<?php
					if ( homeo_get_config('show_property_walk_score', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/walk_score' );
					}
					?>

					
					<?php if ( WP_RealEstate_Review::review_enable() ) { ?>
							<?php comments_template(); ?>
					<?php } ?>

					<?php
					if ( homeo_get_config('show_property_related', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/related' );
					}
					?>
					
					<?php
					if ( homeo_get_config('show_property_subproperties', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/subproperties' );
					}
					?>

					<?php do_action( 'wp_realestate_after_property_content', $post->ID ); ?>
				</div>
				
				<?php if ( is_active_sidebar( 'property-single-sidebar' ) ): ?>
					<div class="col-xs-12 col-md-4 sidebar-property sidebar-wrapper">
				   		<div class="sidebar sidebar-right">
							<div class="close-sidebar-btn hidden-lg hidden-md"><i class="ti-close"></i> <span><?php esc_html_e('Close', 'homeo'); ?></span></div>
					   		<?php dynamic_sidebar( 'property-single-sidebar' ); ?>
				   		</div>
				   	</div>
			   	<?php endif; ?>
			</div>
		</div>
	</div>	
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_property_detail', $post->ID ); ?>