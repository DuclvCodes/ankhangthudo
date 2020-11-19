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

class WP_RealEstate_Wc_Paid_Listings_Mixes {

	public static function get_property_package_products() {
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => array('property_package', 'property_package_subscription'),
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}
	
	public static function create_user_package( $user_id, $product_id, $order_id ) {
		$package = wc_get_product( $product_id );

		if ( !$package->is_type( array('property_package', 'property_package_subscription') ) ) {
			return false;
		}

		$args = apply_filters( 'wp_realestate_wc_paid_listings_create_user_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'property_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'package_count', 0 );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'property_package' );

			// listing metas
			$feature_properties = get_post_meta($product_id, '_feature_properties', true );
			$duration_properties = get_post_meta($product_id, '_properties_duration', true );
			$limit_properties = get_post_meta($product_id, '_properties_limit', true );
			$subscription_type = get_post_meta($product_id, '_property_package_subscription_type', true );

			if ( $feature_properties == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'feature_properties', 'on' );
			}
			update_post_meta( $user_package_id, $prefix.'property_duration', $duration_properties );
			update_post_meta( $user_package_id, $prefix.'property_limit', $limit_properties );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_realestate_wc_paid_listings_create_user_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function approve_property_with_package( $property_id, $user_id, $user_package_id ) {
		if ( self::package_is_valid( $user_id, $user_package_id ) ) {

			$listing = array(
				'ID'            => $property_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			$post_type = get_post_type( $property_id );

			if ( $post_type === 'property' ) {
				delete_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'expiry_date' );

				$review_before = wp_realestate_get_option( 'submission_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$listing['post_status'] = $post_status;
				
			}

			// Do update
			wp_update_post( $listing );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'user_package_id', $user_package_id );
			self::increase_package_count( $user_id, $user_package_id );

			do_action('wp_realestate_wc_paid_listings_approve_property_with_package', $property_id, $user_id, $user_package_id);
		}
	}

	public static function package_is_valid( $user_id, $user_package_id ) {
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$package_count = get_post_meta($user_package_id, $prefix.'package_count', true);
		$property_limit = get_post_meta($user_package_id, $prefix.'property_limit', true);

		if ( ($package_user_id != $user_id) || ($package_count >= $property_limit && $property_limit != 0) ) {
			return false;
		}

		return true;
	}

	public static function increase_package_count( $user_id, $user_package_id ) {
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		
		if ( $package_user_id != $user_id ) {
			return false;
		}
		$package_count = intval(get_post_meta($user_package_id, $prefix.'package_count', true)) + 1;
		
		update_post_meta($user_package_id, $prefix.'package_count', $package_count);
	}

	public static function get_packages_by_user( $user_id, $valid = true, $package_type = 'property_package' ) {
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'user_id',
				'value'   => $user_id,
				'compare' => '='
			)
		);
		if ( $package_type != 'all' ) {
			$meta_query[] = array(
				'key'     => $prefix.'package_type',
				'value'   => $package_type,
				'compare' => '='
			);
		}
		$query_args = array(
			'post_type' => 'property_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query
		);

		$packages = get_posts($query_args);
		$return = array();
		if ( $valid && $packages ) {
			foreach ($packages as $package) {
				$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
				$property_limit = get_post_meta($package->ID, $prefix.'property_limit', true);

				if ( $package_count < $property_limit || $property_limit == 0 ) {
					$return[] = $package;
				}
				
			}
		} else {
			$return = $packages;
		}
		return $return;
	}

	public static function get_listings_for_package( $user_package_id ) {
		$prefix = WP_REALESTATE_PROPERTY_PREFIX;
		
		$query_args = array(
			'post_type' => 'property',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_package_id',
					'value'   => $user_package_id,
					'compare' => '='
				)
			)
		);
		$posts = get_posts( $query_args );

		return $posts;
	}
}

