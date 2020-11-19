<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_property_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('property-single-layout property-single-v5'); ?>>
	<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/gallery-v5', array('gallery_size' => 'homeo-gallery-v5')  ); ?>
	<?php
		$amenities = $materials = $location = $floor_plans = $video_virtual = $facilities = $valuation = $stats_graph = $nearby_yelp = $walk_score = $subproperties = $related = '';
		if ( homeo_get_config('show_property_amenities', true) ) {
			$amenities = WP_RealEstate_Template_Loader::get_template_part( 'single-property/amenities' );
		}
		if ( homeo_get_config('show_property_materials', false) ) {
			$materials = WP_RealEstate_Template_Loader::get_template_part( 'single-property/materials' );
		}
		if ( homeo_get_config('show_property_location', true) ) {
			$location = WP_RealEstate_Template_Loader::get_template_part( 'single-property/location' );
		}
		if ( homeo_get_config('show_property_floor-plans', true) ) {
			$floor_plans = WP_RealEstate_Template_Loader::get_template_part( 'single-property/floor-plans' );
		}
		if ( homeo_get_config('show_property_tabs-video-virtual', true) ) {
			$video_virtual = WP_RealEstate_Template_Loader::get_template_part( 'single-property/tabs-video-virtual' );
		}
		if ( homeo_get_config('show_property_facilities', true) ) {
			$facilities = WP_RealEstate_Template_Loader::get_template_part( 'single-property/facilities' );
		}
		if ( homeo_get_config('show_property_valuation', true) ) {
			$valuation = WP_RealEstate_Template_Loader::get_template_part( 'single-property/valuation' );
		}
		if ( homeo_get_config('show_property_stats_graph', true) ) {
			$stats_graph = WP_RealEstate_Template_Loader::get_template_part( 'single-property/stats_graph' );
		}
		if ( homeo_get_config('show_property_nearby_yelp', true) ) {
			$nearby_yelp = WP_RealEstate_Template_Loader::get_template_part( 'single-property/nearby_yelp' );
		}
		if ( homeo_get_config('show_property_walk_score', true) ) {
			$walk_score = WP_RealEstate_Template_Loader::get_template_part( 'single-property/walk_score' );
		}
		if ( homeo_get_config('show_property_subproperties', true) ) {
			$subproperties = WP_RealEstate_Template_Loader::get_template_part( 'single-property/subproperties' );
		}
		if ( homeo_get_config('show_property_related', true) ) {
			$related = WP_RealEstate_Template_Loader::get_template_part( 'single-property/related' );
		}
	?>
	<div class="panel-affix-wrapper">
		<div class="header-tabs-wrapper panel-affix">
			<ul class="nav justify-content-center flex nav-detail-center">
				
				<li><a href="#property-single-details"><?php esc_html_e('Details', 'homeo'); ?></a></li>

				<?php if ( $amenities ) { ?>
					<li><a href="#property-single-features"><?php esc_html_e('Features', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $materials ) { ?>
					<li><a href="#property-single-materials"><?php esc_html_e('Materials', 'homeo'); ?></a></li>
				<?php } ?>

				<?php if ( $location ) { ?>
					<li><a href="#property-single-location"><?php esc_html_e('Locations', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $floor_plans ) { ?>
					<li><a href="#property-single-floor_plans"><?php esc_html_e('Plans', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $video_virtual ) { ?>
					<li><a href="#property-single-video_virtual"><?php esc_html_e('Video', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $facilities ) { ?>
					<li><a href="#property-single-facilities"><?php esc_html_e('Facilities', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $valuation ) { ?>
					<li><a href="#property-single-valuation"><?php esc_html_e('Valuation', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $stats_graph ) { ?>
					<li><a href="#property-single-stats_graph"><?php esc_html_e('Stats', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $nearby_yelp ) { ?>
					<li><a href="#property-single-nearby_yelp"><?php esc_html_e('Nearby yelp', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $walk_score ) { ?>
					<li><a href="#property-single-walk_score"><?php esc_html_e('Walkscore', 'homeo'); ?></a></li>
				<?php } ?>
				<?php if ( $subproperties ) { ?>
					<li><a href="#property-single-subproperties"><?php esc_html_e('Subproperties', 'homeo'); ?></a></li>
				<?php } ?>

				<?php if ( WP_RealEstate_Review::review_enable() ) { ?>
					<li><a href="#property-single-reviews"><?php esc_html_e('Reviews', 'homeo'); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="<?php echo apply_filters('homeo_property_content_class', 'container');?>">
		
		<div class="buttons-group-center justify-content-center flex">
			<?php if ( homeo_get_config('listing_enable_compare', true) ) { ?>
				<div class="compare">
					<?php
					$args = array(
						'show_text' => true,
						'added_icon_class' => 'flaticon-transfer-1',
						'add_icon_class' => 'flaticon-transfer-1',
					);
					WP_RealEstate_Compare::display_compare_btn($post->ID, $args);
					?>
				</div>
			<?php } ?>
			<div class="send-email">
				<a href="#" class="btn-send-mail">
					<i class="flaticon-envelope"></i>
					<span><?php esc_html_e('Send an email', 'homeo'); ?></span>
				</a>
			</div>
			<?php if ( homeo_get_config('listing_enable_favorite', true) ) { ?>
				<div class="wishlist">
					<?php
					$args = array(
						'show_text' => true
					);
					WP_RealEstate_Favorite::display_favorite_btn($post->ID, $args);
					?>
				</div>
			<?php } ?>
			<?php get_template_part('template-parts/sharebox-property'); ?>
			
			<?php if ( homeo_get_config('listing_enable_printer', true) ) { ?>
				<div class="print">
					<?php homeo_property_print_btn($post, true); ?>
				</div>
			<?php } ?>
		</div>

		<!-- Main content -->
		<div class="content-property-detail">

			<?php if ( is_active_sidebar( 'property-single-sidebar' ) ): ?>
				<a href="javascript:void(0)" class="mobile-sidebar-btn space-10 hidden-lg hidden-md btn-right"><i class="ti-menu-alt"></i> </a>
				<div class="mobile-sidebar-panel-overlay"></div>
			<?php endif; ?>

			<div class="row">
				<div class="col-xs-12 property-detail-main col-md-<?php echo esc_attr( is_active_sidebar( 'property-single-sidebar' ) ? 8 : 12); ?>">

					<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>
					
					<div id="property-single-details">
						<?php
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
					
					<?php if ( $amenities ) { ?>
						<div id="property-single-features">
							<?php echo trim($amenities);; ?>
						</div>
					<?php } ?>

					<?php if ( $materials ) { ?>
						<div id="property-single-materials">
							<?php echo trim($materials);; ?>
						</div>
					<?php } ?>

					<?php if ( $location ) { ?>
						<div id="property-single-location">
							<?php echo trim($location);; ?>
						</div>
					<?php } ?>

					<?php if ( $floor_plans ) { ?>
						<div id="property-single-floor_plans">
							<?php echo trim($floor_plans);; ?>
						</div>
					<?php } ?>

					<?php if ( $video_virtual ) { ?>
						<div id="property-single-video_virtual">
							<?php echo trim($video_virtual);; ?>
						</div>
					<?php } ?>

					<?php if ( $facilities ) { ?>
						<div id="property-single-facilities">
							<?php echo trim($facilities);; ?>
						</div>
					<?php } ?>

					<?php if ( $valuation ) { ?>
						<div id="property-single-valuation">
							<?php echo trim($valuation);; ?>
						</div>
					<?php } ?>

					<?php if ( $stats_graph ) { ?>
						<div id="property-single-stats_graph">
							<?php echo trim($stats_graph);; ?>
						</div>
					<?php } ?>

					<?php if ( $nearby_yelp ) { ?>
						<div id="property-single-nearby_yelp">
							<?php echo trim($nearby_yelp);; ?>
						</div>
					<?php } ?>

					<?php if ( $walk_score ) { ?>
						<div id="property-single-walk_score">
							<?php echo trim($walk_score);; ?>
						</div>
					<?php } ?>

					<?php if ( WP_RealEstate_Review::review_enable() ) { ?>
						<div id="property-single-reviews">
							<?php comments_template(); ?>
						</div>
					<?php } ?>

					<?php if ( $related ) { ?>
						<div id="property-single-related">
							<?php echo trim($related);; ?>
						</div>
					<?php } ?>

					<?php if ( $subproperties ) { ?>
						<div id="property-single-subproperties">
							<?php echo trim($subproperties);; ?>
						</div>
					<?php } ?>

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