<?php

function homeo_get_properties( $params = array() ) {
	$params = wp_parse_args( $params, array(
		'limit' => -1,
		'post_status' => 'publish',
		'get_properties_by' => 'recent',
		'orderby' => '',
		'order' => '',
		'post__in' => array(),
		'fields' => null, // ids
		'author' => null,
		'statuses' => array(),
		'types' => array(),
		'locations' => array(),
		'amenities' => array(),
		'materials' => array(),
	));
	extract($params);

	$query_args = array(
		'post_type'         => 'property',
		'posts_per_page'    => $limit,
		'post_status'       => $post_status,
		'orderby'       => $orderby,
		'order'       => $order,
	);

	$meta_query = array();
	switch ($get_properties_by) {
		case 'recent':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
		case 'featured':
			$meta_query[] = array(
				'key' => WP_REALESTATE_PROPERTY_PREFIX.'featured',
	           	'value' => 'on',
	           	'compare' => '=',
			);
			break;
		case 'urgent':
			$meta_query[] = array(
				'key' => WP_REALESTATE_PROPERTY_PREFIX.'urgent',
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

    $tax_query = array();
    if ( !empty($statuses) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'property_status',
            'field'         => 'slug',
            'terms'         => $statuses,
            'operator'      => 'IN'
        );
    }
    if ( !empty($types) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'property_type',
            'field'         => 'slug',
            'terms'         => $types,
            'operator'      => 'IN'
        );
    }
    if ( !empty($locations) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'property_location',
            'field'         => 'slug',
            'terms'         => $locations,
            'operator'      => 'IN'
        );
    }

    if ( !empty($amenities) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'property_amenity',
            'field'         => 'slug',
            'terms'         => $amenities,
            'operator'      => 'IN'
        );
    }
    if ( !empty($materials) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'property_material',
            'field'         => 'slug',
            'terms'         => $materials,
            'operator'      => 'IN'
        );
    }

    if ( !empty($tax_query) ) {
    	$query_args['tax_query'] = $tax_query;
    }
    
    if ( !empty($meta_query) ) {
    	$query_args['meta_query'] = $meta_query;
    }

	return new WP_Query( $query_args );
}

if ( !function_exists('homeo_property_content_class') ) {
	function homeo_property_content_class( $class ) {
		$prefix = 'properties';
		if ( is_singular( 'property' ) ) {
            $prefix = 'property';
        }
		if ( homeo_get_config($prefix.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'homeo_property_content_class', 'homeo_property_content_class', 1 , 1  );

function homeo_property_template_folder_name($folder) {
	$folder = 'template-properties';
	return $folder;
}
add_filter( 'wp-realestate-theme-folder-name', 'homeo_property_template_folder_name', 10 );

if ( !function_exists('homeo_get_properties_layout_configs') ) {
	function homeo_get_properties_layout_configs() {
		$layout_sidebar = homeo_get_properties_layout_sidebar();
		switch ( $layout_sidebar ) {
		 	case 'left-main':
		 		$configs['left'] = array( 'sidebar' => 'properties-filter-sidebar', 'class' => 'col-md-4 col-lg-4 col-sm-12 col-xs-12'  );
		 		$configs['main'] = array( 'class' => 'col-md-8 col-lg-8 col-sm-12 col-xs-12' );
		 		break;
		 	case 'main-right':
		 	default:
		 		$configs['right'] = array( 'sidebar' => 'properties-filter-sidebar',  'class' => 'col-md-4 col-lg-4 col-sm-12 col-xs-12' ); 
		 		$configs['main'] = array( 'class' => 'col-md-8 col-lg-8 col-sm-12 col-xs-12' );
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
	 			break;
		}
		return $configs; 
	}
}

function homeo_get_properties_layout_sidebar() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = homeo_get_config('properties_layout_sidebar', 'main-right');
	}
	return apply_filters( 'homeo_get_properties_layout_sidebar', $layout_type );
}

function homeo_get_properties_layout_type() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout_type', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = homeo_get_config('properties_layout_type', 'default');
	}
	return apply_filters( 'homeo_get_properties_layout_type', $layout_type );
}

function homeo_get_properties_display_mode() {
	global $post;
	if ( !empty($_GET['filter-display-mode']) ) {
		$display_mode = $_GET['filter-display-mode'];
	} else {
		if ( is_page() && is_object($post) ) {
			$display_mode = get_post_meta( $post->ID, 'apus_page_display_mode', true );
		}
		if ( empty($display_mode) ) {
			$display_mode = homeo_get_config('properties_display_mode', 'grid');
		}
	}
	return apply_filters( 'homeo_get_properties_display_mode', $display_mode );
}

