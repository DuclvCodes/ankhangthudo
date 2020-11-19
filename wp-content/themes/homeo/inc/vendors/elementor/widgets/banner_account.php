<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Banner_Account extends Widget_Base {

	public function get_name() {
        return 'apus_element_banner_account';
    }

	public function get_title() {
        return esc_html__( 'Apus Banner Create Account', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Banner Account', 'homeo' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'content',
            [
                'label' => esc_html__( 'Description', 'homeo' ),
                'type' => Controls_Manager::TEXTAREA,
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
        <div class="widget-banner-account <?php echo esc_attr($el_class); ?>" <?php echo trim($style_bg); ?>>
            <?php if ( !empty($title) ) { ?>
                <h2 class="title-account">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php } ?>
            <?php if ( !empty($content) ) { ?>
                <div class="description"><?php echo trim($content); ?></div>
            <?php } ?>
            <?php if ( !empty($btn_text)  ) { ?>
                <a class="btn radius-50 <?php echo esc_attr(!empty($btn_style) ? $btn_style : ''); ?>" href="<?php echo esc_url( ( !empty($link) ) ? $link :'#' ); ?>"><?php echo esc_html($btn_text); ?></a>
            <?php } ?>
        </div>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Banner_Account );