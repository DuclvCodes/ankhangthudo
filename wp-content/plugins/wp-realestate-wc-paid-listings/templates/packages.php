<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( $packages ) : ?>
	<div class="widget widget-packages widget-subwoo">
		<h2 class="widget-title"><?php esc_html_e( 'Packages', 'wp-realestate-wc-paid-listings' ); ?></h2>
		<div class="row">
			<?php foreach ( $packages as $key => $package ) :
				$product = wc_get_product( $package );
				if ( ! $product->is_type( array( 'property_package' ) ) || ! $product->is_purchasable() ) {
					continue;
				}
				?>

				<div class="col-sm-4 col-xs-12">
					<div class="subwoo-inner <?php echo ($product->is_featured())?'highlight':''; ?>">
						<div class="header-sub">
							<div class="inner-sub">
								<h3 class="title"><?php echo trim($product->get_title()); ?></h3>
								<div class="price">
									<?php echo (!empty($product->get_price())) ? $product->get_price_html() : esc_html__('Free', 'wp-realestate-wc-paid-listings'); ?>
								</div>
							</div>
						</div>
						<div class="bottom-sub">
							<div class="content"><?php echo apply_filters( 'the_content', get_post_field('post_content', $product->get_id()) ) ?></div>
							<div class="button-action">
								<button class="button btn btn-danger" type="submit" name="wjbwpl_property_package" value="<?php echo esc_attr($product->get_id()); ?>" id="package-<?php echo esc_attr($product->get_id()); ?>">
									<?php esc_html_e('Get Started', 'wp-realestate-wc-paid-listings') ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>