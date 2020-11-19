<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Location_Banner extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_location_banner';
    }

	public function get_title() {
        return esc_html__( 'Apus Location Banner', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Location Banner', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slug',
            [
                'label' => esc_html__( 'Location Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your Location Slug here', 'homeo' ),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your title here', 'homeo' ),
            ]
        );

        $this->add_control(
            'show_nb_properties',
            [
                'label' => esc_html__( 'Show Number Properties', 'homeo' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'homeo' ),
                'label_off' => esc_html__( 'Show', 'homeo' ),
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'homeo'),
                    'style2' => esc_html__('Style 2', 'homeo'),
                    'style3' => esc_html__('Style 3', 'homeo'),
                    'style4' => esc_html__('Style 4', 'homeo'),
                    'style5' => esc_html__('Style 5', 'homeo'),
                    'style6' => esc_html__('Style 6', 'homeo'),
                ),
                'default' => 'style1'
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
            'section_box_style',
            [
                'label' => esc_html__( 'Style Box', 'homeo' ),
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
                    '{{WRAPPER}} .location-banner-inner' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'style' => ['style1', 'style3', 'style4'],
                ],
            ]
        );

        // for style 5
        $this->add_responsive_control(
            'height_image_top',
            [
                'label' => esc_html__( 'Height Image Top', 'homeo' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1440,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .location-banner-inner-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'style' => ['style2', 'style5', 'style6'],
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => esc_html__( 'Background', 'homeo' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .location-banner-inner-item',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_overlay',
            [
                'label' => esc_html__( 'Background Overlay', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'background_overlay',
                'selector' => '{{WRAPPER}} .inner',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_border',
            [
                'label' => esc_html__( 'Border Radius', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'homeo' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .location-banner-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Typography', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'homeo' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_control(
            'number_color',
            [
                'label' => esc_html__( 'Number Color', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Number Typography', 'homeo' ),
                'name' => 'number_typography',
                'selector' => '{{WRAPPER}} .number',
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( empty($slug) ) {
            return;
        }
        ?>
        <div class="widget-property-location-banner <?php echo esc_attr($el_class); ?>">
            <?php
            $term = get_term_by( 'slug', $slug, 'property_location' );
            if ($term) {
            ?>
                <a class="location-banner-inner <?php echo esc_attr($style); ?>" href="<?php echo esc_url(get_term_link( $term, 'property_location' )); ?>">
                    <?php 
                        $has_background = in_array( $settings['background_background'], [ 'classic', 'gradient' ], true );
                    ?>

                    <?php if($has_background){ ?>
                        <div class="wrapper-top">
                            <div class="location-banner-inner-item"></div>
                        </div>
                    <?php } ?>

                    <div class="inner <?php echo esc_attr( (!$has_background)?'no-image':'' ); ?>">
                        <div class="info-city">
                            <?php if ( !empty($title) ) { ?>
                                <h4 class="title">
                                    <?php echo trim($title); ?>
                                </h4>
                            <?php } ?>
                            <?php if ( $show_nb_properties ) {
                                    $args = array(
                                        'fields' => 'ids',
                                        'locations' => array($term->slug),
                                        'limit' => 1
                                    );
                                    $query = homeo_get_properties($args);
                                    $count = $query->found_posts;
                                    $number_properties = $count ? WP_RealEstate_Mixes::format_number($count) : 0;
                            ?>
                            <div class="number"><?php echo sprintf(_n('<span>%d</span> Property', '<span>%d</span> Properties', $count, 'homeo'), $number_properties); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
        <?php
    }
}
Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Location_Banner );