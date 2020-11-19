<?php

// Shop Archive settings
function homeo_woo_redux_config($sections, $sidebars, $columns) {
    
    $sections[] = array(
        'icon' => 'el el-shopping-cart',
        'title' => esc_html__('Shop Settings', 'homeo'),
        'fields' => array(
            array(
                'id' => 'products_breadcrumb_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('Breadcrumbs Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'show_product_breadcrumbs',
                'type' => 'switch',
                'title' => esc_html__('Breadcrumbs', 'homeo'),
                'default' => 1
            ),
            array(
                'title' => esc_html__('Breadcrumbs Background Color', 'homeo'),
                'subtitle' => '<em>'.esc_html__('The breadcrumbs background color of the site.', 'homeo').'</em>',
                'id' => 'woo_breadcrumb_color',
                'type' => 'color',
                'transparent' => false,
            ),
            array(
                'id' => 'woo_breadcrumb_image',
                'type' => 'media',
                'title' => esc_html__('Breadcrumbs Background', 'homeo'),
                'subtitle' => esc_html__('Upload a .jpg or .png image that will be your breadcrumbs.', 'homeo'),
            ),
            array(
                'id' => 'woo_breadcrumb_style',
                'type' => 'select',
                'title' => esc_html__('Breadcrumbs Style', 'homeo'),
                'options' => array(
                    'default' => esc_html__('Default', 'homeo'),
                    'center' => esc_html__('Style 1', 'homeo'),
                ),
                'default' => 'default'
            ),
        )
    );
    // Archive settings
    $sections[] = array(
        'title' => esc_html__('Product Archives', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'products_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'show_shop_cat_title',
                'type' => 'switch',
                'title' => esc_html__('Show Shop/Category Title ?', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'product_display_mode',
                'type' => 'select',
                'title' => esc_html__('Products Layout', 'homeo'),
                'subtitle' => esc_html__('Choose a default layout archive product.', 'homeo'),
                'options' => array(
                    'grid' => esc_html__('Grid', 'homeo'),
                    'list' => esc_html__('List', 'homeo'),
                ),
                'default' => 'grid'
            ),
            array(
                'id' => 'product_columns',
                'type' => 'select',
                'title' => esc_html__('Product Columns', 'homeo'),
                'options' => $columns,
                'default' => 4,
                'required' => array('product_display_mode', '=', array('grid'))
            ),
            array(
                'id' => 'number_products_per_page',
                'type' => 'text',
                'title' => esc_html__('Number of Products Per Page', 'homeo'),
                'default' => 12,
                'min' => '1',
                'step' => '1',
                'max' => '100',
                'type' => 'slider'
            ),
            array(
                'id' => 'enable_swap_image',
                'type' => 'switch',
                'title' => esc_html__('Enable Swap Image', 'homeo'),
                'default' => 1
            ),

            array(
                'id' => 'products_sidebar_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('Sidebar Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'product_archive_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'product_archive_layout',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Archive Product Layout', 'homeo'),
                'subtitle' => esc_html__('Select the layout you want to apply on your archive product page.', 'homeo'),
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
                'default' => 'main'
            ),
            array(
                'id' => 'product_archive_left_sidebar',
                'type' => 'select',
                'title' => esc_html__('Archive Left Sidebar', 'homeo'),
                'subtitle' => esc_html__('Choose a sidebar for left sidebar.', 'homeo'),
                'options' => $sidebars
            ),
            array(
                'id' => 'product_archive_right_sidebar',
                'type' => 'select',
                'title' => esc_html__('Archive Right Sidebar', 'homeo'),
                'subtitle' => esc_html__('Choose a sidebar for right sidebar.', 'homeo'),
                'options' => $sidebars
            ),
        )
    );
    
    
    // Product Page
    $sections[] = array(
        'title' => esc_html__('Single Product', 'homeo'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'product_general_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('General Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'product_thumbs_position',
                'type' => 'select',
                'title' => esc_html__('Thumbnails Position', 'homeo'),
                'options' => array(
                    'thumbnails-left' => esc_html__('Thumbnails Left', 'homeo'),
                    'thumbnails-right' => esc_html__('Thumbnails Right', 'homeo'),
                    'thumbnails-bottom' => esc_html__('Thumbnails Bottom', 'homeo'),
                ),
                'default' => 'thumbnails-bottom',
            ),
            array(
                'id' => 'number_product_thumbs',
                'title' => esc_html__('Number Thumbnails Per Row', 'homeo'),
                'default' => 5,
                'min' => '1',
                'step' => '1',
                'max' => '8',
                'type' => 'slider',
            ),
            array(
                'id' => 'show_product_social_share',
                'type' => 'switch',
                'title' => esc_html__('Show Social Share', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'show_product_review_tab',
                'type' => 'switch',
                'title' => esc_html__('Show Product Review Tab', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'product_sidebar_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('Sidebar Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'product_single_layout',
                'type' => 'image_select',
                'compiler' => true,
                'title' => esc_html__('Single Product Sidebar Layout', 'homeo'),
                'subtitle' => esc_html__('Select the layout you want to apply on your Single Product Page.', 'homeo'),
                'options' => array(
                    'main' => array(
                        'title' => esc_html__('Main Only', 'homeo'),
                        'alt' => esc_html__('Main Only', 'homeo'),
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
                'default' => 'main'
            ),
            array(
                'id' => 'product_single_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Is Full Width?', 'homeo'),
                'default' => false
            ),
            array(
                'id' => 'product_single_left_sidebar',
                'type' => 'select',
                'title' => esc_html__('Single Product Left Sidebar', 'homeo'),
                'subtitle' => esc_html__('Choose a sidebar for left sidebar.', 'homeo'),
                'options' => $sidebars
            ),
            array(
                'id' => 'product_single_right_sidebar',
                'type' => 'select',
                'title' => esc_html__('Single Product Right Sidebar', 'homeo'),
                'subtitle' => esc_html__('Choose a sidebar for right sidebar.', 'homeo'),
                'options' => $sidebars
            ),
            array(
                'id' => 'product_block_setting',
                'icon' => true,
                'type' => 'info',
                'raw' => '<h3 style="margin: 0;"> '.esc_html__('Product Block Setting', 'homeo').'</h3>',
            ),
            array(
                'id' => 'show_product_releated',
                'type' => 'switch',
                'title' => esc_html__('Show Products Releated', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'number_product_releated',
                'title' => esc_html__('Number of related products to show', 'homeo'),
                'default' => 3,
                'min' => '1',
                'step' => '1',
                'max' => '50',
                'type' => 'slider',
                'required' => array('show_product_releated', '=', true)
            ),
            array(
                'id' => 'releated_product_columns',
                'type' => 'select',
                'title' => esc_html__('Releated Products Columns', 'homeo'),
                'options' => $columns,
                'default' => 3,
                'required' => array('show_product_releated', '=', true)
            ),

            array(
                'id' => 'show_product_upsells',
                'type' => 'switch',
                'title' => esc_html__('Show Products upsells', 'homeo'),
                'default' => 1
            ),
            array(
                'id' => 'upsells_product_columns',
                'type' => 'select',
                'title' => esc_html__('Upsells Products Columns', 'homeo'),
                'options' => $columns,
                'default' => 3,
                'required' => array('show_product_upsells', '=', true)
            ),
        )
    );
    
    return $sections;
}
add_filter( 'homeo_redux_framwork_configs', 'homeo_woo_redux_config', 10, 3 );