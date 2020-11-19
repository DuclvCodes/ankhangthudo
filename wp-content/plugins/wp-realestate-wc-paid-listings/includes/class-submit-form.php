<?php
/**
 * Submit Form
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_RealEstate_Wc_Paid_Listings_Submit_Form {
	
	public static $package_id = 0;
	public static $listing_user_package;
	public static $is_user_package = false;

	public static function init() {
		add_filter( 'wp_realestate_submit_property_steps',  array( __CLASS__, 'submit_property_steps' ), 5, 1 );

		// get listing package
		if ( ! empty( $_POST['wjbwpl_property_package'] ) ) {
			if ( is_numeric( $_POST['wjbwpl_property_package'] ) ) {
				self::$package_id = absint( $_POST['wjbwpl_property_package'] );
				self::$is_user_package = false;
			} else {
				self::$package_id = absint( substr( $_POST['wjbwpl_property_package'], 5 ) );
				self::$is_user_package = true;
			}
		} elseif ( ! empty( $_COOKIE['chosen_package_id'] ) ) {
			self::$package_id = absint( $_COOKIE['chosen_package_id'] );
			self::$is_user_package = absint( $_COOKIE['chosen_package_is_user_package'] ) === 1;
		}

		add_filter('wp-realestate-get-listing-package-id', array( __CLASS__, 'get_package_id_post' ), 10, 2);
	}

	public static function get_products() {
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

	public static function submit_property_steps($steps) {
		
		$packages = self::get_products();

		if ( !empty($packages) ) {
			$steps['wjb-choose-packages'] = array(
				'view'     => array( __CLASS__, 'choose_package' ),
				'handler'  => array( __CLASS__, 'choose_package_handler' ),
				'priority' => 1
			);

			$steps['wjb-process-packages'] = array(
				'name'     => '',
				'view'     => false,
				'handler'  => array( __CLASS__, 'process_package_handler' ),
				'priority' => 25
			);

			add_filter( 'wp_realestate_submit_property_post_status', array( __CLASS__, 'submit_property_post_status' ), 10, 2 );
		}

		return $steps;
	}

	public static function submit_property_post_status( $status, $property ) {
		switch ( $property->post_status ) {
			case 'preview' :
				return 'pending_payment';
			break;
			case 'expired' :
				return 'expired';
			break;
			default :
				return $status;
			break;
		}
		return $status;
	}

	public static function choose_package($atts = array()) {
		echo WP_RealEstate_Wc_Paid_Listings_Template_Loader::get_template_part('choose-package-form', array('atts' => $atts) );
	}

	public static function get_package_id_post($product_id, $post_id) {
		if ( !empty($post_id) ) {
			if ( self::$package_id ) {
				if ( self::$is_user_package ) {
					$package_id = get_post_meta( self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX . 'product_id', true );
					return $package_id;
				}
			} else {
				if ( metadata_exists('post', $post_id, WP_REALESTATE_PROPERTY_PREFIX.'package_id') ) {
					$package_id = get_post_meta( $post_id, WP_REALESTATE_PROPERTY_PREFIX.'package_id', true );
					return $package_id;
				}
			}
		} else {
			if ( self::$is_user_package ) {
				$package_id = get_post_meta( self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX . 'product_id', true );
				return $package_id;
			}
		}

		return self::$package_id;
	}

	public static function get_package_id() {
		if ( self::$is_user_package ) {
			$package_id = get_post_meta( self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX . 'product_id', true );
			return $package_id;
		}

		return self::$package_id;
	}


	public static function choose_package_handler() {

		if ( !isset( $_POST['security-property-submit-package'] ) || ! wp_verify_nonce( $_POST['security-property-submit-package'], 'wp-realestate-property-submit-package-nonce' )  ) {
			$this->errors[] = esc_html__('Sorry, your nonce did not verify.', 'wp-realestate-wc-paid-listings');
			return;
		}

		$form = WP_RealEstate_Submit_Form::get_instance();

		$validation = self::validate_package();

		if ( is_wp_error( $validation ) ) {
			$form->add_error( $validation->get_error_message() );
			$form->set_step( array_search( 'wjb-choose-packages', array_keys( $form->get_steps() ) ) );
			return false;
		}
		
		wc_setcookie( 'chosen_package_id', self::$package_id );
		wc_setcookie( 'chosen_package_is_user_package', self::$is_user_package ? 1 : 0 );
		

		$form->next_step();
	}

	private static function validate_package() {
		if ( empty( self::$package_id ) ) {
			return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-realestate-wc-paid-listings' ) );
		} elseif ( self::$is_user_package ) {
			if ( ! WP_RealEstate_Wc_Paid_Listings_Mixes::package_is_valid( get_current_user_id(), self::$package_id ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'wp-realestate-wc-paid-listings' ) );
			}
		} else {
			$package = wc_get_product( self::$package_id );
			if ( empty($package) || ($package->get_type() != 'property_package' && ! $package->is_type( 'property_package_subscription' ) ) ) {
				return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-realestate-wc-paid-listings' ) );
			}

			// Don't let them buy the same subscription twice if the subscription is for the package
			if ( class_exists( 'WC_Subscriptions' ) && is_user_logged_in() && $package->is_type( 'property_package_subscription' ) && 'package' === WP_RealEstate_Wc_Paid_Listings_Property_Package_Subscription::get_package_subscription_type(self::$package_id) ) {
				if ( wcs_user_has_subscription( get_current_user_id(), self::$package_id, 'active' ) ) {
					return new WP_Error( 'error', __( 'You already have this subscription.', 'wp-realestate-wc-paid-listings' ) );
				}
			}
		}

		return true;
	}

	public static function process_package_handler() {
		$form = WP_RealEstate_Submit_Form::get_instance();
		$property_id = $form->get_property_id();
		$post_status = get_post_status( $property_id );

		if ( $post_status == 'preview' ) {
			$update_property = array(
				'ID' => $property_id,
				'post_status' => 'pending_payment',
				'post_date' => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_author' => get_current_user_id(),
			);

			wp_update_post( $update_property );
		}

		if ( self::$is_user_package ) {
			$product_id = get_post_meta(self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'product_id', true);
			// Featured
			$feature_properties = get_post_meta(self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'feature_properties', true );
			$featured = '';
			if ( !empty($feature_properties) && $feature_properties === 'yes' ) {
				$featured = 'on';
			}
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX. 'featured', $featured );
			//
			$property_duration = get_post_meta(self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'property_duration', true );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_duration', $property_duration );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_id', $product_id );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'user_package_id', self::$package_id );

			$subscription_type = get_post_meta(self::$package_id, WP_REALESTATE_WC_PAID_LISTINGS_PREFIX.'subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			// Approve the property
			if ( in_array( get_post_status( $property_id ), array( 'pending_payment', 'expired' ) ) ) {
				WP_RealEstate_Wc_Paid_Listings_Mixes::approve_property_with_package( $property_id, get_current_user_id(), self::$package_id );
			}

			
			do_action( 'wjbwpl_process_user_package_handler', self::$package_id, $property_id );

			$form->next_step();
		} elseif ( self::$package_id ) {
			
			// Featured
			$feature_properties = get_post_meta(self::$package_id, '_feature_properties', true );
			$featured = '';
			if ( !empty($feature_properties) && $feature_properties === 'yes' ) {
				$featured = 'on';
			}
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'featured', $featured );
			//
			$property_duration = get_post_meta(self::$package_id, '_properties_duration', true );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_duration', $property_duration );
			update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'package_id', self::$package_id );
			
			$subscription_type = get_post_meta(self::$package_id, '_property_package_subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $property_id, WP_REALESTATE_PROPERTY_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			WC()->cart->add_to_cart( self::$package_id, 1, '', '', array(
				'property_id' => $property_id
			) );

			wc_add_to_cart_message( self::$package_id );

			// remove cookie
			wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );
			wc_setcookie( 'chosen_package_is_user_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wjbwpl_process_package_handler', self::$package_id, $property_id );

			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}
	}

}

WP_RealEstate_Wc_Paid_Listings_Submit_Form::init();