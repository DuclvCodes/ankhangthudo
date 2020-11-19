<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Testimonials extends Widget_Base {

    public function get_name() {
        return 'apus_element_testimonials';
    }

    public function get_title() {
        return esc_html__( 'Apus Testimonials', 'homeo' );
    }

    public function get_icon() {
        return 'eicon-testimonial';
    }

    public function get_categories() {
        return [ 'homeo-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'content', [
                'label' => esc_html__( 'Content', 'homeo' ),
                'type' => Controls_Manager::TEXTAREA
            ]
        );

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Choose Image', 'homeo' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Brand Image', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Name', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'property',
            [
                'label' => esc_html__( 'Property', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link To', 'homeo' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your social link here', 'homeo' ),
                'placeholder' => esc_html__( 'https://your-link.com', 'homeo' ),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__( 'Testimonials', 'homeo' ),
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
                'default' => '1'
            ]
        );
        $this->add_control(
            'show_iconquote',
            [
                'label' => esc_html__( 'Show Icon Quote', 'homeo' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'homeo' ),
                'label_off' => esc_html__( 'Show', 'homeo' ),
            ]
        );
        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'homeo' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'homeo' ),
                'label_off' => esc_html__( 'Show', 'homeo' ),
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'homeo' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'homeo' ),
                'label_off' => esc_html__( 'Show', 'homeo' ),
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'homeo'),
                    'st_white' => esc_html__('White', 'homeo'),
                    'v1' => esc_html__('Special 1', 'homeo'),
                    'v2' => esc_html__('Special 2', 'homeo'),
                ),
                'default' => ''
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
            'section_title_style',
            [
                'label' => esc_html__( 'Tyles', 'homeo' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .widget-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'homeo' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->add_control(
            'test_title_color',
            [
                'label' => esc_html__( 'Testimonial Title Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .name-client a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Testimonial Title Typography', 'homeo' ),
                'name' => 'test_title_typography',
                'selector' => '{{WRAPPER}} .name-client a',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Content Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Content Typography', 'homeo' ),
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );

        $this->add_control(
            'property_color',
            [
                'label' => esc_html__( 'Property Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .property' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Property Typography', 'homeo' ),
                'name' => 'property_typography',
                'selector' => '{{WRAPPER}} .property',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($testimonials) ) {
            ?>
            <div class="widget-testimonials <?php echo esc_attr($el_class.' '.$layout_type); ?> <?php echo esc_attr($show_iconquote ? 'showicon' : 'hiddenicon'); ?>">
                <?php
                if ( $layout_type == 'v1' ) {
                    ?>
                    <div class="wrapper-testimonial-thumbnail">
                        <div class="slick-carousel testimonial-thumbnail" data-centerMode="true" data-items="5" data-smallmedium="3" data-extrasmall="3" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                            <?php foreach ($testimonials as $item) { ?>
                                <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                <?php if ( $img_src ) { ?>
                                    <div class="wrapper-avarta">
                                        <div class="avarta flex-middle">
                                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="slick-carousel v1 testimonial-main" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                        <?php foreach ($testimonials as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="testimonials-item">
                                <div class="info-testimonials">
                                    <?php if ( !empty($item['name']) ) {

                                        $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                        if ( ! empty( $item['link']['url'] ) ) {
                                            $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                        }
                                        echo trim($title);
                                    ?>
                                    <?php } ?>
                                    <?php if ( !empty($item['property']) ) { ?>
                                        <div class="property"><?php echo esc_html($item['property']); ?></div>
                                    <?php } ?> 
                                </div>

                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>

                            </div>
                        <?php } ?>
                    </div>
                    <?php
                } elseif($layout_type == 'v2') { ?>
                    <div class="testimonials-inner">
                    <div class="slick-carousel v2 testimonial-main" data-items="<?php echo esc_attr($columns); ?>" data-smallmedium="1" data-extrasmall="1" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>">
                        <?php foreach ($testimonials as $item) { ?>
                        <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                        
                        <div class="testimonials-item v2">
                            <div class="inner">
                                <div class="top-icon">
                                    <span class="quote-testimonials text-theme">“</span>
                                </div>

                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>

                                <div class="bottom-info flex-middle">
                                    <?php if ( $img_src ) { ?>
                                        <div class="avarta">
                                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                        </div>
                                    <?php } ?>
                                    <div class="info-testimonials">
                                        <?php if ( !empty($item['name']) ) {

                                            $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                            }
                                            echo trim($title);
                                        ?>
                                        <?php } ?>
                                        <?php if ( !empty($item['property']) ) { ?>
                                            <div class="property"><?php echo esc_html($item['property']); ?></div>
                                        <?php } ?> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    </div>
                <?php } else {
                ?>
                    
                    <div class="slick-carousel testimonial-main" data-items="<?php echo esc_attr($columns); ?>" data-smallmedium="1" data-extrasmall="1" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>">
                        <?php foreach ($testimonials as $item) { ?>
                        
                        <div class="testimonials-item">
                            <div class="bottom-info">
                                <?php if ( isset( $item['img_src']['id'] ) ) { ?>
                                <div class="wrapper-avarta">
                                    <div class="avarta">
                                        <?php echo homeo_get_attachment_thumbnail($item['img_src']['id'], 'full'); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="info-testimonials">
                                    <?php if ( !empty($item['name']) ) {

                                        $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                        if ( ! empty( $item['link']['url'] ) ) {
                                            $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                        }
                                        echo trim($title);
                                    ?>
                                    <?php } ?>
                                    <?php if ( !empty($item['property']) ) { ?>
                                        <div class="property"><?php echo esc_html($item['property']); ?></div>
                                    <?php } ?> 
                                </div>
                            </div>
                            <?php if ( !empty($item['content']) ) { ?>
                                <div class="description"><?php echo trim($item['content']); ?></div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    
                <?php } ?>
            </div>
            <?php
        }
    }
}
Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Testimonials );