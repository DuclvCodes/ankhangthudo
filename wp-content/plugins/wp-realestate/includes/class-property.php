<?php
/**
 * Property Listing
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Property {
	
	public static function init() {
		// loop
		add_action( 'wp_realestate_before_property_archive', array( __CLASS__, 'display_properties_results_filters' ), 5 );
		add_action( 'wp_realestate_before_property_archive', array( __CLASS__, 'display_properties_count_results' ), 10 );

		add_action( 'wp_realestate_before_property_archive', array( __CLASS__, 'display_properties_orderby_start' ), 15 );
		add_action( 'wp_realestate_before_property_archive', array( __CLASS__, 'display_properties_orderby' ), 25 );
		add_action( 'wp_realestate_before_property_archive', array( __CLASS__, 'display_properties_orderby_end' ), 100 );
		
		// Ajax endpoints.
		// chart
		add_action( 'wre_ajax_wp_realestate_get_property_chart', array( __CLASS__, 'get_chart_data' ) );

		// nearby yelp
		add_action( 'wre_ajax_wp_realestate_get_nearby_yelp', array( __CLASS__, 'get_nearby_yelp_data' ) );

		// walk score
		add_action( 'wre_ajax_wp_realestate_get_walk_score', array( __CLASS__, 'get_walk_score_data' ) );

		// download attachtment
		add_action('wre_ajax_wp_realestate_ajax_download_attachment', array( __CLASS__, 'process_download_attachment' ) );


		// compatible handlers.
		// chart
		add_action( 'wp_ajax_wp_realestate_get_property_chart', array( __CLASS__, 'get_chart_data' ) );
		add_action( 'wp_ajax_nopriv_wp_realestate_get_property_chart', array( __CLASS__, 'get_chart_data' ) );

		// nearby yelp
		add_action( 'wp_ajax_wp_realestate_get_nearby_yelp', array( __CLASS__, 'get_nearby_yelp_data' ) );
		add_action( 'wp_ajax_nopriv_wp_realestate_get_nearby_yelp', array( __CLASS__, 'get_nearby_yelp_data' ) );

		// walk score
		add_action( 'wp_ajax_wp_realestate_get_walk_score', array( __CLASS__, 'get_walk_score_data' ) );
		add_action( 'wp_ajax_nopriv_wp_realestate_get_walk_score', array( __CLASS__, 'get_walk_score_data' ) );

		// download cv
		add_action('wp_ajax_wp_realestate_ajax_download_attachment', array( __CLASS__, 'process_download_attachment' ) );
		add_action('wp_ajax_nopriv_wp_realestate_ajax_download_attachment', array( __CLASS__, 'process_download_attachment' ) );
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_REALESTATE_PROPERTY_PREFIX.$key, $single);
	}
	
	// add product viewed
	public static function track_property_view() {
	    if ( ! is_singular( 'property' ) ) {
	        return;
	    }

	    global $post;

	    $today = date('Y-m-d', time());
	    $views_by_date = get_post_meta($post->ID, '_views_by_date', true);

	    if( $views_by_date != '' || is_array($views_by_date) ) {
	        if (!isset($views_by_date[$today])) {
	            if ( count($views_by_date) > 60 ) {
	                array_shift($views_by_date);
	            }
	            $views_by_date[$today] = 1;
	        } else {
	            $views_by_date[$today] = intval($views_by_date[$today]) + 1;
	        }
	    } else {
	        $views_by_date = array();
	        $views_by_date[$today] = 1;
	    }
	    $views = get_post_meta($post->ID, WP_REALESTATE_PROPERTY_PREFIX.'views', true);
	    if ( empty($views) ) {
	    	$views = 1;
	    } else {
	    	$views++;
	    }

	    update_post_meta($post->ID, '_views_by_date', $views_by_date);
	    update_post_meta($post->ID, '_recently_viewed', $today);
	    update_post_meta($post->ID, WP_REALESTATE_PROPERTY_PREFIX.'views', $views);
	}

	public static function send_admin_expiring_notice() {
		global $wpdb;

		if ( !wp_realestate_get_option('admin_notice_expiring_listing') ) {
			return;
		}
		$days_notice = wp_realestate_get_option('admin_notice_expiring_listing_days');

		self::get_expiring_properties($days_notice);

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				// send email here.
				$property = get_post($property_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_from, $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'admin_notice_expiring_listing', 'subject');
				$content = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'admin_notice_expiring_listing', 'content');
				
				WP_RealEstate_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
		}
	}

	public static function send_agent_expiring_notice() {
		global $wpdb;

		if ( !wp_realestate_get_option('agent_notice_expiring_listing') ) {
			return;
		}
		$days_notice = wp_realestate_get_option('agent_notice_expiring_listing_days');

		self::get_expiring_properties($days_notice);

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				// send email here.
				$property = get_post($property_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_from, $email_from );
				$email_to = get_the_author_meta( 'user_email', $property->post_author );
				$subject = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'agent_notice_expiring_listing', 'subject');
				$content = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'agent_notice_expiring_listing', 'content');
				
				WP_RealEstate_Email::wp_mail( $email_to, $subject, $content, $headers );
				
			}
		}
	}

	public static function get_expiring_properties($days_notice) {
		global $wpdb;
		
		$prefix = WP_REALESTATE_PROPERTY_PREFIX;

		$notice_before_ts = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * $days_notice );
		$property_ids          = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = %s
			AND postmeta.meta_value = %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'property'
			",
			$prefix.'expiry_date',
			date( 'Y-m-d', $notice_before_ts )
		) );

		return $property_ids;
	}

	public static function check_for_expired_properties() {
		global $wpdb;

		$prefix = WP_REALESTATE_PROPERTY_PREFIX;
		
		// Change status to expired.
		$property_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = %s
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'property'",
				$prefix.'expiry_date',
				date( 'Y-m-d', current_time( 'timestamp' ) )
			)
		);

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				$property_data                = array();
				$property_data['ID']          = $property_id;
				$property_data['post_status'] = 'expired';
				wp_update_post( $property_data );
			}
		}

		// Delete old expired properties.
		if ( apply_filters( 'wp_realestate_delete_expired_properties', false ) ) {
			$property_ids = $wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID FROM {$wpdb->posts} as posts
					WHERE posts.post_type = 'property'
					AND posts.post_modified < %s
					AND posts.post_status = 'expired'",
					date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_realestate_delete_expired_properties_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
				)
			);

			if ( $property_ids ) {
				foreach ( $property_ids as $property_id ) {
					wp_trash_post( $property_id );
				}
			}
		}
	}

	/**
	 * Deletes old previewed properties after 30 days to keep the DB clean.
	 */
	public static function delete_old_previews() {
		global $wpdb;

		// Delete old expired properties.
		$property_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'property'
				AND posts.post_modified < %s
				AND posts.post_status = 'preview'",
				date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_realestate_delete_old_previews_properties_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
			)
		);

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				wp_delete_post( $property_id, true );
			}
		}
	}

	public static function property_statuses() {
		return apply_filters(
			'wp_realestate_property_statuses',
			array(
				'draft'           => _x( 'Draft', 'post status', 'wp-realestate' ),
				'expired'         => _x( 'Expired', 'post status', 'wp-realestate' ),
				'preview'         => _x( 'Preview', 'post status', 'wp-realestate' ),
				'pending'         => _x( 'Pending approval', 'post status', 'wp-realestate' ),
				'pending_approve' => _x( 'Pending approval', 'post status', 'wp-realestate' ),
				'pending_payment' => _x( 'Pending payment', 'post status', 'wp-realestate' ),
				'publish'         => _x( 'Active', 'post status', 'wp-realestate' ),
			)
		);
	}

	public static function is_property_status_changing( $from_status, $to_status ) {
		return isset( $_POST['post_status'] ) && isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== $_POST['post_status'] && ( null === $from_status || $from_status === $_POST['original_post_status'] ) && $to_status === $_POST['post_status'];
	}

	public static function calculate_property_expiry( $property_id ) {
		$duration = absint( wp_realestate_get_option( 'submission_duration' ) );
		$duration = apply_filters( 'wp-realestate-calculate-property-expiry', $duration, $property_id);

		if ( $duration ) {
			return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		}

		return '';
	}
	
	public static function is_featured( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$featured = self::get_post_meta( $post_id, 'featured', true );
		$return = $featured ? true : false;
		return apply_filters( 'wp-realestate-property-is-featured', $return, $post_id );
	}
	
	public static function process_download_attachment() {
	    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
	    $attachment_id = absint($attachment_id);

	    $error_page_url = home_url('/404-error');

	    if ( $attachment_id > 0 ) {

	        $file_post = get_post($attachment_id);
	        $file_path = get_attached_file($attachment_id);

	        if ( !$file_post || !$file_path || !file_exists($file_path) ) {
	            wp_redirect($error_page_url);
	        } else {
	        	
	            header('Content-Description: File Transfer');
	            header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: " . $file_post->post_mime_type);
				header('Content-Disposition:attachment; filename="'. basename($file_path) .'"');
				header("Content-Type: application/force-download");
				header('Content-Length: ' . @filesize($file_path));

	            @readfile($file_path);
	            exit;
	        }
	    } else {
	        wp_redirect($error_page_url);
	    }

	    die;
	}

	public static function get_chart_data() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-property-chart-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( empty($_REQUEST['property_id']) ) {
			$return = array( 'status' => 'error', 'html' => esc_html__('Property not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}
		$property_id = $_REQUEST['property_id'];
		$return = array(
			'stats_labels' => self::get_traffic_labels($property_id),
			'stats_values' => self::get_traffic_data($property_id),
			'stats_view' => esc_html__('Views', 'wp-realestate'),
			'chart_type' => apply_filters('wp-realestate-property-stats-type', 'line'),
			'bg_color' => apply_filters('wp-realestate-property-stats-bg-color', 'rgb(255, 99, 132)'),
	        'border_color' => apply_filters('wp-realestate-property-stats-border-color', 'rgb(255, 99, 132)'),
		);
		echo json_encode($return);
		die();
	}

	public static function get_traffic_labels( $property_id ) {
	    $number_days = apply_filters('wp-realestate-get-traffic-data-nb-days', 15);
	    if( empty($number_days) ) {
	        $number_days = 15;
	    }

	    $views_by_date = get_post_meta($property_id, '_views_by_date', true);

	    if (!is_array($views_by_date)) {
	        $views_by_date = array();
	    }
	    $array_labels = array_keys($views_by_date);
	    $array_labels = array_slice( $array_labels, -1 * $number_days, $number_days, false );

	    return $array_labels;
	}

	public static function get_traffic_data($property_id) {
	    $number_days = apply_filters('wp-realestate-get-traffic-data-nb-days', 15);
	    if( empty($number_days) ) {
	        $number_days = 15;
	    }

	    $views_by_date = get_post_meta( $property_id, '_views_by_date', true );
	    if ( !is_array( $views_by_date ) ) {
	        $views_by_date = array();
	    }
	    $array_values = array_values( $views_by_date );
	    $array_values = array_slice( $array_values, -1 * $number_days, $number_days, false );

	    return $array_values;
	}

	public static function get_nearby_yelp_data() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-property-yelp-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( empty($_REQUEST['property_id']) ) {
			$return = array( 'status' => 'error', 'html' => esc_html__('Property not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}

		$property_id = (int)$_POST['property_id'];

		$meta_obj = WP_RealEstate_Property_Meta::get_instance($property_id);

		$location = $meta_obj->get_post_meta( 'map_location_address' );
		$latitude = $meta_obj->get_post_meta( 'map_location_latitude' );
		$longitude = $meta_obj->get_post_meta( 'map_location_longitude' );

		$all_cats = WP_RealEstate_Property_Yelp::get_yelp_categories();
		$terms = wp_realestate_get_option('api_settings_yelp_categories');
		$limit = apply_filters('wp-realestate-nearby-yelp-limit', 3);

		if ( is_array($terms) && !empty($location) && !empty($latitude) && !empty($longitude) ) {

			$result = WP_RealEstate_Template_Loader::get_template_part( 'single-property/nearby_yelp-content', array(
				'property_id' => $property_id,
				'all_cats' => $all_cats,
				'terms' => $terms,
				'location' => $location,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'limit' => $limit,
			) );
			$html = '';
			$result = trim($result);
			if ( !empty($result) ) {
				$html = '<div class="property-section property-yelp-places">
						<div class="property-section-heading">
        					<h3>'.esc_html__('Yelp Nearby Places', 'wp-realestate').'</h3>
        					<div class="yelp-logo">
								<a href="//yelp.com" target="_blank">
									<span>'.esc_html__('Powered by', 'wp-realestate').'</span>
									<img src="' . get_template_directory_uri() . '/images/yelp-logo.png" alt="">
								</a>
							</div>
        				</div>
        				<div class="property-section-content">'.$result.'</div>
        			</div>';

    			$return = array( 'status' => 'success', 'html' => apply_filters('wp-realestate-get-nearby-yelp-data', $html) );
			}
		}

		if ( empty($return) ) {
			$return = array( 'status' => false, 'html' => esc_html__('do not have yelp', 'wp-realestate') );
		}
		echo json_encode( $return );
		exit();
	}

	function get_walk_score_data() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-property-walk-score-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( empty($_REQUEST['property_id']) ) {
			$return = array( 'status' => 'error', 'html' => esc_html__('Property not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}

		
		$property_id = (int)$_POST['property_id'];
		$walkscore_api_key = wp_realestate_get_option('api_settings_walk_score_api_key', '');
		
		$meta_obj = WP_RealEstate_Property_Meta::get_instance($property_id);

		$latitude = $meta_obj->get_post_meta( 'map_location_latitude' );
		$longitude = $meta_obj->get_post_meta( 'map_location_longitude' );


		if ( $walkscore_api_key != '' && !empty( $latitude ) && !empty( $longitude ) ) {
			
			$result = WP_RealEstate_Template_Loader::get_template_part( 'single-property/walk_score-content', array(
				'property_id' => $property_id,
				'walkscore_api_key' => $walkscore_api_key,
				'latitude' => $latitude,
				'longitude' => $longitude,
			) );
			$html = '';
			$result = trim($result);
			if ( !empty($result) ) {
				$html = '<div class="property-section property-walk-score">
						<div class="property-section-heading">
        					<h3>'.esc_html__('Walk Score', 'wp-realestate').'</h3>
        					<div class="walkscore-logo">
				                <a href="https://www.walkscore.com" target="_blank">
				                    <img src="//cdn.walk.sc/images/api-logo.png" alt="'.esc_html__('Walk Scores', 'wp-realestate').'">
				                </a>
				            </div>
        				</div>
        				<div class="property-section-content">'.$result.'</div>
        			</div>';

    			$return = array( 'status' => 'success', 'html' => apply_filters('wp-realestate-get-walk-score-data', $html) );
			}
		}

		if ( empty($return) ) {
			$return = array( 'status' => 'error', 'html' => esc_html__('do not have walk score', 'wp-realestate') );
		}

		echo json_encode( $return );
		exit();
	}	

	public static function get_property_taxs( $post_id = null, $tax = 'property_status' ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$types = get_the_terms( $post_id, $tax );
		return $types;
	}

	public static function get_property_types_html( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$output = '';
		$types = self::get_property_taxs( $post_id, 'property_type' );
		if ( $types ) {
            foreach ($types as $term) {
                $output .= '<a href="'.get_term_link($term).'">'.wp_kses_post($term->name).'</a>';
            }
        }
		return apply_filters( 'wp-realestate-get-property-types-html', $output, $post_id );
	}

	public static function display_properties_results_filters() {
		$filters = WP_RealEstate_Abstract_Filter::get_filters();

		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/results-filters', array('filters' => $filters));
	}

	public static function display_properties_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);

		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/results-count', $args);
	}

	public static function display_properties_save_search() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/properties-save-search-form');
	}

	public static function display_properties_orderby() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/orderby');
	}

	public static function display_properties_orderby_start() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/orderby-start');
	}

	public static function display_properties_orderby_end() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/property/orderby-end');
	}
}
WP_RealEstate_Property::init();