function homeo_get_properties_inner_style() {
	global $post;
	$display_mode = homeo_get_properties_display_mode();
	if ( $display_mode == 'list' ) {
		return 'list';
	} else {
		if ( is_page() && is_object($post) ) {
			$inner_style = get_post_meta( $post->ID, 'apus_page_inner_grid_style', true );
		}
		if ( empty($inner_style) ) {
			$inner_style = homeo_get_config('properties_inner_grid_style', 'grid');
		}
	}
	return apply_filters( 'homeo_get_properties_inner_style', $inner_style );
}

function homeo_get_properties_columns() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_properties_columns', true );
	}
	if ( empty($columns) ) {
		$columns = homeo_get_config('properties_columns', 3);
	}
	return apply_filters( 'homeo_get_properties_columns', $columns );
}

function homeo_get_properties_elementor_template() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$elementor_template = get_post_meta( $post->ID, 'apus_page_elementor_template', true );
	}
	if ( empty($elementor_template) ) {
		$elementor_template = homeo_get_config('properties_elementor_template', '');
	}
	return apply_filters( 'homeo_get_properties_elementor_template', $elementor_template );
}

function homeo_get_properties_pagination() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$pagination = get_post_meta( $post->ID, 'apus_page_properties_pagination', true );
	}
	if ( empty($pagination) ) {
		$pagination = homeo_get_config('properties_pagination', 'default');
	}
	return apply_filters( 'homeo_get_properties_pagination', $pagination );
}

function homeo_get_property_layout_type() {
	global $post;
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$layout_type = get_post_meta($post->ID, WP_REALESTATE_PROPERTY_PREFIX.'layout_type', true);
	}
	
	if ( empty($layout_type) ) {
		$layout_type = homeo_get_config('property_layout_type', 'v1');
	}
	return apply_filters( 'homeo_get_property_layout_type', $layout_type );
}

function homeo_property_scripts() {
	
	wp_enqueue_style( 'leaflet' );
	wp_enqueue_script( 'jquery-highlight' );
    wp_enqueue_script( 'leaflet' );
    wp_enqueue_script( 'leaflet-GoogleMutant' );
    wp_enqueue_script( 'control-geocoder' );
    wp_enqueue_script( 'esri-leaflet' );
    wp_enqueue_script( 'esri-leaflet-geocoder' );
    wp_enqueue_script( 'leaflet-markercluster' );
    wp_enqueue_script( 'leaflet-HtmlIcon' );

	wp_register_script( 'homeo-property', get_template_directory_uri() . '/js/property.js', array( 'jquery', 'wp-realestate-main', 'perfect-scrollbar', 'imagesloaded' ), '20150330', true );

	$currency_symbol = ! empty( wp_realestate_get_option('currency_symbol') ) ? wp_realestate_get_option('currency_symbol') : '$';
	$dec_point = ! empty( wp_realestate_get_option('money_dec_point') ) ? wp_realestate_get_option('money_dec_point') : '.';
	$thousands_separator = ! empty( wp_realestate_get_option('money_thousands_separator') ) ? wp_realestate_get_option('money_thousands_separator') : '';

	wp_localize_script( 'homeo-property', 'homeo_property_opts', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),

		'dec_point' => $dec_point,
		'thousands_separator' => $thousands_separator,
		'currency' => esc_attr($currency_symbol),
		'monthly_text' => esc_html__('Monthly Payment: ', 'homeo'),
		'compare_added_tooltip_title' => esc_html__('Remove Compare', 'homeo'),
		'compare_add_tooltip_title' => esc_html__('Add Compare', 'homeo'),
		'favorite_added_tooltip_title' => esc_html__('Remove Favorite', 'homeo'),
		'favorite_add_tooltip_title' => esc_html__('Add Favorite', 'homeo'),

		'template' => apply_filters( 'homeo_autocompleate_search_template', '<a href="{{url}}" class="media autocompleate-media">
			<div class="media-left media-middle">
				<img src="{{image}}" class="media-object" height="70" width="70">
			</div>
			<div class="media-body media-middle">
				<h4>{{title}}</h4>
				{{{price}}}
				{{{location}}}
				</div></a>' ),
        'empty_msg' => apply_filters( 'homeo_autocompleate_search_empty_msg', esc_html__( 'Unable to find any listing that match the currenty query', 'homeo' ) ),
	));
	wp_enqueue_script( 'homeo-property' );


	$mapbox_token = '';
	$mapbox_style = '';
	$custom_style = '';
	$googlemap_type = wp_realestate_get_option('googlemap_type', 'roadmap');
	if ( empty($googlemap_type) ) {
		$googlemap_type = 'roadmap';
	}
	$map_service = wp_realestate_get_option('map_service', '');
	if ( $map_service == 'mapbox' ) {
		$mapbox_token = wp_realestate_get_option('mapbox_token', '');
		$mapbox_style = wp_realestate_get_option('mapbox_style', 'streets-v11');
		if ( empty($mapbox_style) || !in_array($mapbox_style, array( 'streets-v11', 'light-v10', 'dark-v10', 'outdoors-v11', 'satellite-v9' )) ) {
			$mapbox_style = 'streets-v11';
		}
	} else {
		$custom_style = wp_realestate_get_option('google_map_style', '');
	}

	wp_register_script( 'homeo-property-map', get_template_directory_uri() . '/js/property-map.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'homeo-property-map', 'homeo_property_map_opts', array(
		'map_service' => $map_service,
		'mapbox_token' => $mapbox_token,
		'mapbox_style' => $mapbox_style,
		'custom_style' => $custom_style,
		'googlemap_type' => $googlemap_type,
		'default_latitude' => wp_realestate_get_option('default_maps_location_latitude', '43.6568'),
		'default_longitude' => wp_realestate_get_option('default_maps_location_longitude', '-79.4512'),
		'default_pin' => wp_realestate_get_option('default_maps_pin', ''),
		
	));
	wp_enqueue_script( 'homeo-property-map' );
}
add_action( 'wp_enqueue_scripts', 'homeo_property_scripts', 10 );

