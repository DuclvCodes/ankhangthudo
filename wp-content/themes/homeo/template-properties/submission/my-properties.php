<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('select2');
wp_enqueue_style('select2');
?>

<?php $my_properties_page_id = wp_realestate_get_option('my_properties_page_id'); ?>

<div class="search-orderby-wrapper flex-middle-sm">
	<h1 class="title-profile"><?php esc_html_e( 'My Properties', 'homeo' ) ; ?></h1>
	<div class="ali-right">
		<div class="flex-middle">
			<div class="search-my-properties-form widget-search">
				<form action="<?php echo esc_url(get_permalink( $my_properties_page_id )); ?>" method="get">
					<div class="input-group">
						<input placeholder="<?php echo esc_html__('Search ...', 'homeo'); ?>" class="form-control" type="text" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
						<span class="input-group-btn">
							<button class="search-submit btn btn-sm btn-search" name="submit">
								<i class="ti-search"></i>
							</button>
						</span>
					</div>
				</form>
			</div>
			<div class="sort-my-properties-form sortby-form">
				<?php
					$orderby_options = apply_filters( 'wp_realestate_my_properties_orderby', array(
						'menu_order'	=> esc_html__( 'Default', 'homeo' ),
						'newest' 		=> esc_html__( 'Newest', 'homeo' ),
						'oldest'     	=> esc_html__( 'Oldest', 'homeo' ),
					) );

					$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
				?>

				<div class="orderby-wrapper flex-middle">
					<span class="text-sort">
						<?php echo esc_html__('Sort by: ','homeo'); ?>
					</span>
					<form class="my-properties-ordering" method="get">
						<select name="orderby" class="orderby">
							<?php foreach ( $orderby_options as $id => $name ) : ?>
								<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
							<?php endforeach; ?>
						</select>
						<input type="hidden" name="paged" value="1" />
						<?php WP_RealEstate_Mixes::query_string_form_fields( null, array( 'orderby', 'submit', 'paged' ) ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$query_vars = array(
		'post_type'     => 'property',
		'post_status'   => apply_filters('wp-realestate-my-properties-post-statuses', array( 'publish', 'expired', 'pending', 'pending_approve', 'pending_payment', 'draft', 'preview' )),
		'paged'         => $paged,
		'author'        => get_current_user_id(),
		'orderby'		=> 'date',
		'order'			=> 'DESC',
	);
	if ( isset($_GET['search']) ) {
		$query_vars['s'] = $_GET['search'];
	}
	if ( isset($_GET['orderby']) ) {
		switch ($_GET['orderby']) {
			case 'menu_order':
				$query_vars['orderby'] = array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
					'ID'         => 'DESC',
				);
				break;
			case 'newest':
				$query_vars['orderby'] = 'date';
				$query_vars['order'] = 'DESC';
				break;
			case 'oldest':
				$query_vars['orderby'] = 'date';
				$query_vars['order'] = 'ASC';
				break;
		}
	}
	$properties = new WP_Query($query_vars);

