<?php
/**
 * Property Package Subscription
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_RealEstate_Wc_Paid_Listings_Property_Package_Subscription {
	public static function init() {
		if ( class_exists( 'WC_Subscriptions_Synchroniser' ) && method_exists( 'WC_Subscriptions_Synchroniser', 'save_subscription_meta' ) ) {
			add_action( 'woocommerce_process_product_meta_property_package_subscription', array('WC_Subscriptions_Synchroniser', 'save_subscription_meta'), 10 );
		}

		add_action( 'added_post_meta', array( __CLASS__, 'updated_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( __CLASS__, 'updated_post_meta' ), 10, 4 );

		add_filter( 'woocommerce_is_subscription', array( __CLASS__, 'woocommerce_is_subscription' ), 10, 2 );

		add_action( 'wp_trash_post', array( __CLASS__, 'wp_trash_post' ) );
		add_action( 'untrash_post', array( __CLASS__, 'untrash_post' ) );

		add_action( 'publish_to_expired', array( __CLASS__, 'check_expired_listing' ) );


		// Subscription is paused
		add_action( 'woocommerce_subscription_status_on-hold', array( __CLASS__, 'subscription_paused' ) ); // When a subscription is put on hold

		// Subscription is ended
		add_action( 'woocommerce_scheduled_subscription_expiration', array( __CLASS__, 'subscription_ended' ) ); // When a subscription expires
		add_action( 'woocommerce_scheduled_subscription_end_of_prepaid_term', array( __CLASS__, 'subscription_ended' ) ); // When a subscription ends after remaining unpaid
		add_action( 'woocommerce_subscription_status_cancelled', array( __CLASS__, 'subscription_ended' ) ); // When the subscription status changes to cancelled

		// Subscription starts
		add_action( 'woocommerce_subscription_status_active', array( __CLASS__, 'subscription_activated' ) ); // When the subscription status changes to active

		// On renewal
		add_action( 'woocommerce_subscription_renewal_payment_complete', array( __CLASS__, 'subscription_renewed' ) ); // When the subscription is renewed

		// Subscription is switched
		add_action( 'woocommerce_subscriptions_switched_item', array( __CLASS__, 'subscription_switched' ), 10, 3 ); // When the subscription is switched and a new subscription is created
		add_action( 'woocommerce_subscription_item_switched', array( __CLASS__, 'subscription_item_switched' ), 10, 4 ); // When the subscription is switched and only the item is 
	}

	public static function updated_post_meta($meta_id, $object_id, $meta_key, $meta_value) {
		$post_type = get_post_type( $object_id );
		if ( $post_type === 'property') {
			$prefix = WP_REALESTATE_PROPERTY_PREFIX;
			if ( $meta_value !== '' && $prefix.'expiry_date' === $meta_key ) {
				$package_id = get_post_meta( $object_id, $prefix.'package_id', true );
				$package = wc_get_product( $package_id );
				$subscription_type = get_post_meta($package_id, '_property_package_subscription_type', true);

				if ( $package && 'listing' === $subscription_type ) {
					update_post_meta( $object_id, $prefix.'expiry_date', '' ); // Never expire automatically
				}
			}
		}
	}

	public static function woocommerce_is_subscription( $is_subscription, $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product && $product->is_type( array( 'property_package_subscription' ) ) ) {
			$is_subscription = true;
		}
		return $is_subscription;
	}

	public static function get_package_subscription_type( $product_id ) {
		$subscription_type = get_post_meta( $product_id, '_package_subscription_type', true );
		return empty( $subscription_type ) ? 'package' : $subscription_type;
	}

	public static function wp_trash_post( $id ) {
		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( $post_type === 'property' ) {
				$prefix = WP_REALESTATE_PROPERTY_PREFIX;
				$package_product_id = get_post_meta( $id, $prefix.'package_id', true );
				$user_package_id = get_post_meta( $id, $prefix.'user_package_id', true );

				if ( $package_product_id ) {
					$subscription_type = self::get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$new_count = get_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', true);
						$new_count --;

						update_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', $new_count);
					}
				}
			}
		}
	}

	/**
	 * If a listing gets restored, the pack may need it's listing count changing
	 */
	public static function untrash_post( $id ) {
		if ( $id > 0 ) {
			$post_type = get_post_type( $id );

			if ( 'property' === $post_type ) {
				$prefix = WP_REALESTATE_PROPERTY_PREFIX;
				$package_product_id = get_post_meta( $id, $prefix.'package_id', true );
				$user_package_id = get_post_meta( $id, $prefix.'user_package_id', true );

				if ( $package_product_id ) {
					$subscription_type = self::get_package_subscription_type( $package_product_id );

					if ( 'listing' === $subscription_type ) {
						$new_count = get_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', true);
						$new_count++;
						$property_limit = get_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'property_limit', true);
						$new_count = min( $property_limit, $new_count );

						update_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', $new_count);
					}
				}
			}
		}
	}

	public static function check_expired_listing( $post ) {
		if ( 'property' === $post->post_type ) {
			$prefix = WP_REALESTATE_PROPERTY_PREFIX;
			$package_product_id = get_post_meta( $post->ID, $prefix.'package_id', true );
			$user_package_id = get_post_meta( $post->ID, $prefix.'user_package_id', true );
			

			if ( $package_product_id ) {
				$subscription_type = self::get_package_subscription_type( $package_product_id );

				if ( 'listing' === $subscription_type ) {
					$new_count = get_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', true);
					$new_count --;
					$new_count = max( 0, $new_count );

					update_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'package_count', $new_count);

					// Remove package meta after adjustment
					delete_post_meta( $post->ID, $prefix.'package_id' );
					delete_post_meta( $post->ID, $prefix.'user_package_id' );
				}
			}
		}
	}

	public static function subscription_paused( $subscription ) {
		self::subscription_ended( $subscription );
	}

	public static function subscription_ended( $subscription ) {
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		$legacy_id = $subscription->get_parent()->get_id() ? $subscription->get_parent()->get_id() : $subscription->get_id();
		foreach ( $subscription->get_items() as $item ) {
			

			$user_packages = get_posts(array(
				'post_type' => array('property_package'),
				'fields' => 'ids',
				'meta_query' => array(
					array(
						'key'     => $prefix.'order_id',
						'value'   => array($legacy_id, $subscription->get_id()),
						'compare' => 'IN'
					),
					array(
						'key'     => $prefix.'product_id',
						'value'   => $item['product_id'],
						'compare' => '='
					),
				)
			));

			if ( $user_packages ) {
				foreach ($user_packages as $user_package_id) {
					$package_type = get_post_meta( $user_package_id, $prefix.'package_type', true );
					$subscription_type = get_post_meta( $user_package_id, $prefix.'subscription_type', true );
					if ( $package_type == 'property_package' ) {
						// Expire listings posted with package

						if ( 'listing' === $subscription_type ) {
							$listing_ids = WP_RealEstate_Wc_Paid_Listings_Mixes::get_listings_for_package( $user_package_id  );
							foreach ( $listing_ids as $listing_id ) {
								$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
								wp_update_post( $listing );

								// Make a record of the subscription ID in case of re-activation
								update_post_meta( $listing_id, '_expired_subscription_id', $subscription->get_id() );
							}
						}
					}

					// Delete the package
					wp_delete_post($user_package_id);
				}
				
			}
		}

		delete_post_meta( $subscription->get_id(), 'wp_realestate_wc_paid_listings_packages_processed' );
	}

	public static function subscription_activated( $subscription ) {
		global $wpdb;
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;

		if ( get_post_meta( $subscription->get_id(), 'wp_realestate_wc_paid_listings_packages_processed', true ) ) {
			return;
		}

		// Remove any old packages for this subscription
		$legacy_id = $subscription->get_parent()->get_id() ? $subscription->get_parent()->get_id() : $subscription->get_id();

		foreach ( $subscription->get_items() as $item ) {
			$user_packages = get_posts(array(
				'post_type' => array('property_package'),
				'fields' => 'ids',
				'meta_query' => array(
					array(
						'key'     => $prefix.'order_id',
						'value'   => array($legacy_id, $subscription->get_id()),
						'compare' => 'IN'
					),
					array(
						'key'     => $prefix.'product_id',
						'value'   => $item['product_id'],
						'compare' => '='
					),
				)
			));

			if ( $user_packages ) {
				foreach ($user_packages as $user_package_id) {
					wp_delete_post($user_package_id);
				}
			}

			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = self::get_package_subscription_type( $item['product_id'] );

			// Give user packages for this subscription
			if ( $product->is_type( array( 'property_package_subscription' ) ) && $subscription->get_user_id() && ! isset( $item['switched_subscription_item_id'] ) ) {

				// Give packages to user
				for ( $i = 0; $i < $item['qty']; $i ++ ) {
					$user_package_id = WP_RealEstate_Wc_Paid_Listings_Mixes::create_user_package( $subscription->get_user_id(), $product->get_id(), $subscription->get_id() );
				}

				/**
				 * If the subscription is associated with listings, see if any
				 * already match this ID and approve them (useful on
				 * re-activation of a sub).
				 */
				if ( 'listing' === $subscription_type ) {
					$listing_ids = (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s", '_expired_subscription_id', $subscription->get_id() ) );
				} else {
					$listing_ids = array();
				}

				$listing_ids[] = isset( $item['property_id'] ) ? $item['property_id'] : '';
				$listing_ids   = array_unique( array_filter( array_map( 'absint', $listing_ids ) ) );

				foreach ( $listing_ids as $listing_id ) {
					if ( in_array( get_post_status( $listing_id ), array( 'pending_payment', 'expired' ) ) ) {
						WP_RealEstate_Wc_Paid_Listings_Mixes::approve_property_with_package( $listing_id, $subscription->get_user_id(), $user_package_id );
						delete_post_meta( $listing_id, '_expired_subscription_id' );
					}
				}
			}
		}

		update_post_meta( $subscription->get_id(), 'wp_realestate_wc_paid_listings_packages_processed', true );
	}

	public static function subscription_renewed( $subscription ) {
		global $wpdb;
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;

		foreach ( $subscription->get_items() as $item ) {
			$product           = wc_get_product( $item['product_id'] );
			$subscription_type = self::get_package_subscription_type( $item['product_id'] );
			$legacy_id         = $subscription->get_parent()->get_id() ? $subscription->get_parent()->get_id() : $subscription->get_id();

			// Renew packages which refresh every term
			$user_packages = get_posts(array(
				'post_type' => array('property_package'),
				'fields' => 'ids',
				'meta_query' => array(
					array(
						'key'     => $prefix.'order_id',
						'value'   => array($legacy_id, $subscription->get_id()),
						'compare' => 'IN'
					),
					array(
						'key'     => $prefix.'product_id',
						'value'   => $item['product_id'],
						'compare' => '='
					),
				)
			));
			if ( 'package' === $subscription_type ) {
				if ( $user_packages ) {
					foreach ($user_packages as $user_package_id) {
						$package_type = get_post_meta( $user_package_id, $prefix.'package_type', true );
						if ( $package_type == 'property_package' ) {
							update_post_meta($user_package_id, $prefix.'package_count', 0);
						}
					}
				} else {
					if ( $product->get_type() == 'property_package_subscription' ) {
						WP_RealEstate_Wc_Paid_Listings_Mixes::create_user_package( $subscription->get_user_id(), $item['product_id'], $subscription->get_id() );
					}
				}

			// Otherwise the listings stay active, but we can ensure they are synced in terms of featured status etc
			} else {
				if ( $user_packages ) {
					foreach ( $user_packages as $user_package_id ) {
						$package_type = get_post_meta( $user_package_id, $prefix.'package_type', true );
						if ( $package_type == 'property_package' ) {
							$feature_properties = get_post_meta($user_package_id, $prefix.'feature_properties', true );
							$featured = $feature_properties === 'yes' ? 1 : 0;
							if ( $listing_ids = WP_RealEstate_Wc_Paid_Listings_Mixes::get_listings_for_package( $user_package_id ) ) {
								foreach ( $listing_ids as $listing_id ) {
									// Featured or not
									update_post_meta( $listing_id, WP_REALESTATE_PROPERTY_PREFIX. 'featured', $featured );
								}
							}
						}
					}
				}
			}
		}
	}

	public static function subscription_switched( $subscription, $new_order_item, $old_order_item ) {
		global $wpdb;

		$new_subscription = (object) array(
			'id'         => $subscription->get_id(),
			'product_id' => $new_order_item['product_id'],
			'product'    => wc_get_product( $new_order_item['product_id'] ),
			'type'       => self::get_package_subscription_type( $new_order_item['product_id'] )
		);

		$old_subscription = (object) array(
			'id'         => $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d ", $new_order_item['switched_subscription_item_id'] ) ),
			'product_id' => $old_order_item['product_id'],
			'product'    => wc_get_product( $old_order_item['product_id'] ),
			'type'       => self::get_package_subscription_type( $old_order_item['product_id'] )
		);

		self::switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	public static function subscription_item_switched( $order, $subscription, $new_order_item_id, $old_order_item_id ) {
		global $wpdb;

		$new_order_item = WC_Subscriptions_Order::get_item_by_id( $new_order_item_id );
		$old_order_item = WC_Subscriptions_Order::get_item_by_id( $old_order_item_id );

		$new_subscription = (object) array(
			'id'           => $subscription->get_id(),
			'subscription' => $subscription,
			'product_id'   => $new_order_item['product_id'],
			'product'      => wc_get_product( $new_order_item['product_id'] ),
			'type'         => self::get_package_subscription_type( $new_order_item['product_id'] )
		);

		$old_subscription = (object) array(
			'id'           => $subscription->get_id(),
			'subscription' => $subscription,
			'product_id'   => $old_order_item['product_id'],
			'product'      => wc_get_product( $old_order_item['product_id'] ),
			'type'         => self::get_package_subscription_type( $old_order_item['product_id'] )
		);

		self::switch_package( $subscription->get_user_id(), $new_subscription, $old_subscription );
	}

	public static function switch_package( $user_id, $new_subscription, $old_subscription ) {
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		// Get the user package
		$user_packages = get_posts(array(
			'post_type' => array('property_package'),
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key'     => $prefix.'order_id',
					'value'   => $old_subscription->id,
					'compare' => '='
				),
				array(
					'key'     => $prefix.'product_id',
					'value'   => $item['product_id'],
					'compare' => '='
				),
			)
		));
		if ( $user_packages ) {

			// If invalid, abort
			if ( ! $new_subscription->product->is_type( array( 'property_package_subscription' ) ) ) {
				return false;
			}

			foreach ($user_packages as $user_package_id) {
				$package_type = get_post_meta( $user_package_id, $prefix.'package_type', true );
				if ( $package_type == 'property_package' ) {
					// Give new package to user
					$switching_to_package_id = WP_RealEstate_Wc_Paid_Listings_Mixes::create_user_package( $user_id, $new_subscription->product_id, $new_subscription->id );

					// Upgrade?
					$package_count = get_post_meta($user_package_id, $prefix.'package_count', true);
					$limit_properties = get_post_meta($new_subscription->product_id, '_properties_limit', true );
					$is_upgrade = ( 0 === $limit_properties || $limit_properties >= $package_count );

					// Delete the old package
					wp_delete_post($user_package_id);

					// Update old listings
					if ( 'listing' === $new_subscription->type && $switching_to_package_id ) {
						$listing_ids = WP_RealEstate_Wc_Paid_Listings_Mixes::get_listings_for_package( $user_package_id );

						$feature_properties = get_post_meta($switching_to_package_id, $prefix.'feature_properties', true );
						$featured = $feature_properties === 'yes' ? 1 : 0;

						foreach ( $listing_ids as $listing_id ) {
							// If we are not upgrading, expire the old listing
							if ( ! $is_upgrade ) {
								$listing = array( 'ID' => $listing_id, 'post_status' => 'expired' );
								wp_update_post( $listing );
							} else {
								WP_RealEstate_Wc_Paid_Listings_Mixes::increase_package_count( $user_id, $switching_to_package_id );
								// Change the user package ID and package ID
								update_post_meta( $listing_id, WP_REALESTATE_PROPERTY_PREFIX.'user_package_id', $switching_to_package_id );
								update_post_meta( $listing_id, WP_REALESTATE_PROPERTY_PREFIX.'package_id', $new_subscription->product_id );
							}

							// Featured or not
							update_post_meta( $listing_id, WP_REALESTATE_PROPERTY_PREFIX.'featured', $featured );
							// Fire action
							do_action( 'wp_realestate_wc_paid_listings_switched_subscription', $listing_id, $user_package_id );
						}
					}
				}
			}
		}
	}


}

WP_RealEstate_Wc_Paid_Listings_Property_Package_Subscription::init();