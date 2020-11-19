<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Properties_Maps extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_properties_maps';
    }

	public function get_title() {
        return esc_html__( 'Apus Properties Maps', 'homeo' );
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
            'status_slugs',
            [
                'label' => esc_html__( 'Statuses Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $this->add_control(
            'type_slugs',
            [
                'label' => esc_html__( 'Types Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $this->add_control(
            'location_slugs',
            [
                'label' => esc_html__( 'Location Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $this->add_control(
            'amenity_slugs',
            [
                'label' => esc_html__( 'Amenities Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
            ]
        );

        $this->add_control(
            'material_slugs',
            [
                'label' => esc_html__( 'Materials Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
                'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'homeo' ),
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

        $this->add_control(
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

        $this->add_control(
            'get_properties_by',
            [
                'label' => esc_html__( 'Get Properties By', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'featured' => esc_html__('Featured Properties', 'homeo'),
                    'recent' => esc_html__('Recent Properties', 'homeo'),
                ),
                'default' => 'recent'
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
            'section_title_style',
            [
                'label' => esc_html__( 'Tyles', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'homeo' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1440,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #properties-google-maps' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        ?>
        <div class="widget-properties-maps <?php echo esc_attr($el_class); ?>">
            <?php if ( $title ) { ?>
                <h2 class="widget-title text-center"><?php echo esc_html($title); ?></h2>
            <?php } ?>
            <div class="widget-content">
                <div id="properties-google-maps" class="properties-google-maps" data-settings="<?php echo esc_attr(json_encode( $settings )); ?>"></div>
                <div class="hidden main-items-wrapper"></div>
            </div>
        </div>
        <?php
    }
}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Properties_Maps );