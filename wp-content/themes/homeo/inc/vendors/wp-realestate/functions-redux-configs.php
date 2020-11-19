<?php

function homeo_wp_realestate_redux_config($sections, $sidebars, $columns) {
    
    $sections[] = array(
        'icon' => 'el el-pencil',
        'title' => esc_html__('Properties Settings', 'homeo'),
        'fields' => array(
            array(
                'id' => 'show_property_breadcrumbs',
                'type' => 'switch',
                'title' => esc_html__('Breadcrumbs', 'homeo'),
                'default' => 1
            ),
            array(
                'title' => esc_html__('Breadcrumbs Background Color', 'homeo'),
                'subtitle' => '<em>'.esc_html__('The breadcrumbs background color of the site.', 'homeo').'</em>',
                'id' => 'property_breadcrumb_color',
                'type' => 'color',
                'transparent' => false,
            ),
            array(
                'id' => 'property_breadcrumb_image',
                'type' => 'media',
                'title' => esc_html__('Breadcrumbs Background', 'homeo'),
                'subtitle' => esc_html__('Upload a .jpg or .png image that will be your breadcrumbs.', 'homeo'),
            ),
            array(
                'id' => 'property_breadcrumb_style',
                'type' => 'select',
                'title' => esc_html__('Breadcrumbs Style', 'homeo'),
                'subtitle' => esc_html__('Choose a style for breadcrumbs.', 'homeo'),
                'options' => array(
                    'horizontal' => esc_html__('Horizontal', 'homeo'),
                    'vertical' => esc_html__('Vertical', 'homeo'),
                ),
                'default' => 'vertical'
            ),
            array(
                'id' => 'listing_general_hour_settings',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3> '.esc_html__('Other Settings', 'homeo').'</h3>',
            ),
            array(
                'id' => 'listing_show_full_phone',
                'type' => 'switch',
                'title' => esc_html__('Show Full Phone Number', 'homeo'),
                'default' => 0,
            ),
            array(
                'id' => 'listing_enable_favorite',
                'type' => 'switch',
                'title' => esc_html__('Enable Favorite', 'homeo'),
                'default' => 1,
            ),
            array(
                'id' => 'listing_enable_compare',
                'type' => 'switch',
                'title' => esc_html__('Enable Compare', 'homeo'),
                'default' => 1,
            ),
        )
    );
    // Archive settings
    $elementor_options = ['' => esc_html__('Choose a Elementor Template', 'homeo')];
    if ( did_action( 'elementor/loaded' ) && is_admin() && !empty($_GET['page']) && $_GET['page'] == '_options' ) {
        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
        
        if ( !empty( $templates ) ) {
            foreach ( $templates as $template ) {
                $elementor_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            }
        }
    }
    $sections[] = array(
        'title' => esc_html__('Property Archives', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'properties_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'properties_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'properties_layout_type',
                'type' => 'select',
                'title' => esc_html__('Properties Layout Style', 'homeo'),
                'subtitle' => esc_html__('Choose a default layout archive property.', 'homeo'),
                'options' => array(
                    'default' => esc_html__('Default', 'homeo'),
                    'half-map' => esc_html__('Half Map', 'homeo'),
                    'half-map-v2' => esc_html__('Half Map - v2', 'homeo'),
                    'half-map-v3' => esc_html__('Half Map - v3', 'homeo'),
                    'top-map' => esc_html__('Top Map', 'homeo'),
                ),
                'default' => 'default',
            ),

            array(
                'id' => 'properties_elementor_template',
                'type' => 'select',
                'title' => esc_html__('Top Content (Elementor Template)', 'homeo'),
                'subtitle' => esc_html__('Choose a Elementor Template to show in top.', 'homeo'),
                'options' => $elementor_options,
                'default' => '',
                'required' => array('properties_layout_type', '=', array('default')),
            ),

            array(
                'id' => 'properties_layout_sidebar',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Sidebar Layout', 'homeo'),
                'subtitle' => esc_html__('Select a sidebar layout', 'homeo'),
                'options' => array(
                    'main' => array(
                        'title' => esc_html__('Main Content', 'homeo'),
                        'alt' => esc_html__('Main Content', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen1.png'
                    ),
                    'left-main' => array(
                        'title' => esc_html__('Left - Main Sidebar', 'homeo'),
                        'alt' => esc_html__('Left - Main Sidebar', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen2.png'
                    ),
                    'main-right' => array(
                        'title' => esc_html__('Main - Right Sidebar', 'homeo'),
                        'alt' => esc_html__('Main - Right Sidebar', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen3.png'
                    ),
                ),
                'default' => 'main-right',
                'required' => array('properties_layout_type', '=', array('default', 'top-map')),
            ),

            array(
                'id' => 'properties_display_mode',
                'type' => 'select',
                'title' => esc_html__('Properties display mode', 'homeo'),
                'subtitle' => esc_html__('Choose a default display mode for archive property.', 'homeo'),
                'options' => array(
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                ),
                'default' => 'grid'
            ),

            array(
                'id' => 'properties_inner_grid_style',
                'type' => 'select',
                'title' => esc_html__('Properties Item Style', 'homeo'),
                'subtitle' => esc_html__('Choose a default display item style for archive property.', 'homeo'),
                'options' => array(
                    'grid' => esc_html__('Grid style default', 'homeo'),
                    'grid-v1' => esc_html__('Grid style v1', 'homeo'),
                    'grid-v2' => esc_html__('Grid style v2', 'homeo'),
                    'grid-v3' => esc_html__('Grid style v3', 'homeo'),
                    'grid-v4' => esc_html__('Grid style v4', 'homeo'),
                ),
                'default' => 'grid',
                'required' => array('properties_display_mode', '=', 'grid')
            ),

            array(
                'id' => 'properties_columns',
                'type' => 'select',
                'title' => esc_html__('Property Columns', 'homeo'),
                'options' => $columns,
                'default' => 3,
                'required' => array('properties_display_mode', '=', array('grid'))
            ),
            array(
                'id' => 'properties_gallery',
                'type' => 'switch',
                'title' => esc_html__('Show Property gallery', 'homeo'),
                'default' => true
            ),

            array(
                'id' => 'properties_pagination',
                'type' => 'select',
                'title' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
                'default' => 'default'
            ),
            array(
                'id' => 'property_placeholder_image',
                'type' => 'media',
                'title' => esc_html__('Placeholder Image', 'homeo'),
                'subtitle' => esc_html__('Upload a .jpg or .png image that will be your placeholder.', 'homeo'),
            ),
        )
    );
    
    
    // Property Page
    $fields = array(
        array(
            'id' => 'property_general_setting',
            'icon' => true,
            'type' => 'info',
            'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
        ),
        array(
            'id' => 'property_fullwidth',
            'type' => 'switch',
            'title' => esc_html__('Is Full Width?', 'homeo'),
            'default' => false
        ),
        array(
            'id' => 'property_layout_type',
            'type' => 'select',
            'title' => esc_html__('Property Layout', 'homeo'),
            'subtitle' => esc_html__('Choose a default layout single property.', 'homeo'),
            'options' => array(
                'v1' => esc_html__('Layout 1', 'homeo'),
                'v2' => esc_html__('Layout 2', 'homeo'),
                'v3' => esc_html__('Layout 3', 'homeo'),
                'v4' => esc_html__('Layout 4', 'homeo'),
                'v5' => esc_html__('Layout 5', 'homeo'),
            ),
            'default' => 'v1',
        ),
        array(
            'id' => 'show_property_social_share',
            'type' => 'switch',
            'title' => esc_html__('Show Social Share', 'homeo'),
            'default' => 1
        ),
    );
    $contents = apply_filters('homeo_property_single_sort_content', array(
        'description' => esc_html__('Description', 'homeo'),
        'energy' => esc_html__('EU Energy', 'homeo'),
        'detail' => esc_html__('Detail', 'homeo'),
        'attachments' => esc_html__('Attachments', 'homeo'),
        'amenities' => esc_html__('Amenities', 'homeo'),
        'materials' => esc_html__('Materials', 'homeo'),
        'location' => esc_html__('Location', 'homeo'),
        'floor-plans' => esc_html__('Floor plans', 'homeo'),
        'tabs-video-virtual' => esc_html__('Video & Virtual tour', 'homeo'),
        'facilities' => esc_html__('Facilities', 'homeo'),
        'valuation' => esc_html__('Valuation', 'homeo'),
        'stats_graph' => esc_html__('Stats graph', 'homeo'),
        'nearby_yelp' => esc_html__('Yelp Nearby', 'homeo'),
        'walk_score' => esc_html__('Walk Score', 'homeo'),
        'subproperties' => esc_html__('Subproperties', 'homeo'),
        'related' => esc_html__('Related', 'homeo'),
    ));
    foreach ($contents as $key => $value) {
        $fields[] = array(
            'id' => 'show_property_'.$key,
            'type' => 'switch',
            'title' => sprintf(esc_html__('Show %s', 'homeo'), $value),
            'default' => 1
        );
    }
    $fields[] = array(
        'id' => 'property_subproperties_number',
        'title' => esc_html__('Number of Subproperties to show', 'homeo'),
        'default' => 2,
        'min' => '1',
        'step' => '1',
        'max' => '50',
        'type' => 'slider',
        'required' => array('show_property_subproperties', '=', true)
    );

    $fields[] = array(
        'id' => 'property_related_number',
        'title' => esc_html__('Number of related properties to show', 'homeo'),
        'default' => 2,
        'min' => '1',
        'step' => '1',
        'max' => '50',
        'type' => 'slider',
        'required' => array('show_property_related', '=', true)
    );

    $fields[] = array(
        'id' => 'property_general_setting',
        'icon' => true,
        'type' => 'info',
        'raw' => '<h3 style="margin: 0;"> '.esc_html__('Print Property Setting', 'homeo').'</h3>',
    );
    $fields[] = array(
        'id' => 'property_enable_printer',
        'type' => 'switch',
        'title' => esc_html__('Show Print Button', 'homeo'),
        'default' => 1
    );
    $fields[] = array(
        'id' => 'print-logo',
        'type' => 'media',
        'title' => esc_html__('Print Logo Upload', 'homeo'),
        'subtitle' => esc_html__('Upload a .png or .gif image that will be your logo.', 'homeo'),
        'required' => array('property_enable_printer', '=', true)
    );

    $contents = apply_filters('homeo_property_single_print_content', array(
        'header' => esc_html__('Print Header', 'homeo'),
        'qrcode' => esc_html__('Qrcode', 'homeo'),
        'agent' => esc_html__('Agent Info', 'homeo'),
        'description' => esc_html__('Description', 'homeo'),
        'energy' => esc_html__('EU Energy', 'homeo'),
        'detail' => esc_html__('Detail', 'homeo'),
        'amenities' => esc_html__('Amenities', 'homeo'),
        'floor-plans' => esc_html__('Floor plans', 'homeo'),
        'facilities' => esc_html__('Facilities', 'homeo'),
        'valuation' => esc_html__('Valuation', 'homeo'),
        'gallery' => esc_html__('Gallery', 'homeo'),
    ));
    foreach ($contents as $key => $value) {
        $fields[] = array(
            'id' => 'show_print_'.$key,
            'type' => 'switch',
            'title' => sprintf(esc_html__('Show %s', 'homeo'), $value),
            'default' => 1,
            'required' => array('property_enable_printer', '=', true)
        );
    }

    $sections[] = array(
        'title' => esc_html__('Single Property', 'homeo'),
        'subsection' => true,
        'fields' => $fields
    );
    return $sections;
}
add_filter( 'homeo_redux_framwork_configs', 'homeo_wp_realestate_redux_config', 10, 3 );


// agents
function homeo_wp_realestate_agent_redux_config($sections, $sidebars, $columns) {
    
    $sections[] = array(
        'icon' => 'el el-pencil',
        'title' => esc_html__('Agent Settings', 'homeo'),
        'fields' => array(
            array(
                'id' => 'show_agent_breadcrumbs',
                'type' => 'switch',
                'title' => esc_html__('Breadcrumbs', 'homeo'),
                'default' => 1
            ),
            array(
                'title' => esc_html__('Breadcrumbs Background Color', 'homeo'),
                'subtitle' => '<em>'.esc_html__('The breadcrumbs background color of the site.', 'homeo').'</em>',
                'id' => 'agent_breadcrumb_color',
                'type' => 'color',
                'transparent' => false,
            ),
            array(
                'id' => 'agent_breadcrumb_image',
                'type' => 'media',
                'title' => esc_html__('Breadcrumbs Background', 'homeo'),
                'subtitle' => esc_html__('Upload a .jpg or .png image that will be your breadcrumbs.', 'homeo'),
            ),
            array(
                'id' => 'agent_breadcrumb_style',
                'type' => 'select',
                'title' => esc_html__('Breadcrumbs Style', 'homeo'),
                'subtitle' => esc_html__('Choose a style for breadcrumbs.', 'homeo'),
                'options' => array(
                    'horizontal' => esc_html__('Horizontal', 'homeo'),
                    'vertical' => esc_html__('Vertical', 'homeo'),
                ),
                'default' => 'vertical'
            ),
        )
    );
    // Archive settings
    $sections[] = array(
        'title' => esc_html__('Agent Archives', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'agents_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'agents_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'agents_layout_sidebar',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Archive Product Layout', 'homeo'),
                'subtitle' => esc_html__('Select the layout you want to apply on your archive agents page.', 'homeo'),
                'options' => array(
                    'main' => array(
                        'title' => esc_html__('Main Content', 'homeo'),
                        'alt' => esc_html__('Main Content', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen1.png'
                    ),
                    'left-main' => array(
                        'title' => esc_html__('Left Sidebar - Main Content', 'homeo'),
                        'alt' => esc_html__('Left Sidebar - Main Content', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen2.png'
                    ),
                    'main-right' => array(
                        'title' => esc_html__('Main Content - Right Sidebar', 'homeo'),
                        'alt' => esc_html__('Main Content - Right Sidebar', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen3.png'
                    ),
                ),
                'default' => 'main-right'
            ),
            array(
                'id' => 'agents_display_mode',
                'type' => 'select',
                'title' => esc_html__('Agents display mode', 'homeo'),
                'subtitle' => esc_html__('Choose a default display mode for archive property.', 'homeo'),
                'options' => array(
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                ),
                'default' => 'grid'
            ),
            array(
                'id' => 'agents_columns',
                'type' => 'select',
                'title' => esc_html__('Agent Columns', 'homeo'),
                'options' => $columns,
                'default' => 4,
                'required' => array('agents_display_mode', '=', array('grid'))
            ),
            array(
                'id' => 'agents_pagination',
                'type' => 'select',
                'title' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
                'default' => 'default'
            )
            
        )
    );
    
    
    // Agent Page
    $sections[] = array(
        'title' => esc_html__('Single Agent', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'agent_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'agent_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'show_agent_social_share',
                'type' => 'switch',
                'title' => esc_html__('Show Social Share', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'agent_property_show',
                'type' => 'switch',
                'title' => esc_html__('Show Agent Properties', 'homeo'),
                'default' => 1
            ),
        )
    );
    
    return $sections;
}
add_filter( 'homeo_redux_framwork_configs', 'homeo_wp_realestate_agent_redux_config', 10, 3 );


// agencies
function homeo_wp_realestate_agency_redux_config($sections, $sidebars, $columns) {
    
    $sections[] = array(
        'icon' => 'el el-pencil',
        'title' => esc_html__('Agency Settings', 'homeo'),
        'fields' => array(
            array(
                'id' => 'show_agency_breadcrumbs',
                'type' => 'switch',
                'title' => esc_html__('Breadcrumbs', 'homeo'),
                'default' => 1
            ),
            array(
                'title' => esc_html__('Breadcrumbs Background Color', 'homeo'),
                'subtitle' => '<em>'.esc_html__('The breadcrumbs background color of the site.', 'homeo').'</em>',
                'id' => 'agency_breadcrumb_color',
                'type' => 'color',
                'transparent' => false,
            ),
            array(
                'id' => 'agency_breadcrumb_image',
                'type' => 'media',
                'title' => esc_html__('Breadcrumbs Background', 'homeo'),
                'subtitle' => esc_html__('Upload a .jpg or .png image that will be your breadcrumbs.', 'homeo'),
            ),
            array(
                'id' => 'agency_breadcrumb_style',
                'type' => 'select',
                'title' => esc_html__('Breadcrumbs Style', 'homeo'),
                'subtitle' => esc_html__('Choose a style for breadcrumbs.', 'homeo'),
                'options' => array(
                    'horizontal' => esc_html__('Horizontal', 'homeo'),
                    'vertical' => esc_html__('Vertical', 'homeo'),
                ),
                'default' => 'vertical'
            ),
        )
    );
    // Archive settings
    $sections[] = array(
        'title' => esc_html__('Agency Archives', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'agencies_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'agencies_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'agencies_layout_sidebar',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Archive Product Layout', 'homeo'),
                'subtitle' => esc_html__('Select the layout you want to apply on your archive agencies page.', 'homeo'),
                'options' => array(
                    'main' => array(
                        'title' => esc_html__('Main Content', 'homeo'),
                        'alt' => esc_html__('Main Content', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen1.png'
                    ),
                    'left-main' => array(
                        'title' => esc_html__('Left Sidebar - Main Content', 'homeo'),
                        'alt' => esc_html__('Left Sidebar - Main Content', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen2.png'
                    ),
                    'main-right' => array(
                        'title' => esc_html__('Main Content - Right Sidebar', 'homeo'),
                        'alt' => esc_html__('Main Content - Right Sidebar', 'homeo'),
                        'img' => get_template_directory_uri() . '/inc/assets/images/screen3.png'
                    ),
                ),
                'default' => 'main-right'
            ),
            array(
                'id' => 'agencies_display_mode',
                'type' => 'select',
                'title' => esc_html__('Agencies display mode', 'homeo'),
                'subtitle' => esc_html__('Choose a default display mode for archive property.', 'homeo'),
                'options' => array(
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                ),
                'default' => 'grid'
            ),
            array(
                'id' => 'agencies_columns',
                'type' => 'select',
                'title' => esc_html__('Agency Columns', 'homeo'),
                'options' => $columns,
                'default' => 4,
                'required' => array('agencies_display_mode', '=', array('grid'))
            ),
            array(
                'id' => 'agencies_pagination',
                'type' => 'select',
                'title' => esc_html__('Pagination Type', 'homeo'),
                'options' => array(
                    'default' => esc_html__('Default', 'homeo'),
                    'loadmore' => esc_html__('Load More Button', 'homeo'),
                    'infinite' => esc_html__('Infinite Scrolling', 'homeo'),
                ),
                'default' => 'default'
            )
        )
    );
    
    
    // Agency Page
    $sections[] = array(
        'title' => esc_html__('Single Agency', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'agency_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'agency_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'show_agency_social_share',
                'type' => 'switch',
                'title' => esc_html__('Show Social Share', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'agency_block_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('Agency Block Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'agency_agent_show',
                'type' => 'switch',
                'title' => esc_html__('Show Agency Agents', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'agency_property_show',
                'type' => 'switch',
                'title' => esc_html__('Show Agency Properties', 'homeo'),
                'default' => 1
            ),
        )
    );
    
    // Archive settings
    $sections[] = array(
        'title' => esc_html__('Register Form', 'homeo'),
        'fields' => array(
            array(
                'id' => 'register_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'register_form_enable_agency',
                'type' => 'switch',
                'title' => esc_html__('Enable Register Agency', 'homeo'),
                'default' => true,
            ),
            array(
                'id' => 'register_form_enable_agent',
                'type' => 'switch',
                'title' => esc_html__('Enable Register Agent', 'homeo'),
                'default' => true,
            ),
        )
    );

    return $sections;
}
add_filter( 'homeo_redux_framwork_configs', 'homeo_wp_realestate_agency_redux_config', 10, 3 );
