<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_realestate_before_agency_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<!-- Main content -->

	<?php do_action( 'wp_realestate_before_agency_content', get_the_ID() ); ?>

		<?php if ( is_active_sidebar( 'agency-single-sidebar' ) ): ?>
			<a href="javascript:void(0)" class="mobile-sidebar-btn space-10 hidden-lg hidden-md btn-right"><i class="ti-menu-alt"></i> </a>
			<div class="mobile-sidebar-panel-overlay"></div>
		<?php endif; ?>

		<div class="row">
			<div class="col-xs-12 list-content-agency col-md-<?php echo esc_attr( is_active_sidebar( 'agency-single-sidebar' ) ? 8 : 12); ?>">
				<!-- breadcrumbs -->
				<?php homeo_render_breadcrumbs(); ?>
				
				<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/header' ); ?>

				<div class="agency-detail-tabs detail-tabs-member">
			        <ul role="tablist" class="nav nav-tabs nav-member">
			            <li class="active">
			                <a href="#tab-agency-overview" data-toggle="tab"><?php esc_html_e('Overview', 'homeo'); ?></a>
			            </li>
			            <?php if ( homeo_get_config('agency_property_show') ) { ?>
			                <li>
			                    <a href="#tab-agency-properties" data-toggle="tab"><?php esc_html_e('Properties', 'homeo'); ?></a>
			                </li>
			            <?php } ?>
			            <?php if ( homeo_get_config('agency_agent_show') ) { ?>
			                <li>
			                    <a href="#tab-agency-agents" data-toggle="tab"><?php esc_html_e('Agents', 'homeo'); ?></a>
			                </li>
			            <?php } ?>
			            <?php if ( WP_RealEstate_Review::review_enable() ) { ?>
			                <li>
			                    <a href="#tab-agency-reviews" data-toggle="tab"><?php esc_html_e('Reviews', 'homeo'); ?></a>
			                </li>
			            <?php } ?>
			        </ul>
				
					<div class="tab-content">
						<div id="tab-agency-overview" class="tab-pane active">
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/description' ); ?>
														
							<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/location' ); ?>
						</div>
						
						<?php if ( homeo_get_config('agency_property_show') ) { ?>
							<div id="tab-agency-properties" class="tab-pane">
								<div class="inner">
									<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/properties' ); ?>
								</div>
							</div>
						<?php } ?>
						<?php if ( homeo_get_config('agency_agent_show') ) { ?>
							<div id="tab-agency-agents" class="tab-pane">
								<div class="inner">
									<?php echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/agents' ); ?>
								</div>
							</div>
						<?php } ?>

						<?php if ( WP_RealEstate_Review::review_enable() ) { ?>
							<div id="tab-agency-reviews" class="tab-pane">
								<!-- Review -->
								<?php comments_template(); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'agency-single-sidebar' ) ): ?>
				<div class="col-xs-12 col-md-4 sidebar-wrapper">
					<div class="sidebar sidebar-right">
						<div class="close-sidebar-btn hidden-lg hidden-md"> <i class="ti-close"></i> <span><?php esc_html_e('Close', 'homeo'); ?></span></div>
				   		<?php dynamic_sidebar( 'agency-single-sidebar' ); ?>
			   		</div>
			   	</div>
		   	<?php endif; ?>
		</div>
		
	<?php do_action( 'wp_realestate_after_agency_sidebar', get_the_ID() ); ?>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_agency_detail', get_the_ID() ); ?>