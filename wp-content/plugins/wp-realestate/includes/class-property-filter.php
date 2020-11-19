<?php
/**
 * Property Filter
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Property_Filter extends WP_RealEstate_Abstract_Filter {
	
	public static function init() {
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'taxonomy' ) );

		add_filter( 'wp-realestate-property-filter-query', array( __CLASS__, 'filter_query_property' ), 10, 2 );
  		add_filter( 'wp-realestate-property-query-args', array( __CLASS__, 'filter_query_args_property' ), 10, 2 );
	}

	public static function get_fields() {
		return apply_filters( 'wp-realestate-default-property-filter-fields', array(
			'center-location' => array(
				'name' => __( 'Location', 'wp-realestate' ),
				'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_input_location'),
				'placeholder' => __( 'All Location', 'wp-realestate' ),
				'show_distance' => true,
				'toggle' => true,
				'for_post_type' => 'property',
			),
		));
	}
	
	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'property' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'property' || $suppress_filters ) {
			return;
		}

		$limit = wp_realestate_get_option('number_properties_per_page', 10);
		$query_vars = &$query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;
		
		return self::filter_query( $query );
	}

	public static function taxonomy($query) {
		$is_correct_taxonomy = false;
		if ( is_tax( 'property_type' ) || is_tax( 'property_amenity' ) || is_tax( 'property_location' ) || is_tax( 'property_status' ) || is_tax( 'property_label' ) || is_tax( 'property_material' ) || apply_filters( 'wp-realestate-property-query-taxonomy', false ) ) {
			$is_correct_taxonomy = true;
		}

		if ( ! $is_correct_taxonomy  || ! $query->is_main_query() || is_admin() ) {
			return;
		}

		$limit = wp_realestate_get_option('number_properties_per_page', 10);
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
		$params = apply_filters( 'wp_realestate_property_filter_params', $params );

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
		
		return apply_filters('wp-realestate-property-filter-query', $query, $params);
	}

	public static function get_query_var_filter($query_vars, $params) {
		$ids = null;
		$query_vars = self::orderby($query_vars, $params);

		// Property title
		if ( ! empty( $params['filter-title'] ) ) {
			global $wp_realestate_property_keyword;
			$wp_realestate_property_keyword = sanitize_text_field( wp_unslash($params['filter-title']) );
			$query_vars['s'] = sanitize_text_field( wp_unslash($params['filter-title']) );
			add_filter( 'posts_search', array( __CLASS__, 'get_properties_keyword_search' ) );
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
		// price
		if ( isset($params['filter-price-from']) && intval($params['filter-price-from']) >= 0 && isset($params['filter-price-to']) && intval($params['filter-price-to']) > 0) {
			if ( $params['filter-price-from'] == 0 ) {
				$meta_query[] = array(
					'relation' => 'OR',
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'price',
			           	'value' => array( intval($params['filter-price-from']), intval($params['filter-price-to']) ),
			           	'compare'   => 'BETWEEN',
						'type'      => 'NUMERIC',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'price',
			           	'value' => '',
			           	'compare'   => '==',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'price',
			           	'compare'   => 'NOT EXISTS',
					),
		       	);
			} else {
				$meta_query[] = array(
		           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'price',
		           	'value' => array( intval($params['filter-price-from']), intval($params['filter-price-to']) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);
			}
		}

		if ( ! empty( $params['filter-featured'] ) ) {
			$meta_query[] = array(
				'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'featured',
				'value'     => 'on',
				'compare'   => '==',
			);
		}

		// Rooms
	    if ( ! empty( $params['filter-rooms'] ) ) {
		    $meta_query[] = array(
			    'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'rooms',
			    'value'     => sanitize_text_field( wp_unslash($params['filter-rooms']) ),
			    'compare'   => '>=',
			    'type'      => 'NUMERIC',
		    );
	    }

		// Beds
		if ( ! empty( $params['filter-beds'] ) ) {
			$meta_query[] = array(
				'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'beds',
				'value'     => sanitize_text_field( wp_unslash($params['filter-beds']) ),
				'compare'   => '>=',
				'type'      => 'NUMERIC',
			);
		}

	    // Year built
	    if ( ! empty( $params['filter-year_built'] ) ) {
		    $meta_query[] = array(
			    'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'year_built',
			    'value'     => sanitize_text_field( wp_unslash($params['filter-year_built']) ),
			    'compare'   => '>=',
			    'type'      => 'NUMERIC',
		    );
	    }

		// Baths
		if ( ! empty( $params['filter-baths'] ) ) {
			$meta_query[] = array(
				'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'baths',
				'value'     => sanitize_text_field( wp_unslash($params['filter-baths']) ),
				'compare'   => '>=',
				'type'      => 'NUMERIC',
			);
		}

		if ( isset($params['filter-home_area-from']) && intval($params['filter-home_area-from']) >= 0 && isset($params['filter-home_area-to']) && intval($params['filter-home_area-to']) > 0) {
			if ( $params['filter-home_area-from'] == 0 ) {
				$meta_query[] = array(
					'relation' => 'OR',
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'home_area',
			           	'value' => array( intval($params['filter-home_area-from']), intval($params['filter-home_area-to']) ),
			           	'compare'   => 'BETWEEN',
						'type'      => 'NUMERIC',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'home_area',
			           	'value' => '',
			           	'compare'   => '==',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'home_area',
			           	'compare'   => 'NOT EXISTS',
					),
		       	);
			} else {
				$meta_query[] = array(
		           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'home_area',
		           	'value' => array( intval($params['filter-home_area-from']), intval($params['filter-home_area-to']) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);
			}
		}

		if ( isset($params['filter-lot_area-from']) && intval($params['filter-lot_area-from']) >= 0 && isset($params['filter-lot_area-to']) && intval($params['filter-lot_area-to']) > 0) {
			if ( $params['filter-lot_area-from'] == 0 ) {
				$meta_query[] = array(
					'relation' => 'OR',
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'lot_area',
			           	'value' => array( intval($params['filter-lot_area-from']), intval($params['filter-lot_area-to']) ),
			           	'compare'   => 'BETWEEN',
						'type'      => 'NUMERIC',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'lot_area',
			           	'value' => '',
			           	'compare'   => '==',
					),
					array(
			           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'lot_area',
			           	'compare'   => 'NOT EXISTS',
					),
		       	);
			} else {
				$meta_query[] = array(
		           	'key' => WP_REALESTATE_PROPERTY_PREFIX . 'lot_area',
		           	'value' => array( intval($params['filter-lot_area-from']), intval($params['filter-lot_area-to']) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);
			}
		}
		
		// Garages
		if ( ! empty( $params['filter-garages'] ) ) {
			$meta_query[] = array(
				'key'       => WP_REALESTATE_PROPERTY_PREFIX . 'garages',
				'value'     => sanitize_text_field( wp_unslash($params['filter-garages']) ),
				'compare'   => '>=',
				'type'      => 'NUMERIC',
			);
		}

		return $meta_query;
	}

	public static function get_tax_filter($params) {
		$tax_query = array();
		if ( ! empty( $params['filter-status'] ) ) {
			if ( is_array($params['filter-status']) ) {
				$field = is_numeric( $params['filter-status'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-status'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_status',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-status'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_status',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-status']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-type'] ) ) {
			if ( is_array($params['filter-type']) ) {
				$field = is_numeric( $params['filter-type'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-type'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_type',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-type'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_type',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-type']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-location'] ) ) {
			if ( is_array($params['filter-location']) ) {
				$field = is_numeric( $params['filter-location'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-location'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_location',
					'field'     => $field,
					'terms'     => array_values($params['filter-location']),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-location'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_location',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-location']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-amenity'] ) ) {
			if ( is_array($params['filter-amenity']) ) {
				$field = is_numeric( $params['filter-amenity'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-amenity'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_amenity',
					'field'     => $field,
					'terms'     => array_values($params['filter-amenity']),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-amenity'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_amenity',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-amenity']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-material'] ) ) {
			if ( is_array($params['filter-material']) ) {
				$field = is_numeric( $params['filter-material'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-material'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_material',
					'field'     => $field,
					'terms'     => array_values($params['filter-material']),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-material'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_material',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-material']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-label'] ) ) {
			if ( is_array($params['filter-label']) ) {
				$field = is_numeric( $params['filter-label'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-label'] ) ) );
				$tax_query[] = array(
					'taxonomy'  => 'property_label',
					'field'     => $field,
					'terms'     => array_values($params['filter-label']),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-label'] ) ? 'term_id' : 'slug';
				$tax_query[] = array(
					'taxonomy'  => 'property_label',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-label']) ),
					'compare'   => '==',
				);
			}
		}

		return $tax_query;
	}

	public static function get_properties_keyword_search( $search ) {
		global $wpdb, $wp_realestate_property_keyword;

		// Searchable Meta Keys: set to empty to search all meta keys.
		$searchable_meta_keys = array(
			WP_REALESTATE_PROPERTY_PREFIX.'address',
			WP_REALESTATE_PROPERTY_PREFIX.'property_id',
		);

		$searchable_meta_keys = apply_filters( 'wp_realestate_searchable_meta_keys', $searchable_meta_keys );

		// Set Search DB Conditions.
		$conditions = array();

		// Search Post Meta.
		if ( apply_filters( 'wp_realestate_search_post_meta', true ) ) {

			// Only selected meta keys.
			if ( $searchable_meta_keys ) {
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ( '" . implode( "','", array_map( 'esc_sql', $searchable_meta_keys ) ) . "' ) AND meta_value LIKE '%" . esc_sql( $wp_realestate_property_keyword ) . "%' )";
			} else {
				// No meta keys defined, search all post meta value.
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%" . esc_sql( $wp_realestate_property_keyword ) . "%' )";
			}
		}

		// Search taxonomy.
		$conditions[] = "{$wpdb->posts}.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id WHERE t.name LIKE '%" . esc_sql( $wp_realestate_property_keyword ) . "%' )";
		
		$conditions = apply_filters( 'wp_realestate_search_conditions', $conditions, $wp_realestate_property_keyword );
		if ( empty( $conditions ) ) {
			return $search;
		}

		$conditions_str = implode( ' OR ', $conditions );

		if ( ! empty( $search ) ) {
			$search = preg_replace( '/^ AND /', '', $search );
			$search = " AND ( {$search} OR ( {$conditions_str} ) )";
		} else {
			$search = " AND ( {$conditions_str} )";
		}
		remove_filter( 'posts_search', array( __CLASS__, 'get_properties_keyword_search' ) );
		return $search;
	}

	public static function filter_query_property($query, $params) {
    	$query_vars = $query->query_vars;

		$meta_query = self::filter_meta($query_vars, $params);

		$query->set('meta_query', $meta_query);
		return $query;
    }

    public static function filter_query_args_property($query_vars, $params) {
    	$meta_query = self::filter_meta($query_vars, $params);

		$query_vars['meta_query'] = $meta_query;
		return $query_vars;
    }

    public static function filter_meta($query_args, $params) {
    	if ( isset($query_args['meta_query']) ) {
			$meta_query = $query_args['meta_query'];
		} else {
			$meta_query = array();
		}
		if ( empty($params) || !is_array($params) ) {
			return $meta_query;
		}
		$filter_fields = WP_RealEstate_Custom_Fields::filter_custom_fields(array());

		$cfielddate = [];
    	foreach ( $params as $key => $value ) {
    		if ( !empty($value) && strrpos( $key, 'filter-cfielddate-', -strlen( $key ) ) !== false ) {
    			$cfielddate[$key] = $value;
    		}
			if ( !empty($value) && strrpos( $key, 'filter-cfield-', -strlen( $key ) ) !== false ) {
				$custom_key = str_replace( 'filter-cfield-', '', $key );

		        if ( !empty($filter_fields[$custom_key]) ) {
		            $fielddata = $filter_fields[$custom_key];

		            $field_type = $fielddata['type'];
		            $meta_key = $custom_key;

		            switch ($field_type) {
		            	
		            	case 'text':
		            	case 'textarea':
		            	case 'wysiwyg':
		            	case 'number':
		            	case 'url':
		            	case 'email':
		            		$meta_query[] = array(
								'key'       => $meta_key,
								'value'     => $value,
								'compare'   => 'LIKE',
							);
		            		break;
	            		case 'radio':
	            		case 'select':
	            		case 'pw_select':
		            		$meta_query[] = array(
								'key'       => $meta_key,
								'value'     => $value,
								'compare'   => '=',
							);
		            		break;
	            		case 'checkbox':
	            			$meta_query[] = array(
								'key'       => $meta_key,
								'value'     => 'on',
								'compare'   => '=',
							);
							break;
	            		case 'pw_multiselect':
	            		case 'multiselect':
	            		case 'multicheck':
	            			if ( is_array($value) ) {
	            				$multi_meta = array( 'relation' => 'OR' );
	            				foreach ($value as $val) {
	            					$multi_meta[] = array(
	            						'key'       => $meta_key,
										'value'     => '"'.$val.'"',
										'compare'   => 'LIKE',
	            					);
	            				}
	            				$meta_query[] = $multi_meta;
	            			} else {
	            				$meta_query[] = array(
									'key'       => $meta_key,
									'value'     => '"'.$value.'"',
									'compare'   => 'LIKE',
								);
	            			}
	            			break;
		            }
		        }
			}
		}
		if ( !empty($cfielddate) ) {
			
			foreach ( $cfielddate as $key => $values ) {
				if ( !empty($values) && is_array($values) && count($values) == 2 ) {
					$custom_key = str_replace( 'filter-cfielddate-', '', $key );

			        if ( !empty($filter_fields[$custom_key]) ) {
			            $fielddata = $filter_fields[$custom_key];

			            $field_type = $fielddata['type'];
			            $meta_key = $custom_key;

			            
						if ( !empty($values['from']) && !empty($values['to']) ) {
							$meta_query[] = array(
					           	'key' => $meta_key,
					           	'value' => array($values['from'], $values['to']),
					           	'compare'   => 'BETWEEN',
								'type' 		=> 'DATE',
							);
						} elseif ( !empty($values['from']) && empty($values['to']) ) {
							$meta_query[] = array(
					           	'key' => $meta_key,
					           	'value' => $values['from'],
					           	'compare'   => '>',
								'type' 		=> 'DATE',
					       	);
						} elseif (empty($values['from']) && !empty($values['to']) ) {
							$meta_query[] = array(
					           	'key' => $meta_key,
					           	'value' => $values['to'],
					           	'compare'   => '<',
								'type' 		=> 'DATE',
					       	);
						}

			        }
				}
			}
		}

		if ( !empty($params['filter-counter']) ) {
			foreach ( $params['filter-counter'] as $key => $value ) {
				if ( !empty($value) && strrpos( $key, 'filter-cfield-', -strlen( $key ) ) !== false ) {
					$custom_key = str_replace( 'filter-cfield-', '', $key );

			        if ( !empty($filter_fields[$custom_key]) ) {
			            $fielddata = $filter_fields[$custom_key];

			            $field_type = $fielddata['type'];

			            $meta_key = $custom_key;
			            switch ($field_type) {
			            	
			            	case 'text':
			            	case 'textarea':
			            	case 'wysiwyg':
			            	case 'number':
			            	case 'url':
			            	case 'email':
			            		$meta_query[] = array(
									'key'       => $meta_key,
									'value'     => $value,
									'compare'   => 'LIKE',
								);
			            		break;
		            		case 'radio':
		            		case 'select':
		            		case 'pw_select':
			            		$meta_query[] = array(
									'key'       => $meta_key,
									'value'     => $value,
									'compare'   => '=',
								);
			            		break;
		            		case 'checkbox':
		            			$meta_query[] = array(
									'key'       => $meta_key,
									'value'     => 'on',
									'compare'   => '=',
								);
								break;
		            		case 'pw_multiselect':
		            		case 'multiselect':
		            		case 'multicheck':
		            			if ( is_array($value) ) {
		            				$multi_meta = array( 'relation' => 'OR' );
		            				foreach ($value as $val) {
		            					$multi_meta[] = array(
		            						'key'       => $meta_key,
											'value'     => '"'.$val.'"',
											'compare'   => 'LIKE',
		            					);
		            				}
		            				$meta_query[] = $multi_meta;
		            			} else {
		            				$meta_query[] = array(
										'key'       => $meta_key,
										'value'     => '"'.$value.'"',
										'compare'   => 'LIKE',
									);
		            			}
		            			break;
			            }
			        }
				}
			}
		}
		
		return $meta_query;
    }

	public static function display_filter_value($key, $value, $filters) {
		$url = urldecode(WP_RealEstate_Mixes::get_full_current_url());
		if ( is_array($value) ) {
			$value = array_filter( array_map( 'sanitize_title', wp_unslash( $value ) ) );
		} else {
			$value = sanitize_text_field( wp_unslash($value) );
		}
		switch ($key) {
			case 'filter-status':
				self::render_filter_tax($key, $value, 'property_status', $url);
				break;
			case 'filter-location':
				self::render_filter_tax($key, $value, 'property_location', $url);
				break;
			case 'filter-type':
				self::render_filter_tax($key, $value, 'property_type', $url);
				break;
			case 'filter-amenity':
				self::render_filter_tax($key, $value, 'property_amenity', $url);
				break;
			case 'filter-price':
				if ( isset($value[0]) && isset($value[1]) ) {
					$from = WP_RealEstate_Price::format_price($value[0], true);
					$to = WP_RealEstate_Price::format_price($value[1], true);
					
					$rm_url = self::remove_url_var($key . '-from=' . $value[0], $url);
					$rm_url = self::remove_url_var($key . '-to=' . $value[1], $rm_url);
					self::render_filter_result_item( $from.' - '.$to, $rm_url );
				}
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
				$orderby_options = apply_filters( 'wp-realestate-properties-orderby', array(
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
		if ( is_array($value) ) {
			$value = array_filter( array_map( 'sanitize_title', wp_unslash( $value ) ) );
		} else {
			$value = sanitize_text_field( wp_unslash($value) );
		}
		switch ($key) {
			case 'filter-status':
				self::render_filter_tax_simple($key, $value, 'property_status', esc_html__('Status', 'wp-realestate'));
				break;
			case 'filter-location':
				self::render_filter_tax_simple($key, $value, 'property_location', esc_html__('Location', 'wp-realestate'));
				break;
			case 'filter-type':
				self::render_filter_tax_simple($key, $value, 'property_type', esc_html__('Type', 'wp-realestate'));
				break;
			case 'filter-amenity':
				self::render_filter_tax_simple($key, $value, 'property_amenity', esc_html__('Tag', 'wp-realestate'));
				break;
			case 'filter-price':
				if ( isset($value[0]) && isset($value[1]) ) {
					$from = WP_RealEstate_Price::format_price($value[0]);
					$to = WP_RealEstate_Price::format_price($value[1]);
					
					self::render_filter_result_item_simple( $from.' - '.$to, esc_html__('Price', 'wp-realestate') );
				}
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
				$orderby_options = apply_filters( 'wp-realestate-properties-orderby', array(
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

WP_RealEstate_Property_Filter::init();