<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Agents extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_agents';
    }

	public function get_title() {
        return esc_html__( 'Apus Agents', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Agents', 'homeo' ),
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
            'content',
            [
                'label' => esc_html__( 'Description', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => esc_html__( 'Limit', 'homeo' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Limit agents to display', 'homeo' ),
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
            'get_agents_by',
            [
                'label' => esc_html__( 'Get Agents By', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'featured' => esc_html__('Featured Agents', 'homeo'),
                    'recent' => esc_html__('Recent Agents', 'homeo'),
                ),
                'default' => 'recent'
            ]
        );

        $this->add_control(
            'agent_item_style',
            [
                'label' => esc_html__( 'Agent Item Style', 'homeo' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid Default', 'homeo'),
                    'grid-v1' => esc_html__('Grid V1', 'homeo'),
                    'grid-v2' => esc_html__('Grid V2', 'homeo'),
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
                'default' => 'carousel'
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
            'view_all',
            [
                'label' => esc_html__( 'View All', 'homeo' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'homeo' ),
                'label_off' => esc_html__( 'Show', 'homeo' ),
            ]
        );

        $this->add_control(
            'text_view',
            [
                'label' => esc_html__( 'Text View All', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'See All Properties',
                'condition' => [
                    'view_all' => ['yes'],
                ]
            ]
        );

        $this->add_control(
            'link_view',
            [
                'label' => esc_html__( 'View Link', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your Link here', 'homeo' ),
                'condition' => [
                    'view_all' => ['yes'],
                ]
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

        $args = array(
            'limit' => $limit,
            'get_agents_by' => $get_agents_by,
            'orderby' => $orderby,
            'order' => $order
        );
        $loop = homeo_get_agents($args);
        if ( $loop->have_posts() ) {

            $columns = !empty($columns) ? $columns : 3;
            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 3;
            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 2;
            
            $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
            $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
            $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
            ?>
            <div class="widget-agents <?php echo esc_attr($layout_type); ?> <?php echo esc_attr($el_class); ?>">
                <?php if( $title || !empty($content) || ( $view_all == 'yes' && !(empty($link_view)) && !(empty($text_view)) ) ){ ?>
                    <div class="top-info-widget flex-middle">
                        <div class="info-left">
                            <?php if ( $title ) { ?>
                                <h2 class="title"><?php echo esc_html($title); ?></h2>
                            <?php } ?>
                            <?php if ( !empty($content) ) { ?>
                                <div class="description"><?php echo trim($content); ?></div>
                            <?php } ?>
                        </div>
                        <?php if ( $view_all == 'yes' && !(empty($link_view)) && !(empty($text_view)) ) { ?>
                            <div class="ali-right">
                                <a href="<?php echo esc_url( $link_view ); ?>" class="btn-view">
                                    <?php echo esc_html($text_view); ?><i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="widget-content">
                    <?php if ( $layout_type == 'carousel' ): ?>
                        <div class="slick-carousel <?php echo esc_attr( ( $loop->post_count <= $columns )?'hidden-dots':'' ); ?>"
                            data-items="<?php echo esc_attr($columns); ?>"
                            data-smallmedium="<?php echo esc_attr( $columns_tablet ); ?>"
                            data-extrasmall="<?php echo esc_attr($columns_mobile); ?>"

                            data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
                            data-slidestoscroll_smallmedium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                            data-slidestoscroll_extrasmall="<?php echo esc_attr($slides_to_scroll_mobile); ?>"
                            
                            data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>">
                            <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                                <div class="item">
                                    <?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-'.$agent_item_style ); ?>
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
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                <div class="col-md-<?php echo esc_attr($mdcol); ?> col-sm-<?php echo esc_attr($smcol); ?> col-xs-<?php echo esc_attr($columns_mobile); ?>">
                                    <?php echo WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/inner-'.$agent_item_style ); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
            <?php
        }

    }

}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Agents );