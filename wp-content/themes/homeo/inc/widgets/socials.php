<?php

class Homeo_Socials_Widget extends Apus_Widget {
    public function __construct() {
        parent::__construct(
            'apus_socials_widget',
            esc_html__('Apus Socials', 'homeo'),
            array( 'description' => esc_html__( 'Socials for website.', 'homeo' ), )
        );
        $this->widgetName = 'socials';

        add_action('admin_enqueue_scripts', array($this, 'scripts'));
    }

    public function scripts() {
        wp_enqueue_script( 'homeo-admin', get_template_directory_uri().'/js/admin.js', array( 'jquery' ), '1.0', true );
    }

    public function getTemplate() {
        $this->template = 'socials.php';
    }

    public function widget( $args, $instance ) {
        $this->display($args, $instance);
    }
    
    public function form( $instance ) {
        $list_socials = array(
            'facebook'      => 'Facebook',
            'twitter'       => 'Twitter',
            'youtube'       => 'Youtube',
            'pinterest'     => 'Pinterest',
            'google-plus'   => 'Google plus',
            'linkedin'      => 'LinkedIn',
            'instagram'      => 'Instagram'
        );
        $styles = array(
            esc_html__('Style 1', 'homeo') => 'style1',
            esc_html__('Style 2', 'homeo') => 'style2'
        );
        $defaults = array('title' => 'Find us on social networks', 'layout' => 'default', 'socials' => array());
        $instance = wp_parse_args((array) $instance, $defaults);
        
    ?>
    <div class="apus_socials">

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'homeo'); ?></label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'socials' )); ?>"><?php esc_html_e('Select socials:', 'homeo'); ?></label>
            <br>
        <?php
            foreach ($list_socials as $key => $value):
                $checked = (isset($instance['socials'][$key]['status']) && ($instance['socials'][$key]['status'])) ? 1: 0;
                $link = (isset($instance['socials'][$key]['page_url'])) ? $instance['socials'][$key]['page_url']: '';
        ?>
                <p>
                <input class="checkbox" type="checkbox" <?php checked( $checked, 1 ); ?> id="<?php echo esc_attr( $key ); ?>"
                    name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][status]" />
                    <label for="<?php echo esc_attr($this->get_field_name('socials') ); ?>[<?php echo esc_attr( $key ); ?>][status]">
                        <?php echo 'Show '.esc_html( $value ); ?>
                    </label>
                <input type="hidden" name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][name]" value=<?php echo esc_attr( $value ); ?> />
                </p>
                <p style="display: <?php echo esc_attr($checked? 'block': 'none'); ?>" id="<?php echo esc_attr($this->get_field_id($key)); ?>" class="text_url <?php echo esc_attr( $key ); ?>">
                    <label for="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]">
                        <?php echo esc_html( $value ).' Page URL: '; ?>
                    </label>
                    <input class="widefat" type="text"
                        id="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]"
                        name="<?php echo esc_attr($this->get_field_name('socials')); ?>[<?php echo esc_attr( $key ); ?>][page_url]"
                        value="<?php echo esc_url($link); ?>"
                    />
                </p>
            <?php endforeach; ?>
        </p>
         <p>
            <label for="<?php echo esc_attr($this->get_field_id('styles')); ?>">
                <?php echo esc_html__('Style:', 'homeo' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('styles')); ?>" name="<?php echo esc_attr($this->get_field_name('styles')); ?>">
                <?php foreach ( $styles as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected($instance['styles'],$value); ?> ><?php echo esc_html( $key ); ?></option>
                <?php } ?>
            </select>
        </p>
    </div>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        
        $instance['title'] = $new_instance['title'];
        $instance['socials'] = $new_instance['socials'];
        $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : 'default';
        $instance['styles'] = ( ! empty( $new_instance['styles'] ) ) ? $new_instance['styles'] : 'style1';
        return $instance;

    }
}
if ( function_exists('apus_framework_reg_widget') ) {
    apus_framework_reg_widget('Homeo_Socials_Widget');
}