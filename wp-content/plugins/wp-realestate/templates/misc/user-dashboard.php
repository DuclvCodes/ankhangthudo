<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


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

?>

<div class="user-dashboard-wrapper">
	<h1 class="title"><?php esc_html_e('Dashboard', 'wp-realestate'); ?></h1>
	<div class="statistics row">
		<div class="col-sm-6">
			<div class="posted-properties">
				<div class="properties-count"><?php echo WP_RealEstate_Mixes::format_number($count_properties); ?></div>
				<h4><?php esc_html_e('Posted Properties', 'wp-realestate'); ?></h4>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="favorite">
				<div class="properties-count"><?php echo WP_RealEstate_Mixes::format_number($favorite); ?></div>
				<h4><?php esc_html_e('Favorites', 'wp-realestate'); ?></h4>
			</div>
		</div>
	</div>
	<div class="recent-wrapper row">
		<div class="col-sm-8">
			<!-- recent review -->
			<?php
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

			<div class="user-reviews">
				<?php
				if ( $comments ) {
					
					echo '<ul class="list-reviews">';
						wp_list_comments(array(
							'per_page' => $number,
							'page' => 1,
							'reverse_top_level' => false,
							'callback' => array('WP_RealEstate_Review', 'user_reviews')
						), $comments);
					echo '</ul>';
				} else { ?>
					<div class="not-found"><?php esc_html_e('No reviews found.', 'wp-realestate'); ?></div>
				<?php } ?>

			</div>
		</div>
		<div class="col-sm-4">
			<!-- recent message -->
		</div>
	</div>
</div>
