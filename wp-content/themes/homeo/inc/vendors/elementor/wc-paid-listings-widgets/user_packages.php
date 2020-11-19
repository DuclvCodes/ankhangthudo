<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_User_Packages extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_user_packages';
    }

	public function get_title() {
        return esc_html__( 'Apus User Packages', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
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
        ?>
        <div class="box-dashboard-wrapper">
            <div class="inner-list">
                <?php if ($title!=''): ?>
                    <h2 class="title">
                        <?php echo esc_attr( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( ! is_user_logged_in() ) {
                    ?>
                    <div class="box-list-2">
                        <div class="text-warning"><?php  esc_html_e( 'Please login to see this page.', 'homeo' ); ?></div>
                    </div>  
                    <?php
                } else {
                    $packages = WP_RealEstate_Wc_Paid_Listings_Mixes::get_packages_by_user( get_current_user_id(), false );
                    if ( !empty($packages) ) {
                    ?>
                        <div class="widget-user-packages <?php echo esc_attr($el_class); ?>">
                            <div class="widget-content table-responsive">
                                <table class="user-packages">
                                    <thead>
                                        <tr>
                                            <td><?php esc_html_e('ID', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Package', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Package Type', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Package Info', 'homeo'); ?></td>

                                            <td><?php esc_html_e('Status', 'homeo'); ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($packages as $package) {
                                            $prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
                                            $package_type = get_post_meta($package->ID, $prefix. 'package_type', true);
                                            $package_types = WP_RealEstate_Wc_Paid_Listings_Post_Type_Packages::package_types();

                                        ?>
                                            <tr>
                                                <td><?php echo trim($package->ID); ?></td>
                                                <td class="title"><?php echo trim($package->post_title); ?></td>
                                                <td>
                                                    <?php
                                                        if ( !empty($package_types[$package_type]) ) {
                                                            echo esc_html($package_types[$package_type]);
                                                        } else {
                                                            echo '--';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="package-info-wrapper">
                                                    <?php
                                                        switch ($package_type) {
                                                            case 'property_package':
                                                            default:
                                                                $feature_properties = get_post_meta($package->ID, $prefix. 'feature_properties', true);
                                                                $package_count = get_post_meta($package->ID, $prefix. 'package_count', true);
                                                                $property_limit = get_post_meta($package->ID, $prefix. 'property_limit', true);
                                                                $property_duration = get_post_meta($package->ID, $prefix. 'property_duration', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title"><?php esc_html_e('Featured:', 'homeo'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $feature_properties == 'on' ) {
                                                                                    esc_html_e('Yes', 'homeo');
                                                                                } else {
                                                                                    esc_html_e('No', 'homeo');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title"><?php esc_html_e('Posted:', 'homeo'); ?></span>
                                                                        <span class="value"><?php echo intval($package_count); ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title"><?php esc_html_e('Limit Posts:', 'homeo'); ?></span>
                                                                        <span class="value"><?php echo intval($property_limit); ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title"><?php esc_html_e('Listing Duration:', 'homeo'); ?></span>
                                                                        <span class="value"><?php echo intval($property_duration); ?></span>
                                                                    </li>
                                                                </ul>
                                                                <?php
                                                                break;
                                                        }
                                                    ?>
                                                    </div>
                                                </td>
                                                <td>

                                                    <?php
                                                        $valid = false;
                                                        $user_id = get_current_user_id();
                                                        switch ($package_type) {
                                                            case 'property_package':
                                                            default:
                                                                $valid = WP_RealEstate_Wc_Paid_Listings_Mixes::package_is_valid($user_id, $package->ID);
                                                                break;
                                                        }
                                                        if ( !$valid ) {
                                                            echo '<span class="action finish">'.esc_html__('Finished', 'homeo').'</span>';
                                                        } else {
                                                            echo '<span class="action active">'.esc_html__('Active', 'homeo').'</span>';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="not-found"><?php esc_html_e('Don\'t have any packages', 'homeo'); ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php }

}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_User_Packages );