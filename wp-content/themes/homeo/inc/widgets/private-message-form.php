<?php

class Homeo_Widget_Private_Message_Form extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_private_message_form',
            esc_html__('Private Message Form', 'homeo'),
            array( 'description' => esc_html__( 'Show property|agent|agency private message form', 'homeo' ), )
        );
        $this->widgetName = 'private_message_form';
    }

    public function getTemplate() {
        $this->template = 'private-message-form.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Send Message to %1s',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
            <span class="desc"><?php esc_html_e('Enter %1s for property|agent|agency name', 'homeo'); ?></span>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
}
if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Widget_Private_Message_Form');
}