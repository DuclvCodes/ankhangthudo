<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_RealEstate_Properties_Slider extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_realestate_properties_slider';
    }

	public function get_title() {
        return esc_html__( 'Apus Properties Slider', 'homeo' );
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'property_id', [
                'label' => esc_html__( 'Property ID', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter property ID', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'image',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image Background', 'homeo' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Background Here', 'homeo' ),
            ]
        );

        $repeater->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT
            ]
        );

        $repeater->add_control(
            'desc',
            [
                'label' => esc_html__( 'Description', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'rows' => 2,
                'default' => '',
            ]
        );


        $this->add_control(
            'sliders',
            [
                'label' => esc_html__( 'Sliders', 'homeo' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your property tabs here', 'homeo' ),
                'fields' => $repeater->get_controls(),
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
            'section_typography_style',
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
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_control(
            'des_color',
            [
                'label' => esc_html__( 'Description Color', 'homeo' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .desc' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_des',
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .desc',
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($sliders) ) {
        ?>
        <div class="widget-properties-slider <?php echo esc_attr($el_class); ?>">

            <div class="slick-carousel no-gap" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>">
                <?php foreach ($sliders as $slider): ?>
                    <div class="item p-relative">
                        
                        <?php if ( !empty($slider['image']['id']) ) { ?>
                            <div class="banner-image">
                                <?php echo homeo_get_attachment_thumbnail($slider['image']['id'], 'homeo-slider'); ?>
                            </div>
                        <?php } ?>
                        <div class="content-wrapper <?php echo esc_attr( (!empty($slider['image']['id']))?'has-image':'' ) ?>">
                        <div class="container">
                            <div class="properties-slider-inner flex-middle-sm">
                                <?php if ( $slider['title'] || $slider['desc'] ) { ?>
                                    <div class="left">
                                        <?php if ( $slider['title'] ) { ?>
                                            <h1 class="title"><?php echo trim($slider['title']); ?></h1>
                                        <?php } ?>
                                        <?php if ( $slider['desc'] ) { ?>
                                            <div class="desc"><?php echo trim($slider['desc']); ?></div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <?php
                                if ( !empty($slider['property_id']) ) {

                                    $post_object = get_post( $slider['property_id'] );
                                    if ( $post_object ) {
                                        setup_postdata( $GLOBALS['post'] =& $post_object );
                                        global $post;
                                        ?>
                                            <div class="property-grid-slider property-grid property-item ali-right">
                                                <div class="inner">
                                                <div class="top-info">
                                                    <?php
                                                        $featured = homeo_property_display_featured_icon($post, false);
                                                        $status_label = homeo_property_display_status_label($post, false);
                                                        $labels = homeo_property_display_label($post, false);
                                                        if ( $featured || $status_label || $labels ) {
                                                            ?>
                                                            <div class="top-label">
                                                                <?php echo trim($status_label); ?>
                                                                <?php echo trim($featured); ?>
                                                                <?php echo trim($labels); ?>
                                                            </div>
                                                            <?php
                                                        }
                                                    ?>

                                                    <div class="property-information">
                                                        <?php homeo_property_display_type($post, 'no-icon-title', true); ?>

                                                        <?php the_title( sprintf( '<h2 class="property-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                                                        <?php homeo_property_display_full_location($post, 'icon'); ?>

                                                        <?php
                                                        $suffix = wp_realestate_get_option('measurement_unit_area');
                                                        $lot_area = homeo_property_display_meta($post, 'lot_area', '', $suffix.':');
                                                        $beds = homeo_property_display_meta($post, 'beds', '', esc_html__('Beds:', 'homeo'));
                                                        $baths = homeo_property_display_meta($post, 'baths', '', esc_html__('Baths:', 'homeo'));

                                                        if ( $lot_area || $beds || $baths || $garages ) {
                                                        ?>
                                                            <div class="property-metas flex-middle flex-wrap">
                                                                <?php
                                                                    
                                                                    if ( $beds || $baths || $garages ) {
                                                                        ?>
                                                                            <?php
                                                                                echo trim($beds);
                                                                                echo trim($baths);
                                                                                echo trim($lot_area);
                                                                            ?>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </div>
                                                        <?php } ?>

                                                        <div class="bottom-label flex-middle">

                                                            <?php homeo_property_display_price($post, 'no-icon-title', true); ?>

                                                            <div class="ali-right">
                                                                <?php
                                                                    if ( homeo_get_config('listing_enable_favorite', true) ) {
                                                                        WP_RealEstate_Favorite::display_favorite_btn($post->ID);
                                                                    }
                                                                    if ( homeo_get_config('listing_enable_compare', true) ) {
                                                                        $args = array(
                                                                            'added_icon_class' => 'flaticon-transfer-1',
                                                                            'add_icon_class' => 'flaticon-transfer-1',
                                                                        );
                                                                        WP_RealEstate_Compare::display_compare_btn($post->ID, $args);
                                                                    }
                                                                ?>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <?php
                                                    $postdate = homeo_property_display_postdate($post, 'no-icon-title', 'ago', false);
                                                    $author = homeo_property_display_author($post, 'logo', false);
                                                    if ( $postdate || $author ) {
                                                ?>
                                                    <div class="property-metas-bottom flex-middle">
                                                        <?php echo trim($author); ?>
                                                        <div class="ali-right">
                                                            <?php echo trim($postdate); ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                </div>
                                            </div>
                                        <?php

                                        wp_reset_postdata();
                                    }

                                }
                                ?>
                            </div>
                        </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php
        }
    }
}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_RealEstate_Properties_Slider );