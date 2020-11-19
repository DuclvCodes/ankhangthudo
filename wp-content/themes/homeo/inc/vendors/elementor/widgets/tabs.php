<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Abolire_Elementor_Tabs extends Widget_Base {

    public function get_name() {
        return 'apus_element_tabs';
    }

    public function get_title() {
        return esc_html__( 'Apus Tabs', 'homeo' );
    }

    public function get_icon() {
        return 'eicon-tabs';
    }

    public function get_categories() {
        return [ 'homeo-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Tabs', 'homeo' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $tabs = new Repeater();

        $tabs->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();

        if ( empty( $templates ) ) {

            $this->add_control(
                'no_templates',
                array(
                    'label' => false,
                    'type'  => Controls_Manager::RAW_HTML,
                    'raw'   => $this->empty_templates_message(),
                )
            );

            return;
        }

        $options = [
            '0' => '— ' . esc_html__( 'Select', 'homeo' ) . ' —',
        ];

        $types = [];

        foreach ( $templates as $template ) {
            $options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            $types[ $template['template_id'] ] = $template['type'];
        }

        $tabs->add_control(
            'content_type',
            [
                'label'       => esc_html__( 'Content Type', 'homeo' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'template',
                'options'     => [
                    'template' => esc_html__( 'Template', 'homeo' ),
                    'editor'   => esc_html__( 'Editor', 'homeo' ),
                ],
                'label_block' => 'true',
            ]
        );

        $tabs->add_control(
            'item_template_id',
            [
                'label'       => esc_html__( 'Choose Template', 'homeo' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => '0',
                'options'     => $options,
                'types'       => $types,
                'label_block' => 'true',
                'condition'   => [
                    'content_type' => 'template',
                ]
            ]
        );

        $tabs->add_control(
            'content',
            [
                'label'      => esc_html__( 'Content', 'homeo' ),
                'type'       => Controls_Manager::WYSIWYG,
                'default'    => esc_html__( 'Tab Item Content', 'homeo' ),
                'dynamic' => [
                    'active' => true,
                ],
                'condition'   => [
                    'content_type' => 'editor',
                ]
            ]
        );


        $this->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Enter title here' , 'homeo' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__( 'Tabs', 'homeo' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $tabs->get_controls(),
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'homeo'),
                    'style1' => esc_html__('Style 1', 'homeo'),
                    'style2' => esc_html__('Style 2', 'homeo'),
                    'style3' => esc_html__('Style 3', 'homeo'),
                    'style4' => esc_html__('Style 4', 'homeo'),
                    'style5' => esc_html__('Style 5', 'homeo'),
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
    }

    protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $_id = homeo_random_key();

        ?>
        <div class="widget-tabs <?php echo esc_attr($el_class.' '.$style); ?>">
            <?php if ( !empty($title) ): ?>
                <h2 class="widget-title">
                    <?php echo esc_attr( $title ); ?>
                </h2>
            <?php endif; ?>
            <div class="widget-content">
                <ul role="tablist" class="nav nav-tabs">
                    <?php $i = 0; foreach ($tabs as $tab) : ?>
                        <li class="<?php echo esc_attr($i == 0 ? 'active' : '');?>">
                            <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" data-toggle="tab">
                                <?php if ( !empty($tab['title']) ) { ?>
                                    <?php echo esc_attr($tab['title']); ?>
                                <?php } ?>
                            </a>
                        </li>
                    <?php $i++; endforeach; ?>
                </ul>
                <div class="tab-content">

                    <?php $i = 0; foreach ($tabs as $tab) : ?>
                        <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane fade <?php echo esc_attr($i == 0 ? 'in active' : ''); ?>">

                            <div class="tabs-inner">

                                <?php
                                $ele_obj = \Elementor\Plugin::$instance;
                                $content_html = '';
                                switch ( $tab[ 'content_type' ] ) {
                                    case 'template':

                                        if ( '0' !== $tab['item_template_id'] ) {

                                            $template_content = $ele_obj->frontend->get_builder_content_for_display( $tab['item_template_id'] );

                                            if ( ! empty( $template_content ) ) {
                                                $content_html .= $template_content;

                                                if ( Plugin::$instance->editor->is_edit_mode() ) {
                                                    $link = add_query_arg(
                                                        array(
                                                            'elementor' => '',
                                                        ),
                                                        get_permalink( $tab['item_template_id'] )
                                                    );

                                                    $content_html .= sprintf( '<div class="homeo__edit-cover" data-template-edit-link="%s"><i class="fa fa-pencil"></i><span>%s</span></div>', $link, esc_html__( 'Edit Template', 'homeo' ) );
                                                }
                                            } else {
                                                $content_html = $this->no_template_content_message();
                                            }
                                        } else {
                                            $content_html = $this->no_templates_message();
                                        }
                                    break;

                                    case 'editor':
                                        if ( !empty($tab['content']) ) {
                                            $content_html = trim( $tab['content'] );
                                        }
                                    break;
                                }
                                echo trim($content_html);
                                ?>
                                
                            </div>
                        </div>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function no_templates_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'Template is not defined.', 'homeo' ) . '</span></div>';
    }

    public function no_template_content_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'The tabs are working. Please, note, that you have to add a template to the library in order to be able to display it inside the tabs.', 'homeo' ) . '</span></div>';
    }

    public function empty_templates_message() {
        $output = '<div id="elementor-widget-template-empty-templates">';
            $output .= '<div class="elementor-widget-template-empty-templates-icon"><i class="eicon-nerd"></i></div>';
            $output .= '<div class="elementor-widget-template-empty-templates-title">' . esc_html__( 'You Haven’t Saved Templates Yet.', 'homeo' ) . '</div>';
            $output .= '<div class="elementor-widget-template-empty-templates-footer">';
                $output .= esc_html__( 'What is Library?', 'homeo' );
                $output .= '<a class="elementor-widget-template-empty-templates-footer-url" href="https://go.elementor.com/docs-library/" target="_blank">' . esc_html__( 'Read our tutorial on using Library templates.', 'homeo' ) . '</a>';
            $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
    
}

Plugin::instance()->widgets_manager->register_widget_type( new Abolire_Elementor_Tabs );