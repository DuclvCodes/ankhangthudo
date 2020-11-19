<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Social_Links extends Widget_Base {

	public function get_name() {
        return 'apus_element_social_links';
    }

	public function get_title() {
        return esc_html__( 'Apus Social Links', 'homeo' );
    }

    public function get_icon() {
        return 'fa fa-share-square-o';
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

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title', [
                'label' => esc_html__( 'Social Title', 'homeo' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Social Title' , 'homeo' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'social_icon',
            [
                'label' => esc_html__( 'Icon', 'homeo' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'social',
                'label_block' => true,
                'default' => [
                    'value' => 'fab fa-facebook-f',
                    'library' => 'fa-brands',
                ],
                'recommended' => [
                    'fa-brands' => [
                        'android',
                        'apple',
                        'behance',
                        'bitbucket',
                        'codepen',
                        'delicious',
                        'deviantart',
                        'digg',
                        'dribbble',
                        'elementor',
                        'facebook',
                        'flickr',
                        'foursquare',
                        'free-code-camp',
                        'github',
                        'gitlab',
                        'globe',
                        'google-plus',
                        'houzz',
                        'instagram',
                        'jsfiddle',
                        'linkedin',
                        'medium',
                        'meetup',
                        'mixcloud',
                        'odnoklassniki',
                        'pinterest',
                        'product-hunt',
                        'reddit',
                        'shopping-cart',
                        'skype',
                        'slideshare',
                        'snapchat',
                        'soundcloud',
                        'spotify',
                        'stack-overflow',
                        'steam',
                        'stumbleupon',
                        'telegram',
                        'thumb-tack',
                        'tripadvisor',
                        'tumblr',
                        'twitch',
                        'twitter',
                        'viber',
                        'vimeo',
                        'vk',
                        'weibo',
                        'weixin',
                        'whatsapp',
                        'wordpress',
                        'xing',
                        'yelp',
                        'youtube',
                        '500px',
                    ],
                    'fa-solid' => [
                        'envelope',
                        'link',
                        'rss',
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'homeo' ),
                'type' => Controls_Manager::URL,
                'label_block' => true,
                'default' => [
                    'is_external' => 'true',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'homeo' ),
            ]
        );

        $this->add_control(
            'social_icon_list',
            [
                'label' => esc_html__( 'Social Icons', 'homeo' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'social_icon' => [
                            'value' => 'fab fa-facebook',
                            'library' => 'fa-brands',
                        ],
                    ],
                    [
                        'social_icon' => [
                            'value' => 'fab fa-twitter',
                            'library' => 'fa-brands',
                        ],
                    ],
                    [
                        'social_icon' => [
                            'value' => 'fab fa-google-plus',
                            'library' => 'fa-brands',
                        ],
                    ],
                ],
                'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( social_icon, social, true, migrated, true ) }}}',
            ]
        );
        
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'homeo' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Normal', 'homeo'),
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
            'section_title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
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
                    '{{WRAPPER}} .widget-title ' => 'color: {{VALUE}};',
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

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Social', 'homeo' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'social_color',
            [
                'label' => esc_html__( 'Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .social a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Hover Color', 'homeo' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .social a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .social a:focus' => 'color: {{VALUE}};',
                ],
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
                    '{{WRAPPER}} .social' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings ); 
        $migration_allowed = Icons_Manager::is_migration_allowed();
        
        ?>

        <div class="widget-socials widget <?php echo esc_attr($el_class.' '.$style); ?>">
            <?php if ( !empty($title) ) { ?>
                <h2 class="widget-title">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php } ?>
            <ul class="social list-inline">
                <?php
                foreach ( $settings['social_icon_list'] as $index => $item ) {
                    $migrated = isset( $item['__fa4_migrated']['social_icon'] );
                    $is_new = empty( $item['social'] ) && $migration_allowed;
                    $social = '';

                    // add old default
                    if ( empty( $item['social'] ) && ! $migration_allowed ) {
                        $item['social'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
                    }

                    if ( ! empty( $item['social'] ) ) {
                        $social = str_replace( 'fa fa-', '', $item['social'] );
                    }

                    if ( ( $is_new || $migrated ) && 'svg' !== $item['social_icon']['library'] ) {
                        $social = explode( ' ', $item['social_icon']['value'], 2 );
                        if ( empty( $social[1] ) ) {
                            $social = '';
                        } else {
                            $social = str_replace( 'fa-', '', $social[1] );
                        }
                    }
                    if ( 'svg' === $item['social_icon']['library'] ) {
                        $social = '';
                    }

                    $link_key = 'link_' . $index;

                    $this->add_render_attribute( $link_key, 'href', $item['link']['url'] );

                    if ( $item['link']['is_external'] ) {
                        $this->add_render_attribute( $link_key, 'target', '_blank' );
                    }

                    if ( $item['link']['nofollow'] ) {
                        $this->add_render_attribute( $link_key, 'rel', 'nofollow' );
                    }
                    ?>
                    <li>
                        <a <?php echo trim($this->get_render_attribute_string( $link_key )); ?>>
                            <?php
                            if ( $is_new || $migrated ) {
                                Icons_Manager::render_icon( $item['social_icon'] );
                            } else { ?>
                                <i class="<?php echo esc_attr( $item['social'] ); ?>"></i>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div> 
        <?php 
    }
    
    protected function _content_template() {
        ?>
        <# var iconsHTML = {}; #>
        <div class="widget-socials">
            <ul class="social list-inline">
                <# _.each( settings.social_icon_list, function( item, index ) {
                    var link = item.link ? item.link.url : '',
                        migrated = elementor.helpers.isIconMigrated( item, 'social_icon' );
                        social = elementor.helpers.getSocialNetworkNameFromIcon( item.social_icon, item.social, false, migrated );
                    #>
                    <li>
                        <a href="{{ link }}">
                            <span class="elementor-screen-only">{{{ social }}}</span>
                            <#
                                iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.social_icon, {}, 'i', 'object' );
                                if ( ( ! item.social || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
                                    {{{ iconsHTML[ index ].value }}}
                                <# } else { #>
                                    <i class="{{ item.social }}"></i>
                                <# }
                            #>
                        </a>
                    </li>
                <# } ); #>
            </ul>
        </div>
        <?php
    }
}
Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Social_Links );