function homeo_is_properties_page() {
	if ( is_page() ) {
		$page_name = basename(get_page_template());
		if ( $page_name == 'page-properties.php' ) {
			return true;
		}
	} elseif( is_post_type_archive('property') || is_tax('property_status') || is_tax('property_type') || is_tax('property_location') || is_tax('property_tag') ) {
		return true;
	}
	return false;
}

function homeo_property_metaboxes($fields) {
	// property

	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$prefix = WP_REALESTATE_PROPERTY_PREFIX;
		if ( !empty($fields) ) {
			$fields[ $prefix . 'tab-layout-version' ] = array(
				'id' => $prefix . 'tab-layout-version',
				'icon' => 'dashicons-admin-appearance',
				'title' => esc_html__( 'Layout Type', 'homeo' ),
				'fields' => array(
					array(
						'name'              => esc_html__( 'Layout Type', 'homeo' ),
						'id'                => $prefix . 'layout_type',
						'type'              => 'select',
						'options'			=> array(
			                '' => esc_html__('Global Settings', 'homeo'),
			                'v1' => esc_html__('Version 1', 'homeo'),
			                'v2' => esc_html__('Version 2', 'homeo'),
			                'v3' => esc_html__('Version 3', 'homeo'),
			                'v4' => esc_html__('Version 4', 'homeo'),
			                'v5' => esc_html__('Version 5', 'homeo'),
			            ),
					)
				)
			);
		}
	}
	
	return $fields;
}
add_filter( 'wp-realestate-admin-custom-fields', 'homeo_property_metaboxes' );


add_filter('wp_realestate_settings_general', 'homeo_properties_settings_general', 10);
function homeo_properties_settings_general($fields) {
	$rfields = array();
	foreach ($fields as $key => $field) {
		$rfields[] = $field;
		if ( $field['id'] == 'default_maps_location_longitude' ) {
			$rfields[] = array(
				'name'    => esc_html__( 'Map Pin', 'homeo' ),
				'desc'    => esc_html__( 'Enter your map pin', 'homeo' ),
				'id'      => 'default_maps_pin',
				'type'    => 'file',
				'options' => array(
					'url' => true,
				),
				'query_args' => array(
					'type' => array(
						'image/gif',
						'image/jpeg',
						'image/png',
					),
				),
			);
		}
	}
	return $rfields;
}

add_action( 'wre_ajax_homeo_get_ajax_properties', 'homeo_get_ajax_properties' );

add_action( 'wp_ajax_homeo_get_ajax_properties', 'homeo_get_ajax_properties' );
add_action( 'wp_ajax_nopriv_homeo_get_ajax_properties', 'homeo_get_ajax_properties' );
function homeo_get_ajax_properties() {
	$settings = !empty($_POST['settings']) ? $_POST['settings'] : array();

    extract( $settings );

    $status_slugs = !empty($status_slugs) ? array_map('trim', explode(',', $status_slugs)) : array();
    $type_slugs = !empty($type_slugs) ? array_map('trim', explode(',', $type_slugs)) : array();
    $location_slugs = !empty($location_slugs) ? array_map('trim', explode(',', $location_slugs)) : array();
    $amenity_slugs = !empty($amenity_slugs) ? array_map('trim', explode(',', $amenity_slugs)) : array();
    $material_slugs = !empty($material_slugs) ? array_map('trim', explode(',', $material_slugs)) : array();

    $args = array(
        'limit' => $limit,
        'get_properties_by' => $get_properties_by,
        'orderby' => $orderby,
        'order' => $order,
        'statuses' => $status_slugs,
        'types' => $type_slugs,
        'locations' => $location_slugs,
        'amenities' => $amenity_slugs,
        'materials' => $material_slugs,
    );
    $loop = homeo_get_properties($args);
    
    if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) : $loop->the_post();
        	echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-grid' );
        endwhile;
        wp_reset_postdata();
    }
    exit();
}

