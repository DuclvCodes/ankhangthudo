<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Properties_Tabs extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_properties_tabs';
    }

	public function get_title() {
        return esc_html__( 'Apus Properties Tabs', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Properties', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title', [
                'label' => esc_html__( 'Tab Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT
            ]
        );

        $repeater->add_control(
            'status_slugs',
            [
                'label' => esc_html__( 'Statuses Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'type_slugs',
            [
                'label' => esc_html__( 'Types Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'location_slugs',
            [
                'label' => esc_html__( 'Location Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'amenity_slugs',
            [
                'label' => esc_html__( 'Amenities Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'material_slugs',
            [
                'label' => esc_html__( 'Materials Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'orderby',
            [
                'label' => esc_html__( 'Order by', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'homeo'),
                    'date' => esc_html__('Date', 'homeo'),
                    'ID' => esc_html__('ID', 'homeo'),
                    'author' => esc_html__('Author', 'homeo'),
                    'title' => esc_html__('Title', 'homeo'),
                    'modified' => esc_html__('Modified', 'homeo'),
                    'rand' => esc_html__('Random', 'homeo'),
                    'comment_count' => esc_html__('Comment count', 'homeo'),
                    'menu_order' => esc_html__('Menu order', 'homeo'),
                ),
                'default' => ''
            ]
        );

        $repeater->add_control(
            'order',
            [
                'label' => esc_html__( 'Sort order', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'homeo'),
                    'ASC' => esc_html__('Ascending', 'homeo'),
                    'DESC' => esc_html__('Descending', 'homeo'),
                ),
                'default' => ''
            ]
        );

        $repeater->add_control(
            'get_properties_by',
            [
                'label' => esc_html__( 'Get Properties By', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'featured' => esc_html__('Featured Properties', 'homeo'),
                    'urgent' => esc_html__('Urgent Properties', 'homeo'),
                    'recent' => esc_html__('Recent Properties', 'homeo'),
                ),
                'default' => 'recent'
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'homeo' ),
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__( 'Tabs', 'homeo' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your property tabs here', 'homeo' ),
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => esc_html__( 'Limit', 'homeo' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Limit properties to display', 'homeo' ),
                'default' => 4
            ]
        );
        
        $this->add_control(
            'property_item_style',
            [
                'label' => esc_html__( 'Property Item Style', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid Default', 'homeo'),
                    'grid-v1' => esc_html__('Grid V1', 'homeo'),
                    'grid-v2' => esc_html__('Grid V2', 'homeo'),
                    'grid-v3' => esc_html__('Grid V3', 'homeo'),
                    'grid-v4' => esc_html__('Grid V4', 'homeo'),
                ),
                'default' => 'grid'
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'homeo'),
                    'carousel' => esc_html__('Carousel', 'homeo'),
                ),
                'default' => 'grid'
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'homeo' ),
                'options' => $columns,
                'condition' => [
                    'columns!' => '1',
                    'layout_type' => 'carousel',
                ],
                'frontend_available' => true,
                'default' => 3,
            ]
        );

        $this->add_control(
            'rows',
            [
                'label' => esc_html__( 'Rows', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'placeholder' => esc_html__( 'Enter your rows number here', 'homeo' ),
                'default' => 1,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label'         => esc_html__( 'Show Navigation', 'homeo' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'homeo' ),
                'label_off'     => esc_html__( 'Hide', 'homeo' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'         => esc_html__( 'Show Pagination', 'homeo' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'homeo' ),
                'label_off'     => esc_html__( 'Hide', 'homeo' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'         => esc_html__( 'Autoplay', 'homeo' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'homeo' ),
                'label_off'     => esc_html__( 'No', 'homeo' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label'         => esc_html__( 'Infinite Loop', 'homeo' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'homeo' ),
                'label_off'     => esc_html__( 'No', 'homeo' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'view_more_text',
            [
                'label' => esc_html__( 'View More Button Text', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your view more text here', 'homeo' ),
            ]
        );

        $this->add_control(
            'view_more_url',
            [
                'label' => esc_html__( 'View More URL', 'homeo' ),
                'type' => Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your view more url here', 'homeo' ),
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'homeo' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'homeo' ),
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $_id = homeo_random_key();
        ?>
        <div class="widget-properties-tabs <?php echo esc_attr($layout_type); ?> <?php echo esc_attr($el_class); ?>">

            <div class="top-info flex-middle-sm">
                <?php if ( $title ) { ?>
                    <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
                <?php } ?>
                <div class="ali-right">
                    <ul role="tablist" class="nav nav-tabs">
                        <?php $tab_count = 0; foreach ($tabs as $tab) : ?>
                            <li class="<?php echo esc_attr($tab_count == 0 ? 'active' : '');?>">
                                <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" data-toggle="tab">
                                    <?php if ( !empty($tab['title']) ) { ?>
                                        <?php echo trim($tab['title']); ?>
                                    <?php } ?>
                                </a>
                            </li>
                        <?php $tab_count++; endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <?php
                    $columns = !empty($columns) ? $columns : 3;
                    $columns_tablet = !empty($columns_tablet) ? $columns_tablet : $columns;
                    $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
                    
                    $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                    $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                    $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;

                    $tab_count = 0; foreach ($tabs as $tab) : ?>
                    <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" class="tab-pane <?php echo esc_attr($tab_count == 0 ? 'active' : ''); ?>">
                        <?php

                        $category_slugs = !empty($tab['category_slugs']) ? array_map('trim', explode(',', $tab['category_slugs'])) : array();
                        $type_slugs = !empty($tab['type_slugs']) ? array_map('trim', explode(',', $tab['type_slugs'])) : array();
                        $location_slugs = !empty($tab['location_slugs']) ? array_map('trim', explode(',', $tab['location_slugs'])) : array();

                        $args = array(
                            'limit' => $limit,
                            'get_properties_by' => !empty($tab['get_properties_by']) ? $tab['get_properties_by'] : 'recent',
                            'orderby' => !empty($tab['orderby']) ? $tab['orderby'] : '',
                            'order' => !empty($tab['order']) ? $tab['order'] : '',
                            'categories' => $category_slugs,
                            'types' => $type_slugs,
                            'locations' => $location_slugs,
                        );
                        $loop = homeo_get_properties($args);
                        if ( $loop->have_posts() ) {
                            ?>
                            <?php if ( $layout_type == 'carousel' ): ?>
                                <div class="slick-carousel"
                                    data-items="<?php echo esc_attr($columns); ?>"
                                    data-smallmedium="<?php echo esc_attr( $columns_tablet ); ?>"
                                    data-extrasmall="<?php echo esc_attr($columns_mobile); ?>"

                                    data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
                                    data-slidestoscroll_smallmedium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                                    data-slidestoscroll_extrasmall="<?php echo esc_attr($slides_to_scroll_mobile); ?>"

                                    data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>">
                                    <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                                        <div class="cl-inner">
                                            <?php echo WP_RealEstate_Template_Loader::get_template_part( 'template-properties/properties-styles/inner-'.$property_item_style ); ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <?php
                                    $mdcol = 12/$columns;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                ?>
                                <div class="row">
                                    <?php $i = 1; while ( $loop->have_posts() ) : $loop->the_post();
                                        $classes = '';
                                        if ( $i%$columns == 1 ) {
                                            $classes .= ' md-clearfix lg-clearfix';
                                        }
                                        if ( $i%$columns_tablet == 1 ) {
                                            $classes .= ' sm-clearfix';
                                        }
                                        if ( $i%$columns_mobile == 1 ) {
                                            $classes .= ' xs-clearfix';
                                        }
                                    ?>
                                        <div class="col-md-<?php echo esc_attr($mdcol); ?> col-sm-<?php echo esc_attr($smcol); ?> col-xs-<?php echo esc_attr( $xscol ); ?> <?php echo esc_attr($classes); ?>">
                                            <?php echo WP_RealEstate_Template_Loader::get_template_part( 'template-properties/properties-styles/inner-'.$property_item_style ); ?>
                                        </div>
                                    <?php $i++; endwhile; ?>
                                </div>
                            <?php endif; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php } ?>
                    </div>
                <?php $tab_count++; endforeach; ?>
            </div>
            <?php 
            if ( $view_more_text ) { ?>
                <div class="bottom-remore text-center">
                    <?php
                    $view_more_html = '<a class="btn btn-theme-second" href="'.esc_url($view_more_url['url']).'" target="'.esc_attr($view_more_url['is_external'] ? '_blank' : '_self').'" '.($view_more_url['nofollow'] ? 'rel="nofollow"' : '').'>' . $view_more_text . '</a>';
                    echo trim($view_more_html);
                    ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Properties_Tabs );