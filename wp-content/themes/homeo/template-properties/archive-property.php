<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;
if ( isset( $_REQUEST['load_type'] ) && WP_RealEstate_Mixes::is_ajax_request() ) {
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_RealEstate_Template_Loader::get_template_part('archive-property-ajax-full', array('properties' => $wp_query));
	} else {
		echo WP_RealEstate_Template_Loader::get_template_part('archive-property-ajax-properties', array('properties' => $wp_query));
	}

} else {
	get_header();

	$layout_type = homeo_get_properties_layout_type();

	$args = array(
		'properties' => $wp_query
	);

	if ( $layout_type == 'half-map' || $layout_type == 'half-map-v2' || $layout_type == 'half-map-v3' ) {
		if ( $layout_type == 'half-map' ) {
			$first_class = 'col-md-4 col-lg-4 col-xs-12 first_class no-padding';
			$second_class = 'col-md-8 col-lg-8 col-xs-12 second_class';
			$sidebar = 'properties-filter-top-half-map';
			$sidebar_wrapper_class = 'properties-filter-top-half-map';
		} elseif ( $layout_type == 'half-map-v2' ) {
			$first_class = 'col-md-5 col-lg-7 col-md-push-7 col-lg-push-5 col-xs-12 no-padding first_class';
			$second_class = 'col-md-7 col-lg-5 col-xs-12 col-md-pull-5 col-lg-pull-7 second_class';
			$sidebar = 'properties-filter-sidebar-fixed';
			$sidebar_wrapper_class = 'properties-filter-sidebar-wrapper';
		} else {
			$first_class = 'col-md-5 col-lg-4 col-xs-12 col-lg-push-8 col-lg-push-7 no-padding first_class';
			$second_class = 'col-md-7 col-lg-8 col-xs-12 col-lg-pull-4 col-md-pull-5 second_class';
			$sidebar = 'properties-filter-sidebar-fixed';
			$sidebar_wrapper_class = 'properties-filter-sidebar-wrapper';
		}
	?>
		<section id="main-container" class="inner">
			<?php if ( is_active_sidebar( $sidebar ) && ($layout_type == 'half-map-v2' || $layout_type == 'half-map-v3') ){ ?>
				<div class="hidden-lg hidden-md text-center half-map-v2-action-filter">
					<!-- show filter -->
					<span class="btn btn-show-filter">
						<i class="flaticon-filter-results-button"></i><span><?php echo esc_html__('Filter & Map', 'homeo'); ?></span>
					</span>
				</div>
			<?php } ?>
			<div class="row no-margin layout-type-<?php echo esc_attr($layout_type); ?>">

				<div class="<?php echo esc_attr($first_class); ?>">
					<div id="properties-google-maps" class="fix-map hidden-xs hidden-sm">
						<?php if ( is_active_sidebar( $sidebar ) && ($layout_type == 'half-map-v2' || $layout_type == 'half-map-v3') ){ ?>
							<!-- show filter -->
							<span class="btn btn-show-filter <?php echo esc_attr( ($layout_type == 'half-map-v3')?'hidden-lg':'') ?>">
								<i class="flaticon-filter-results-button"></i><span><?php echo esc_html__('Show Filter', 'homeo'); ?></span>
							</span>
						<?php } ?>
					</div>
				</div>

				<div id="main-content" class="<?php echo esc_attr($second_class); ?>">
					<div class="inner-left <?php echo esc_attr( is_active_sidebar( $sidebar )? 'has-sidebar':'' ); ?>">

						<?php if( is_active_sidebar( $sidebar ) && ($layout_type == 'half-map') ){ ?>

				   			<div class="mobile-groups-button hidden-lg hidden-md clearfix text-center">
								<button class=" btn btn-xs btn-theme btn-view-map" type="button"><i class="fas fa-map" aria-hidden="true"></i> <?php esc_html_e( 'Map View', 'homeo' ); ?></button>
								<button class=" btn btn-xs btn-theme btn-view-listing hidden-sm hidden-xs" type="button"><i class="fas fa-list" aria-hidden="true"></i> <?php esc_html_e( 'Properties View', 'homeo' ); ?></button>
							</div>

				   			<div class="<?php echo esc_attr($sidebar_wrapper_class); ?>">
				   				<div class="inner">
						   			<?php dynamic_sidebar( $sidebar ); ?>
						   		</div>
						   	</div>

					   	<?php } elseif ( is_active_sidebar( $sidebar ) && ($layout_type == 'half-map-v2' || $layout_type == 'half-map-v3') ){ ?>
							<div class="<?php echo esc_attr($sidebar_wrapper_class); ?>">

								<div class="mobile-groups-button hidden-lg hidden-md clearfix text-center">
									<button class=" btn btn-xs btn-theme btn-view-map" type="button"><i class="fas fa-map" aria-hidden="true"></i> <?php esc_html_e( 'Map View', 'homeo' ); ?></button>
									<button class=" btn btn-xs btn-theme btn-view-listing hidden-sm hidden-xs" type="button"><i class="fas fa-list" aria-hidden="true"></i> <?php esc_html_e( 'Properties View', 'homeo' ); ?></button>
								</div>
								<div class="filter-scroll inner">
						   			<?php dynamic_sidebar( $sidebar ); ?>
						   			<span class="close-filter">
							   			<i class="flaticon-close"></i>
							   		</span>
						   		</div>

					   		</div>
					   		<div class="over-dark-filter"></div>

					   	<?php } ?>

					   	<div class="content-listing">
					   		
							<main id="main" class="site-main layout-type-<?php echo esc_attr($layout_type); ?>" role="main">

								<?php
									echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', $args);

									echo WP_RealEstate_Template_Loader::get_template_part('loop/property/pagination', array('properties' => $wp_query));
								?>

							</main><!-- .site-main -->
						</div>
					</div>
				</div><!-- .content-area -->
			</div>
		</section>
	<?php
	} else {
		$sidebar_configs = homeo_get_properties_layout_configs();
		$layout_sidebar = homeo_get_properties_layout_sidebar();
	?>
		
		<section id="main-container" class="inner layout-type-<?php echo esc_attr($layout_type); ?>">
			<?php if ( $layout_type == 'top-map' ) { ?>
				<div class="mobile-groups-button hidden-lg hidden-md clearfix text-center">
					<button class=" btn btn-sm btn-theme btn-view-map" type="button"><i class="fas fa-map" aria-hidden="true"></i> <?php esc_html_e( 'Map View', 'homeo' ); ?></button>
					<button class=" btn btn-sm btn-theme  btn-view-listing hidden-sm hidden-xs" type="button"><i class="fas fa-list" aria-hidden="true"></i> <?php esc_html_e( 'Properties View', 'homeo' ); ?></button>
				</div>
				<div id="properties-google-maps" class="hidden-sm hidden-xs top-map"></div>
				<?php if ( $layout_sidebar == 'main' && is_active_sidebar( 'properties-filter-top-sidebar' ) ) { ?>
					<div class="properties-filter-top-sidebar-wrapper filter-top-sidebar-wrapper <?php echo apply_filters('homeo_property_content_class', 'container');?>">
				   		<?php dynamic_sidebar( 'properties-filter-top-sidebar' ); ?>
				   	</div>
				<?php } ?>
			<?php } else {
				if ( did_action( 'elementor/loaded' ) ) {
					$ele_obj = \Elementor\Plugin::$instance;
					$template_id = homeo_get_properties_elementor_template();
					if ( !empty($template_id) ) {
					    echo trim( $ele_obj->frontend->get_builder_content_for_display( $template_id ) );
					}
				}
			}
			?>

			<?php
				$_html = WP_RealEstate_Template_Loader::get_template_part('loop/property/properties-save-search-form');

				if ( $layout_type !== 'top-map' && $layout_sidebar == 'main' && is_active_sidebar( 'properties-filter-top-sidebar' ) ) {
					$_html .= '<div class="show-filter-btn-wrapper">';
					$_html .= '<a class="btn btn-show-filter btn-show-filter-top" href="javascript:void(0);"><i class="flaticon-filter-results-button"></i><span>'.esc_html__('Show Filter', 'homeo').'</span></a>';
					$_html .= '</div>';
				} else {
					$properties_page = WP_RealEstate_Mixes::get_properties_page_url();
					$display_mode = homeo_get_properties_display_mode();
					$_html .= homeo_display_mode_form($display_mode, $properties_page);
				}
				homeo_render_breadcrumbs($_html);
			?>

			<?php if ( $layout_type !== 'top-map' && $layout_sidebar == 'main' && is_active_sidebar( 'properties-filter-sidebar' ) ) { ?>
				<div class="properties-filter-sidebar-wrapper">
					<div class="inner">
				   		<?php dynamic_sidebar( 'properties-filter-sidebar-fixed' ); ?>
				   		<span class="close-filter">
				   			<i class="flaticon-close"></i>
				   		</span>
			   		</div>
			   	</div>
			   	<div class="over-dark-filter"></div>
			<?php } ?>

			<div class="main-content <?php echo apply_filters('homeo_property_content_class', 'container');?> inner">
				
				<?php homeo_before_content( $sidebar_configs ); ?>
				
				<div class="row">
					<?php homeo_display_sidebar_left( $sidebar_configs ); ?>

					<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
						<main id="main" class="site-main layout-type-<?php echo esc_attr($layout_type); ?>" role="main">

							<?php
								echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', $args);

								echo WP_RealEstate_Template_Loader::get_template_part('loop/property/pagination', array('properties' => $wp_query));
							?>

						</main><!-- .site-main -->
					</div><!-- .content-area -->
					
					<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
				</div>

			</div>
		</section>
	<?php
	}

	get_footer();
}