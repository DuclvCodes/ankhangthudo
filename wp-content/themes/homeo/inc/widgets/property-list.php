<?php

class Homeo_Widget_Property_List extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_widget_property_list',
            esc_html__('Apus Simple Properties List', 'homeo'),
            array( 'description' => esc_html__( 'Show list of property', 'homeo' ), )
        );
        $this->widgetName = 'property_list';
    }

    public function getTemplate() {
        $this->template = 'property-list.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => 'Latest Properties',
            'number_post' => '4',
            'orderby' => '',
            'order' => '',
            'get_properties_by' => 'recent',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        // Widget admin form
        $orderbys = array(
            '' => esc_html__('Default', 'homeo'),
            'date' => esc_html__('Date', 'homeo'),
            'ID' => esc_html__('ID', 'homeo'),
            'author' => esc_html__('Author', 'homeo'),
            'title' => esc_html__('Title', 'homeo'),
            'modified' => esc_html__('Modified', 'homeo'),
            'rand' => esc_html__('Random', 'homeo'),
            'comment_count' => esc_html__('Comment count', 'homeo'),
            'menu_order' => esc_html__('Menu order', 'homeo'),
        );
        $orders = array(
            '' => esc_html__('Default', 'homeo'),
            'ASC' => esc_html__('Ascending', 'homeo'),
            'DESC' => esc_html__('Descending', 'homeo'),
        );
        $get_properties_bys = array(
            'featured' => esc_html__('Featured Properties', 'homeo'),
            'urgent' => esc_html__('Urgent Properties', 'homeo'),
            'recent' => esc_html__('Recent Properties', 'homeo'),
        );
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>">
                <?php echo esc_html__('Order By:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>">
                <?php foreach ($orderbys as $key => $title) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['orderby'], $key); ?> ><?php echo esc_html( $title ); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('order')); ?>">
                <?php echo esc_html__('Order:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('order')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>">
                <?php foreach ($orders as $key => $title) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['order'], $key); ?> ><?php echo esc_html( $title ); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('get_properties_by')); ?>">
                <?php echo esc_html__('Get properties by:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('get_properties_by')); ?>" name="<?php echo esc_attr($this->get_field_name('get_properties_by')); ?>">
                <?php foreach ($get_properties_bys as $key => $title) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['get_properties_by'], $key); ?> ><?php echo esc_html( $title ); ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'number_post' )); ?>"><?php esc_html_e( 'Num Posts:', 'homeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number_post' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number_post' )); ?>" type="text" value="<?php echo esc_attr($instance['number_post']); ?>" />
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_post'] = ( ! empty( $new_instance['number_post'] ) ) ? strip_tags( $new_instance['number_post'] ) : '';
        $instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : '';
        $instance['order'] = ( ! empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order'] ) : '';
        $instance['get_properties_by'] = ( ! empty( $new_instance['get_properties_by'] ) ) ? strip_tags( $new_instance['get_properties_by'] ) : '';
        return $instance;

    }
}
if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Widget_Property_List');
}