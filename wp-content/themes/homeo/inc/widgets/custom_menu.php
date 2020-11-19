<?php

class Homeo_Custom_Menu extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_custom_menu',
            esc_html__('Apus Custom Menu Widget', 'homeo'),
            array( 'description' => esc_html__( 'Show custom menu', 'homeo' ), )
        );
        $this->widgetName = 'custom_menu';
    }

    public function getTemplate() {
        $this->template = 'custom-menu.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Custom Menu',
            'nav_menu' => '',
            'style' => '',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form

        $custom_menus = array();
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $single_menu ) {
                if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->slug ) ) {
                    $custom_menus[ $single_menu->name ] = $single_menu->slug;
                }
            }
        }
        $styles = array(
            esc_html__('Default', 'homeo') => ''
        );
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu')); ?>">
                <?php echo esc_html__('Menu:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected($instance['nav_menu'],$value); ?> ><?php echo esc_html( $key ); ?></option>
                <?php } ?>
            </select>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>">
                <?php echo esc_html__('Style:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <?php foreach ( $styles as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected($instance['style'],$value); ?> ><?php echo esc_html( $key ); ?></option>
                <?php } ?>
            </select>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
        $instance['nav_menu'] = ( ! empty( $new_instance['nav_menu'] ) ) ? $new_instance['nav_menu'] : '';
        $instance['style'] = ( ! empty( $new_instance['style'] ) ) ? $new_instance['style'] : '';
        return $instance;

    }
}
if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Custom_Menu');
}