<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Type_Banner extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_type_banner';
    }

	public function get_title() {
        return esc_html__( 'Apus Type Properties Banner', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Type Banner', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slug',
            [
                'label' => esc_html__( 'Type Slug', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your Type Slug here', 'homeo' ),
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
            'selected_icon',
            [
                'label' => esc_html__( 'Icon', 'homeo' ),
                'type' => Elementor\Controls_Manager::ICON,
                'label_block' => true,
                'default' => 'fa fa-star',
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
            'custom_url',
            [
                'label' => esc_html__( 'Custom URL', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your custom url', 'homeo' ),
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

        // tab normal and hover

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_box_normal',
                [
                    'label' => esc_html__( 'Normal', 'homeo' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background',
                    'label' => esc_html__( 'Background', 'homeo' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .type-banner-property',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_box_hover',
                [
                    'label' => esc_html__( 'Hover', 'homeo' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_hover',
                    'label' => esc_html__( 'Background', 'homeo' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .type-banner-property:hover',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();


        $this->start_controls_section(
            'section_overlay',
            [
                'label' => esc_html__( 'Background Overlay', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'style' => 'style3',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_overlay_style' );

            $this->start_controls_tab(
                'tab_overlay_normal',
                [
                    'label' => esc_html__( 'Normal', 'homeo' ),
                ]
            );

                $this->add_group_control(
                    Elementor\Group_Control_Background::get_type(),
                    [
                        'name' => 'background_overlay',
                        'selector' => '{{WRAPPER}} .overlay',
                    ]
                );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_overlay_hover',
                [
                    'label' => esc_html__( 'Hover', 'homeo' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_overlay_hover',
                    'selector' => '{{WRAPPER}} .type-banner-property:hover .overlay',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab    

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
                    '{{WRAPPER}} .type-banner-property' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        <div class="widget-property-type-banner <?php echo esc_attr($el_class); ?>">
            <?php
            $term = get_term_by( 'slug', $slug, 'property_type' );
            if ($term) {
                $url = get_term_link( $term, 'property_type' );
                if ( !empty($custom_url) ) {
                    $url = $custom_url;
                }
            ?>
                <a class="type-banner-property <?php echo esc_attr($style); ?>" href="<?php echo esc_url($url); ?>">
                    <?php 
                        $has_background_overlay = in_array( $settings['background_overlay_background'], [ 'classic', 'gradient' ], true );
                    ?>

                    <?php if($has_background_overlay){ ?>
                        <div class="overlay"></div>
                    <?php } ?>

                    <?php if ( ! empty( $selected_icon ) ) : ?>
                        <span class="icon">
                            <i class="<?php echo esc_attr( $selected_icon ); ?>"></i>
                        </span>
                    <?php endif; ?>
                    <div class="info">
                        <?php if ( !empty($title) ) { ?>
                            <h4 class="title">
                                <?php echo trim($title); ?>
                            </h4>
                        <?php } ?>
                        <?php if ( $show_nb_properties ) {
                                $args = array(
                                    'fields' => 'ids',
                                    'types' => array($term->slug),
                                    'limit' => 1
                                );
                                $query = homeo_get_properties($args);
                                $count = $query->found_posts;
                                $number_properties = $count ? WP_RealEstate_Mixes::format_number($count) : 0;
                        ?>
                        <div class="number"><?php echo sprintf(_n('<span>%d</span> Property', '<span>%d</span> Properties', $count, 'homeo'), $number_properties); ?></div>
                        <?php } ?>
                    </div>
                </a>
            <?php } ?>
        </div>
        <?php
    }
}
Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Type_Banner );