add_action( 'wre_ajax_homeo_get_ajax_properties_load_more', 'homeo_get_ajax_properties_load_more' );

add_action( 'wp_ajax_homeo_get_ajax_properties_load_more', 'homeo_get_ajax_properties_load_more' );
add_action( 'wp_ajax_nopriv_homeo_get_ajax_properties_load_more', 'homeo_get_ajax_properties_load_more' );
function homeo_get_ajax_properties_load_more() {
	$paged = !empty($_POST['paged']) ? $_POST['paged'] : '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : '';
	$type = !empty($_POST['type']) ? $_POST['type'] : 'agent';


	if ( empty($paged) || empty($post_id) ) {
		$return = array(
			'paged' => 1,
			'output' => '',
			'load_more' => false
		);
		echo wp_json_encode($return);
        exit;
	}
	$return = array(
		'paged' => $paged + 1,
		'output' => '',
		'load_more' => false
	);
    if ( $type == 'agent' ) {
    	$loop = WP_RealEstate_Query::get_agents_properties(array(
		    'agent_ids' => array($post_id),
		    'post_per_page' => get_option('posts_per_page'),
		    'paged' => $paged
		));
    } else {
    	$agents = WP_RealEstate_Query::get_agency_agents( $post_id, array('fields' => 'ids') );
		if ( !empty($agents->posts) ) {
		    $loop = WP_RealEstate_Query::get_agents_properties(array(
		        'agent_ids' => $agents->posts,
		        'post_per_page' => get_option('posts_per_page'),
		        'paged' => $paged
		    ));
		}
    }
    $output = '';
    if ( !empty($loop) && $loop->have_posts() ) {
    	$return['load_more'] = $loop->max_num_pages > $paged ? true : false;
        while ( $loop->have_posts() ) : $loop->the_post();
        	$output .= '<div class="col-xs-12 list-item">';
        	$output .= WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list-member' );
        	$output .= '</div>';
        endwhile;
        wp_reset_postdata();
    }
    $return['output'] = $output;
    echo wp_json_encode($return);
    exit();
}

add_action( 'wre_ajax_homeo_get_ajax_agents_load_more', 'homeo_get_ajax_agents_load_more' );

add_action( 'wp_ajax_homeo_get_ajax_agents_load_more', 'homeo_get_ajax_agents_load_more' );
add_action( 'wp_ajax_nopriv_homeo_get_ajax_agents_load_more', 'homeo_get_ajax_agents_load_more' );
function homeo_get_ajax_agents_load_more() {
	$paged = !empty($_POST['paged']) ? $_POST['paged'] : '';
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : '';


	if ( empty($paged) || empty($post_id) ) {
		$return = array(
			'paged' => 1,
			'output' => '',
			'load_more' => false
		);
		echo wp_json_encode($return);
        exit;
	}
	$return = array(
		'paged' => $paged + 1,
		'output' => '',
		'load_more' => false
	);
	
	$loop = WP_RealEstate_Query::get_agency_agents($post_id, array(
	    'post_per_page' => get_option('posts_per_page'),
	    'paged' => $paged
	));
    
    $output = '';
    if ( !empty($loop) && $loop->have_posts() ) {
    	$return['load_more'] = $loop->max_num_pages > $paged ? true : false;
        while ( $loop->have_posts() ) : $loop->the_post();
        	$output .= '<div class="col-xs-12 list-item">';
        	$output .= WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-list' );
        	$output .= '</div>';
        endwhile;
        wp_reset_postdata();
    }
    $return['output'] = $output;
    echo wp_json_encode($return);
    exit();
}

remove_action( 'wp_realestate_before_property_archive', array( 'WP_RealEstate_Property', 'display_properties_results_filters' ), 5 );

function homeo_display_mode_form($display_mode, $form_url) {
	ob_start();
	?>
	<div class="properties-display-mode-wrapper">
		<form class="properties-display-mode" method="get" action="<?php echo esc_url($form_url); ?>">
			<div class="inner">
				<label for="filter-display-mode-grid">
					<input id="filter-display-mode-grid" type="radio" name="filter-display-mode" value="grid" <?php checked('grid', $display_mode); ?>> <i class="flaticon-menu"></i>
				</label>
				<label for="filter-display-mode-list">
					<input id="filter-display-mode-list" type="radio" name="filter-display-mode" value="list" <?php checked('list', $display_mode); ?>> <i class="flaticon-menu-1"></i>
				</label>
			</div>
			<?php WP_RealEstate_Mixes::query_string_form_fields( null, array( 'filter-display-mode', 'submit' ) ); ?>
		</form>
	</div>
	<?php
	$output = ob_get_clean();
	return $output;
}

