<?php
/**
 * Property Package
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_RealEstate_Wc_Paid_Listings_Property_Package {
	public static function init() {
		add_filter('wp-realestate-calculate-property-expiry', array( __CLASS__, 'calculate_property_expiry' ), 10, 2 );
		add_filter('wp-realestate-get-package-id-by-user-package', array( __CLASS__, 'get_package_id' ), 10, 2 );
	}

	public static function calculate_property_expiry($duration, $property_id) {
		if ( metadata_exists( 'post', $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_duration' ) ) {
			$duration = get_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_duration', true );
		}

		return $duration;
	}

	public static function get_package_id($package_id, $user_package_id) {
		$package_id = get_post_meta($user_package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'product_id', true);
		return $package_id;
	}
	
}

WP_RealEstate_Wc_Paid_Listings_Property_Package::init();