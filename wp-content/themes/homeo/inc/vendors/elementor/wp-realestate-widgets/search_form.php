<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Search_Form extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_search_form';
    }

	public function get_title() {
        return esc_html__( 'Apus Properties Search Form', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Search Form', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
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

        $fields = apply_filters( 'wp-realestate-default-property-filter-fields', array() );
        $search_fields = array( '' => esc_html__('Choose a field', 'homeo') );
        foreach ($fields as $key => $field) {
            $name = $field['name'];
            if ( empty($field['name']) ) {
                $name = $key;
            }
            $search_fields[$key] = $name;
        }

        $repeater = new Elementor\Repeater();

        $repeater->add_control(
            'filter_field',
            [
                'label' => esc_html__( 'Filter field', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $search_fields
            ]
        );
        
        $repeater->add_control(
            'placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
            ]
        );

        $repeater->add_control(
            'enable_autocompleate_search',
            [
                'label' => esc_html__( 'Enable autocompleate search', 'homeo' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Yes', 'homeo' ),
                'label_off' => esc_html__( 'No', 'homeo' ),
                'condition' => [
                    'filter_field' => 'title',
                ],
            ]
        );

        $columns = array();
        for ($i=1; $i <= 12 ; $i++) { 
            $columns[$i] = sprintf(esc_html__('%d Columns', 'homeo'), $i);
        }
        $repeater->add_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'default' => 1
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'homeo' ),
                'type' => Elementor\Controls_Manager::ICON
            ]
        );

        $this->add_control(
            'main_search_fields',
            [
                'label' => esc_html__( 'Main Search Fields', 'homeo' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_control(
            'show_advance_search',
            [
                'label'         => esc_html__( 'Show Advanced Search', 'homeo' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'homeo' ),
                'label_off'     => esc_html__( 'Hide', 'homeo' ),
                'return_value'  => true,
                'default'       => true,
            ]
        );

        $this->add_control(
            'advance_search_fields',
            [
                'label' => esc_html__( 'Advanced Search Fields', 'homeo' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_control(
            'filter_btn_text',
            [
                'label' => esc_html__( 'Button Text', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Find Property',
            ]
        );

        $this->add_control(
            'btn_columns',
            [
                'label' => esc_html__( 'Button Columns', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'default' => 1
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout Type', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'horizontal' => esc_html__('Horizontal', 'homeo'),
                    'vertical' => esc_html__('Vertical', 'homeo'),
                ),
                'default' => 'horizontal'
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'homeo'),
                    'style1' => esc_html__('Style 1', 'homeo'),
                    'style2' => esc_html__('Style 2', 'homeo'),
                    'style3' => esc_html__('Style 3', 'homeo'),
                    'style4' => esc_html__('Style Icon', 'homeo'),
                ),
                'default' => ''
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



        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs( 'tabs_button_style' );

            $this->start_controls_tab(
                'tab_button_normal',
                [
                    'label' => esc_html__( 'Normal', 'homeo' ),
                ]
            );
            
            $this->add_control(
                'button_color',
                [
                    'label' => esc_html__( 'Button Color', 'homeo' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_button',
                    'label' => esc_html__( 'Background', 'homeo' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .btn-submit',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button',
                    'label' => esc_html__( 'Border', 'homeo' ),
                    'selector' => '{{WRAPPER}} .btn-submit',
                ]
            );

            $this->add_control(
                'padding_button',
                [
                    'label' => esc_html__( 'Padding', 'homeo' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'homeo' ),
                ]
            );

            $this->add_control(
                'button_hover_color',
                [
                    'label' => esc_html__( 'Button Color', 'homeo' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_button_hover',
                    'label' => esc_html__( 'Background', 'homeo' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus',
                ]
            );

            $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__( 'Border Color', 'homeo' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'border_button_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'padding_button_hover',
                [
                    'label' => esc_html__( 'Padding', 'homeo' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->end_controls_section();


        $this->start_controls_section(
            'section_border_style',
            [
                'label' => esc_html__( 'Border', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => esc_html__( 'Box Shadow', 'homeo' ),
                'selector' => '{{WRAPPER}} .content-main-inner',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_typography_style',
            [
                'label' => esc_html__( 'Typography', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Text Color', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-search' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .advance-search-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .circle-check' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control:-ms-input-placeholder ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::placeholder ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .select2-selection--single .select2-selection__placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        
        $search_page_url = WP_RealEstate_Mixes::get_properties_page_url();

        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        $filter_fields = apply_filters( 'wp-realestate-default-property-filter-fields', array() );
        $instance = array();
        $widget_id = homeo_random_key();
        $args = array( 'widget_id' => $widget_id );
        ?>
        <div class="widget-property-search-form <?php echo esc_attr($el_class.' '.$style.' '.$layout_type); ?>">
            
            <?php if ( $title ) { ?>
                <h2 class="title"><?php echo esc_html($title); ?></h2>
            <?php } ?>
            
            <form action="<?php echo esc_url($search_page_url); ?>" class="form-search filter-listing-form <?php echo esc_attr($style); ?>" method="GET">
                <div class="search-form-inner">
                    <?php if ( $layout_type == 'horizontal' ) { ?>
                        <div class="main-inner clearfix">
                            <div class="content-main-inner">
                                <div class="row row-20">
                                    <?php
                                    if ( !empty($main_search_fields) ) {
                                        foreach ($main_search_fields as $item) {
                                            if ( empty($filter_fields[$item['filter_field']]['field_call_back']) ) {
                                                continue;
                                            }
                                            $filter_field = $filter_fields[$item['filter_field']];
                                            if ( $item['filter_field'] == 'title' ) {
                                                if ($item['enable_autocompleate_search']) {
                                                    wp_enqueue_script( 'handlebars', get_template_directory_uri() . '/js/handlebars.min.js', array(), null, true);
                                                    wp_enqueue_script( 'typeahead-jquery', get_template_directory_uri() . '/js/typeahead.bundle.min.js', array('jquery', 'handlebars'), null, true);
                                                    $filter_field['add_class'] = 'apus-autocompleate-input';
                                                }
                                            }
                                            if ( isset($item['icon']) ) {
                                                $filter_field['icon'] = $item['icon'];
                                            }
                                            if ( isset($item['placeholder']) ) {
                                                $filter_field['placeholder'] = $item['placeholder'];
                                            }
                                            $filter_field['show_title'] = false;
                                            $columns = !empty($item['columns']) ? $item['columns'] : '1';
                                            ?>
                                            <div class="col-xs-12 col-md-<?php echo esc_attr($columns); ?>">
                                                <?php call_user_func( $filter_field['field_call_back'], $instance, $args, $item['filter_field'], $filter_field ); ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="col-xs-12 col-md-<?php echo esc_attr($btn_columns); ?> form-group form-group-search">
                                        <div class="flex-middle justify-content-end-lg">
                                            <?php if ( $show_advance_search && !empty($advance_search_fields) ) { ?>
                                                <div class="advance-link">
                                                    <a href="javascript:void(0);" class=" advance-search-btn"><?php esc_html_e('Advanced', 'homeo'); ?> <i class="flaticon-more"></i></a>
                                                </div>
                                            <?php } ?>
                                            
                                            <?php if($style == 'style4') {?>
                                                <button class="btn-submit btn" type="submit">
                                                    <i class="flaticon-magnifying-glass"></i>
                                                </button>
                                            <?php }else{ ?>
                                                <button class="btn-submit btn btn-theme btn-inverse" type="submit">
                                                    <?php echo trim($filter_btn_text); ?>
                                                </button>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ( $show_advance_search && !empty($advance_search_fields) ) {
                            ?>
                            <div class="advance-search-wrapper">
                                <div class="advance-search-wrapper-fields">
                                    <div class="row row-20">
                                        <?php
                                        $sub_class = '';
                                        foreach ($advance_search_fields as $item) {
                                            if ( empty($filter_fields[$item['filter_field']]['field_call_back']) ) {
                                                continue;
                                            }
                                            $filter_field = $filter_fields[$item['filter_field']];
                                            if ( isset($item['placeholder']) ) {
                                                $filter_field['placeholder'] = $item['placeholder'];
                                            }

                                            if ( isset($item['icon']) ) {
                                                $filter_field['icon'] = $item['icon'];
                                            }

                                            if($item['filter_field'] == 'amenity'){
                                                $sub_class = 'wrapper-amenity';
                                                $filter_field['show_title'] = true;
                                            }else{
                                                $filter_field['show_title'] = false;
                                                $sub_class = '';
                                            }

                                            $columns = !empty($item['columns']) ? $item['columns'] : '1';
                                            ?>
                                            <div class="col-xs-12 col-md-<?php echo esc_attr($columns.' '.$sub_class); ?>">
                                                <?php call_user_func( $filter_field['field_call_back'], $instance, $args, $item['filter_field'], $filter_field ); ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    <?php } else { ?>
                        <div class="main-inner clearfix">
                            <div class="content-main-inner">
                                <div class="row">
                                    <?php
                                    if ( !empty($main_search_fields) ) {
                                        foreach ($main_search_fields as $item) {
                                            if ( empty($filter_fields[$item['filter_field']]['field_call_back']) ) {
                                                continue;
                                            }
                                            $filter_field = $filter_fields[$item['filter_field']];
                                            if ( $item['filter_field'] == 'title' ) {
                                                if ($item['enable_autocompleate_search']) {
                                                    wp_enqueue_script( 'handlebars', get_template_directory_uri() . '/js/handlebars.min.js', array(), null, true);
                                                    wp_enqueue_script( 'typeahead-jquery', get_template_directory_uri() . '/js/typeahead.bundle.min.js', array('jquery', 'handlebars'), null, true);
                                                    $filter_field['add_class'] = 'apus-autocompleate-input';
                                                }
                                            }
                                            if ( isset($item['icon']) ) {
                                                $filter_field['icon'] = $item['icon'];
                                            }
                                            if ( isset($item['placeholder']) ) {
                                                $filter_field['placeholder'] = $item['placeholder'];
                                            }
                                            $filter_field['show_title'] = false;
                                            $columns = !empty($item['columns']) ? $item['columns'] : '1';
                                            ?>
                                            <div class="col-xs-12 col-md-<?php echo esc_attr($columns); ?>">
                                                <?php call_user_func( $filter_field['field_call_back'], $instance, $args, $item['filter_field'], $filter_field ); ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>

                                    <?php if( $show_advance_search && !empty($advance_search_fields)){ ?>
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <div class="advance-link">
                                                    <a href="javascript:void(0);" class=" advance-search-btn"><?php esc_html_e('Advanced', 'homeo'); ?> <i class="flaticon-more"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>

                                <?php
                                if ( $show_advance_search && !empty($advance_search_fields) ) {
                                    ?>
                                    <div class="advance-search-wrapper">
                                        <div class="advance-search-wrapper-fields">
                                            <div class="row">
                                                <?php
                                                foreach ($advance_search_fields as $item) {
                                                    if ( empty($filter_fields[$item['filter_field']]['field_call_back']) ) {
                                                        continue;
                                                    }
                                                    $filter_field = $filter_fields[$item['filter_field']];
                                                    if ( isset($item['placeholder']) ) {
                                                        $filter_field['placeholder'] = $item['placeholder'];
                                                    }
                                                    if ( isset($item['icon']) ) {
                                                        $filter_field['icon'] = $item['icon'];
                                                    }
                                                    $filter_field['show_title'] = false;
                                                    $columns = !empty($item['columns']) ? $item['columns'] : '1';
                                                    ?>
                                                    <div class="col-xs-12 col-md-<?php echo esc_attr($columns); ?>">
                                                        <?php call_user_func( $filter_field['field_call_back'], $instance, $args, $item['filter_field'], $filter_field ); ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                                <div class="row">
                                    <div class="col-xs-12 col-md-<?php echo esc_attr($btn_columns); ?> form-group-search">

                                        <?php if($style == 'style4') {?>
                                            <button class="btn-submit btn-block btn" type="submit">
                                                <i class="flaticon-magnifying-glass"></i>
                                            </button>
                                        <?php }else{ ?>
                                            <button class="btn-submit btn-block btn btn-theme btn-inverse" type="submit">
                                                <?php echo trim($filter_btn_text); ?>
                                            </button>
                                        <?php } ?>

                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        
                    <?php } ?>
                </div>
            </form>
        </div>
        <?php
    }
}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Search_Form );