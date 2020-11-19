<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('select2');
wp_enqueue_style('select2');
?>

<?php $my_properties_page_id = wp_realestate_get_option('my_properties_page_id'); ?>


<div class="search-orderby-wrapper">
	<div class="search-my-properties-form">
		<form action="<?php echo esc_url(get_permalink( $my_properties_page_id )); ?>" method="get">
			<div class="form-group">
				<input type="text" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
			</div>
			<div class="submit-wrapper">
				<button class="search-submit" name="submit">
					<?php esc_html_e( 'Search ...', 'wp-realestate' ); ?>
				</button>
			</div>
		</form>
	</div>
	<div class="sort-my-properties-form sortby-form">
		<?php
			$orderby_options = apply_filters( 'wp_realestate_my_properties_orderby', array(
				'menu_order'	=> esc_html__( 'Default', 'wp-realestate' ),
				'newest' 		=> esc_html__( 'Newest', 'wp-realestate' ),
				'oldest'     	=> esc_html__( 'Oldest', 'wp-realestate' ),
			) );

			$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
		?>

		<div class="orderby-wrapper">
			<span>
				<?php echo esc_html__('Sort by: ','wp-realestate'); ?>
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

<?php
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$query_vars = array(
		'post_type'     => 'property',
		'post_status'   => 'any',
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
	// query_posts($query_vars);
	$properties = new WP_Query($query_vars);

	if ( $properties->have_posts() ) : ?>
	<table class="property-table">
		<thead>
			<tr>
				<th class="property-title"><?php esc_html_e('Property Title', 'wp-realestate'); ?></th>
				<th class="property-status"><?php esc_html_e('Status', 'wp-realestate'); ?></th>
				<th class="property-actions"></th>
			</tr>
		</thead>
		<tbody>
		<?php while ( $properties->have_posts() ) : $properties->the_post(); global $post; ?>
			<tr class="my-properties-item">
				<td class="property-table-info">
					
					<div class="property-table-info-content">
						<div class="property-table-info-content-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

							<?php $is_urgent = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'urgent', true ); ?>
							<?php if ( $is_urgent ) : ?>
								<span class="urgent-lable"><?php esc_html_e( 'Urgent', 'wp-realestate' ); ?></span>
							<?php endif; ?>

							<?php $is_featured = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'featured', true ); ?>
							<?php if ( $is_featured ) : ?>
								<span class="featured-lable"><?php esc_html_e( 'Featured', 'wp-realestate' ); ?></span>
							<?php endif; ?>

						</div>

						<?php $location = WP_RealEstate_Query::get_property_location_name(); ?>
						<?php if ( ! empty( $location ) ) : ?>
							<div class="property-table-info-content-location">
								<?php echo wp_kses( $location, wp_kses_allowed_html( 'post' ) ); ?>
							</div>
						<?php endif; ?>
						
						<div class="property-table-info-content-date-expiry">
							<div class="property-table-info-content-date">
								<?php esc_html_e('Created: ', 'wp-realestate'); ?>
								<span><?php the_time( get_option('date_format') ); ?></span>
							</div>
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
					</div>
				</td>

				<td class="property-table-status min-width nowrap">
					<div class="property-table-actions-inner <?php echo esc_attr($post->post_status); ?>">
						<?php echo get_post_status(); ?>
					</div>
				</td>

				<td class="property-table-actions min-width nowrap">
					<a class="view-btn" href="<?php the_permalink(); ?>" title="<?php esc_attr_e('View', 'wp-realestate'); ?>"><?php esc_html_e('View', 'wp-realestate'); ?></a>

					<?php if ( ! empty( $my_properties_page_id ) ) :
						$edit_url = get_permalink( $my_properties_page_id );
						$edit_url = add_query_arg( 'property_id', $post->ID, remove_query_arg( 'property_id', $edit_url ) );
						$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $edit_url ) );
					?>
						<a class="edit-btn" href="<?php echo esc_url($edit_url); ?>" class="property-table-action" title="<?php esc_attr_e('Edit', 'wp-realestate'); ?>">
							<?php esc_html_e( 'Edit', 'wp-realestate' ); ?>
						</a>
					<?php endif; ?>

					<a class="remove-btn property-table-action property-button-delete" href="javascript:void(0)" data-property_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-delete-property-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'wp-realestate'); ?>">
						<?php esc_html_e( 'Remove', 'wp-realestate' ); ?>
					</a>

				</td>
			</tr>
		<?php endwhile; ?>
		</tbody>
	</table>

	<?php
		WP_RealEstate_Mixes::custom_pagination( array(
			'max_num_pages' => $properties->max_num_pages,
			'prev_text'     => '<i class="flaticon-left-arrow"></i>',
			'next_text'     => '<i class="flaticon-right-arrow"></i>',
			'wp_query' 		=> $properties
		));
		
		wp_reset_postdata();
	?>
<?php else : ?>
	<div class="alert alert-warning">
		<p><?php esc_html_e( 'You don\'t have any properties, yet. Start by creating new one.', 'wp-realestate' ); ?></p>
	</div>
<?php endif; ?>
