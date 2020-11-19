<?php

if ( ! function_exists( 'homeo_body_classes' ) ) {
	function homeo_body_classes( $classes ) {
		global $post;
		$show_footer_mobile = homeo_get_config('show_footer_mobile', true);

		if ( is_page() && is_object($post) ) {
			$class = get_post_meta( $post->ID, 'apus_page_extra_class', true );
			if ( !empty($class) ) {
				$classes[] = trim($class);
			}
			if(get_post_meta( $post->ID, 'apus_page_header_transparent',true) && get_post_meta( $post->ID, 'apus_page_header_transparent',true) == 'yes' ){
				$classes[] = 'header_transparent';
			}
			if(get_post_meta( $post->ID, 'apus_page_header_fixed',true) && get_post_meta( $post->ID, 'apus_page_header_fixed',true) == 'yes' ){
				$classes[] = 'header_fixed';
			}
			// layout
			if(get_post_meta( $post->ID, 'apus_page_layout', true )){
				$classes[] = get_post_meta( $post->ID, 'apus_page_layout', true );
			}
		}

		if ( homeo_get_config('preload', true) ) {
			$classes[] = 'apus-body-loading';
		}
		if ( homeo_get_config('image_lazy_loading') ) {
			$classes[] = 'image-lazy-loading';
		}
		if ( $show_footer_mobile ) {
			$classes[] = 'body-footer-mobile';
		}
		if ( homeo_is_wp_realestate_activated() ) {
			if ( homeo_is_properties_page() ) {
				$layout_type = homeo_get_properties_layout_type();
				if ( $layout_type == 'half-map' || $layout_type == 'half-map-v2' || $layout_type == 'half-map-v3' ) {
					$classes[] = 'no-footer';
					$classes[] = 'fix-header';
				}
			}

		}
		if ( homeo_get_config('keep_header') ) {
			$classes[] = 'has-header-sticky';
		}
		
		return $classes;
	}
	add_filter( 'body_class', 'homeo_body_classes' );
}

if ( !function_exists('homeo_get_header_layouts') ) {
	function homeo_get_header_layouts() {
		$headers = array();
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'apus_header',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$headers[$post->post_name] = $post->post_title;
		}
		return $headers;
	}
}

if ( !function_exists('homeo_get_header_layout') ) {
	function homeo_get_header_layout() {
		global $post;
		if ( is_page() && is_object($post) && isset($post->ID) ) {
			global $post;
			$header = get_post_meta( $post->ID, 'apus_page_header_type', true );
			if ( empty($header) || $header == 'global' ) {
				return homeo_get_config('header_type');
			}
			return $header;
		}
		return homeo_get_config('header_type');
	}
	add_filter( 'homeo_get_header_layout', 'homeo_get_header_layout' );
}

function homeo_display_header_builder($header_slug) {
	$args = array(
		'name'        => $header_slug,
		'post_type'   => 'apus_header',
		'post_status' => 'publish',
		'numberposts' => 1
	);
	$posts = get_posts($args);
	foreach ( $posts as $post ) {
		if ( homeo_get_config('keep_header') ) {
			$classes = array('apus-header visible-lg');
		}else{
			$classes = array('apus-header no_keep_header visible-lg');
		}
		$classes[] = $post->post_name.'-'.$post->ID;

		echo '<div id="apus-header" class="'.esc_attr(implode(' ', $classes)).'">';
		if ( homeo_get_config('keep_header') ) {
	        echo '<div class="main-sticky-header">';
	    }
			echo apply_filters( 'homeo_generate_post_builder', do_shortcode( $post->post_content ), $post, $post->ID);
		if ( homeo_get_config('keep_header') ) {
			echo '</div>';
	    }
		echo '</div>';
	}
}

if ( !function_exists('homeo_get_footer_layouts') ) {
	function homeo_get_footer_layouts() {
		$footers = array();
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'apus_footer',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$footers[$post->post_name] = $post->post_title;
		}
		return $footers;
	}
}

