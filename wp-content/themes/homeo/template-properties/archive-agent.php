<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;
if ( isset( $_REQUEST['load_type'] ) && WP_RealEstate_Mixes::is_ajax_request() ) {
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_RealEstate_Template_Loader::get_template_part('archive-agent-ajax-full', array('agents' => $wp_query));
	} else {
		echo WP_RealEstate_Template_Loader::get_template_part('archive-agent-ajax-agents', array('agents' => $wp_query));
	}

} else {
	get_header();
	$sidebar_configs = homeo_get_agents_layout_configs();

	$agents_page = WP_RealEstate_Mixes::get_agents_page_url();
	$display_mode = homeo_get_agents_display_mode();
	$display_mode_html = homeo_display_mode_form($display_mode, $agents_page);
	homeo_render_breadcrumbs($display_mode_html);
	?>
	<section id="main-container" class="main-content <?php echo apply_filters('homeo_agent_content_class', 'container');?> inner">
		<?php if ( homeo_get_agents_layout_sidebar() == 'main' && is_active_sidebar( 'agents-filter-top-sidebar' ) ) { ?>
			<div class="agents-filter-top-sidebar-wrapper filter-top-sidebar-wrapper <?php echo apply_filters('homeo_page_content_class', 'container');?>">
		   		<?php dynamic_sidebar( 'agents-filter-top-sidebar' ); ?>
		   	</div>
		<?php } ?>
		
		<?php homeo_before_content( $sidebar_configs ); ?>
		<div class="row">
			<?php homeo_display_sidebar_left( $sidebar_configs ); ?>

			<div id="main-content" class="col-sm-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
				<div id="main" class="site-main layout-type-grid" role="main">

					<?php
						echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/archive-inner', array('agents' => $wp_query));
						
						echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/pagination', array('agents' => $wp_query));
					?>

				</div><!-- .site-main -->
			</div><!-- .content-area -->
			
			<?php homeo_display_sidebar_right( $sidebar_configs ); ?>
			
		</div>
	</section>
	<?php get_footer();
}