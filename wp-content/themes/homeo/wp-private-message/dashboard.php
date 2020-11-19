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
<div class="box-dashboard-message">
	<h1 class="title-profile"><?php echo esc_html__('Messages','homeo') ?></h1>
	<?php if ( $loop->have_posts() ) { ?>
		<a href="javascript:void(0);" class="btn toggle-message-btn">
			<?php esc_html_e('Show messages box', 'homeo'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
		</a>
		<div class="message-section-wrapper">
			<div class="message-inner row flex-lg">
				<div class="col-xs-12 col-lg-4 lg-28 flex">
					<div class="list-message-wrapper ">
						<form id="search-message-form" class="search-message-form" action="" method="post">
							<div class="search-wrapper-message widget-search">
								<div class="input-group">
						            <input type="text" class="form-control" name="search" placeholder="<?php esc_attr_e( 'Search Contacts...', 'homeo' ); ?>">
						            <span class="input-group-btn">
						            	<button class="search-message-btn btn"><i class="ti-search"></i></button>
						            </span>
						        </div>
						      	<input type="hidden" name="action" value="wp_private_message_search_message">
					        </div>
					        <?php wp_nonce_field( 'wp-private-message-search-message', 'wp-private-message-search-message-nonce' ); ?>
					        <?php
					        $search_read = isset($_REQUEST['search_read']) ? $_REQUEST['search_read'] : 'all';
					        ?>
					        <div class="filter-options">
					        	<ul class="list-options-action">
					        		<li><input id="search_read_all" type="radio" name="search_read" value="all" <?php checked($search_read, 'all'); ?>><label for="search_read_all"><?php esc_html_e('All', 'homeo'); ?></label></li>
					        		<li><input id="search_read_read" type="radio" name="search_read" value="read" <?php checked($search_read, 'read'); ?>><label for="search_read_read"><?php esc_html_e('Read', 'homeo'); ?></label></li>
					        		<li><input id="search_read_unread" type="radio" name="search_read" value="unread" <?php checked($search_read, 'unread'); ?>><label for="search_read_unread"><?php esc_html_e('Unread', 'homeo'); ?></label></li>
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
									<a href="javascript:void(0);" class="loadmore-message-btn" data-paged="<?php echo esc_attr($next_page); ?>"><?php esc_html_e( 'Load more', 'homeo' ); ?></a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-lg-8 lg-72 flex">
					<div class="replies-content">
						<?php
					    	echo WP_Private_Message_Template_Loader::get_template_part( 'reply-section', array( 'post' => $selected_post ) );
					  	?>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="not-found space-padding-lg-30 space-padding-xs-15 bg-white"><?php esc_html_e('No message found', 'homeo'); ?></div>
	<?php } ?>
</div>