if ( !function_exists('homeo_get_footer_layout') ) {
	function homeo_get_footer_layout() {
		if ( is_page() ) {
			global $post;
			$footer = '';
			if ( is_object($post) && isset($post->ID) ) {
				$footer = get_post_meta( $post->ID, 'apus_page_footer_type', true );
				if ( empty($footer) || $footer == 'global' ) {
					return homeo_get_config('footer_type', '');
				}
			}
			return $footer;
		}
		return homeo_get_config('footer_type', '');
	}
	add_filter('homeo_get_footer_layout', 'homeo_get_footer_layout');
}

function homeo_display_footer_builder($footer_slug) {
	$show_footer_desktop_mobile = homeo_get_config('show_footer_desktop_mobile', false);
	$args = array(
		'name'        => $footer_slug,
		'post_type'   => 'apus_footer',
		'post_status' => 'publish',
		'numberposts' => 1
	);
	$posts = get_posts($args);
	foreach ( $posts as $post ) {
		$classes = array('apus-footer footer-builder-wrapper');
		if ( !$show_footer_desktop_mobile ) {
			$classes[] = '';
		}
		$classes[] = $post->post_name;


		echo '<div id="apus-footer" class="'.esc_attr(implode(' ', $classes)).'">';
		echo '<div class="apus-footer-inner">';
		echo apply_filters( 'homeo_generate_post_builder', do_shortcode( $post->post_content ), $post, $post->ID);
		echo '</div>';
		echo '</div>';
	}
}

if ( !function_exists('homeo_blog_content_class') ) {
	function homeo_blog_content_class( $class ) {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		if ( homeo_get_config('blog_'.$page.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'homeo_blog_content_class', 'homeo_blog_content_class', 1 , 1  );


if ( !function_exists('homeo_get_blog_layout_configs') ) {
	function homeo_get_blog_layout_configs() {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		$left = homeo_get_config('blog_'.$page.'_left_sidebar');
		$right = homeo_get_config('blog_'.$page.'_right_sidebar');

		switch ( homeo_get_config('blog_'.$page.'_layout') ) {
		 	case 'left-main':
			 	if ( is_active_sidebar( $left ) ) {
			 		$configs['left'] = array( 'sidebar' => $left, 'class' => 'col-md-4 col-sm-12 col-xs-12'  );
			 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12 pull-right' );
			 	}
		 		break;
		 	case 'main-right':
		 		if ( is_active_sidebar( $right ) ) {
			 		$configs['right'] = array( 'sidebar' => $right,  'class' => 'col-md-4 col-sm-12 col-xs-12 pull-right' ); 
			 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
			 	}
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
	 			break;
		 	default:
		 		if ( is_active_sidebar( 'sidebar-default' ) ) {
			 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'col-md-4 col-xs-12' ); 
			 		$configs['main'] = array( 'class' => 'col-md-8 col-xs-12' );
			 	} else {
			 		$configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
			 	}
		 		break;
		}
		if ( empty($configs) ) {
			if ( is_active_sidebar( 'sidebar-default' ) ) {
		 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'col-md-4 col-xs-12' ); 
		 		$configs['main'] = array( 'class' => 'col-md-8 col-xs-12' );
		 	} else {
		 		$configs['main'] = array( 'class' => 'col-md-12 col-sm-12 col-xs-12' );
		 	}
		}
		return $configs; 
	}
}

if ( !function_exists('homeo_page_content_class') ) {
	function homeo_page_content_class( $class ) {
		global $post;
		if (is_object($post)) {
			$fullwidth = get_post_meta( $post->ID, 'apus_page_fullwidth', true );
			if ( !$fullwidth || $fullwidth == 'no' ) {
				return $class;
			}
		}
		return 'container-fluid';
	}
}
add_filter( 'homeo_page_content_class', 'homeo_page_content_class', 1 , 1  );

if ( !function_exists('homeo_get_page_layout_configs') ) {
	function homeo_get_page_layout_configs() {
		global $post;
		if ( is_object($post) ) {
			$left = get_post_meta( $post->ID, 'apus_page_left_sidebar', true );
			$right = get_post_meta( $post->ID, 'apus_page_right_sidebar', true );

			switch ( get_post_meta( $post->ID, 'apus_page_layout', true ) ) {
			 	case 'left-main':
			 		if ( is_active_sidebar( $left ) ) {
				 		$configs['left'] = array( 'sidebar' => $left, 'class' => ' col-md-4 col-sm-12 col-xs-12'  );
				 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
				 	}
			 		break;
			 	case 'main-right':
			 		if ( is_active_sidebar( $right ) ) {
				 		$configs['right'] = array( 'sidebar' => $right,  'class' => ' col-md-4 col-sm-12 col-xs-12' ); 
				 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
				 	}
			 		break;
		 		case 'main':
		 			$configs['main'] = array( 'class' => 'col-xs-12 clearfix' );
		 			break;
			 	default:
			 		if ( is_active_sidebar( 'sidebar-default' ) ) {
				 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => ' col-md-4 col-sm-12 col-xs-12' ); 
				 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
				 	} else {
				 		$configs['main'] = array( 'class' => 'col-xs-12 clearfix full-default' );
				 	}
			 		break;
			}

			if ( empty($configs) ) {
				if ( is_active_sidebar( 'sidebar-default' ) ) {
			 		$configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'col-md-4 col-sm-12 col-xs-12' ); 
			 		$configs['main'] = array( 'class' => 'col-md-8 col-sm-12 col-xs-12' );
			 	} else {
			 		$configs['main'] = array( 'class' => 'col-xs-12 clearfix full-default' );
			 	}
			}
		} else {
			$configs['main'] = array( 'class' => 'col-xs-12' );
		}
		return $configs; 
	}
}

