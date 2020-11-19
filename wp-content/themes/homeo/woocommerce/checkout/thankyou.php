<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit; ?>

<div class="box-white-inner">
	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'homeo' ); ?></p>

			<p class="woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'homeo' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'homeo' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'homeo' ), $order ); ?></p>

			<ul class="woocommerce-thankyou-order-details order_details">
				<li class="order">
					<?php esc_html_e( 'Order Number:', 'homeo' ); ?>
					<strong><?php echo trim($order->get_order_number()); ?></strong>
				</li>
				<li class="date">
					<?php esc_html_e( 'Date:', 'homeo' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
				</li>
				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'homeo' ); ?>
						<strong><?php echo trim($order->get_billing_email()); ?></strong>
					</li>
				<?php endif; ?>
				<li class="total">
					<?php esc_html_e( 'Total:', 'homeo' ); ?>
					<strong><?php echo trim($order->get_formatted_order_total()); ?></strong>
				</li>
				<?php if ( $order->get_payment_method_title() ) : ?>
				<li class="method">
					<?php esc_html_e( 'Payment Method:', 'homeo' ); ?>
					<strong><?php echo trim($order->get_payment_method_title()); ?></strong>
				</li>
				<?php endif; ?>
			</ul>
			<div class="clear"></div>

		<?php endif; ?>
		<div class="woo-pay-perfect text-theme">
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		</div>

	<?php else : ?>

		<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'homeo' ), null ); ?></p>

	<?php endif; ?>
	<div class="refund-shop">
		<a class="btn btn-theme" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php echo esc_html__('COUNTINUE SHOPPING','homeo') ?></a>
	</div>
</div>

<?php if ( $order ) : ?>
	<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>	
<?php endif; ?>