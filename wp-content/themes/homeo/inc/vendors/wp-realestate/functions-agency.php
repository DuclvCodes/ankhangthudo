<?php

function homeo_get_agencies( $params = array() ) {
	$params = wp_parse_args( $params, array(
		'limit' => -1,
		'post_status' => 'publish',
		'get_agencies_by' => 'recent',
		'orderby' => '',
		'order' => '',
		'post__in' => array(),
		'fields' => null, // ids
		'author' => null,
	));
	extract($params);

	$query_args = array(
		'post_type'         => 'agency',
		'posts_per_page'    => $limit,
		'post_status'       => $post_status,
		'orderby'       => $orderby,
		'order'       => $order,
	);

	$meta_query = array();
	switch ($get_agencies_by) {
		case 'recent':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
		case 'featured':
			$meta_query[] = array(
				'key' => WP_REALESTATE_AGENCY_PREFIX.'featured',
	           	'value' => 'on',
	           	'compare' => '=',
			);
			break;
	}

	if ( !empty($post__in) ) {
    	$query_args['post__in'] = $post__in;
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

	return new WP_Query( $query_args );
}

if ( !function_exists('homeo_agency_content_class') ) {
	function homeo_agency_content_class( $class ) {
		$prefix = 'agencies';
		if ( is_singular( 'agency' ) ) {
            $prefix = 'agency';
        }
		if ( homeo_get_config($prefix.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'homeo_agency_content_class', 'homeo_agency_content_class', 1 , 1 );

if ( !function_exists('homeo_get_agencies_layout_configs') ) {
	function homeo_get_agencies_layout_configs() {
		$layout_type = homeo_get_agencies_layout_sidebar();
		switch ( $layout_type ) {
		 	case 'left-main':
		 		$configs['left'] = array( 'sidebar' => 'agencies-filter-sidebar', 'class' => 'col-md-4 col-sm-12 col-xs-12'  );
		 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
		 		break;
		 	case 'main-right':
		 	default:
		 		$configs['right'] = array( 'sidebar' => 'agencies-filter-sidebar',  'class' => 'col-md-4 col-sm-12 col-xs-12' ); 
		 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
	 			break;
		}
		return $configs; 
	}
}

function homeo_get_agencies_layout_sidebar() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = homeo_get_config('agencies_layout_sidebar', 'main-right');
	}
	return apply_filters( 'homeo_get_agencies_layout_sidebar', $layout_type );
}

function homeo_get_agencies_display_mode() {
	global $post;
	if ( !empty($_GET['filter-display-mode']) ) {
		$display_mode = $_GET['filter-display-mode'];
	} else {
		if ( is_page() && is_object($post) ) {
			$display_mode = get_post_meta( $post->ID, 'apus_page_agencies_display_mode', true );
		}
		if ( empty($display_mode) ) {
			$display_mode = homeo_get_config('agencies_display_mode', 'grid');
		}
	}
	return apply_filters( 'homeo_get_agencies_display_mode', $display_mode );
}

function homeo_get_agencies_inner_style() {
	$display_mode = homeo_get_agencies_display_mode();
	if ( $display_mode == 'grid' ) {
		$inner_style = 'grid';
	} else {
		$inner_style = 'list';
	}
	return apply_filters( 'homeo_get_agencies_inner_style', $inner_style );
}

function homeo_get_agencies_columns() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_agencies_columns', true );
	}
	if ( empty($columns) ) {
		$columns = homeo_get_config('agencies_columns', 3);
	}
	return apply_filters( 'homeo_get_agencies_columns', $columns );
}

function homeo_get_agencies_pagination() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$pagination = get_post_meta( $post->ID, 'apus_page_agencies_pagination', true );
	}
	if ( empty($pagination) ) {
		$pagination = homeo_get_config('agencies_pagination', 'default');
	}
	return apply_filters( 'homeo_get_agencies_pagination', $pagination );
}

function homeo_is_agencies_page() {
	if ( is_page() ) {
		$page_name = basename(get_page_template());
		if ( $page_name == 'page-agencies.php' ) {
			return true;
		}
	} elseif( is_archive('agency') ) {
		return true;
	}
	return false;
}



// custom fields
add_filter( 'cmb2_meta_boxes', 'homeo_is_agencies_fields', 100 );
function homeo_is_agencies_fields( array $metaboxes ) {
	$prefix = WP_REALESTATE_AGENCY_PREFIX;
	if ( !empty($metaboxes[ $prefix . 'contact_details' ]['fields']) ) {
		$fields = $metaboxes[ $prefix . 'contact_details' ]['fields'];
		$rfields = array();
		foreach ($fields as $key => $field) {
			$rfields[] = $field;
			if ( !empty($field['id']) && $field['id'] == $prefix . 'phone' ) {
				$rfields[] = array(
					'name'              => esc_html__( 'Cover Photo', 'homeo' ),
					'id'                => $prefix . 'cover_photo',
					'type'              => 'file',
					'query_args' => array(
						'type' => array(
							'image/gif',
							'image/jpeg',
							'image/png',
						),
					),
					'preview_size' => 'medium'
				);
			}
		}
		$metaboxes[ $prefix . 'contact_details' ]['fields'] = $rfields;
	}
	return $metaboxes;
}

add_filter( 'wp-realestate-agency-fields-front', 'homeo_is_agencies_fields_front', 100 );
function homeo_is_agencies_fields_front($fields) {
	$prefix = WP_REALESTATE_AGENCY_PREFIX;
	$fields[] = array(
		'name'              => esc_html__( 'Cover Photo', 'homeo' ),
		'id'                => $prefix . 'cover_photo',
		'type'              => 'wp_realestate_file',
		'mime_types' => array( 'gif', 'jpeg', 'jpg', 'png' ),
		'ajax'				=> true,
		'multiple'			=> false,
		'priority' => 2.5
	);
	return $fields;
}