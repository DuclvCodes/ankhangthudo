<?php
/**
 * Price
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Query {
	
	public static function get_posts( $params = array(), $filter_params = null ) {
		$params = wp_parse_args( $params, array(
			'post_type' => 'property',
			'post_per_page' => -1,
			'paged' => 1,
			'post_status' => 'publish',
			'post__in' => array(),
			'post__not_in' => array(),
			'fields' => null, // ids
			'author' => null,
			'meta_query' => null,
			'tax_query' => null,
			'orderby' => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			),
			'order' => 'DESC',
			's' => ''
		));
		extract($params);

		$query_args = array(
			'post_type'         => $post_type,
			'paged'         	=> $paged,
			'posts_per_page'    => $post_per_page,
			'post_status'       => $post_status,
			'orderby'       	=> $orderby,
			'order'       		=> $order,
		);

		if ( !empty($post__in) ) {
	    	$query_args['post__in'] = $post__in;
	    }
	    
	    if ( !empty($post__not_in) ) {
	    	$query_args['post__not_in'] = $post__not_in;
	    }

	    if ( !empty($s) ) {
	    	$query_args['s'] = $s;
	    }

	    if ( !empty($fields) ) {
	    	$query_args['fields'] = $fields;
	    }

	    if ( !empty($author) ) {
	    	$query_args['author'] = $author;
	    }

	    if ( !empty($meta_query) ) {
	    	$query_args['meta_query'] = $meta_query;
	    }

	    if ( !empty($tax_query) ) {
	    	$query_args['tax_query'] = $tax_query;
	    }
	    
	    if ( $filter_params != null ) {
			// TODO: apply filter params
			switch ($post_type) {
				case 'property':
					$query_args = WP_RealEstate_Property_Filter::get_query_var_filter($query_args, $filter_params);

					// Meta query
					$meta_query = WP_RealEstate_Property_Filter::get_meta_filter($filter_params);
					
					if ( $meta_query ) {
						$query_args['meta_query'] = $meta_query;
					}

					// Tax query
					$tax_query = WP_RealEstate_Property_Filter::get_tax_filter($filter_params);
					if ( $tax_query ) {
						$query_args['tax_query'] = $tax_query;
					}

					break;
				case 'agent':
					$query_args = WP_RealEstate_Agent_Filter::get_query_var_filter($query_args, $filter_params);

					// Meta query
					$meta_query = WP_RealEstate_Agent_Filter::get_meta_filter($filter_params);
					if ( $meta_query ) {
						$query_args['meta_query'] = $meta_query;
					}
					
					// Tax query
					$tax_query = WP_RealEstate_Agent_Filter::get_tax_filter($filter_params);
					if ( $tax_query ) {
						$query_args['tax_query'] = $tax_query;
					}

					break;
				case 'agency':
					$query_args = WP_RealEstate_Agency_Filter::get_query_var_filter($query_args, $filter_params);

					// Meta query
					$meta_query = WP_RealEstate_Agency_Filter::get_meta_filter($filter_params);
					if ( $meta_query ) {
						$query_args['meta_query'] = $meta_query;
					}
					
					// Tax query
					$tax_query = WP_RealEstate_Agency_Filter::get_tax_filter($filter_params);
					if ( $tax_query ) {
						$query_args['tax_query'] = $tax_query;
					}

					break;
			}

			$query_args = apply_filters('wp-realestate-'.$post_type.'-query-args', $query_args, $filter_params);
		}
		
		$query = new WP_Query( $query_args );

		return $query;
	}

	public static function get_agents( $params = array() ) {
		$params = wp_parse_args( $params, array(
			'post_per_page' => -1,
			'post_status' => 'publish',
			'ids' => array()
		));
		extract($params);

		$query_args = array(
			'post_type'         => 'agent',
			'posts_per_page'    => $post_per_page,
			'post_status'       => $post_status,
		);

		if ( !empty($ids) ) {
	    	$query_args['post__in'] = $ids;
	    }

		return new WP_Query( $query_args );
	}
	
	public static function get_property_location_name( $post_id = null, $separator = ',' ) {
		static $property_locations;

		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! empty( $property_locations[ $post_id ] ) ) {
			return $property_locations[ $post_id ];
		}

		$locations = wp_get_post_terms( $post_id, 'property_location', array(
	        'orderby'   => 'parent',
	        'order'     => 'DESC',
		) );

		if ( is_array( $locations ) && count( $locations ) > 0 ) {
			$output = '';

			$locations = array_reverse( $locations );

			foreach ( $locations as $key => $location ) {
				$output .= '<a href="' . get_term_link( $location, 'property_location' ). '">' . $location->name . '</a>';

				if ( array_key_exists( $key + 1, $locations ) ) {
					$output .= ' <span class="separator">' . $separator . '</span> ';
				}
			}

			$property_locations[ $post_id ] = $output;
			return $output;
		}

		return false;
	}
	
	public static function get_min_max_meta_value( $key, $post_type = 'property', $meta_condition = array() ){
	    global $wpdb;
	    $cash_key = md5($key.'_'.$post_type.json_encode($meta_condition));
	    $results = wp_cache_get($cash_key);

	    if ($results === false) {
	    	$sql  = "SELECT min( CAST( postmeta.meta_value AS UNSIGNED ) ) as min, max( CAST( postmeta.meta_value AS UNSIGNED ) ) as max FROM {$wpdb->posts} ";
			$sql .= " LEFT JOIN {$wpdb->postmeta} as postmeta ON {$wpdb->posts}.ID = postmeta.post_id ";
			$sql .= " 	WHERE {$wpdb->posts}.post_type = %s
						AND {$wpdb->posts}.post_status = 'publish'
						AND postmeta.meta_key='%s' ";
			if ( !empty($meta_condition) ) {
				$sql .= " AND {$wpdb->posts}.ID IN (
						SELECT {$wpdb->posts}.ID
						FROM {$wpdb->posts}
						LEFT JOIN {$wpdb->postmeta} as pmeta ON {$wpdb->posts}.ID = pmeta.post_id
						WHERE {$wpdb->posts}.post_type = '%s'
								AND {$wpdb->posts}.post_status = 'publish'
								AND pmeta.meta_key='%s' AND pmeta.meta_value='%s'
					) ";
				$query = $wpdb->prepare( $sql, $post_type, $key, $post_type, $meta_condition['key'], $meta_condition['value'] );
				
			} else {
		        $query = $wpdb->prepare( $sql, $post_type, $key);
		    }

	        $results = $wpdb->get_row( $query );
	        wp_cache_set( $cash_key, $results, '', DAY_IN_SECONDS );
	    }

	    return $results;
	}

	public static function get_agents_properties( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'agent_ids' => array(),
			'post_per_page' => -1,
			'paged' => 1,
			'orderby' => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			),
			'order' => 'DESC',
			'fields' => ''
		));
		extract($args);

		if ( empty($agent_ids) ) {
			return false;
		}

		$user_ids = array(0);
		foreach ($agent_ids as $agent_id) {
			$user_id = WP_RealEstate_User::get_user_by_agent_id($agent_id);
			if ( $user_id ) {
				$user_ids[] = $user_id;
			}
		}

		$query_args = array(
			'post_type'         => 'property',
			'posts_per_page'    => $post_per_page,
			'paged'    			=> $paged,
			'orderby'    		=> $orderby,
			'order'    			=> $order,
			'fields'    		=> $fields,
			'author__in'        => $user_ids
		);

		return new WP_Query( $query_args );
	}
	
	public static function get_agencies( $count = -1 ) {
		$args = array(
			'post_type'         => 'agency',
			'posts_per_page'    => $count,
		);

		return new WP_Query( $args );
	}
	
	public static function get_agency_agents( $post_id = null, $args = array() ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$args = wp_parse_args( $args, array(
			'post_per_page' => -1,
			'paged' => 1,
			'orderby' => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			),
			'order' => 'DESC',
			'fields' => ''
		));
		extract($args);

		$agents = WP_RealEstate_Agency::get_post_meta($post_id, 'agents', false);
		
		if ( $agents ) {
			$args = array(
				'post_type'         => 'agent',
				'posts_per_page'    => $post_per_page,
				'paged'    			=> $paged,
				'orderby'    		=> $orderby,
				'order'    			=> $order,
				'fields'    		=> $fields,
				'post__in'        	=> $agents
			);
			return new WP_Query( $args );
		}
		return false;
	}
}
