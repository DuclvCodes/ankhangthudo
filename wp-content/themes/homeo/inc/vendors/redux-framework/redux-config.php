<?php

if (!class_exists('Homeo_Redux_Framework_Config')) {

    class Homeo_Redux_Framework_Config
    {
        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;

        public function __construct()
        {
            if (!class_exists('ReduxFramework')) {
                return;
            }
            add_action('init', array($this, 'initSettings'), 10);
        }

        public function initSettings()
        {
            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        public function setSections()
        {
            global $wp_registered_sidebars;
            $sidebars = array();

            if ( is_admin() && !empty($wp_registered_sidebars) ) {
                foreach ($wp_registered_sidebars as $sidebar) {
                    $sidebars[$sidebar['id']] = $sidebar['name'];
                }
            }
            $columns = array( '1' => esc_html__('1 Column', 'homeo'),
                '2' => esc_html__('2 Columns', 'homeo'),
                '3' => esc_html__('3 Columns', 'homeo'),
                '4' => esc_html__('4 Columns', 'homeo'),
                '5' => esc_html__('5 Columns', 'homeo'),
                '6' => esc_html__('6 Columns', 'homeo'),
                '7' => esc_html__('7 Columns', 'homeo'),
                '8' => esc_html__('8 Columns', 'homeo'),
            );
            
            $general_fields = array();
            $general_fields[] = array(
                'id' => 'preload',
                'type' => 'switch',
                'title' => esc_html__('Preload Website', 'homeo'),
                'default' => true,
            );
            $general_fields[] = array(
                'id' => 'media-preload-icon',
                'type' => 'media',
                'title' => esc_html__('Preload Icon', 'homeo'),
                'subtitle' => esc_html__('Upload a .png or .gif image that will be your preload icon.', 'homeo'),
                'required' => array('preload', '=', true)
            );
            $general_fields[] = array(
                'id' => 'image_lazy_loading',
                'type' => 'switch',
                'title' => esc_html__('Image Lazy Loading', 'homeo'),
                'default' => true,
            );
            // General Settings Tab
            $this->sections[] = array(
                'icon' => 'el-icon-cogs',
                'title' => esc_html__('General', 'homeo'),
                'fields' => $general_fields
            );
            // Header
            $this->sections[] = array(
                'icon' => 'el el-website',
                'title' => esc_html__('Header', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'header_settings',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3> '.esc_html__('Header Settings', 'homeo').'</h3>',
                    ),
                    array(
                        'id' => 'header_type',
                        'type' => 'select',
                        'title' => esc_html__('Header Layout Type', 'homeo'),
                        'subtitle' => esc_html__('Choose a header for your website.', 'homeo'),
                        'options' => homeo_get_header_layouts(),
                        'desc' => sprintf(wp_kses(__('You can add or edit a header in <a href="%s" target="_blank">Headers Builder</a>', 'homeo'), array('a' => array('href' => array())) ), admin_url( 'edit.php?post_type=apus_megamenu') ),
                    ),
                    array(
                        'id' => 'keep_header',
                        'type' => 'switch',
                        'title' => esc_html__('Sticky Header', 'homeo'),
                        'default' => false
                    ),
                    array(
                        'id' => 'header_mobile_settings',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3> '.esc_html__('Header Mobile Settings', 'homeo').'</h3>',
                    ),
                    array(
                        'id' => 'media-mobile-logo',
                        'type' => 'media',
                        'title' => esc_html__('Mobile Logo Upload', 'homeo'),
                        'subtitle' => esc_html__('Upload a .png or .gif image that will be your logo.', 'homeo'),
                    ),
                    array(
                        'id' => 'header_mobile_menu',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Header Mobile Menu', 'homeo'),
                        'default' => true
                    ),
                    array(
                        'id' => 'header_mobile_login',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Header Mobile Login', 'homeo'),
                        'default' => true
                    ),
                    array(
                        'id' => 'header_mobile_add_property_btn',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Header Mobile "Add Property" button', 'homeo'),
                        'default' => true
                    ),
                )
            );
            // Footer
            $this->sections[] = array(
                'icon' => 'el el-website',
                'title' => esc_html__('Footer', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'footer_type',
                        'type' => 'select',
                        'title' => esc_html__('Footer Layout Type', 'homeo'),
                        'subtitle' => esc_html__('Choose a footer for your website.', 'homeo'),
                        'options' => homeo_get_footer_layouts()
                    ),
                    array(
                        'id' => 'back_to_top',
                        'type' => 'switch',
                        'title' => esc_html__('Back To Top Button', 'homeo'),
                        'subtitle' => esc_html__('Toggle whether or not to enable a back to top button on your pages.', 'homeo'),
                        'default' => true,
                    ),
                )
            );

            // Blog settings
            $this->sections[] = array(
                'icon' => 'el el-pencil',
                'title' => esc_html__('Blog', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'show_blog_breadcrumbs',
                        'type' => 'switch',
                        'title' => esc_html__('Breadcrumbs', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'title' => esc_html__('Breadcrumbs Background Color', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('The breadcrumbs background color of the site.', 'homeo').'</em>',
                        'id' => 'blog_breadcrumb_color',
                        'type' => 'color',
                        'transparent' => false,
                    ),
                    array(
                        'id' => 'blog_breadcrumb_image',
                        'type' => 'media',
                        'title' => esc_html__('Breadcrumbs Background', 'homeo'),
                        'subtitle' => esc_html__('Upload a .jpg or .png image that will be your breadcrumbs.', 'homeo'),
                    ),
                    array(
                        'id' => 'blog_breadcrumb_style',
                        'type' => 'select',
                        'title' => esc_html__('Breadcrumbs Style', 'homeo'),
                        'subtitle' => esc_html__('Choose a style for breadcrumbs.', 'homeo'),
                        'options' => array(
                            'horizontal' => esc_html__('Horizontal', 'homeo'),
                            'vertical' => esc_html__('Vertical', 'homeo'),
                        ),
                        'default' => 'horizontal'
                    ),
                )
            );
            // Archive Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Blog & Post Archives', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'blog_archive_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Layout', 'homeo'),
                        'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'homeo'),
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
                        'default' => 'left-main'
                    ),
                    array(
                        'id' => 'blog_archive_fullwidth',
                        'type' => 'switch',
                        'title' => esc_html__('Is Full Width?', 'homeo'),
                        'default' => false
                    ),
                    array(
                        'id' => 'blog_archive_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Archive Left Sidebar', 'homeo'),
                        'subtitle' => esc_html__('Choose a sidebar for left sidebar.', 'homeo'),
                        'options' => $sidebars
                    ),
                    array(
                        'id' => 'blog_archive_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Archive Right Sidebar', 'homeo'),
                        'subtitle' => esc_html__('Choose a sidebar for right sidebar.', 'homeo'),
                        'options' => $sidebars
                        
                    ),
                    array(
                        'id' => 'blog_display_mode',
                        'type' => 'select',
                        'title' => esc_html__('Display Mode', 'homeo'),
                        'options' => array(
                            'grid' => esc_html__('Grid 1', 'homeo'),
                            'grid-v2' => esc_html__('Grid 2', 'homeo'),
                            'list' => esc_html__('List 1', 'homeo'),
                        ),
                        'default' => 'list'
                    ),
                    array(
                        'id' => 'blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Blog Columns', 'homeo'),
                        'options' => $columns,
                        'default' => 1
                    ),
                    array(
                        'id' => 'blog_item_thumbsize',
                        'type' => 'text',
                        'title' => esc_html__('Thumbnail Size', 'homeo'),
                        'subtitle' => esc_html__('This featured for the site is using Visual Composer.', 'homeo'),
                        'desc' => esc_html__('Enter thumbnail size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height) .', 'homeo'),
                    ),

                )
            );
            // Single Blogs settings
            $this->sections[] = array(
                'subsection' => true,
                'title' => esc_html__('Blog', 'homeo'),
                'fields' => array(
                    
                    array(
                        'id' => 'blog_single_layout',
                        'type' => 'image_select',
                        'compiler' => true,
                        'title' => esc_html__('Archive Blog Layout', 'homeo'),
                        'subtitle' => esc_html__('Select the variation you want to apply on your store.', 'homeo'),
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
                        'default' => 'left-main'
                    ),
                    array(
                        'id' => 'blog_single_fullwidth',
                        'type' => 'switch',
                        'title' => esc_html__('Is Full Width?', 'homeo'),
                        'default' => false
                    ),
                    array(
                        'id' => 'blog_single_left_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Single Blog Left Sidebar', 'homeo'),
                        'subtitle' => esc_html__('Choose a sidebar for left sidebar.', 'homeo'),
                        'options' => $sidebars
                    ),
                    array(
                        'id' => 'blog_single_right_sidebar',
                        'type' => 'select',
                        'title' => esc_html__('Single Blog Right Sidebar', 'homeo'),
                        'subtitle' => esc_html__('Choose a sidebar for right sidebar.', 'homeo'),
                        'options' => $sidebars
                    ),
                    array(
                        'id' => 'show_blog_social_share',
                        'type' => 'switch',
                        'title' => esc_html__('Show Social Share', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'show_blog_releated',
                        'type' => 'switch',
                        'title' => esc_html__('Show Releated Posts', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'number_blog_releated',
                        'type' => 'text',
                        'title' => esc_html__('Number of related posts to show', 'homeo'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'default' => 2,
                        'min' => '1',
                        'step' => '1',
                        'max' => '20',
                        'type' => 'slider'
                    ),
                    array(
                        'id' => 'releated_blog_columns',
                        'type' => 'select',
                        'title' => esc_html__('Releated Blogs Columns', 'homeo'),
                        'required' => array('show_blog_releated', '=', '1'),
                        'options' => $columns,
                        'default' => 2
                    ),

                )
            );
            
            $this->sections = apply_filters( 'homeo_redux_framwork_configs', $this->sections, $sidebars, $columns );
            
            // 404 page
            $this->sections[] = array(
                'title' => esc_html__('404 Page', 'homeo'),
                'fields' => array(
                    array(
                        'title' => esc_html__('Images Icon', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('Icon for 404 error.', 'homeo').'</em>',
                        'id' => 'icon-img',
                        'type' => 'media',
                    ),
                    array(
                        'title' => esc_html__('Images Background', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('Background for 404 error.', 'homeo').'</em>',
                        'id' => 'bg-img',
                        'type' => 'media',
                    ),
                    array(
                        'id' => '404_title',
                        'type' => 'text',
                        'title' => esc_html__('Title', 'homeo'),
                        'default' => '404'
                    ),
                    array(
                        'id' => '404_description',
                        'type' => 'editor',
                        'title' => esc_html__('Description', 'homeo'),
                        'default' => 'Sorry but the page you are looking for does not exist, have been removed, name changed or is temporarity unavailable.'
                    )
                )
            );
            
            // Style
            $this->sections[] = array(
                'icon' => 'el el-icon-css',
                'title' => esc_html__('Custom Style', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'custom_color',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3> '.esc_html__('Custom Color', 'homeo').'</h3>',
                    ),
                    array(
                        'title' => esc_html__('Main Theme Color', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('The main color of the site.', 'homeo').'</em>',
                        'id' => 'main_color',
                        'type' => 'color',
                        'transparent' => false,
                    ),
                    array(
                        'title' => esc_html__('Button Background Color', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('The main color of the site.', 'homeo').'</em>',
                        'id' => 'button_color',
                        'type' => 'color',
                        'transparent' => false,
                    ),
                    array(
                        'title' => esc_html__('Button Background Hover Color', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('The main color of the site.', 'homeo').'</em>',
                        'id' => 'button_hover_color',
                        'type' => 'color',
                        'transparent' => false,
                    ),

                    array(
                        'title' => esc_html__('Header Mobile Color', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('The header mobile color of the site.', 'homeo').'</em>',
                        'id' => 'header_mobile_color',
                        'type' => 'color',
                        'transparent' => false,
                    ),

                    // Typography
                    array(
                        'id' => 'main_font_info',
                        'icon' => true,
                        'type' => 'info',
                        'raw' => '<h3> '.esc_html__('Custom Typography', 'homeo').'</h3>',
                    ),
                    array (
                        'title' => esc_html__('Main Font Face', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('Pick the Main Font for your site.', 'homeo').'</em>',
                        'id' => 'main_font',
                        'type' => 'typography',
                        'line-height' => false,
                        'text-align' => false,
                        'font-style' => false,
                        'font-weight' => false,
                        'all_styles'=> true,
                        'font-size' => true,
                        'color' => false,
                        'default' => array (
                            'font-family' => '',
                            'subsets' => '',
                        )
                    ),
                    array(
                        'title' => esc_html__('Heading Font Face', 'homeo'),
                        'subtitle' => '<em>'.esc_html__('Pick the Heading Font for your site.', 'homeo').'</em>',
                        'id' => 'heading_font',
                        'type' => 'typography',
                        'line-height' => false,
                        'text-align' => false,
                        'font-style' => false,
                        'font-weight' => true,
                        'all_styles'=> true,
                        'font-size' => false,
                        'color' => false,
                        'default' => array (
                            'font-family' => '',
                            'subsets' => '',
                        )
                    ),
                )
            );
            
            
            // Social Media
            $this->sections[] = array(
                'icon' => 'el el-file',
                'title' => esc_html__('Social Media', 'homeo'),
                'fields' => array(
                    array(
                        'id' => 'facebook_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Facebook Share', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'twitter_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable twitter Share', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'linkedin_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable linkedin Share', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'pinterest_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable pinterest Share', 'homeo'),
                        'default' => 1
                    ),
                    array(
                        'id' => 'more_share',
                        'type' => 'switch',
                        'title' => esc_html__('Enable More Share', 'homeo'),
                        'default' => 1
                    ),
                )
            );
            // Custom Code
            
            $this->sections[] = array(
                'title' => esc_html__('Import / Export', 'homeo'),
                'desc' => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'homeo'),
                'icon' => 'el-icon-refresh',
                'fields' => array(
                    array(
                        'id' => 'opt-import-export',
                        'type' => 'import_export',
                        'title' => 'Import Export',
                        'subtitle' => 'Save and restore your Redux options',
                        'full_width' => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'type' => 'divide',
            );


        }
        /**
         * All the possible arguments for Redux.
         * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
         * */
        public function setArguments()
        {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.
            
            $preset = homeo_get_demo_preset();
            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name' => 'homeo_theme_options'.$preset,
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name' => $theme->get('Name'),
                // Name that appears at the top of your panel
                'display_version' => $theme->get('Version'),
                // Version that appears at the top of your panel
                'menu_type' => 'menu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu' => true,
                // Show the sections below the admin menu item or not
                'menu_title' => esc_html__('Theme Options', 'homeo'),
                'page_title' => esc_html__('Theme Options', 'homeo'),

                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography' => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar' => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon' => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable' => 'homeo_options',
                // Set a different name for your global variable other than the opt_name
                'dev_mode' => false,
                // Show the time the page took to load, etc
                'update_notice' => false,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer' => true,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority' => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent' => 'themes.php',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions' => 'manage_options',
                // Permissions needed to access the options panel.
                'menu_icon' => '',
                // Specify a custom URL to an icon
                'last_tab' => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon' => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug' => '_options',
                // Page slug used to denote the panel
                'save_defaults' => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show' => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark' => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time' => 60 * MINUTE_IN_SECONDS,
                'output' => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag' => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database' => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info' => false,
                // REMOVE
                'use_cdn' => true
            );
            return $this->args;
        }

    }

    global $reduxConfig;
    $reduxConfig = new Homeo_Redux_Framework_Config();
}

if ( function_exists('apus_framework_redux_register_custom_extension_loader') ) {
    $preset = homeo_get_demo_preset();
    $opt_name = 'homeo_theme_options'.$preset;
    add_action("redux/extensions/{$opt_name}/before", 'apus_framework_redux_register_custom_extension_loader', 0);
}


function homeo_redux_remove_notice() {
    return 'bub';
}
$preset = homeo_get_demo_preset();
$opt_name = 'homeo_theme_options'.$preset;
add_action('redux/' . $opt_name . '/aNFM_filter', 'homeo_redux_remove_notice');