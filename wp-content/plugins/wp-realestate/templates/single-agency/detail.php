<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$email = WP_RealEstate_Agent::get_post_meta($post->ID, 'email', true);
$web = WP_RealEstate_Agent::get_post_meta($post->ID, 'web', true);
$phone = WP_RealEstate_Agent::get_post_meta($post->ID, 'phone', true);
$address = WP_RealEstate_Agent::get_post_meta($post->ID, 'address', true);

?>
<div class="agent-detail-detail">

    <?php if ( has_post_thumbnail() ) { ?>
        <div class="agent-thumbnail">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </div>
    <?php } ?>
    <div class="agent-information">
        <?php the_title( sprintf( '<h2 class="entry-title agent-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        <?php  if ($address) { ?>
            <div class="address"><?php echo $address; ?></div>
        <?php } ?>

        <ul class="list">
            <?php if ( $email ) { ?>
                <li>
                    <div class="icon">
                        <i class="flaticon-eye"></i>
                    </div>
                    <div class="details">
                        <div class="text"><?php esc_html_e('Email', 'wp-realestate'); ?></div>
                        <div class="value"><?php echo wp_kses_post($email); ?></div>
                    </div>
                </li>
            <?php } ?>

            <?php if ( $web ) { ?>
                <li>
                    <div class="icon">
                        <i class="flaticon-label"></i>
                    </div>
                    <div class="details">
                        <div class="text"><?php esc_html_e('Website', 'wp-realestate'); ?></div>
                        <div class="value"><?php echo wp_kses_post($web); ?></div>
                    </div>
                </li>
            <?php } ?>

            
            <?php if ( $phone ) { ?>
                <li>
                    <div class="icon">
                        <i class="flaticon-timeline"></i>
                    </div>
                    <div class="details">
                        <div class="text"><?php esc_html_e('Phone', 'wp-realestate'); ?></div>
                        <div class="value"><?php echo wp_kses_post($phone); ?></div>
                    </div>
                </li>
            <?php } ?>
            
        </ul>
    </div>
</div>