<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Features_Box extends Widget_Base {

	public function get_name() {
        return 'apus_element_features_box';
    }

	public function get_title() {
        return esc_html__( 'Apus Features Box', 'homeo' );
    }

	public function get_icon() {
        return 'eicon-image-box';
    }

	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Features Box', 'homeo' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image_icon',
            [
                'label' => esc_html__( 'Image or Icon', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'icon' => esc_html__('Icon', 'homeo'),
                    'image' => esc_html__('Image', 'homeo'),
                ),
                'default' => 'image'
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'homeo' ),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-star',
                'condition' => [
                    'image_icon' => 'icon',
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Choose Image', 'homeo' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );
        $repeater->add_control(
            'title_text',
            [
                'label' => esc_html__( 'Title & Description', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'This is the heading', 'homeo' ),
                'placeholder' => esc_html__( 'Enter your title', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'description_text',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Description', 'homeo' ),
                'placeholder' => esc_html__( 'Enter your description', 'homeo' ),
                'separator' => 'none',
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link to', 'homeo' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'homeo' ),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'features',
            [
                'label' => esc_html__( 'Features Box', 'homeo' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '3'
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'homeo'),
                    'style2' => esc_html__('Style 2', 'homeo'),
                    'style3' => esc_html__('Style 3', 'homeo'),
                ),
                'default' => 'style1'
            ]
        );
        $this->add_control(
            'layout',
            [
                'label' => esc_html__( 'Layout', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'carousel' => esc_html__('Carousel', 'homeo'),
                    'grid' => esc_html__('Grid', 'homeo'),
                ),
                'default' => 'carousel',
            ]
        );
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'homeo' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'homeo' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'homeo' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'homeo' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Justified', 'homeo' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-inner' => 'text-align: {{VALUE}};',
                ],
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'homeo' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'homeo' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__( 'Box Style', 'homeo' ),
                'tab' => Controls_Manager::TAB_STYLE,
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

            $this->add_control(
                'color',
                [
                    'label' => esc_html__( 'Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color',
                    'selector' => '{{WRAPPER}} .item-inner',
                ]
            );

            $this->add_control(
                'box_br',
                [
                    'label' => esc_html__( 'Border Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner' => 'border-color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'boxshadow_color',
                [
                    'label' => esc_html__( 'Box Shadow', 'homeo' ),
                    'type' => Controls_Manager::BOX_SHADOW,
                    'selectors' => [
                        '{{SELECTOR}} .item-inner' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
                    ],
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
            $this->add_control(
                'color_hover',
                [
                    'label' => esc_html__( 'Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color_hover',
                    'selector' => '{{WRAPPER}} .item-inner:hover',
                ]
            );
            $this->add_control(
                'box_br_hv',
                [
                    'label' => esc_html__( 'Border Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'boxshadow_hv_color',
                [
                    'label' => esc_html__( 'Box Shadow', 'homeo' ),
                    'type' => Controls_Manager::BOX_SHADOW,
                    'selectors' => [
                        '{{SELECTOR}} .item-inner:hover' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'bg_line_hover',
                [
                    'label' => esc_html__( 'Color Line Under', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner:before' => 'background-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'style' => 'style2',
                    ],
                ]
            );
            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();


        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__( 'Heading Style', 'homeo' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__( 'Color Hover', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .item-inner:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Heading Typography', 'homeo' ),
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon Style', 'homeo' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_width',
            [
                'label' => esc_html__( 'Icon Width', 'homeo' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .features-box-image' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_height',
            [
                'label' => esc_html__( 'Icon Height', 'homeo' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .features-box-image' => 'height: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        // tab for icon
        $this->start_controls_tabs( 'tabs_icon_style' );

            $this->start_controls_tab(
                'tab_icon_normal',
                [
                    'label' => esc_html__( 'Normal', 'homeo' ),
                ]
            );
            $this->add_control(
                'icon_color',
                [
                    'label' => esc_html__( 'Icon Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_color',
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'icon_box_shadow',
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'homeo' ),
                ]
            );

            $this->add_control(
                'icon_hover_color',
                [
                    'label' => esc_html__( 'Icon Hover Color', 'homeo' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-inner:hover .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_hover_color',
                    'selector' => '{{WRAPPER}}  .item-inner:hover .features-box-image',
                ]
            );
            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'icon_hover_box_shadow',
                    'selector' => '{{WRAPPER}} .item-inner:hover .features-box-image',
                ]
            );
            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Icon Typography', 'homeo' ),
                'name' => 'icon_typography',
                'selector' => '{{WRAPPER}} .features-box-image',
            ]
        );
        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($features) ) {
            ?>
            <div class="widget-features-box <?php echo esc_attr($el_class.' '.$alignment.' '.$style); ?>">
                <?php if($layout == 'carousel') {?>
                    <div class="slick-carousel <?php echo esc_attr( (count($features) <= $columns )?'hidden-dots':'' ); ?>" data-items="<?php echo esc_attr($columns); ?>" data-smallmedium="<?php echo esc_attr(($columns > 1)?2:1); ?>" data-extrasmall="1" data-pagination="true" data-nav="false">
                        <?php foreach ($features as $item): ?>
                            <div class="item">
                                <div class="item-inner">
                                    <div class="top-inner">
                                        <?php if(!empty($item['number'])) {?>
                                            <div class="number">
                                                <?php echo (int)$item['number']; ?>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        $has_content = ! empty( $item['title_text'] ) || ! empty( $item['description_text'] );
                                        $html = '';

                                        if ( $item['image_icon'] == 'image' ) {
                                            if ( ! empty( $item['image']['url'] ) ) {
                                                $this->add_render_attribute( 'image', 'src', $item['image']['url'] );
                                                $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $item['image'] ) );
                                                $this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $item['image'] ) );


                                                $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $image_html = '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>' . $image_html . '</a>';
                                                }

                                                $html .= '<div class="features-box-image img">' . $image_html . '</div>';
                                            }
                                        } elseif ( $item['image_icon'] == 'icon' && !empty($item['icon'])) {
                                            $html .= '<div class="features-box-image icon"><i class="'.$item['icon'].'"></i></div>';
                                        }
                                    $html .= '</div>';
                                    if ( $has_content ) {
                                        $html .= '<div class="features-box-content">';

                                        if ( ! empty( $item['title_text'] ) ) {
                                            
                                            $title_html = $item['title_text'];

                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $html .= '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'><h3 class="title">'.$title_html.'</h3></a>';
                                            } else {
                                                $html .= sprintf( '<h3 class="title">%1$s</h3>', $title_html );
                                            }
                                        }

                                        if ( ! empty( $item['description_text'] ) ) {
                                            $html .= sprintf( '<div class="description">%1$s</div>', $item['description_text'] );
                                        }

                                        $html .= '</div>';
                                    }

                                    echo trim($html);
                                    ?>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php }elseif($layout == 'grid'){  $item['number'] =1;?>  
                    <div class="row">
                        <?php foreach ($features as $item): ?>
                            <div class="item col-xs-12 col-sm-<?php echo esc_attr( ($columns > 1)?6:12 ); ?> col-md-<?php echo esc_attr(12/$columns);?>">
                                <div class="item-inner">
                                     <div class="top-inner">
                                        <?php if(!empty($item['number'])) {?>
                                            <div class="number">
                                                <?php echo (int)$item['number']; ?>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        $has_content = ! empty( $item['title_text'] ) || ! empty( $item['description_text'] );
                                        $html = '';

                                        if ( $item['image_icon'] == 'image' ) {
                                            if ( ! empty( $item['image']['url'] ) ) {
                                                $this->add_render_attribute( 'image', 'src', $item['image']['url'] );
                                                $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $item['image'] ) );
                                                $this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $item['image'] ) );


                                                $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $image_html = '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>' . $image_html . '</a>';
                                                }

                                                $html .= '<div class="features-box-image img">' . $image_html . '</div>';
                                            }
                                        } elseif ( $item['image_icon'] == 'icon' && !empty($item['icon'])) {
                                            $html .= '<div class="features-box-image icon"><i class="'.$item['icon'].'"></i></div>';
                                        }
                                    $html .= '</div>';
                                    if ( $has_content ) {
                                        $html .= '<div class="features-box-content">';

                                        if ( ! empty( $item['title_text'] ) ) {
                                            
                                            $title_html = $item['title_text'];

                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $html .= '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'><h3 class="title">'.$title_html.'</h3></a>';
                                            } else {
                                                $html .= sprintf( '<h3 class="title">%1$s</h3>', $title_html );
                                            }
                                        }

                                        if ( ! empty( $item['description_text'] ) ) {
                                            $html .= sprintf( '<div class="description">%1$s</div>', $item['description_text'] );
                                        }

                                        $html .= '</div>';
                                    }

                                    echo trim($html);
                                    ?>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }
}

Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Features_Box );