if ( !function_exists( 'homeo_random_key' ) ) {
    function homeo_random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }
}

if ( !function_exists('homeo_substring') ) {
    function homeo_substring($string, $limit, $afterlimit = '[...]') {
        if ( empty($string) ) {
        	return $string;
        }
       	$string = explode(' ', wp_strip_all_tags( $string ), $limit);

        if (count($string) >= $limit) {
            array_pop($string);
            $string = implode(" ", $string) .' '. $afterlimit;
        } else {
            $string = implode(" ", $string);
        }
        $string = preg_replace('`[[^]]*]`','',$string);
        return strip_shortcodes( $string );
    }
}

function homeo_is_apus_framework_activated() {
	return defined('APUS_FRAMEWORK_VERSION') ? true : false;
}

function homeo_is_cmb2_activated() {
	return defined('CMB2_LOADED') ? true : false;
}

function homeo_is_woocommerce_activated() {
	return class_exists( 'woocommerce' ) ? true : false;
}

function homeo_is_revslider_activated() {
	return class_exists( 'RevSlider' ) ? true : false;
}

function homeo_is_mailchimp_activated() {
	return class_exists( 'MC4WP_Form_Manager' ) ? true : false;
}

function homeo_is_wp_realestate_activated() {
	return class_exists( 'WP_RealEstate' ) ? true : false;
}

function homeo_is_wp_realestate_wc_paid_listings_activated() {
	return class_exists( 'WP_RealEstate_Wc_Paid_Listings' ) ? true : false;
}

function homeo_is_wp_private_message() {
	return class_exists( 'WP_Private_Message' ) ? true : false;
}


function homeo_header_footer_templates( $template ) {
	$post_type = get_post_type();
	if ( $post_type ) {
		$custom_post_types = array( 'apus_footer', 'apus_header', 'apus_megamenu' );
		if ( in_array( $post_type, $custom_post_types ) ) {
			if ( is_single() ) {
				$post_type = str_replace('_', '-', $post_type);
				return get_template_directory() . '/single-' . $post_type.'.php';
			}
		}
	}

	return $template;
}
add_filter( 'template_include', 'homeo_header_footer_templates' );