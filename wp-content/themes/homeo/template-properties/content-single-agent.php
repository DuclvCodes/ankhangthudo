<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_agent_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<!-- Main content -->

	<?php do_action( 'wp_realestate_before_agent_content', get_the_ID() ); ?>
	
		<?php if ( is_active_sidebar( 'agent-single-sidebar' ) ): ?>
			<a href="javascript:void(0)" class="mobile-sidebar-btn space-10 hidden-lg hidden-md btn-right"><i class="ti-menu-alt"></i> </a>
			<div class="mobile-sidebar-panel-overlay"></div>
		<?php endif; ?>

		<div class="row">
			<div class="col-xs-12 list-content-agent col-md-<?php echo esc_attr( is_active_sidebar( 'agent-single-sidebar' ) ? 8 : 12); ?>">
				<!-- Breadscrumb -->
				<?php homeo_render_breadcrumbs(); ?>
				
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/header' ); ?>

				<div class="agent-detail-tabs detail-tabs-member">
			        <ul role="tablist" class="nav nav-tabs nav-member">
			            <li class="active">
			                <a href="#tab-agent-overview" data-toggle="tab"><?php esc_html_e('Overview', 'homeo'); ?></a>
			            </li>
			            <?php if ( homeo_get_config('agent_property_show') ) { ?>
			                <li>
			                    <a href="#tab-agent-properties" data-toggle="tab"><?php esc_html_e('Properties', 'homeo'); ?></a>
			                </li>
			            <?php } ?>
			            <?php if ( WP_RealEstate_Review::review_enable() ) { ?>
			                <li>
			                    <a href="#tab-agent-reviews" data-toggle="tab"><?php esc_html_e('Reviews', 'homeo'); ?></a>
			                </li>
			            <?php } ?>
			            
			        </ul>
					<div class="tab-content">
						<div id="tab-agent-overview" class="tab-pane active">

							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/description' ); ?>
							
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/location' ); ?>
						</div>
						<?php if ( homeo_get_config('agent_property_show') ) { ?>
							<div id="tab-agent-properties" class="tab-pane">
								<div class="inner">
									<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/properties' ); ?>
								</div>
							</div>
						<?php } ?>
						<?php if ( WP_RealEstate_Review::review_enable() ) { ?>
							<div id="tab-agent-reviews" class="tab-pane">
								<!-- Review -->
								<?php comments_template(); ?>
							</div>
						<?php } ?>
						
					</div>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'agent-single-sidebar' ) ): ?>
			   	<div class="col-xs-12 col-md-4 sidebar-wrapper">
					<div class="sidebar sidebar-right">
						<div class="close-sidebar-btn hidden-lg hidden-md"> <i class="ti-close"></i> <span><?php esc_html_e('Close', 'homeo'); ?></span></div>
				   		<?php dynamic_sidebar( 'agent-single-sidebar' ); ?>
			   		</div>
			   	</div>
		   	<?php endif; ?>
		</div>
		
	<?php do_action( 'wp_realestate_after_agent_sidebar', get_the_ID() ); ?>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agent_detail', get_the_ID() ); ?>