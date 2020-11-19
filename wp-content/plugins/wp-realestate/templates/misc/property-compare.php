<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !empty($property_ids) && is_array($property_ids) ) {
	$query_args = array(
		'post_type'         => 'property',
		'posts_per_page'    => -1,
		'paged'    			=> 1,
		'post_status'       => 'publish',
		'post__in'       	=> $property_ids,
		'fields'			=> 'ids'
	);

	$properties = new WP_Query($query_args);
	if ( $properties->have_posts() ) {
		?>
		<div class="wrapper-compare">
			<table class="compare-tables">
				<thead>
					<tr>
						<th>
							<?php esc_html_e('Basic Info', 'wp-realestate'); ?>
						</th>
						<?php
						foreach ($properties->posts as $property_id) {
							$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($property_id);
							$price = $obj_property_meta->get_price_html();
							?>
							<th>
								<div class="thumb">
									<?php if ( has_post_thumbnail( $property_id ) ) : ?>
										<?php echo get_the_post_thumbnail( $property_id, 'thumbnail' ); ?>
						            <?php endif; ?>
						            <a href="javascript:void(0);" class="btn-remove-property-compare" data-property_id="<?php echo esc_attr($property_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-remove-property-compare-nonce' )); ?>">
										<i class="flaticon-cross" aria-hidden="true"></i> remove
									</a>
								</div>

								<?php if ( $price ) { ?>
					                <div class="property-price"><?php echo $price; ?></div>
					            <?php } ?>

								<h3 class="entry-title"><a href="<?php echo esc_url(get_permalink( $property_id )); ?>"><?php echo get_the_title( $property_id ) ?></a></h3>
							</th>
							<?php
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
						$compare_fields = WP_RealEstate_Compare::compare_fields();
						$count = 0;
						foreach ($compare_fields as $key => $field) {
							if ( wp_realestate_get_option('enable_compare_'.$field['id'], 'on') == 'on' ) {
								?>
								<tr class="<?php echo esc_attr($count%2 == 0 ? 'tr-0' : 'tr-1'); ?>">
									<td><?php echo trim($field['name']); ?></td>
									<?php foreach ($properties->posts as $property_id) { ?>
										<td>
											<?php echo trim(WP_RealEstate_Compare::get_data($key, $property_id, $field)); ?>
										</td>
									<?php } ?>
								</tr>
								<?php
								$count++;
							}
						}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
} else {
?>
	<div class="not-found"><?php esc_html_e('No properties found.', 'wp-realestate'); ?></div>
<?php
}