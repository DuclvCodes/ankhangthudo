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

class WP_RealEstate_Abstract_Filter {

	public static function has_filter($params = null) {
		if ( empty($params) ) {
			$params = $_GET;
		}
		if ( ! empty( $params ) && is_array( $params ) ) {
			foreach ( $params as $key => $value ) {
				if ( strrpos( $key, 'filter-', -strlen( $key ) ) !== false ) {
					return true;
				}
			}
		}
		return false;
	}

	public static function get_filters($params = null) {
		$filters = array();
		if ( empty($params) ) {
			if ( ! empty( $_GET ) && is_array( $_GET ) ) {
				$params = $_GET;
			}
		}
		
		if ( ! empty( $params ) && is_array( $params ) ) {
			foreach ( $params as $key => $value ) {
				if ( strrpos( $key, 'filter-', -strlen( $key ) ) !== false && !empty($value) || $key == 'filter-price-from' ) {
					$filters[$key] = $value;
				}
			}

			if ( isset($filters['filter-price-from']) && isset($filters['filter-price-to']) ) {
				$filters['filter-price'] = array($filters['filter-price-from'], $filters['filter-price-to'] );
				unset($filters['filter-price-from']);
				unset($filters['filter-price-to']);
			}
			if ( isset($filters['filter-center-latitude']) ) {
				unset($filters['filter-center-latitude']);
			}
			if ( isset($filters['filter-center-longitude']) ) {
				unset($filters['filter-center-longitude']);
			}
			if ( !empty($filters['filter-distance']) && !isset($filters['filter-center-location']) ) {
				unset($filters['filter-distance']);
			}
		}
		
		return $filters;
	}
	
	public static function orderby($query_vars, $params) {
		// Order
		if ( ! empty( $params['filter-orderby'] ) ) {
			switch ( $params['filter-orderby'] ) {
				case 'newest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'DESC';
					break;
				case 'oldest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'ASC';
					break;
				case 'random':
					$query_vars['orderby'] = 'rand';
					break;
				case 'title':
					$query_vars['orderby'] = 'title';
					break;
				case 'published':
					$query_vars['orderby'] = 'date';
					break;
				case 'price':
					$query_vars['meta_key'] = WP_REALESTATE_PROPERTY_PREFIX . 'price';
					$query_vars['orderby'] = 'meta_value_num';
					break;
			}
		} else {
			$query_vars['order'] = 'DESC';
			$query_vars['orderby'] = array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			);
		}

