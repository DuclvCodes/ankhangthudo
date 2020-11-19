<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user = wp_get_current_user();
$post_ids = array();
if ( WP_RealEstate_User::is_agent($user->ID) ) {
	$post_ids[] = WP_RealEstate_User::get_agent_by_user_id($user->ID);
} elseif ( WP_RealEstate_User::is_agency($user->ID) ) {
	$post_ids[] = WP_RealEstate_User::get_agency_by_user_id($user->ID);
}

$query_vars = array(
	'post_type'     => 'property',
	'post_status'   => 'publish',
	'author'        => $user->ID,
	'fields'		=> 'ids',
	'posts_per_page' => -1
);
$loop = new WP_Query($query_vars);
if ( !empty($loop->posts) ) {
	$post_ids = array_merge($post_ids, $loop->posts);
}
$args = array(
	'post_type' => array('property', 'agent', 'agency'),
	'status' => 'approve',
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
		$number = apply_filters( 'wp-realestate-get-my-reviews-limit', get_option('posts_per_page'));
		$max_page = ceil(count($comments)/$number);
		$page = !empty($_GET['cpage']) ? $_GET['cpage'] : 1;

		echo '<ul class="list-reviews">';
			wp_list_comments(array(
				'per_page' => $number,
				'page' => $page,
				'reverse_top_level' => false,
				'callback' => array('WP_RealEstate_Review', 'user_reviews')
			), $comments);
		echo '</ul>';

		$pargs = array(
			'base' => add_query_arg( 'cpage', '%#%' ),
			'format' => '',
			'total' => $max_page,
			'current' => $page,
			'echo' => true,
			'add_fragment' => ''
		);
		WP_RealEstate_Mixes::paginate_links( $pargs );
	} else { ?>
		<div class="not-found"><?php esc_html_e('No reviews found.', 'wp-realestate'); ?></div>
	<?php } ?>
</div>