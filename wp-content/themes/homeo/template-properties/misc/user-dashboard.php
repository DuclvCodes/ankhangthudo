<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query_vars_pending = array(
	'post_type'     => 'property',
	'post_status'   => 'pending',
	'author'        => $user_id,
	'fields'		=> 'ids',
	'posts_per_page' => -1
);
$properties_pending = new WP_Query($query_vars_pending);
$count_properties_pending = $properties_pending->post_count;

$query_vars = array(
	'post_type'     => 'property',
	'post_status'   => 'publish',
	'author'        => $user_id,
	'fields'		=> 'ids',
	'posts_per_page' => -1
);
$properties = new WP_Query($query_vars);
$count_properties = $properties->post_count;

$favorite = WP_RealEstate_Favorite::get_property_favorites();
$favorite = is_array($favorite) ? count($favorite) : 0;

$user = wp_get_current_user();

$post_ids = array();
if ( WP_RealEstate_User::is_agent($user->ID) ) {
	$post_ids[] = WP_RealEstate_User::get_agent_by_user_id($user->ID);
} elseif ( WP_RealEstate_User::is_agency($user->ID) ) {
	$post_ids[] = WP_RealEstate_User::get_agency_by_user_id($user->ID);
}

if ( !empty($properties->posts) ) {
	$post_ids = array_merge($post_ids, $properties->posts);
}
$number = apply_filters('wp-realestate-dashboard-number-reviews', 3);
$args = array(
	'post_type' => array('property', 'agent', 'agency'),
	'status' => 'approve',
	'number'  => $number,
	'meta_query' => array(
        array(
           'key' => '_rating_avg',
           'value' => 0,
           'compare' => '>',
        )
    )
);
$comments = null;
if ( !empty($post_ids) ) {
	$comments = WP_RealEstate_Review::get_comments( $args, $post_ids );
}
?>

<div class="user-dashboard-wrapper">
	<h1 class="title-profile"><?php esc_html_e('Hello ', 'homeo'); echo esc_html($user->data->display_name); ?></h1>
	<div class="statistics row space-30">
		<div class="col-sm-3 col-xs-12">
			<div class="posted-properties dashboard-box box-white-dashboard flex-middle">
				<div class="inner-left">
					<div class="properties-count"><?php echo WP_RealEstate_Mixes::format_number($count_properties); ?></div>
					<h4><?php esc_html_e('Published', 'homeo'); ?></h4>
				</div>
				<div class="inner-right bg-properties ali-right">
					<i class="flaticon-home"></i>
				</div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-12">
			<div class="posted-properties dashboard-box box-white-dashboard flex-middle">
				<div class="inner-left">
					<div class="properties-count"><?php echo WP_RealEstate_Mixes::format_number($count_properties_pending); ?></div>
					<h4><?php esc_html_e('Pending', 'homeo'); ?></h4>
				</div>
				<div class="inner-right bg-pending ali-right">
					<i class="flaticon-home"></i>
				</div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-12">
			<div class="favorite dashboard-box box-white-dashboard flex-middle">
				<div class="inner-left">
					<div class="properties-count"><?php echo WP_RealEstate_Mixes::format_number($favorite); ?></div>
					<h4><?php esc_html_e('Favorites', 'homeo'); ?></h4>
				</div>
				<div class="inner-right bg-favorites ali-right">
					<i class="flaticon-heart"></i>
				</div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-12">
			<div class="favorite dashboard-box box-white-dashboard flex-middle">
				<div class="inner-left">
					<div class="properties-count">
						<?php 
							if ( $comments ){
								echo count($comments);
							} else{
								echo 0;
							}
						?>
						</div>
					<h4><?php esc_html_e('Reviews', 'homeo'); ?></h4>
				</div>
				<div class="inner-right bg-view ali-right">
					<i class="flaticon-view"></i>
				</div>
			</div>
		</div>
		
	</div>
	<div class="recent-wrapper-dashboard row">
		<div class="col-xs-12 <?php echo esc_attr(homeo_is_wp_private_message() ? 'col-sm-8' : ''); ?>">
			<!-- recent review -->
			
			<div class="user-reviews box-white-dashboard">
				<h3 class="title"><?php echo esc_html__('Recent Reviews','homeo') ?></h3>
				<?php
				if ( $comments ) {
					
					echo '<ul class="list-reviews comment-list">';
						wp_list_comments(array(
							'per_page' => $number,
							'page' => 1,
							'reverse_top_level' => false,
							'callback' => array('WP_RealEstate_Review', 'user_reviews')
						), $comments);
					echo '</ul>';
				} else { ?>
					<div class="not-found"><?php esc_html_e('No reviews found.', 'homeo'); ?></div>
				<?php } ?>
			</div>

		</div>
		<?php if ( homeo_is_wp_private_message() ) { ?>
			<div class="col-sm-4 col-xs-12">
				<!-- recent message -->
				<?php
					$args = array(
						'post_per_page' => 5,
						'author' => $user->ID,
					);
					$loop = WP_Private_Message_Message::get_list_messages($args);
					if ( $loop->have_posts() ) {
						?>
						<div class="box-white-dashboard">
							<h3 class="title"><?php echo esc_html__('Messages','homeo') ?></h3>
							<ul class="list-message-small">
								<?php
								$dashboard_id = wp_private_message_get_option('message_dashboard_page_id');
								$dashboard_link = get_permalink($dashboard_id);

								while ( $loop->have_posts() ) : $loop->the_post();
									global $post;
									$args = array(
										'post_per_page' => 1,
										'paged' => 1,
										'parent' => $post->ID,
									);
									$reply_messages = WP_Private_Message_Message::get_list_reply_messages($args);
									$read = get_post_meta($post->ID, '_read_'.get_current_user_id(), true);
									$yourself_id = get_current_user_id();
									$sender = get_post_meta($post->ID, '_sender', true);
									$recipient = get_post_meta($post->ID, '_recipient', true);
									if ( $yourself_id == $sender ) {
										$recipient_id = $recipient;
									} else {
										$recipient_id = $sender;
									}
									if ( $read ) {
										$classes = ' read';
									} else {
										$classes = ' unread';
									}
									$url_link = add_query_arg( 'id', $post->ID, $dashboard_link );
									?>
									<li id="message-id-<?php echo esc_attr($post->ID); ?>" class="<?php echo esc_attr($classes); ?>">
										<a class="message-item-small" href="<?php echo esc_url($url_link); ?>">
											<div class="avatar">
												<?php homeo_private_message_user_avatar( $recipient_id ); ?>
											</div>
											<div class="content">
												<h4 class="user-name"><?php echo esc_html( get_the_author_meta('display_name', $recipient_id)); ?>
													<span class="message-time"> -
														<?php if ( $reply_messages->have_posts() ) { ?>
															<?php foreach ($reply_messages->posts as $rpost) {?>
																	<?php echo human_time_diff(get_the_time('U', $rpost), current_time('timestamp')); ?>
															<?php } ?>
														<?php } else { ?>
																<?php echo human_time_diff(get_the_time('U', $post), current_time('timestamp')); ?>
														<?php } ?>
													</span>
												</h4>
												<div class="message-title"><?php echo esc_html($post->post_title); ?></div>
											</div>
										</a>
									</li>
									<?php
								endwhile;
								wp_reset_postdata();
								?>
							</ul>
						</div>
						<?php
					}
				?>
			</div>
		<?php } ?>
	</div>
</div>
