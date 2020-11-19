<?php
/**
 * Order
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_RealEstate_Wc_Paid_Listings_Order {

	
	public static function init() {
		add_action( 'woocommerce_thankyou', array( __CLASS__, 'woocommerce_thankyou' ), 5 );

		// Change Order Statuses
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'order_paid' ) );

		// Delete User
		add_action( 'delete_user', array( __CLASS__, 'delete_user_packages' ) );
	}

	
	public static function woocommerce_thankyou( $order_id ) {
		global $wp_post_types;

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item ) {
			if ( isset( $item['property_id'] ) && 'publish' === get_post_status( $item['property_id'] ) ) {
				switch ( get_post_status( $item['property_id'] ) ) {
					case 'pending' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once approved.', 'wp-realestate-wc-paid-listings' ), get_the_title( $item['property_id'] ) ) );
					break;
					case 'pending_payment' :
					case 'expired' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once payment has been confirmed.', 'wp-realestate-wc-paid-listings' ), get_the_title( $item['property_id'] ) ) );
					break;
					default :
						echo wpautop( sprintf( __( '%s has been submitted successfully.', 'wp-realestate-wc-paid-listings' ), get_the_title( $item['property_id'] ) ) );
					break;
				}

				echo '<p class="property-submit-done-paid-listing-actions">';

				if ( 'publish' === get_post_status( $item['property_id'] ) ) {
					echo '<a class="button" href="' . get_permalink( $item['property_id'] ) . '">' . __( 'View Property', 'wp-realestate-wc-paid-listings' ) . '</a> ';
				} elseif ( wp_realestate_get_option( 'my_properties_page_id' ) ) {
					echo '<a class="button" href="' . get_permalink( wp_realestate_get_option( 'my_properties_page_id' ) ) . '">' . __( 'View Dashboard', 'wp-realestate-wc-paid-listings' ) . '</a> ';
				}

				echo '</p>';

			}
		}
	}

	
	public static function order_paid( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( get_post_meta( $order_id, 'wp_realestate_wc_paid_listings_packages_processed', true ) ) {
			return;
		}
		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );

			if ( $product->is_type( array( 'property_package' ) ) && $order->get_customer_id() ) {

				// create packages for user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = WP_RealEstate_Wc_Paid_Listings_Mixes::create_user_package( $order->get_customer_id(), $product->get_id(), $order_id );
				}

				// Approve listing with new package
				if ( isset( $item['property_id'] ) ) {
					$listing = get_post( $item['property_id'] );

					if ( in_array( $listing->post_status, array( 'pending_payment', 'expired' ) ) ) {
						WP_RealEstate_Wc_Paid_Listings_Mixes::approve_property_with_package( $listing->ID, $order->get_customer_id(), $user_package_id );
					}
				}
			}
		}
		
		update_post_meta( $order_id, 'wp_realestate_wc_paid_listings_packages_processed', true );
	}

	
	public static function delete_user_packages( $user_id ) {
		if ( $user_id ) {
			$packages = get_posts(array(
				'post_type' => array('property_package'),
				'meta_query' => array(
					array(
						'key'     => WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'user_id',
						'value'   => $user_id,
						'compare' => '='
					)
				)
			));
			if ( !empty($packages) ) {
				foreach ($packages as $package) {
					wp_delete_post($package->ID, true);
				}
			}
		}

	}
}
WP_RealEstate_Wc_Paid_Listings_Order::init();
