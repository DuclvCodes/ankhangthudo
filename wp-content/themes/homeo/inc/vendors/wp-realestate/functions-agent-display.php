<?php

function homeo_agent_display_image($post, $size = 'thumbnail') {
	?>
    <div class="agent-logo-wrapper">
        <a class="agent-logo" href="<?php echo esc_url( get_permalink($post) ); ?>">
            <?php if ( has_post_thumbnail($post->ID) ) { ?>
            	<?php
                $post_thumbnail_id = get_post_thumbnail_id($post->ID);
                echo homeo_get_attachment_thumbnail( $post_thumbnail_id, $size );
                ?>
            <?php } ?>
        </a>
    </div>
    <?php
}

function homeo_agent_display_full_location($post, $display_type = 'normal', $echo = true) {
	ob_start();
	$location = WP_RealEstate_Agent::get_post_meta( $post->ID, 'address', true );
	if ( empty($location) ) {
		$location = WP_RealEstate_Agent::get_post_meta( $post->ID, 'map_location_address', true );
	}
	if( $display_type == 'icon' ){
		$prefix = '<i class="flaticon-location-pin"></i>';
	} elseif( $display_type == 'title' ) {
		$prefix = '<span class="location-label">'.esc_html__('Location:', 'homeo').'</span>';
	} else {
		$prefix = '';
	}
	if ( $location ) {
		?>
		<div class="property-location"><?php echo trim($prefix); ?> <a href="<?php echo esc_url( '//maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>" target="_blank"><?php echo esc_html($location); ?></a></div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_nb_properties($post) {
	$user_id = WP_RealEstate_User::get_user_by_agent_id($post->ID);
	$args = array(
	        'post_type' => 'property',
	        'post_per_page' => -1,
	        'post_status' => 'publish',
	        'fields' => 'ids',
	        'author' => $user_id
	    );
	$properties = WP_RealEstate_Query::get_posts($args);
	$count_properties = $properties->found_posts;
	
	?>
	<div class="nb-property">
        <?php echo sprintf(_n('%d Property', '%d Properties', intval($count_properties), 'homeo'), intval($count_properties)); ?>
    </div>
    <?php
}

function homeo_agent_display_nb_views($post) {
	$agent_id = $post->ID;
	$views = WP_RealEstate_Agent::get_post_meta($agent_id, 'views_count', true);
	$views_display = $views ? WP_RealEstate_Mixes::format_number($views) : 0;
	?>
	<div class="nb_views">
        <?php echo sprintf(_n('<span class="text-blue">%d</span> <span class="text">View</span>', '<span class="text-blue">%d</span> <span class="text">Views</span>', intval($views), 'homeo'), $views_display); ?>
    </div>
    <?php
}

function homeo_agent_display_featured_icon($post) {
	$featured = WP_RealEstate_Agent::get_post_meta( $post->ID, 'featured', true );
	if ( $featured ) { ?>
        <span class="featured featured-property" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'homeo'); ?>"><i class="fas fa-star"></i></span>
    <?php }
}

function homeo_agent_display_phone($post, $display_type = 'no-title', $echo = true, $always_show_phone = false) {
	$phone = WP_RealEstate_Agent::get_post_meta( $post->ID, 'phone' );
	ob_start();
	if ( $phone ) {
        $show_full = homeo_get_config('listing_show_full_phone', false);
        $hide_phone = $show_full ? false : true;
        $hide_phone = apply_filters('homeo_phone_hide_number', $hide_phone );
        if ( $always_show_phone ) {
            $hide_phone = false;
        }
        
        $add_class = '';
        if ( $hide_phone ) {
            $add_class = 'phone-hide';
        }
		if ( $display_type == 'title' ) {
            ?>
            <div class="phone-wrapper agent-phone with-title <?php echo esc_attr($add_class); ?>">
                <span><?php esc_html_e('Phone:', 'homeo'); ?></span>
            <?php
        } elseif ($display_type == 'icon') {
            ?>
            <div class="phone-wrapper agent-phone with-icon <?php echo esc_attr($add_class); ?>">
                <i class="ti-headphone-alt"></i>
        <?php
        } else {
            ?>
            <div class="phone-wrapper agent-phone <?php echo esc_attr($add_class); ?>">
            <?php
        }

        ?>
            <a class="phone" href="tel:<?php echo trim($phone); ?>"><?php echo trim($phone); ?></a>
            <?php if ( $hide_phone ) {
                $dispnum = substr($phone, 0, (strlen($phone)-3) ) . str_repeat("*", 3);
            ?>
                <span class="phone-show" onclick="this.parentNode.classList.add('show');"><?php echo trim($dispnum); ?> <span><?php esc_html_e('show', 'homeo'); ?></span></span>
            <?php } ?>
        </div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_fax($post, $display_type = 'no-title', $echo = true) {
    $fax = WP_RealEstate_Agent::get_post_meta( $post->ID, 'fax' );
    ob_start();
    if ( $fax ) {
        if ( $display_type == 'title' ) {
            ?>
            <div class="agent-fax with-title">
                <span><?php esc_html_e('Fax:', 'homeo'); ?></span>
            <?php
        } elseif ($display_type == 'icon') {
            ?>
            <div class="agent-fax with-icon">
                <i class="ti-world"></i>
        <?php
        } else {
            ?>
            <div class="agent-fax">
            <?php
        }
        ?>
            <?php echo trim($fax); ?>
        </div>
        <?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function homeo_agent_display_email($post, $display_type = 'no-title', $echo = true) {
	$email = WP_RealEstate_Agent::get_post_meta( $post->ID, 'email' );
	ob_start();
	if ( $email ) {
		if ( $display_type == 'title' ) {
            ?>
            <div class="agent-email with-title">
                <span><?php esc_html_e('Email:', 'homeo'); ?></span>
            <?php
        } elseif ($display_type == 'icon') {
            ?>
            <div class="agent-email with-icon">
                <i class="ti-email"></i>
        <?php
        } else {
            ?>
            <div class="agent-email">
            <?php
        }
        ?>
            <a href="mailto:<?php echo trim($email); ?>"><?php echo trim($email); ?></a>
        </div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_website($post, $display_type = 'no-title', $echo = true) {
	$website = WP_RealEstate_Agent::get_post_meta( $post->ID, 'website' );
	ob_start();
	if ( $website ) {
        if ( $display_type == 'title' ) {
            ?>
            <div class="agent-website with-title">
                <span><?php esc_html_e('Website:', 'homeo'); ?></span>
            <?php
        } elseif ($display_type == 'icon') {
            ?>
            <div class="agent-website with-icon">
                <i class="ti-world"></i>
        <?php
        } else {
            ?>
            <div class="agent-website">
            <?php
        }
        ?>
            <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo trim($website); ?></a>
        </div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_job($post, $echo = true) {
	$job = WP_RealEstate_Agent::get_post_meta( $post->ID, 'job' );
	ob_start();
	if ( $job ) {
		?>
		<div class="property-job"><?php echo esc_html($job); ?></div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_property($post, $echo = true) {
	$property = WP_RealEstate_Agent::get_post_meta( $post->ID, 'property' );
	ob_start();
	if ( $property ) {
		?>
		<div class="property-property"><?php echo esc_html($property); ?></div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_agent_display_socials($post, $echo = true) {
	$socials = WP_RealEstate_Agent::get_post_meta( $post->ID, 'socials' );
	$output = '';
    if ( $socials ) {
        foreach ($socials as $social) {
            if ( !empty($social['network']) && !empty($social['url']) ) {
                $output .= '<a href="'.esc_url($social['url']).'" target="_blank"><i class="'.esc_attr($social['network']).'"></i></a>';
            }
        }
    }
    if ( $output ) {
        $output = '<div class="agency-socials socials-member">'.$output.'</div>';
    }
    if ( $echo ) {
        echo trim($output);
    } else {
        return $output;
    }
}

function homeo_agent_display_rating($post) {
    if ( WP_RealEstate_Review::review_enable($post->ID) ) {
        $average_rating = get_post_meta( $post->ID, '_average_rating', true );
        ?>
        <?php if($average_rating > 0) { ?>
            <div class="rating-wrapper">
                <?php echo trim($average_rating); ?>
                <i class="fas fa-star"></i>
            </div>
        <?php } ?>
        <?php
    }
}
