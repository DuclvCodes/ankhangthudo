<?php

class Homeo_Widget_Mortgage_Calculator extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_mortgage_calculator',
            esc_html__('Apus Mortgage Calculator Widget', 'homeo'),
            array( 'description' => esc_html__( 'Show Mortgage Calculator', 'homeo' ), )
        );
        $this->widgetName = 'mortgage_calculator';
    }

    public function getTemplate() {
        $this->template = 'mortgage-calculator.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Mortgage Calculator'
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;

    }
}

if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Widget_Mortgage_Calculator');
}