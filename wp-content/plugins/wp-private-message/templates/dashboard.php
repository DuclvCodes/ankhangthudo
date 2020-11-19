<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}
$args = array(
	'post_per_page' => wp_private_message_get_option('number_message_per_page', 10),
	'paged' => $paged,
	'author' => $user_id,
);
$loop = WP_Private_Message_Message::get_list_messages($args); ?>
<h3 class="user-name"><?php echo esc_html__('Messages','wp-private-message') ?></h3>
<?php if ( $loop->have_posts() ) { ?>
	<a href="javascript:void(0);" class="btn toggle-message-btn">
		<?php esc_html_e('Show messages box', 'wp-private-message'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
	</a>
	<div class="message-section-wrapper">
		<div class="message-inner clearfix">

			<div class="list-message-wrapper">
				<form id="search-message-form" class="search-message-form" action="" method="post">
					<div class="search-wrapper-message">
						<div class="form-group">
				            <input type="text" class="form-control" name="search" placeholder="<?php esc_attr_e( 'Search', 'wp-private-message' ); ?>">
				        </div>
				      	<input type="hidden" name="action" value="wp_private_message_search_message">
				        <button class="search-message-btn btn"><i class="ti-search"></i></button>
			        </div>
			        <?php wp_nonce_field( 'wp-private-message-search-message', 'wp-private-message-search-message-nonce' ); ?>
			        <?php
			        $search_read = isset($_REQUEST['search_read']) ? $_REQUEST['search_read'] : 'all';
			        ?>
			        <div class="filter-options">
			        	<ul class="list-options-action">
			        		<li><input id="search_read_all" type="radio" name="search_read" value="all" <?php checked($search_read, 'all'); ?>><label for="search_read_all"><?php esc_html_e('All', 'wp-private-message'); ?></label></li>
			        		<li><input id="search_read_read" type="radio" name="search_read" value="read" <?php checked($search_read, 'read'); ?>><label for="search_read_read"><?php esc_html_e('Read', 'wp-private-message'); ?></label></li>
			        		<li><input id="search_read_unread" type="radio" name="search_read" value="unread" <?php checked($search_read, 'unread'); ?>><label for="search_read_unread"><?php esc_html_e('Unread', 'wp-private-message'); ?></label></li>
			        	</ul>
			        </div>
				</form>
				<div class="list-message-inner">
					<ul class="list-message">
						<?php
						$i = 0;
						$selected = isset($_GET['id']) ? $_GET['id'] : '';
						$selected_post = '';
						while ( $loop->have_posts() ) : $loop->the_post();
							global $post;
							
							if ( $i == 0 && empty($selected) ) {
								$selected = $post->ID;
							}
							$classes = '';
							if ( $selected == $post->ID ) {
								$classes = 'active';
								$selected_post = $post;
							}
							echo WP_Private_Message_Template_Loader::get_template_part( 'message-item', array( 'classes' => $classes, 'post' => $post ) );

							$i++;
						endwhile;
						wp_reset_postdata();

						?>
					</ul>

					<?php
					$next_page = $paged + 1;
					if ( $next_page <= $loop->max_num_pages ) { ?>
						<div class="loadmore-action">
							<a href="javascript:void(0);" class="loadmore-message-btn" data-paged="<?php echo esc_attr($next_page); ?>"><?php esc_html_e( 'Load more', 'wp-private-message' ); ?></a>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="replies-content">
				<?php
			    	echo WP_Private_Message_Template_Loader::get_template_part( 'reply-section', array( 'post' => $selected_post ) );
			  	?>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="not-found"><?php esc_html_e('No message found', 'wp-private-message'); ?></div>
<?php } ?>
