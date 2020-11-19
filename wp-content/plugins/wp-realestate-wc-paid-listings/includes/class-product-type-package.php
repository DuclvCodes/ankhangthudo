<?php
/**
 * product type: package
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wp_realestate_wc_paid_listings_register_package_product_type() {
	class WP_RealEstate_Wc_Paid_Listings_Product_Type_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			$this->product_type = 'property_package';
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'property_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_realestate_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	if ( class_exists( 'WC_Subscriptions' ) ) {
		class WP_RealEstate_Wc_Paid_Listings_Product_Type_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				$this->product_type = 'property_package_subscription';
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'property_package_subscription';
		    }

		    public function is_sold_individually() {
				return apply_filters( 'wp_realestate_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}
	}
}

add_action( 'init', 'wp_realestate_wc_paid_listings_register_package_product_type' );


function wp_realestate_wc_paid_listings_add_property_package_product( $types ) {
	$types[ 'property_package' ] = __( 'Property Package', 'wp-realestate-wc-paid-listings' );
	if ( class_exists( 'WC_Subscriptions' ) ) {
		$types['property_package_subscription'] = __( 'Property Package Subscription', 'wp-realestate-wc-paid-listings' );
	}
	return $types;
}

add_filter( 'product_type_selector', 'wp_realestate_wc_paid_listings_add_property_package_product' );

function wp_realestate_wc_paid_listings_woocommerce_product_class( $classname, $product_type ) {

    if ( $product_type == 'property_package' ) { // notice the checking here.
        $classname = 'WP_RealEstate_Wc_Paid_Listings_Product_Type_Package';
    }

    if ( class_exists( 'WC_Subscriptions' ) ) {
	    if ( $product_type == 'property_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_RealEstate_Wc_Paid_Listings_Product_Type_Package_Subscription';
	    }
    }

    return $classname;
}

add_filter( 'woocommerce_product_class', 'wp_realestate_wc_paid_listings_woocommerce_product_class', 10, 2 );


/**
 * Show pricing fields for package product.
 */
function wp_realestate_wc_paid_listings_package_custom_js() {

	if ( 'product' != get_post_type() ) {
		return;
	}

	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
			// property package
			jQuery('.product_data_tabs .general_tab').show();
        	jQuery('#general_product_data .pricing').addClass('show_if_property_package').show();
			jQuery('.inventory_options').addClass('show_if_property_package').show();
			jQuery('.inventory_options').addClass('show_if_property_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_property_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_property_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_property_package').show();

		});
	</script><?php
}
add_action( 'admin_footer', 'wp_realestate_wc_paid_listings_package_custom_js' );


function wp_realestate_wc_paid_listings_woocommerce_subscription_product_types( $types ) {
	$types[] = 'property_package_subscription';
	return $types;
}
add_filter( 'woocommerce_subscription_product_types', 'wp_realestate_wc_paid_listings_woocommerce_subscription_product_types' );


function wp_realestate_wc_paid_listings_package_options_product_tab_content() {
	global $post;
	$post_id = $post->ID;
	?>
	<!-- Property Package -->
	<div class="options_group show_if_property_package show_if_property_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_property_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-realestate-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-realestate-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_property_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-realestate-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-realestate-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_property_package_subscription',
				) );
			}

			woocommerce_wp_checkbox( array(
				'id' 		=> '_feature_properties',
				'label' 	=> __( 'Feature Properties?', 'wp-realestate-wc-paid-listings' ),
				'description'	=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-realestate-wc-paid-listings' ),
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_properties_limit',
				'label'			=> __( 'Properties Limit', 'wp-realestate-wc-paid-listings' ),
				'desc_tip'		=> 'true',
				'description'	=> __( 'The number of listings a user can post with this package', 'wp-realestate-wc-paid-listings' ),
				'type' 			=> 'number',
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_properties_duration',
				'label'			=> __( 'Properties Duration (Days)', 'wp-realestate-wc-paid-listings' ),
				'desc_tip'		=> 'true',
				'description'	=> __( 'The number of days that the listings will be active', 'wp-realestate-wc-paid-listings' ),
				'type' 			=> 'number',
			) );

			do_action('wp_realestate_wc_paid_listings_package_options_product_tab_content');
		?>
	</div>

	<?php
}
add_action( 'woocommerce_product_options_general_product_data', 'wp_realestate_wc_paid_listings_package_options_product_tab_content' );

/**
 * Save the Property Package custom fields.
 */
function wp_realestate_wc_paid_listings_save_package_option_field( $post_id ) {
	$feature_properties = isset( $_POST['_feature_properties'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_feature_properties', $feature_properties );
	
	if ( isset( $_POST['_property_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_property_package_subscription_type', sanitize_text_field( $_POST['_property_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_properties_limit'] ) ) {
		update_post_meta( $post_id, '_properties_limit', sanitize_text_field( $_POST['_properties_limit'] ) );
	}

	if ( isset( $_POST['_properties_duration'] ) ) {
		update_post_meta( $post_id, '_properties_duration', sanitize_text_field( $_POST['_properties_duration'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_property_package', 'wp_realestate_wc_paid_listings_save_package_option_field'  );
add_action( 'woocommerce_process_product_meta_property_package_subscription', 'wp_realestate_wc_paid_listings_save_package_option_field'  );