function homeo_properties_display_mode_form() {
	$output = '';
	$layout_type = homeo_get_properties_layout_type();
	if ( $layout_type == 'half-map' || $layout_type == 'half-map-v2' || $layout_type == 'half-map-v3' ) {
		$properties_page = WP_RealEstate_Mixes::get_properties_page_url();
		$display_mode = homeo_get_properties_display_mode();
		$output = homeo_display_mode_form($display_mode, $properties_page);
	}
	echo trim($output);
}
add_action( 'wp_realestate_before_property_archive', 'homeo_properties_display_mode_form', 30 );

function homeo_properties_display_save_search() {
	$output = '';
	$layout_type = homeo_get_properties_layout_type();
	if ( $layout_type == 'half-map' || $layout_type == 'half-map-v2' || $layout_type == 'half-map-v3' ) {
		$output = WP_RealEstate_Template_Loader::get_template_part('loop/property/properties-save-search-form');
	}
	echo trim($output);
}
add_action( 'wp_realestate_before_property_archive', 'homeo_properties_display_save_search', 35 );

function homeo_placeholder_img_src( $size = 'thumbnail' ) {
	$src               = get_template_directory_uri() . '/images/placeholder.png';
	$placeholder_image = homeo_get_config('property_placeholder_image');
	if ( !empty($placeholder_image['id']) ) {
        if ( is_numeric( $placeholder_image['id'] ) ) {
			$image = wp_get_attachment_image_src( $placeholder_image['id'], $size );

			if ( ! empty( $image[0] ) ) {
				$src = $image[0];
			}
		} else {
			$src = $placeholder_image;
		}
    }

	return apply_filters( 'homeo_job_placeholder_img_src', $src );
}

