<?php
/**
 * Agent Filter
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Agent_Filter extends WP_RealEstate_Abstract_Filter {
	
	public static function init() {
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'taxonomy' ) );
	}

	public static function get_fields() {
		return apply_filters( 'wp-realestate-default-agent-filter-fields', array(
			'title'	=> array(
				'name' => __( 'Search Keywords', 'wp-realestate' ),
				'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_input'),
				'placeholder' => __( 'Write Agent Name', 'wp-realestate' ),
				'toggle' => true,
				'for_post_type' => 'agent',
			),
			'category' => array(
				'name' => __( 'Category', 'wp-realestate' ),
				'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
				'taxonomy' => 'agent_category',
				'placeholder' => __( 'All Categories', 'wp-realestate' ),
				'toggle' => true,
				'for_post_type' => 'agent',
			),
			'location' => array(
				'name' => __( 'Location List', 'wp-realestate' ),
				'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
				'taxonomy' => 'agent_location',
				'placeholder' => __( 'All Locations', 'wp-realestate' ),
				'toggle' => true,
				'for_post_type' => 'agent',
			),
			'center-location' => array(
				'name' => __( 'Location', 'wp-realestate' ),
				'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_input_location'),
				'placeholder' => __( 'All Location', 'wp-realestate' ),
				'show_distance' => true,
				'toggle' => true,
				'for_post_type' => 'agent',
			),
		));
	}
	
	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'agent' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'agent' || $suppress_filters ) {
			return;
		}

		$limit = wp_realestate_get_option('number_agents_per_page', 10);
		$query_vars = &$query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;
		
		return self::filter_query( $query );
	}

	public static function taxonomy($query) {
		$is_correct_taxonomy = false;
		if ( is_tax( 'agent_category' ) || is_tax( 'agent_location' ) || apply_filters( 'wp-realestate-agent-query-taxonomy', false ) ) {
			$is_correct_taxonomy = true;
		}

		if ( ! $is_correct_taxonomy  || ! $query->is_main_query() || is_admin() ) {
			return;
		}

		$limit = wp_realestate_get_option('number_agents_per_page', 10);
		$query_vars = $query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;

		return self::filter_query( $query );
	}


	public static function filter_query( $query = null, $params = array() ) {
		global $wpdb, $wp_query;

		if ( empty( $query ) ) {
			$query = $wp_query;
		}

		if ( empty( $params ) ) {
			$params = $_GET;
		}
		
		// Filter params
		$params = apply_filters( 'wp_realestate_agent_filter_params', $params );

		// Initialize variables
		$query_vars = $query->query_vars;
		$query_vars = self::get_query_var_filter($query_vars, $params);
		$query->query_vars = $query_vars;

		// Meta query
		$meta_query = self::get_meta_filter($params);
		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}

		// Tax query
		$tax_query = self::get_tax_filter($params);
		if ( $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}
		
		return apply_filters('wp-realestate-agent-filter-query', $query, $params);
	}

	public static function get_query_var_filter($query_vars, $params) {
		$ids = null;
		$query_vars = self::orderby($query_vars, $params);

		// Agent title
		if ( ! empty( $params['filter-title'] ) ) {
			global $wp_realestate_agent_keyword;
			$wp_realestate_agent_keyword = sanitize_text_field( wp_unslash($params['filter-title']) );
			$query_vars['s'] = sanitize_text_field( wp_unslash($params['filter-title']) );
		}

		$distance_ids = self::filter_by_distance($params);
		if ( !empty($distance_ids) ) {
			$ids = self::build_post_ids( $ids, $distance_ids );
		}
    	
    	
		if ( ! empty( $params['filter-author'] ) ) {
			$query_vars['author'] = sanitize_text_field( wp_unslash($params['filter-author']) );
		}

		// Post IDs
		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			$query_vars['post__in'] = $ids;
		}
		
		return $query_vars;
	}

	public static function get_meta_filter($params) {
		$meta_query = array();
		
		if ( ! empty( $params['filter-featured'] ) ) {
			$meta_query[] = array(
				'key'       => WP_REALESTATE_AGENT_PREFIX . 'featured',
				'value'     => 'on',
				'compare'   => '==',
			);
		}

		return $meta_query;
	}

	public static function get_tax_filter($params) {
		$tax_query = array();
		
		if ( ! empty( $params['filter-category'] ) ) {
			if ( is_array($params['filter-category']) ) {
				$field = is_numeric( $params['filter-category'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-category'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'agent_category',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-category'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'agent_category',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-category']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-location'] ) ) {
			if ( is_array($params['filter-location']) ) {
				$field = is_numeric( $params['filter-location'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-location'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'agent_location',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-location'] ) ? 'term_id' : 'slug';
				
				$tax_query[] = array(
					'taxonomy'  => 'agent_location',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-location']) ),
					'compare'   => '==',
				);
			}
		}

		return $tax_query;
	}

	public static function display_filter_value($key, $value, $filters) {
		$url = urldecode(WP_RealEstate_Mixes::get_full_current_url());
		
		switch ($key) {
			case 'filter-category':
				self::render_filter_tax($key, $value, 'agent_category', $url);
				break;
			case 'filter-location':
				self::render_filter_tax($key, $value, 'agent_location', $url);
				break;
			case 'filter-distance':
				if ( !empty($filters['filter-center-location']) ) {
					$distance_type = apply_filters( 'wp_realestate_filter_distance_type', 'miles' );
					$title = $value.' '.$distance_type;
					$rm_url = self::remove_url_var( $key . '=' . $value, $url);
					self::render_filter_result_item( $title, $rm_url );
				}
				break;
			case 'filter-featured':
				$title = esc_html__('Featured', 'wp-realestate');
				$rm_url = self::remove_url_var($key . $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-author':
				$user_info = get_userdata($value);
				if ( is_object($user_info) ) {
					$title = $user_info->display_name;
				} else {
					$title = $value;
				}
				$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-orderby':
				$orderby_options = apply_filters( 'wp-realestate-agents-orderby', array(
					'menu_order' => esc_html__('Default', 'wp-realestate'),
					'newest' => esc_html__('Newest', 'wp-realestate'),
					'oldest' => esc_html__('Oldest', 'wp-realestate'),
					'random' => esc_html__('Random', 'wp-realestate'),
				));
				$title = $value;
				if ( !empty($orderby_options[$value]) ) {
					$title = $orderby_options[$value];
				}
				$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			default:
				if ( is_array($value) ) {
					foreach ($value as $val) {
						$rm_url = self::remove_url_var( $key . '[]=' . $val, $url);
						self::render_filter_result_item( $val, $rm_url);
					}
				} else {
					$rm_url = self::remove_url_var( $key . '=' . $value, $url);
					self::render_filter_result_item( $value, $rm_url);
				}
				
				break;
		}
	}


	public static function display_filter_value_simple($key, $value, $filters) {
		
		switch ($key) {
			case 'filter-category':
				self::render_filter_tax_simple($key, $value, 'agent_category', esc_html__('Category', 'wp-realestate'));
				break;
			case 'filter-location':
				self::render_filter_tax_simple($key, $value, 'agent_location', esc_html__('Location', 'wp-realestate'));
				break;
			case 'filter-distance':
				if ( !empty($filters['filter-center-location']) ) {
					$distance_type = apply_filters( 'wp_realestate_filter_distance_type', 'miles' );
					$title = $value.' '.$distance_type;
					self::render_filter_result_item_simple( $title, esc_html__('Distance', 'wp-realestate') );
				}
				break;
			case 'filter-featured':
				$title = esc_html__('Yes', 'wp-realestate');
				self::render_filter_result_item_simple( $title, esc_html__('Featured', 'wp-realestate') );
				break;
			case 'filter-author':
				$user_info = get_userdata($value);
				if ( is_object($user_info) ) {
					$title = $user_info->display_name;
				} else {
					$title = $value;
				}
				self::render_filter_result_item_simple( $title, esc_html__('Author', 'wp-realestate') );
				break;
			case 'filter-orderby':
				$orderby_options = apply_filters( 'wp-realestate-agents-orderby', array(
					'menu_order' => esc_html__('Default', 'wp-realestate'),
					'newest' => esc_html__('Newest', 'wp-realestate'),
					'oldest' => esc_html__('Oldest', 'wp-realestate'),
					'random' => esc_html__('Random', 'wp-realestate'),
				));
				$title = $value;
				if ( !empty($orderby_options[$value]) ) {
					$title = $orderby_options[$value];
				}
				self::render_filter_result_item_simple( $title, esc_html__('Orderby', 'wp-realestate') );
				break;
			default:
				$label = str_replace('filter-custom-', '', $key);
				$label = str_replace('filter-', '', $label);
				$label = str_replace('-', ' ', $label);
				if ( is_array($value) ) {
					foreach ($value as $val) {
						self::render_filter_result_item_simple( $val, $label);
					}
				} else {
					self::render_filter_result_item_simple( $value, $label);
				}
				
				break;
		}
	}
}

WP_RealEstate_Agent_Filter::init();