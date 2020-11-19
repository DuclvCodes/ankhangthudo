<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Banner extends Widget_Base {

	public function get_name() {
        return 'apus_element_banner';
    }

	public function get_title() {
        return esc_html__( 'Apus Banner', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Banner', 'homeo' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'img_bg_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image Background', 'homeo' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Background Here', 'homeo' ),
            ]
        );
        $this->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'homeo' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'homeo' ),
            ]
        );

        $this->add_responsive_control(
            'img_align',
            [
                'label' => esc_html__( 'Image Alignment', 'homeo' ),
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
                    '{{WRAPPER}} .banner-image' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__( 'URL', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your Button Link here', 'homeo' ),
            ]
        );

        $this->add_control(
            'content',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'type' => Controls_Manager::WYSIWYG,
                'placeholder' => esc_html__( 'Enter your content here', 'homeo' ),
            ]
        );

        $this->add_control(
            'btn_text',
            [
                'label' => esc_html__( 'Button Text', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your button text here', 'homeo' ),
            ]
        );

        $this->add_control(
            'btn_style',
            [
                'label' => esc_html__( 'Button Style', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'btn-theme' => esc_html__('Theme Color', 'homeo'),
                    'btn-theme btn-outline' => esc_html__('Theme Outline Color', 'homeo'),
                    'btn-default' => esc_html__('Default ', 'homeo'),
                    'btn-primary' => esc_html__('Primary ', 'homeo'),
                    'btn-success' => esc_html__('Success ', 'homeo'),
                    'btn-info' => esc_html__('Info ', 'homeo'),
                    'btn-warning' => esc_html__('Warning ', 'homeo'),
                    'btn-danger' => esc_html__('Danger ', 'homeo'),
                    'btn-pink' => esc_html__('Pink ', 'homeo'),
                    'btn-white' => esc_html__('White ', 'homeo'),
                ),
                'default' => 'btn-default'
            ]
        );

        $this->add_responsive_control(
            'content_align',
            [
                'label' => esc_html__( 'Content Alignment', 'homeo' ),
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
                    '{{WRAPPER}} .banner-content' => 'text-align: {{VALUE}};',
                ],
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
                    'style4' => esc_html__('Style 4', 'homeo'),
                ),
                'default' => 'style1'
            ]
        );
        $this->add_control(
            'vertical',
            [
                'label' => esc_html__( 'Vertical Content', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'flex-top' => esc_html__('Top', 'homeo'),
                    'flex-middle' => esc_html__('Middle', 'homeo'),
                    'flex-bottom' => esc_html__('Bottom', 'homeo'),
                ),
                'default' => 'flex-middle'
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

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        $img_bg_src = ( isset( $img_bg_src['id'] ) && $img_bg_src['id'] != 0 ) ? wp_get_attachment_url( $img_bg_src['id'] ) : '';
        $style_bg = '';
        if ( !empty($img_bg_src) ) {
            $style_bg = 'style="background-image:url('.esc_url($img_bg_src).')"';
        }
        ?>
        <div class="widget-banner updow <?php echo esc_attr($el_class.' '.$style); ?>" <?php echo trim($style_bg); ?>>
            <?php if ( !empty($link) ) { ?>
                <a href="<?php echo esc_url($link); ?>">
            <?php } ?>
                <div class="inner <?php echo esc_attr($vertical); ?>">
                    <?php
                    if ( !empty($img_src['id']) ) {
                    ?>
                        <div class="p-static col-xs-<?php echo esc_attr(!empty($content) ? '6':'12' ); ?>">
                            <div class="banner-image">
                                <?php echo homeo_get_attachment_thumbnail($img_src['id'], 'full'); ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ( (!empty($content) && !empty($btn_text)) || !empty($content) ) { ?>
                        <div class="p-static col-xs-6 col-sm-<?php echo esc_attr( (!empty($img_src['id']))? '6':'12' ); ?>">
                            <div class="banner-content">
                                <?php if ( !empty($content) ) { ?>
                                    <?php echo trim($content); ?>
                                <?php } ?>
                                <?php if ( !empty($btn_text) ) { ?>
                                    <div class="link-bottom">
                                        <span class="btn radius-50 <?php echo esc_attr(!empty($btn_style) ? $btn_style : ''); ?>"><?php echo esc_html($btn_text); ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ( !empty($btn_text) && empty($content) ) { ?>
                        <span class="btn radius-50 <?php echo esc_attr(!empty($btn_style) ? $btn_style : ''); ?>"><?php echo esc_html($btn_text); ?></span>
                    <?php } ?>
                </div>
            <?php if ( !empty($link) ) { ?>
                </a>
            <?php } ?>
        </div>
        <?php

    }

}

Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Banner );