function homeo_compare_footer_html() {
	if ( !homeo_get_config('listing_enable_compare', true) ) {
		return;
	}
	$compare_ids = WP_RealEstate_Compare::get_compare_items(); ?>
	<div id="compare-sidebar" class="<?php echo esc_attr(count($compare_ids) > 0 ? 'active' : ''); ?>">
		<h3 class="title"><?php echo esc_html__('Compare Properties', 'homeo'); ?></h3>
		<div class="compare-sidebar-inner">
			<div class="compare-list">
				<?php
					if ( count($compare_ids) > 0 ) {
						$page_id = wp_realestate_get_option('compare_properties_page_id');
	            		$submit_url = $page_id ? get_permalink($page_id) : home_url( '/' );
						
						foreach ($compare_ids as $property_id) {
							$post_object = get_post( $property_id );
	                        if ( $post_object ) {
	                            setup_postdata( $GLOBALS['post'] =& $post_object );
	                            echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list-compare-small' );
	                        }
						}
					}
				?>
			</div>
			<?php if ( count($compare_ids) > 0 ) { ?>
				<div class="compare-actions">
					<div class="row-20 clearfix">
						<div class="col-xs-6">
						<a href="<?php echo esc_url($submit_url); ?>" class="btn btn-theme-second btn-block btn-sm"><?php echo esc_html__('Compare', 'homeo'); ?></a>
						</div>
						<div class="col-xs-6">
						<a href="javascript:void(0);" class="btn-remove-compare-all btn btn-danger btn-block btn-sm" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-remove-property-compare-nonce' )); ?>"><?php echo esc_html__('Clear', 'homeo'); ?></a>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="compare-sidebar-btn">
			<?php esc_html_e( 'Compare', 'homeo' ); ?> (<span class="count"><?php echo count($compare_ids); ?></span>)
		</div>
	</div><!-- .widget-area -->
<?php
}
add_action( 'wp_footer', 'homeo_compare_footer_html', 10 );

function homeo_add_remove_property_compare_return($return) {
	$compare_ids = WP_RealEstate_Compare::get_compare_items();
	$output = '';
	if ( !empty($compare_ids) && count($compare_ids) > 0 ) {
		ob_start();
		$page_id = wp_realestate_get_option('compare_properties_page_id');
		$submit_url = $page_id ? get_permalink($page_id) : home_url( '/' );
		?>
		<div class="compare-list">
			<?php
			foreach ($compare_ids as $property_id) {
				$post_object = get_post( $property_id );
                if ( $post_object ) {
                    setup_postdata( $GLOBALS['post'] =& $post_object );
                    echo WP_RealEstate_Template_Loader::get_template_part( 'properties-styles/inner-list-compare-small' );
                }
			}
			?>
		</div>
		<div class="compare-actions">
			<div class="row-20 clearfix">
				<div class="col-xs-6">
				<a href="<?php echo esc_url($submit_url); ?>" class="btn btn-theme-second btn-block btn-sm"><?php echo esc_html__('Compare', 'homeo'); ?></a>
				</div>
				<div class="col-xs-6">
				<a href="javascript:void(0);" class="btn-remove-compare-all btn btn-danger btn-block btn-sm" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-realestate-remove-property-compare-nonce' )); ?>"><?php echo esc_html__('Clear', 'homeo'); ?></a>
				</div>
			</div>
		</div>
		<?php
		$output = ob_get_clean();
	}
	$return['html_output'] = $output;
	$return['count'] = !empty($compare_ids) ? count($compare_ids) : 0;
	

	return $return;
}
add_filter( 'wp-realestate-process-add-property-compare-return', 'homeo_add_remove_property_compare_return', 10, 1 );
add_filter( 'wp-realestate-process-remove-property-compare-return', 'homeo_add_remove_property_compare_return', 10, 1 );


remove_action( 'wp_realestate_before_property_archive', array( 'WP_RealEstate_Property', 'display_properties_orderby_start' ), 15 );
add_action( 'wp_realestate_before_property_archive', array( 'WP_RealEstate_Property', 'display_properties_orderby_start' ), 1 );



// autocomplete search properties
add_action( 'wre_ajax_homeo_autocomplete_search_properties', 'homeo_autocomplete_search_properties' );

add_action( 'wp_ajax_homeo_autocomplete_search_properties', 'homeo_autocomplete_search_properties' );
add_action( 'wp_ajax_nopriv_homeo_autocomplete_search_properties', 'homeo_autocomplete_search_properties' );

function homeo_autocomplete_search_properties() {
    // Query for suggestions
    $suggestions = array();
    $args = array(
		'post_type' => 'property',
		'posts_per_page' => 10,
		'fields' => 'ids'
	);
    $filter_params = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
	$jobs = WP_RealEstate_Query::get_posts( $args, $filter_params );

	if ( !empty($jobs->posts) ) {
		foreach ($jobs->posts as $post_id) {
			$suggestion['title'] = get_the_title($post_id);
			$suggestion['url'] = get_permalink($post_id);

			if ( has_post_thumbnail( $post_id ) ) {
	            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
	            $suggestion['image'] = $image[0];
	        } else {
	            $suggestion['image'] = homeo_placeholder_img_src();
	        }
	        $suggestion['location'] = homeo_property_display_full_location_without_url($post_id, 'icon', false);
	        $suggestion['price'] = homeo_property_display_price($post_id, 'icon', false);

        	$suggestions[] = $suggestion;
		}
		wp_reset_postdata();
	}

    echo json_encode( $suggestions );
 
    exit;
}


function homeo_user_display_phone($phone, $display_type = 'no-title', $echo = true, $always_show_phone = false) {
    ob_start();
    if ( $phone ) {
        $show_full = homeo_get_config('listing_show_full_phone', false);
        $hide_phone = $show_full ? false : true;
        $hide_phone = apply_filters('homeo_phone_hide_number', $hide_phone );
        if ( $always_show_phone ) {
        	$hide_phone = false;
        }
        $add_class = '';
        if ( $hide_phone ) {
            $add_class = 'phone-hide';
        }
        if ( $display_type == 'title' ) {
            ?>
            <div class="phone-wrapper agent-phone with-title <?php echo esc_attr($add_class); ?>">
                <span><?php esc_html_e('Phone:', 'homeo'); ?></span>
            <?php
        } elseif ($display_type == 'icon') {
            ?>
            <div class="phone-wrapper agent-phone with-icon <?php echo esc_attr($add_class); ?>">
                <i class="ti-headphone-alt"></i>
        <?php
        } else {
            ?>
            <div class="phone-wrapper agent-phone <?php echo esc_attr($add_class); ?>">
            <?php
        }

        ?>
            <a class="phone" href="tel:<?php echo trim($phone); ?>"><?php echo trim($phone); ?></a>
            <?php if ( $hide_phone ) {
                $dispnum = substr($phone, 0, (strlen($phone)-3) ) . str_repeat("*", 3);
            ?>
                <span class="phone-show" onclick="this.parentNode.classList.add('show');"><?php echo trim($dispnum); ?> <span><?php esc_html_e('show', 'homeo'); ?></span></span>
            <?php } ?>
        </div>
        <?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}


add_action( 'wp_ajax_nopriv_homeo_ajax_print_property', 'homeo_ajax_print_property' );
add_action( 'wp_ajax_homeo_ajax_print_property', 'homeo_ajax_print_property' );

add_action( 'wre_ajax_homeo_ajax_print_property', 'homeo_ajax_print_property' );

function homeo_ajax_print_property () {
	if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'homeo-printer-property-nonce' )  ) {
		exit();
	}
	if( !isset($_POST['property_id'])|| !is_numeric($_POST['property_id']) ){
        exit();
    }

    $property_id = intval($_POST['property_id']);
    $the_post = get_post( $property_id );

    if( $the_post->post_type != 'property' || $the_post->post_status != 'publish' ) {
        exit();
    }
    setup_postdata( $GLOBALS['post'] =& $the_post );
    global $post;

    
    print  '<html><head><link href="'.get_stylesheet_uri().'" rel="stylesheet" type="text/css" />';
    if( is_rtl() ) {
    	print '<link href="'.get_template_directory_uri().'/css/bootstrap-rtl.css" rel="stylesheet" type="text/css" />';
    } else {
	    print  '<html><head><link href="'.get_template_directory_uri().'/css/bootstrap.css" rel="stylesheet" type="text/css" />';
	}
    print  '<html><head><link href="'.get_template_directory_uri().'/css/all-awesome.css" rel="stylesheet" type="text/css" />';
    print  '<html><head><link href="'.get_template_directory_uri().'/css/flaticon.css" rel="stylesheet" type="text/css" />';
    print  '<html><head><link href="'.get_template_directory_uri().'/css/themify-icons.css" rel="stylesheet" type="text/css" />';
    print  '<html><head><link href="'.get_template_directory_uri().'/css/template.css" rel="stylesheet" type="text/css" />';


    print '</head>';
    print '<script>window.onload = function() { window.print(); }</script>';
    print '<body>';

    $logo = homeo_get_config('print-logo');
    if( isset($logo['url']) && !empty($logo['url']) ) {
    	$print_logo = $logo['url'];
    } else {
    	$print_logo = get_template_directory_uri().'/images/logo.svg';
    }
    $title = get_the_title( $property_id );

    $image_id = get_post_thumbnail_id( $property_id );
    $full_img = wp_get_attachment_image_src($image_id, 'homeo-slider');
    $full_img = $full_img [0];

    ?>

    <section id="section-body">
        <!--start detail content-->
        <section class="section-detail-content">
            <div class="detail-bar print-detail">
                
                <?php if ( homeo_get_config('show_print_header', true) ) { ?>
	            	<div class="print-header-top">
	                    <div class="inner">
	                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="print-logo">
	                            <img src="<?php echo esc_url($print_logo); ?>" alt="logo">
	                            <span class="tag-line"><?php bloginfo( 'description' ); ?></span>
	                        </a>
	                    </div>
	                </div>
	            <?php } ?>

                <div class="print-header-middle">
                    <div class="print-header-middle-left">
                        <h1><?php echo esc_attr($title); ?></h1>
                        <?php homeo_property_display_full_location($post,'no-icon-title',true); ?>
                    </div>
                    <div class="print-header-middle-right">
                        <?php homeo_property_display_price($post); ?>
                    </div>
                </div>

                <?php if( !empty($full_img) ) { ?>
	                <div class="print-banner">
	                    <div class="print-main-image">
                            <img src="<?php echo esc_url( $full_img ); ?>" alt="<?php echo esc_attr($title); ?>">
                            <?php if ( homeo_get_config('show_print_qrcode', true) ) { ?>
	                            <img class="qr-image" src="https://chart.googleapis.com/chart?chs=105x104&cht=qr&chl=<?php echo esc_url( get_permalink($property_id) ); ?>&choe=UTF-8" title="<?php echo esc_attr($title); ?>" />
	                        <?php } ?>
	                    </div>
	                </div>
                <?php } ?>
                <?php
                
                if ( homeo_get_config('show_print_agent', true) ) {
                	$author_id = $post->post_author;
					$avatar = $a_phone = $a_website = $a_title = '';
					if ( WP_RealEstate_User::is_agency($author_id) ) {
						$agency_id = WP_RealEstate_User::get_agency_by_user_id($author_id);
						$agency_post = get_post($agency_id);
						$author_email = homeo_agency_display_email($agency_post, 'no-title', false);
						
						$post_thumbnail_id = get_post_thumbnail_id($agency_id);
	            		$avatar = wp_get_attachment_image( $post_thumbnail_id, 'thumbnail' );

						$a_title = get_the_title($agency_id);
						$a_phone = homeo_agency_display_phone($agency_post, 'no-title', false, true);
						$a_website = homeo_agency_display_website($agent_post, 'no-title', false);
					} elseif ( WP_RealEstate_User::is_agent($author_id) ) {
						$agent_id = WP_RealEstate_User::get_agent_by_user_id($author_id);
						$agent_post = get_post($agent_id);
						$author_email = homeo_agent_display_email($agent_post, 'no-title', false);

						$post_thumbnail_id = get_post_thumbnail_id($agent_id);
	            		$avatar = wp_get_attachment_image( $post_thumbnail_id, 'thumbnail' );

						$a_title = get_the_title($agent_id);
						$a_phone = homeo_agent_display_phone($agent_post, 'no-title', false, true);
						$a_website = homeo_agent_display_website($agent_post, 'no-title', false);
					} else {
						$user_id = $post->post_author;
						$author_email = get_the_author_meta('user_email');
						$a_title = get_the_author_meta('display_name');
						$a_phone = get_user_meta($user_id, '_phone', true);
						$a_phone = homeo_user_display_phone($a_phone, 'no-title', false, true);
						$a_website = get_user_meta($user_id, '_url', true);
					}
            	?>
                    <div class="print-block">
                    	<h3><?php esc_html_e( 'Contact Agent', 'homeo' ); ?></h3>
                        <div class="agent-media">
                            <div class="media-image-left">
                                <?php if ( !empty($avatar) ) {
									echo trim($avatar);
								} else {
							        echo get_avatar($post->post_author, 180);
								} ?>
                            </div>
                            <div class="media-body-right">
                                
                                <h4 class="title"><?php echo trim($a_title); ?></h4>
								<div class="phone"><?php echo trim($a_phone); ?></div>
								<div class="email"><?php echo trim($author_email); ?></div>
								<div class="website"><?php echo trim($a_website); ?></div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div id="property-single-details">
					<?php
					if ( homeo_get_config('show_print_description', true) ) {
						?>
						<div class="description inner">
						    <h3 class="title"><?php esc_html_e('Overview', 'homeo'); ?></h3>
						    <div class="description-inner">
						        <?php the_content(); ?>
						        <?php do_action('wp-realestate-single-property-description', $post); ?>
						    </div>
						</div>
						<?php
					}
					
					if ( homeo_get_config('show_print_energy', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/energy' );
					}
					
					?>

					<?php
					if ( homeo_get_config('show_print_detail', true) ) {
						echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/detail' );
					}
					?>

				</div>

				<?php
				if ( homeo_get_config('show_print_amenities', true) ) {
					echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/amenities' );
				}
				?>

				<?php
				if ( homeo_get_config('show_print_floor-plans', true) ) {
					echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/floor-plans-print' );
				}
				?>
				
				<?php
				if ( homeo_get_config('show_print_facilities', true) ) {
					echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/facilities' );
				}
				?>

				<?php
				if ( homeo_get_config('show_print_valuation', true) ) {
					echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/valuation' );
				}

				$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);
				$gallery = $obj_property_meta->get_post_meta( 'gallery' );
				if ( homeo_get_config('show_print_gallery', true) && $gallery ) {
				?>
					<div class="print-gallery">
						<div class="detail-title-inner">
                            <h4 class="title-inner"><?php esc_html_e('Property images', 'homeo'); ?></h4>
                        </div>
                        <div class="row">
							<?php foreach ( $gallery as $id => $src ) { ?>
				                <div class="print-gallery-image col-xs-12 col-sm-6">
				                    <?php echo wp_get_attachment_image( $id, 'homeo-slider' ); ?>
				                </div>
			                <?php } ?>
		                </div>
		          	</div>
	          	<?php } ?>
				
            </div>
        </section>
    </section>


    <?php
    
    wp_reset_postdata();

    print '</body></html>';
    wp_die();
}



// demo function
function homeo_check_demo_account() {
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'agency' || strtolower($user_obj->data->user_login) == 'agent' ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Demo users are not allowed to modify information.', 'homeo') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}
}

add_action('wp-realestate-process-forgot-password', 'homeo_check_demo_account', 10);
add_action('wp-realestate-process-change-password', 'homeo_check_demo_account', 10);
add_action('wp-realestate-before-delete-profile', 'homeo_check_demo_account', 10);
add_action('wp-realestate-before-remove-property-alert', 'homeo_check_demo_account', 10 );
add_action('wp-realestate-before-change-profile-normal', 'homeo_check_demo_account', 10 );
add_action('wp-realestate-process-add-agent', 'homeo_check_demo_account', 10 );
add_action('wp-realestate-process-remove-agent', 'homeo_check_demo_account', 10 );
add_action('wp-realestate-process-remove-before-save', 'homeo_check_demo_account', 10);

function homeo_check_demo_account2($error) {
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'agency' || strtolower($user_obj->data->user_login) == 'agent' ) {
			$error[] = esc_html__('Demo users are not allowed to modify information.', 'homeo');
		}
	}
	return $error;
}
add_filter('wp-realestate-submission-validate', 'homeo_check_demo_account2', 10, 2);
add_filter('wp-realestate-edit-validate', 'homeo_check_demo_account2', 10, 2);

function homeo_check_demo_account3($post_id, $prefix) {
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'agency' || strtolower($user_obj->data->user_login) == 'agent' ) {
			$_SESSION['messages'][] = array( 'danger', esc_html__('Demo users are not allowed to modify information.', 'homeo') );
			$redirect_url = get_permalink( wp_realestate_get_option('edit_profile_page_id') );
			WP_RealEstate_Mixes::redirect( $redirect_url );
			exit();
		}
	}
}
add_action('wp-realestate-process-profile-before-change', 'homeo_check_demo_account3', 10, 2);

function homeo_check_demo_account4() {
	if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'agency' || strtolower($user_obj->data->user_login) == 'agent' ) {
			$return['msg'] = esc_html__('Demo users are not allowed to modify information.', 'homeo');
			$return['status'] = false;
			echo json_encode($return); exit;
		}
	}
}
add_action('wp-private-message-before-reply-message', 'homeo_check_demo_account4');
add_action('wp-private-message-before-add-message', 'homeo_check_demo_account4');
add_action('wp-private-message-before-delete-message', 'homeo_check_demo_account4');