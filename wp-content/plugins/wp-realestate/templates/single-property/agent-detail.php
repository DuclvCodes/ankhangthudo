<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

if ( !empty($post->post_author) ) {
    $user_id = $post->post_author;

    if ( WP_RealEstate_User::is_agent($user_id) ) {
        $agent_id = WP_RealEstate_User::get_agent_by_user_id($user_id);

        $display_name = WP_RealEstate_Agent::get_post_meta($agent_id, 'display_name', true);
        $phone = WP_RealEstate_Agent::get_post_meta($agent_id, 'phone', true);
        $email = WP_RealEstate_Agent::get_post_meta($agent_id, 'email', true);
        $job = WP_RealEstate_Agent::get_post_meta($agent_id, 'job', true);

    } elseif ( WP_RealEstate_User::is_agency($user_id) ) {
        $agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);

        $display_name = WP_RealEstate_Agency::get_post_meta($agency_id, 'display_name', true);
        $phone = WP_RealEstate_Agency::get_post_meta($agency_id, 'phone', true);
        $email = WP_RealEstate_Agency::get_post_meta($agency_id, 'email', true);
    } else {
        $userdata = get_userdata($post->post_author);
        $display_name = get_user_meta($post->post_author, 'display_name', true);
        $phone = get_user_meta($post->post_author, 'phone', true);
        $email = get_user_meta($post->post_author, 'email', true);
    }
    ?>
    <div class="wrapper-agent-info">
        <h3><?php echo esc_html__('Agent Info', 'wp-realestate'); ?></h3>
        <div class="property-detail-agent">
            <?php if ( !empty($display_name) ) { ?>
                <div class="agent-display_name">
                    <div class="title-info"><?php echo esc_html__('Name :', 'wp-realestate'); ?></div>
                    <?php echo $display_name; ?>
                </div>
            <?php } ?>
            <?php if ( !empty($email) ) { ?>
                <div class="agent-email">
                    <div class="title-info"><?php echo esc_html__('Email :', 'wp-realestate'); ?></div>
                    <?php echo $email; ?>
                </div>
            <?php } ?>
            <?php if ( !empty($phone) ) { ?>
                <div class="agent-phone">
                    <div class="title-info"><?php echo esc_html__('Phone :', 'wp-realestate'); ?></div>
                    <?php echo $phone; ?>
                </div>
            <?php } ?>
            <?php if ( !empty($job) ) { ?>
                <div class="agent-job">
                    <div class="title-info"><?php echo esc_html__('Position :', 'wp-realestate'); ?></div>
                    <?php echo $job; ?>
                </div>
            <?php } ?>

            <?php do_action('wp-realestate-single-property-agent-detail', $post); ?>
        </div>
    </div>
    <?php
}
