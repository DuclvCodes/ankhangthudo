<?php

if ( !function_exists( 'homeo_page_metaboxes' ) ) {
	function homeo_page_metaboxes(array $metaboxes) {
		global $wp_registered_sidebars;
        $sidebars = array();

        if ( !empty($wp_registered_sidebars) ) {
            foreach ($wp_registered_sidebars as $sidebar) {
                $sidebars[$sidebar['id']] = $sidebar['name'];
            }
        }
        $headers = array_merge( array('global' => esc_html__( 'Global Setting', 'homeo' )), homeo_get_header_layouts() );
        $footers = array_merge( array('global' => esc_html__( 'Global Setting', 'homeo' )), homeo_get_footer_layouts() );

		$prefix = 'apus_page_';

        $columns = array(
            '' => esc_html__( 'Global Setting', 'homeo' ),
            '1' => esc_html__('1 Column', 'homeo'),
            '2' => esc_html__('2 Columns', 'homeo'),
            '3' => esc_html__('3 Columns', 'homeo'),
            '4' => esc_html__('4 Columns', 'homeo'),
            '6' => esc_html__('6 Columns', 'homeo')
        );
        $elementor_options = ['' => esc_html__('Global Setting', 'homeo')];
        if ( did_action( 'elementor/loaded' ) ) {
            $ele_obj = \Elementor\Plugin::$instance;
            $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
            
            if ( !empty( $templates ) ) {
                foreach ( $templates as $template ) {
                    $elementor_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
                }
            }
        }
        // Properties Page
        $fields = array(
            array(
                'name' => esc_html__( 'Properties Layout', 'homeo' ),
                'id'   => $prefix.'layout_type',
                'type' => 'select',
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'default' => esc_html__('Default', 'homeo'),
                    'half-map' => esc_html__('Half Map', 'homeo'),
                    'half-map-v2' => esc_html__('Half Map - v2', 'homeo'),
                    'half-map-v3' => esc_html__('Half Map - v3', 'homeo'),
                    'top-map' => esc_html__('Top Map', 'homeo'),
                )
            ),
            array(
                'name' => esc_html__( 'Top Content (Elementor Template)', 'homeo' ),
                'id'   => $prefix.'elementor_template',
                'type' => 'select',
                'options' => $elementor_options
            ),
            array(
                'id' => $prefix.'display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                )
            ),
            array(
                'id' => $prefix.'inner_grid_style',
                'type' => 'select',
                'name' => esc_html__('Properties grid style', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'grid' => esc_html__('Grid Default', 'homeo'),
                    'grid-v1' => esc_html__('Grid V1', 'homeo'),
                    'grid-v2' => esc_html__('Grid V2', 'homeo'),
                    'grid-v3' => esc_html__('Grid V3', 'homeo'),
                    'grid-v4' => esc_html__('Grid V4', 'homeo'),
                ),
            ),
            array(
                'id' => $prefix.'properties_columns',
                'type' => 'select',
                'name' => esc_html__('Grid Listing Columns', 'homeo'),
                'options' => $columns,
            ),
            array(
                'id' => $prefix.'properties_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
            ),
        );
        
        $metaboxes[$prefix . 'properties_setting'] = array(
            'id'                        => $prefix . 'properties_setting',
            'title'                     => esc_html__( 'Properties Settings', 'homeo' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );


        // Agents Page
        $fields = array(
            array(
                'id' => $prefix.'agents_columns',
                'type' => 'select',
                'name' => esc_html__('Agent Columns', 'homeo'),
                'options' => $columns,
                'description' => esc_html__('Apply for display mode is grid and simple.', 'homeo'),
            ),
            array(
                'id' => $prefix.'agents_display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                )
            ),
            array(
                'id' => $prefix.'agents_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
            ),
        );
        $metaboxes[$prefix . 'agents_setting'] = array(
            'id'                        => $prefix . 'agents_setting',
            'title'                     => esc_html__( 'Agents Settings', 'homeo' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // Agencies Page
        $fields = array(
            array(
                'id' => $prefix.'agencies_columns',
                'type' => 'select',
                'name' => esc_html__('Agency Columns', 'homeo'),
                'options' => $columns,
                'description' => esc_html__('Apply for display mode is grid.', 'homeo'),
            ),
            array(
                'id' => $prefix.'agencies_display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                )
            ),
            array(
                'id' => $prefix.'agencies_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'homeo' ),
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
            ),
        );
        $metaboxes[$prefix . 'agencies_setting'] = array(
            'id'                        => $prefix . 'agencies_setting',
            'title'                     => esc_html__( 'Agencies Settings', 'homeo' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // General
	    $fields = array(
			array(
				'name' => esc_html__( 'Select Layout', 'homeo' ),
				'id'   => $prefix.'layout',
				'type' => 'select',
				'options' => array(
					'main' => esc_html__('Main Content Only', 'homeo'),
					'left-main' => esc_html__('Left Sidebar - Main Content', 'homeo'),
					'main-right' => esc_html__('Main Content - Right Sidebar', 'homeo')
				)
			),
			array(
                'id' => $prefix.'fullwidth',
                'type' => 'select',
                'name' => esc_html__('Is Full Width?', 'homeo'),
                'default' => 'no',
                'options' => array(
                    'no' => esc_html__('No', 'homeo'),
                    'yes' => esc_html__('Yes', 'homeo')
                )
            ),
            array(
                'id' => $prefix.'left_sidebar',
                'type' => 'select',
                'name' => esc_html__('Left Sidebar', 'homeo'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'right_sidebar',
                'type' => 'select',
                'name' => esc_html__('Right Sidebar', 'homeo'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'show_breadcrumb',
                'type' => 'select',
                'name' => esc_html__('Show Breadcrumb?', 'homeo'),
                'options' => array(
                    'no' => esc_html__('No', 'homeo'),
                    'yes' => esc_html__('Yes', 'homeo')
                ),
                'default' => 'yes',
            ),
            array(
                'id' => $prefix.'breadcrumb_color',
                'type' => 'colorpicker',
                'name' => esc_html__('Breadcrumb Background Color', 'homeo')
            ),
            array(
                'id' => $prefix.'breadcrumb_image',
                'type' => 'file',
                'name' => esc_html__('Breadcrumb Background Image', 'homeo')
            ),
            array(
                'id' => $prefix.'breadcrumb_style',
                'type' => 'select',
                'name' => esc_html__('Breadcrumb Style', 'homeo'),
                'options' => array(
                    'horizontal' => esc_html__('Horizontal', 'homeo'),
                    'vertical' => esc_html__('Vertical', 'homeo'),
                ),
                'default' => 'horizontal'
            ),

            array(
                'id' => $prefix.'header_type',
                'type' => 'select',
                'name' => esc_html__('Header Layout Type', 'homeo'),
                'description' => esc_html__('Choose a header for your website.', 'homeo'),
                'options' => $headers,
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'header_transparent',
                'type' => 'select',
                'name' => esc_html__('Header Transparent', 'homeo'),
                'description' => esc_html__('Choose a header for your website.', 'homeo'),
                'options' => array(
                    'no' => esc_html__('No', 'homeo'),
                    'yes' => esc_html__('Yes', 'homeo')
                ),
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'header_fixed',
                'type' => 'select',
                'name' => esc_html__('Header Fixed Top', 'homeo'),
                'description' => esc_html__('Choose a header position', 'homeo'),
                'options' => array(
                    'no' => esc_html__('No', 'homeo'),
                    'yes' => esc_html__('Yes', 'homeo')
                ),
                'default' => 'no'
            ),
            array(
                'id' => $prefix.'footer_type',
                'type' => 'select',
                'name' => esc_html__('Footer Layout Type', 'homeo'),
                'description' => esc_html__('Choose a footer for your website.', 'homeo'),
                'options' => $footers,
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'extra_class',
                'type' => 'text',
                'name' => esc_html__('Extra Class', 'homeo'),
                'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'homeo')
            )
    	);
		
	    $metaboxes[$prefix . 'display_setting'] = array(
			'id'                        => $prefix . 'display_setting',
			'title'                     => esc_html__( 'Display Settings', 'homeo' ),
			'object_types'              => array( 'page' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => $fields
		);

	    return $metaboxes;
	}
}
add_filter( 'cmb2_meta_boxes', 'homeo_page_metaboxes' );

if ( !function_exists( 'homeo_cmb2_style' ) ) {
	function homeo_cmb2_style() {
        wp_enqueue_style( 'homeo-cmb2-style', get_template_directory_uri() . '/inc/vendors/cmb2/assets/style.css', array(), '1.0' );
		wp_enqueue_script( 'homeo-admin', get_template_directory_uri() . '/js/admin.js', array( 'jquery' ), '20150330', true );
	}
}
add_action( 'admin_enqueue_scripts', 'homeo_cmb2_style' );


