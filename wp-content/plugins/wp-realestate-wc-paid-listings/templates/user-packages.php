<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( $user_packages ) : ?>
	<div class="widget widget-your-packages">
		<h2 class="widget-title"><?php esc_html_e( 'Your Packages', 'wp-realestate-wc-paid-listings' ); ?></h2>
		<div class="row">
			<?php
				$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
			foreach ( $user_packages as $key => $package ) :
				$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
				$property_limit = get_post_meta($package->ID, $prefix.'property_limit', true);
				$property_duration = get_post_meta($package->ID, $prefix.'property_duration', true);
				$feature_properties = get_post_meta($package->ID, $prefix.'feature_properties', true);
			?>
				<div class="col-sm-4 col-xs-12 user-property-package">
					<h3 class="title"><?php echo trim($package->post_title); ?></h3>
					<ul class="package-information">
						<?php
						if ( $property_limit ) {
							?>
							<li>
								<?php echo sprintf( _n( '%s property posted out of %d', '%s properties posted out of %d', $package_count, 'wp-realestate-wc-paid-listings' ), $package_count, $property_limit ); ?>
							</li>
							<?php
						} else {
							?>
							<li>
								<?php echo sprintf( _n( '%s property posted', '%s properties posted', $package_count, 'wp-realestate-wc-paid-listings' ), $package_count ); ?>
							</li>
							<?php
						}

						if ( $property_duration ) {
							?>
							<li>
								<?php echo sprintf( _n( 'listed for %s day', 'listed for %s days', $property_duration, 'wp-realestate-wc-paid-listings' ), $property_duration ); ?>
							</li>
							<?php
						}

						?>
						<li>
							<?php echo sprintf(__( 'Featured Property: %s', 'wp-realestate-wc-paid-listings' ), $feature_properties ? __( 'Yes', 'wp-realestate-wc-paid-listings' ) : __( 'No', 'wp-realestate-wc-paid-listings' )  ); ?>
						</li>
					</ul>

					<button class="btn btn-danger" type="submit" name="wjbwpl_property_package" value="user-<?php echo esc_attr($package->ID); ?>">
						<?php esc_html_e('Add Listing', 'wp-realestate-wc-paid-listings') ?>
					</button>

				</div>
			<?php endforeach; ?>
		</div>
		
	</div>
<?php endif; ?>