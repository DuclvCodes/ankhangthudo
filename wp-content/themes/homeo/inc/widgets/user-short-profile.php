<?php

class Homeo_Widget_User_Short_Profile extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_user_short_profile',
            esc_html__('Apus User Short Profile', 'homeo'),
            array( 'description' => esc_html__( 'Show User Short Profile in sidebar', 'homeo' ), )
        );
        $this->widgetName = 'user_short_profile';
    }

    public function getTemplate() {
        $this->template = 'user-short-profile.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => '',
            'nav_menu_agent' => '',
            'nav_menu_agency' => '',
            'nav_menu_user' => '',
        );
        $instance = wp_parse_args((array) $instance, $defaults);

        $custom_menus = array( '' => esc_html__('Choose a menu', 'homeo') );
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $single_menu ) {
                if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->slug ) ) {
                    $custom_menus[ $single_menu->slug ] = $single_menu->name;
                }
            }
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_agent')); ?>">
                <?php echo esc_html__('Agent Account Menu:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_agent')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_agent')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_agent'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_agency')); ?>">
                <?php echo esc_html__('Agency Account Menu:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_agency')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_agency')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_agency'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_user')); ?>">
                <?php echo esc_html__('User Account Menu:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_user')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_user')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_user'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
        $instance['nav_menu_agent'] = ( ! empty( $new_instance['nav_menu_agent'] ) ) ? $new_instance['nav_menu_agent'] : '';
        $instance['nav_menu_agency'] = ( ! empty( $new_instance['nav_menu_agency'] ) ) ? $new_instance['nav_menu_agency'] : '';
        $instance['nav_menu_user'] = ( ! empty( $new_instance['nav_menu_user'] ) ) ? $new_instance['nav_menu_user'] : '';
        return $instance;
    }
}
if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Widget_User_Short_Profile');
}