		return $query_vars;
	}

	public static function build_post_ids( $haystack, array $ids ) {
		if ( ! is_array( $haystack ) ) {
			$haystack = array();
		}

		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			return array_intersect( $haystack, $ids );
		} else {
			$haystack = $ids;
		}

		return $haystack;
	}
	
	public static function filter_by_distance($params) {
		$distance_ids = array();
		if ( ! empty( $params['filter-center-location'] ) && ! empty( $params['filter-center-latitude'] ) && ! empty( $params['filter-center-longitude'] ) ) {
			$filter_distance = 50;
			if ( ! empty( $params['filter-distance'] ) ) {
				$filter_distance = $params['filter-distance'];
			}
		    $post_ids = self::get_posts_by_distance( $params['filter-center-latitude'], $params['filter-center-longitude'], $filter_distance );

		    if ( $post_ids ) {
			    foreach ( $post_ids as $post ) {
					$distance_ids[] = $post->ID;
			    }
			}
			if ( empty( $distance_ids ) || ! $distance_ids ) {
	            $distance_ids = array(0);
			}
	    }
	    
	    return $distance_ids;
	}

	public static function get_posts_by_distance($latitude, $longitude, $distance) {
		global $wpdb;
		$distance_type = apply_filters( 'wp_realestate_filter_distance_type', 'miles' );
		$earth_distance = $distance_type == 'miles' ? 3959 : 6371;

		$prefix = WP_REALESTATE_PROPERTY_PREFIX;
		$post_stype = 'property';
		$post_ids = false;
		$sql = $wpdb->prepare( "
			SELECT $wpdb->posts.ID, 
				( %s * acos( cos( radians(%s) ) * cos( radians( latmeta.meta_value ) ) * cos( radians( longmeta.meta_value ) - radians(%s) ) + sin( radians(%s) ) * sin( radians( latmeta.meta_value ) ) ) ) AS distance, latmeta.meta_value AS latitude, longmeta.meta_value AS longitude
			FROM $wpdb->posts
			INNER JOIN $wpdb->postmeta AS latmeta ON $wpdb->posts.ID = latmeta.post_id
			INNER JOIN $wpdb->postmeta AS longmeta ON $wpdb->posts.ID = longmeta.post_id
			WHERE $wpdb->posts.post_type = %s AND $wpdb->posts.post_status = 'publish' AND latmeta.meta_key=%s AND longmeta.meta_key=%s
			HAVING distance < %s
			ORDER BY $wpdb->posts.menu_order ASC, distance ASC",
			$earth_distance,
			$latitude,
			$longitude,
			$latitude,
			$post_stype,
			$prefix.'map_location_latitude',
			$prefix.'map_location_longitude',
			$distance
		);

		if ( apply_filters( 'wp_realestate_get_propertys_cache_results', false ) ) {
			$to_hash         = json_encode( array($earth_distance, $latitude, $longitude, $latitude, $distance, $post_stype) );
			$query_args_hash = 'wp_realestate_' . md5( $to_hash . WP_REALESTATE_PLUGIN_VERSION );

			$post_ids = get_transient( $query_args_hash );
		}

		if ( ! $post_ids ) {
			$post_ids = $wpdb->get_results( $sql, OBJECT_K );
			if ( !empty($query_args_hash) ) {
				set_transient( $query_args_hash, $post_ids, DAY_IN_SECONDS );
			}
		}

		return $post_ids;
	}

	public static function filter_count($name, $term_id, $field) {
		$args = array(
			'post_type' => !empty($field['for_post_type']) ? $field['for_post_type'] : 'property',
			'post_per_page' => 1,
			'fields' => 'ids'
		);
		$params = array();
		if ( WP_RealEstate_Abstract_Filter::has_filter() ) {
			$params = $_GET;
		}
		
		if ( !empty($params[$name]) ) {
			$values = $params[$name];
			if ( is_array($values) ) {
				$values[] = $term_id;
			} else {
				$values = $term_id;
			}
			$params[$name] = $values;
		} else {
			$params[$name] = $term_id;
		}

		$query_hash = md5( json_encode($args) ) .'-'. md5( json_encode($params) );
		
		$cached_counts = (array) get_transient( 'wp_realestate_filter_counts' );
		if ( ! isset( $cached_counts[ $query_hash ] ) ) {
			$loop = WP_RealEstate_Query::get_posts($args, $params);
			$cached_counts[ $query_hash ] = $loop->found_posts;
			set_transient( 'wp_realestate_filter_counts', $cached_counts, DAY_IN_SECONDS );
		}

		return $cached_counts[ $query_hash ];
	}
	
	public static function get_term_name($term_id, $tax) {
		$term = get_term($term_id, $tax);
		if ( $term ) {
			return $term->name;
		}
		return '';
	}

	public static function render_filter_tax($key, $value, $tax, $url) {
		if ( is_array($value) ) {
			foreach ($value as $val) {
				$name = self::get_term_name($val, $tax);
				$rm_url = self::remove_url_var($key . '[]=' . $val, $url);
				self::render_filter_result_item($name, $rm_url);
			}
		} else {
			$name = self::get_term_name($value, $tax);
			$rm_url = self::remove_url_var($key . '=' . $value, $url);
			self::render_filter_result_item($name, $rm_url);
		}
	}

	public static function remove_url_var($url_var, $url) {
		$str = "?" . $url_var;
		if ( strpos($url, $str) !== false ) {
		    $rm_url = str_replace($url_var, "", $url);
		    $rm_url = str_replace('?&', "?", $rm_url);
		} else {
			$rm_url = str_replace("&" . $url_var, "", $url);
		}
		return $rm_url;
	}

	public static function render_filter_result_item($value, $rm_url) {
		if ( $value ) {
		?>
			<li><a href="<?php echo esc_url($rm_url); ?>" ><span class="close-value">x</span><?php echo trim($value); ?></a></li>
			<?php
		}
	}

	public static function render_filter_tax_simple($key, $value, $tax, $label) {
		if ( is_array($value) ) {
			foreach ($value as $val) {
				$name = self::get_term_name($val, $tax);
				self::render_filter_result_item_simple($name, $label);
			}
		} else {
			$name = self::get_term_name($value, $tax);
			self::render_filter_result_item_simple($name, $label);
		}
	}

	public static function render_filter_result_item_simple($value, $label) {
		if ( $value ) {
		?>
			<li><strong class="text"><?php echo trim($label); ?>:</strong> <span class="value"><?php echo trim($value); ?></span></li>
			<?php
		}
	}


	// filter function
	public static function filter_get_name($key, $field) {
		$prefix = 'filter';
		if ( !empty($field['filter-name-prefix']) ) {
			$prefix = $field['filter-name-prefix'];
		}
		$name = $prefix.'-'.$key;
		return apply_filters('wp-realestate-filter-get-name', $name, $key, $field);
	}

	public static function filter_field_input($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/text' );
	}

	public static function filter_date_field_input($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/text_date' );
	}

	public static function filter_field_input_location($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/text_location' );
	}
    
    public static function filter_field_input_distance($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : apply_filters( 'wp_realestate_filter_distance_default', 50 );

		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/distance' );
	}

	public static function filter_field_year_built_range_slider($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';
		$min_max = WP_RealEstate_Query::get_min_max_meta_value(WP_REALESTATE_PROPERTY_PREFIX.'year_built', 'property');
		if ( empty($min_max) ) {
			return;
		}
		$min    = floor( $min_max->min );
		// $max    = ceil( $min_max->max );
		$max    = date("Y");

		if ( $min == $max ) {
			return;
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/range_slider' );
	}

	public static function filter_field_property_price($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

		$price_min = WP_RealEstate_Query::get_min_max_meta_value(WP_REALESTATE_PROPERTY_PREFIX.'price', 'property');
		$price_max = WP_RealEstate_Query::get_min_max_meta_value(WP_REALESTATE_PROPERTY_PREFIX.'max_price', 'property');
		if ( empty($price_min) && empty($price_max) ) {
			return;
		}
		$min = $max = 0;
		//$min = $price_min->min < $price_max->min ? $price_min->min : $price_max->min;
		$max = $price_min->max > $price_max->max ? $price_min->max : $price_max->max;
		
		if ( $min >= $max ) {
			return;
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/price_range_slider' );
	}

	public static function filter_field_checkbox($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/checkbox' );
	}

	public static function filter_field_select($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';
		$options = array();
		if ( !empty($field['options']) ) {
			foreach ($field['options'] as $key => $value) {
				$options[] = array('value' => $key, 'text' => $value);
			}
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/select' );
	}

	public static function filter_field_taxonomy_radio_list($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = '';
		if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

		$options = array();
		$query_args = array( 'hierarchical' => 1, 'hide_empty' => false  );
		$terms = get_terms($field['taxonomy'], $query_args);

		if ( ! is_wp_error( $terms ) && ! empty ( $terms ) ) {
			foreach ($terms as $term) {
				$options[] = array(
					'value' => $term->slug,
					'text' => $term->name
				);
			}
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/radios' );
	}

	public static function filter_field_taxonomy_check_list($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = '';
		if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

		$options = array();
		$query_args = array( 'hierarchical' => 1, 'hide_empty' => false  );
		$terms = get_terms($field['taxonomy'], $query_args);

		if ( ! is_wp_error( $terms ) && ! empty ( $terms ) ) {
			foreach ($terms as $term) {
				$options[] = array(
					'value' => $term->slug,
					'text' => $term->name
				);
			}
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/check_list' );
	}

	public static function filter_field_taxonomy_select($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = '';
		if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

		$options = array();
		$query_args = array( 'hierarchical' => 1, 'hide_empty' => false  );
		$terms = get_terms($field['taxonomy'], $query_args);

		if ( ! is_wp_error( $terms ) && ! empty ( $terms ) ) {
			foreach ($terms as $term) {
				$options[] = array(
					'value' => $term->slug,
					'text' => $term->name
				);
			}
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/select' );
	}

	public static function filter_field_taxonomy_hierarchical_radio_list($instance, $args, $key, $field) {
	    $name = self::filter_get_name($key, $field);
	    $selected = '';
	    if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

	    include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/tax_radios' );
	}

	public static function filter_field_taxonomy_hierarchical_check_list($instance, $args, $key, $field) {
	    $name = self::filter_get_name($key, $field);
	    $selected = '';
	    if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

	    include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/tax_check_list' );
	}

	public static function filter_field_taxonomy_hierarchical_select($instance, $args, $key, $field) {
	    $name = self::filter_get_name($key, $field);
	    $selected = '';
	    if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }
	    
	    include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/tax_select' );
	}

	public static function filter_field_location_select($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
	    // $selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';
	    $selected = '';
	    if ( !empty( $_GET[$name] ) ) {
	    	$selected = $_GET[$name];
	    } elseif ( !empty($field['taxonomy']) && is_tax($field['taxonomy']) ) {
	    	global $wp_query;
			
			$term =	$wp_query->queried_object;
			if ( isset( $term->slug) ) {
				$selected = $term->slug;
			}
	    }

	    $location_type = wp_realestate_get_option('location_multiple_fields', 'yes');
	    // echo $location_type; die;
	    if ( $location_type === 'no' ) {
	    	include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/tax_select' );
	    } else {
	    	include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/regions_select' );
	    }
	}

	public static function get_the_level($id, $type = 'property_location') {
	  	return count( get_ancestors($id, $type) );
	}

	public static function hierarchical_tax_tree($catId = 0, $depth = 0, $input_name, $key, $field, $selected, $input_type = 'checkbox') {
		$output = $return = '';
		$next_depth = $depth + 1;
		if ( empty($field['taxonomy']) ) {
			return;
		}

		$terms = get_terms($field['taxonomy'], array( 'hierarchical' => 1, 'hide_empty' => false, 'parent' =>  $catId ));

		if ( ! is_wp_error( $terms ) && ! empty ( $terms ) ) {
			$_id = WP_RealEstate_Mixes::random_key();
			foreach ($terms as $term) {
			  	$checked = '';
			  	if ( !empty($selected) ) {
			        if ( is_array($selected) ) {
			            if ( in_array($term->slug, $selected) || in_array($term->term_id, $selected) ) {
			                $checked = ' checked="checked"';
			            }
			        } elseif ( $term->slug == $selected || $term->term_id == $selected ) {
			            $checked = ' checked="checked"';
			        }
			    }

			    $output .= '<li class="list-item">';
			        $output .= '<div class="list-item-inner">';
			        if ( $input_type == 'checkbox' ) {
			              $output .= '<input id="'.esc_attr($term->slug.'-'.$_id).'" type="checkbox" name="'.esc_attr($input_name).'[]" value="'.esc_attr($term->slug).'" '.$checked.'>';
			          } else {
			              $output .= '<input id="'.esc_attr($term->slug.'-'.$_id).'" type="radio" name="'.esc_attr($input_name).'" value="'.esc_attr($term->slug).'" '.$checked.'>';
			          }
			        $output .= '<label for="'.esc_attr($term->slug.'-'.$_id).'">'.wp_kses_post($term->name).'</label>';

			        $child_output = self::hierarchical_tax_tree($term->term_id, $next_depth, $input_name, $key, $field, $selected, $input_type);
			        if ( $child_output ) {
		              	$output .= '<span class="caret"></span>';
			        }
			        $output .= '</div>';

			        $output .= $child_output;

			  	$output .= '</li>';
			}
			if ( $output ) {
			  	$return = '<ul class="terms-list circle-check level-'.$depth.'">'.$output.'</ul>';
			}
		}

		return $return;
	}

	public static function hierarchical_tax_option_tree($catId = 0, $depth = 0, $input_name, $key, $field, $selected ){
		$output = $show_depth = '';
		$next_depth = $depth + 1;
		for ($i = 1; $i <= $depth; $i++) {
		    $show_depth .= '-';
		}
		if ( empty($field['taxonomy']) ) {
			return;
		}

		$terms = get_terms($field['taxonomy'], array( 'hierarchical' => 1, 'hide_empty' => false, 'parent' =>  $catId ));

		if ( ! is_wp_error( $terms ) && ! empty ( $terms ) ) {
			foreach ($terms as $term) {
			  	$selected_html = '';
			  	if ( !empty($selected) ) {
			        if ( is_array($selected) ) {
			            if ( in_array($term->slug, $selected) || in_array($term->term_id, $selected) ) {
			                $selected_html = ' selected="selected"';
			            }
			        } elseif ( $term->slug == $selected || $term->term_id == $selected ) {
			            $selected_html = ' selected="selected"';
			        }
			    }
			    $output .= '<option value="'.esc_attr($term->slug).'" '.$selected_html.'>';
			        
			        $output .= $show_depth.' '.wp_kses_post($term->name);
			        
			  	$output .= '</option>';

			  	$output .= self::hierarchical_tax_option_tree($term->term_id, $next_depth, $input_name, $key, $field, $selected);
			}
		}

		return $output;
	}

	public static function filter_field_min_max_input($instance, $args, $key, $field) {
		$min_name = 'filter-'.$key.'-from';
		$max_name = 'filter-'.$key.'-to';
	    $min_selected = !empty( $_GET[$min_name] ) ? $_GET[$min_name] : '';
	    $max_selected = !empty( $_GET[$max_name] ) ? $_GET[$max_name] : '';

	    include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/min_max_text' );
	}

	public static function filter_field_range_slider($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
		$selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';
		$min_max = WP_RealEstate_Query::get_min_max_meta_value($field['id'], 'property');
		if ( empty($min_max) ) {
			return;
		}
		//$min    = floor( $min_max->min );
		$min    = 0;
		$max    = ceil( $min_max->max );

		if ( $min == $max ) {
			return;
		}
		include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/range_slider' );
	}

	public static function filter_field_number_select($instance, $args, $key, $field) {
		$name = self::filter_get_name($key, $field);
	    $selected = !empty( $_GET[$name] ) ? $_GET[$name] : '';

	    include WP_RealEstate_Template_Loader::locate( 'widgets/filter-fields/number_select' );
	}
}
