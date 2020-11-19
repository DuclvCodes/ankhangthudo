<?php
/**
 * Package
 *
 * @package    wp-realestate-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WP_RealEstate_Wc_Paid_Listings_Post_Type_Packages {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );

    	add_action( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

    	add_filter( 'manage_edit-property_package_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_property_package_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action('restrict_manage_posts', array( __CLASS__, 'filter_property_package_by_type' ));
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => esc_html__( 'User Package', 'wp-realestate-wc-paid-listings' ),
			'singular_name'         => esc_html__( 'User Package', 'wp-realestate-wc-paid-listings' ),
			'add_new'               => esc_html__( 'Add New Package', 'wp-realestate-wc-paid-listings' ),
			'add_new_item'          => esc_html__( 'Add New Package', 'wp-realestate-wc-paid-listings' ),
			'edit_item'             => esc_html__( 'Edit Package', 'wp-realestate-wc-paid-listings' ),
			'new_item'              => esc_html__( 'New Package', 'wp-realestate-wc-paid-listings' ),
			'all_items'             => esc_html__( 'User Packages', 'wp-realestate-wc-paid-listings' ),
			'view_item'             => esc_html__( 'View Package', 'wp-realestate-wc-paid-listings' ),
			'search_items'          => esc_html__( 'Search Package', 'wp-realestate-wc-paid-listings' ),
			'not_found'             => esc_html__( 'No Packages found', 'wp-realestate-wc-paid-listings' ),
			'not_found_in_trash'    => esc_html__( 'No Packages found in Trash', 'wp-realestate-wc-paid-listings' ),
			'parent_item_colon'     => '',
			'menu_name'             => esc_html__( 'User Packages', 'wp-realestate-wc-paid-listings' ),
	    );

	    register_post_type( 'property_package',
	      	array(
		        'labels'            => apply_filters( 'wp_realestate_wc_paid_listings_postype_package_fields_labels' , $labels ),
		        'supports'          => array( 'title' ),
		        'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
		        'show_in_menu'		=> 'edit.php?post_type=property',
	      	)
	    );
  	}
	
	public static function package_types() {
		return apply_filters('wp-realestate-wc-paid-listings-package-types', array(
			'property_package' => __('Property Package', 'wp-realestate-wc-paid-listings'),
		));
	}

	public static function get_packages() {
		$packages = array( '' => __('Choose a package', 'wp-realestate-wc-paid-listings') );
		$product_packages = WP_RealEstate_Wc_Paid_Listings_Mixes::get_property_package_products();
		if ( !empty($product_packages) ) {
			foreach ($product_packages as $product) {
				$packages[$product->ID] = $product->post_title;
			}
		}
		return $packages;
	}

  	public static function fields( array $metaboxes ) {
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;


		$package_types = array_merge(array('' => __('Choose package type', 'wp-realestate-wc-paid-listings')), self::package_types());
		$metaboxes[ $prefix . 'general' ] = array(
			'id'                        => $prefix . 'general',
			'title'                     => __( 'General Options', 'wp-realestate-wc-paid-listings' ),
			'object_types'              => array( 'property_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Order Id', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'order_id',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'User id', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'user_id',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'Package Type', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'package_type',
					'type'              => 'select',
					'options'			=> $package_types
				),
			),
		);

		$packages = self::get_packages();
		$metaboxes[ $prefix . 'property_package' ] = array(
			'id'                        => $prefix . 'property_package',
			'title'                     => __( 'Property Package Options', 'wp-realestate-wc-paid-listings' ),
			'object_types'              => array( 'property_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Package Count', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'package_count',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					)
				),
				array(
					'name'              => __( 'Featured Properties', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'feature_properties',
					'type'              => 'checkbox',
					'desc'				=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-realestate-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Property duration', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'property_duration',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the properties will be active', 'wp-realestate-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Properties limit', 'wp-realestate-wc-paid-listings' ),
					'id'                => $prefix . 'property_limit',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of properties a user can post with this package', 'wp-realestate-wc-paid-listings' ),
				),
			),
		);

		return $metaboxes;
	}


	/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns() {
		$fields = array(
			'cb' 				=> '<input type="checkbox" />',
			'title' 			=> __( 'Title', 'wp-realestate' ),
			'package_type' 		=> __( 'Package Type', 'wp-realestate' ),
			'author' 			=> __( 'Author', 'wp-realestate' ),
			'date' 				=> __( 'Date', 'wp-realestate' ),
		);
		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		global $post;
		$prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
		switch ( $column ) {
			case 'package_type':
				$package_type = get_post_meta($post->ID, $prefix.'package_type', true );
				$package_types = self::package_types();
				if ( !empty($package_types[$package_type]) ) {
					echo $package_types[$package_type];
				} else {
					echo '-';
				}
				break;
		}
	}

	public static function filter_property_package_by_type() {
		global $typenow;
		if ( $typenow == 'property_package') {
			// categories
			$selected = isset($_GET['package_type']) ? $_GET['package_type'] : '';
			$package_types = self::package_types();
			if ( ! empty( $package_types ) ){
				?>
				<select name="package_type">
					<option value=""><?php esc_html_e('All package types', 'wp-realestate'); ?></option>
					<?php
					foreach ($package_types as $key => $title) {
						?>
						<option value="<?php echo esc_attr($key); ?>" <?php selected($selected, $key); ?>><?php echo esc_html($title); ?></option>
						<?php
					}
				?>
				</select>
				<?php
			}
		}
	}

}

WP_RealEstate_Wc_Paid_Listings_Post_Type_Packages::init();