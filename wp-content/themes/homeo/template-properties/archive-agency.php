<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;
if ( isset( $_REQUEST['load_type'] ) && WP_RealEstate_Mixes::is_ajax_request() ) {
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_RealEstate_Template_Loader::get_template_part('archive-agency-ajax-full', array('agencies' => $wp_query));
	} else {
		echo WP_RealEstate_Template_Loader::get_template_part('archive-agency-ajax-agencies', array('agencies' => $wp_query));
	}

} else {
	get_header();
	$sidebar_configs = homeo_get_agencies_layout_configs();

	$agencies_page = WP_RealEstate_Mixes::get_agencies_page_url();
	$display_mode = homeo_get_agencies_display_mode();
	$display_mode_html = homeo_display_mode_form($display_mode, $agencies_page);
	homeo_render_breadcrumbs($display_mode_html);
	?>
	<section id="main-container" class="main-content  <?php echo apply_filters('homeo_agency_content_class', 'container');?> inner">
		
		<?php if ( homeo_get_agencies_layout_sidebar() == 'main' && is_active_sidebar( 'agencies-filter-top-sidebar' ) ) { ?>
			<div class="agencies-filter-top-sidebar-wrapper filter-top-sidebar-wrapper <?php echo apply_filters('homeo_page_content_class', 'container');?>">
		   		<?php dynamic_sidebar( 'agencies-filter-top-sidebar' ); ?>
		   	</div>
		<?php } ?>

		<?php homeo_before_content( $sidebar_configs ); ?>
		<div class="row">
			<?php homeo_display_sidebar_left( $sidebar_configs ); ?>

			<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
				<div id="main" class="site-main layout-type-grid" role="main">

					<?php
						echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/archive-inner', array('agencies' => $wp_query));

						echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/pagination', array('agencies' => $wp_query));
					?>

				</div><!-- .site-main -->
			</div><!-- .content-area -->
			
			<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
			
		</div>
	</section>
	<?php get_footer();
}