?>
<div class="box-white-dashboard">
	<div class="inner">
	<div class="layout-my-properties flex-middle header-layout">
		<div class="property-thumbnail-wrapper">
			<?php echo esc_html__('Image','homeo') ?>
		</div>
		<div class="layout-left flex-middle inner-info">
			<div class="inner-info-left">
				<?php echo esc_html__('Information','homeo') ?>
			</div>
			<div class="hidden-xs">
				<?php echo esc_html__('Expiry','homeo') ?>
			</div>
			<div class="hidden-xs">
				<?php echo esc_html__('Status','homeo') ?>
			</div>
			<div class="hidden-xs">
				<?php echo esc_html__('View','homeo') ?>
			</div>
			<div>
				<?php echo esc_html__('Action','homeo') ?>
			</div>
		</div>
	</div>
	<?php if ( $properties->have_posts() ) : ?>
		<?php while ( $properties->have_posts() ) : $properties->the_post(); global $post; ?>

			<div class="my-properties-item property-item">
				<div class="flex-middle layout-my-properties">
					<div class="property-thumbnail-wrapper">
						<?php homeo_property_display_image( $post, 'homeo-property-list' ); ?>
						<?php $is_featured = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'featured', true ); ?>
						<div class="top-label">
							<?php if ( $is_featured ) : ?>
								<span class="featured-property"><?php echo esc_html__('Featured','homeo') ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="inner-info flex-middle layout-left">
						<div class="inner-info-left">
							<h3 class="property-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

							<div class="property-date">
								<i class="flaticon-calendar"></i>
								<span><?php the_time( get_option('date_format') ); ?></span>
							</div>

							<?php homeo_property_display_full_location($post, 'icon'); ?>
            				<?php homeo_property_display_price($post, 'no-icon-title', true); ?>

						</div>
						<div class="property-info-date-expiry hidden-xs">
							<div class="property-table-info-content-expiry">
								<?php
									$expires = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX.'expiry_date', true);
									if ( $expires ) {
										echo '<span>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) ) . '</span>';
									} else {
										echo '--';
									}
								?>
							</div>
						</div>
						<div class="status-property-wrapper hidden-xs">
							<span class="status-property <?php echo esc_attr($post->post_status); ?>">
								<?php
									$post_status = get_post_status_object( $post->post_status );
									if ( !empty($post_status->label) ) {
										echo esc_html($post_status->label);
									} else {
										echo esc_html($post->post_status);
									}
								?>
							</span>
						</div>
						<div class="view-property-wrapper hidden-xs">
							<?php
								$views = get_post_meta($post->ID, WP_REALESTATE_PROPERTY_PREFIX.'views', true);
								echo WP_RealEstate_Mixes::format_number($views);
							?>
						</div>
						<div class="warpper-action-property">

							<?php
							$my_properties_page_url = get_permalink( $my_properties_page_id );
							$my_properties_page_url = add_query_arg( 'property_id', $post->ID, remove_query_arg( 'property_id', $my_properties_page_url ) );
							switch ( $post->post_status ) {
								case 'publish' :
									$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_properties_page_url ) );
									
									$edit_able = wp_realestate_get_option('user_edit_published_submission');
									if ( $edit_able !== 'no' ) {
									?>
										<a data-toggle="tooltip" href="<?php echo esc_url($edit_url); ?>" class="edit-btn btn-action-icon edit  job-table-action" title="<?php esc_attr_e('Edit', 'homeo'); ?>">
											<i class="flaticon-edit"></i>
										</a>
									<?php } ?>
									<?php
									break;
								case 'expired' :
									$relist_url = add_query_arg( 'action', 'relist', remove_query_arg( 'action', $my_properties_page_url ) );
									?>
									<a data-toggle="tooltip" href="<?php echo esc_url($relist_url); ?>" class="btn-action-icon view  job-table-action" title="<?php esc_attr_e('Relist', 'homeo'); ?>">
										<i class="flaticon-reply"></i>
									</a>
									<?php
									break;
								case 'pending_payment':
								case 'pending_approve':
								case 'pending' :
									$edit_able = wp_realestate_get_option('user_edit_published_submission');
									if ( $edit_able !== 'no' ) {
										$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_properties_page_url ) );
										?>
										<a data-toggle="tooltip" href="<?php echo esc_url($edit_url); ?>" class="edit-btn btn-action-icon edit  job-table-action" title="<?php esc_attr_e('Edit', 'homeo'); ?>">
											<i class="flaticon-edit"></i>
										</a>
										<?php
									}
								break;
								case 'draft' :
								case 'preview' :
									$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_properties_page_url ) );
									?>
									<a data-toggle="tooltip" href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon edit  job-table-action" title="<?php esc_attr_e('Continue', 'homeo'); ?>">
										<i class="flaticon-view"></i>
									</a>
									<?php
									break;
							}
							?>

							<a data-toggle="tooltip" class="remove-btn property-table-action property-button-delete" href="javascript:void(0)" data-property_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-delete-property-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'homeo'); ?>">
								<i class="flaticon-garbage"></i>
							</a>
						</div>
					</div>
				</div>

			</div>
		<?php endwhile; ?>
		<?php
			WP_RealEstate_Mixes::custom_pagination( array(
				'max_num_pages' => $properties->max_num_pages,
				'prev_text'     => '<i class="flaticon-left-arrow-1"></i>',
				'next_text'     => '<i class="flaticon-right-arrow"></i>',
				'wp_query' 		=> $properties
			));
			
			wp_reset_postdata();
		?>
	<?php else : ?>
		<div class="alert alert-warning">
			<p><?php esc_html_e( 'You don\'t have any properties yet. Start by creating new one.', 'homeo' ); ?></p>
		</div>
	<?php endif; ?>
	</div>
</div>