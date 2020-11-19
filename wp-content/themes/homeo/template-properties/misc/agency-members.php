<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = get_current_user_id();
$agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$loop = WP_RealEstate_Query::get_agency_agents($agency_id, array(
    'post_per_page' => get_option('posts_per_page'),
    'paged' => $paged
));
wp_enqueue_script('jquery-ui-autocomplete');
?>
<h1 class="title-profile"><?php esc_html_e('Team Members', 'homeo'); ?></h1>
<div class="agency-agents-member">
	<div class="agency-agents-list">
		<div class="box-white-dashboard">
			<h3 class="title"><?php esc_html_e( 'All Members', 'homeo' ) ; ?></h3>
			<?php if ( !empty($loop) && $loop->have_posts() ) { ?>
			    <div class="agency-agents-list-inner">
			        <?php
			            while ( $loop->have_posts() ) : $loop->the_post();
			                echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-list-team' );
			            endwhile;
			        ?>
			    </div>
			    
			    <?php
			    WP_RealEstate_Mixes::custom_pagination( array(
					'max_num_pages' => $loop->max_num_pages,
					'prev_text'     => esc_html__( 'Previous page', 'homeo' ),
					'next_text'     => esc_html__( 'Next page', 'homeo' ),
					'wp_query' 		=> $loop
				));

	            wp_reset_postdata();
	            ?>
			<?php } else { ?>
				<div class="not-found space-padding-lg-30 space-padding-xs-15"><?php esc_html_e('No agents found.', 'homeo'); ?></div>
			<?php } ?>
		</div>
	</div>
	<!-- Form list -->
	<div class="agency-agents-form-wrapper box-white-dashboard">
		<h3 class="title"><?php esc_html_e('Add Member', 'homeo'); ?></h3>
		<form action="" method="get" class="agency-add-agents-form">
			<div class="form-group team-agent-autocomplete-wrapper">
				<div class="team-agent-wrapper"></div>
				
				<input id="team-agent-autocomplete" type="text" name="agent_name" class="agent-autocomplete form-control" placeholder="<?php echo esc_html__( 'Search..', 'homeo' ); ?>">
			</div>
			<div class="clearfix">
				<button class="search-submit btn btn-theme btn-inverse border-2" name="submit"><?php echo esc_html__( 'Add Agent', 'homeo' ); ?></button>
				<input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-agency-add-agent-nonce' )); ?>">
			</div>
		</form>
	</div>
</div>