<?php
/**
 * Shortcodes
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Shortcodes {
	/**
	 * Initialize shortcodes
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
	    add_action( 'wp', array( __CLASS__, 'check_logout' ) );

	    // login | register
		add_shortcode( 'wp_realestate_logout', array( __CLASS__, 'logout' ) );
	    add_shortcode( 'wp_realestate_login', array( __CLASS__, 'login' ) );
	    add_shortcode( 'wp_realestate_register', array( __CLASS__, 'register' ) );

	    // profile
	    add_shortcode( 'wp_realestate_user_dashboard', array( __CLASS__, 'user_dashboard' ) );
	    add_shortcode( 'wp_realestate_change_password', array( __CLASS__, 'change_password' ) );
	    add_shortcode( 'wp_realestate_change_profile', array( __CLASS__, 'change_profile' ) );
    	add_shortcode( 'wp_realestate_approve_user', array( __CLASS__, 'approve_user' ) );

    	// user
		add_shortcode( 'wp_realestate_submission', array( __CLASS__, 'submission' ) );
	    add_shortcode( 'wp_realestate_my_properties', array( __CLASS__, 'my_properties' ) );
	    add_shortcode( 'wp_realestate_my_saved_search', array( __CLASS__, 'my_saved_search' ) );
	    add_shortcode( 'wp_realestate_my_property_favorite', array( __CLASS__, 'my_property_favorites' ) );
	    add_shortcode( 'wp_realestate_property_compare', array( __CLASS__, 'my_property_compare' ) );
	    add_shortcode( 'wp_realestate_user_reviews', array( __CLASS__, 'user_reviews' ) );

	    // agency
	    add_shortcode( 'wp_realestate_agency_members', array( __CLASS__, 'agency_members' ) );

	    // list
	    add_shortcode( 'wp_realestate_properties', array( __CLASS__, 'properties' ) );
	    add_shortcode( 'wp_realestate_agents', array( __CLASS__, 'agents' ) );
	    add_shortcode( 'wp_realestate_agencies', array( __CLASS__, 'agencies' ) );
	}

	/**
	 * Logout checker
	 *
	 * @access public
	 * @param $wp
	 * @return void
	 */
	public static function check_logout( $wp ) {
		$post = get_post();

		if ( is_object( $post ) ) {
			if ( strpos( $post->post_content, '[wp_realestate_logout]' ) !== false ) {
				wp_redirect( html_entity_decode( wp_logout_url( home_url( '/' ) ) ) );
				exit();
			}
		}
	}

	/**
	 * Logout
	 *
	 * @access public
	 * @return void
	 */
	public static function logout( $atts ) {}

	/**
	 * Login
	 *
	 * @access public
	 * @return string
	 */
	public static function login( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/login' );
	}

	/**
	 * Login
	 *
	 * @access public
	 * @return string
	 */
	public static function register( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/register' );
	}

	/**
	 * Submission index
	 *
	 * @access public
	 * @return string|void
	 */
	public static function submission( $atts ) {
	    if ( ! is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    
		$form = WP_RealEstate_Submit_Form::get_instance();

		return $form->output();
	}

	public static function edit_form( $atts ) {
	    if ( ! is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    
		$form = WP_RealEstate_Edit_Form::get_instance();

		return $form->output();
	}
	/**
	 * Submission index
	 *
	 * @access public
	 * @param $atts
	 * @return void
	 */
	public static function my_properties( $atts ) {
		if ( ! is_user_logged_in() ) {
			return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
		}
		if ( ! empty( $_REQUEST['action'] ) ) {
			$action = sanitize_title( $_REQUEST['action'] );

			if ( $action == 'edit' ) {
				return self::edit_form($atts);
			}
		}
		return WP_RealEstate_Template_Loader::get_template_part( 'submission/my-properties' );
	}
	
	/**
	 * Agent dashboard
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function user_dashboard( $atts ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/user-dashboard', array( 'user_id' => $user_id ) );
	    } else {
	    	return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	}

	/**
	 * Change password
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function change_password( $atts ) {
		if ( ! is_user_logged_in() ) {
			return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
		}

		return WP_RealEstate_Template_Loader::get_template_part( 'misc/password-form' );
	}

	/**
	 * Change profile
	 *
	 * @access public
	 * @param $atts
	 * @return void
	 */
	public static function change_profile( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    
	    $metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
	    $metaboxes_form = array();
	    $user_id = get_current_user_id();
	    if ( WP_RealEstate_User::is_agent($user_id) ) {
	    	if ( ! isset( $metaboxes[ WP_REALESTATE_AGENT_PREFIX . 'fields_front' ] ) ) {
				return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-realestate' );
			}
			$metaboxes_form = $metaboxes[ WP_REALESTATE_AGENT_PREFIX . 'fields_front' ];
			$post_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
	    } elseif( WP_RealEstate_User::is_agency($user_id) ) {
	    	if ( ! isset( $metaboxes[ WP_REALESTATE_AGENCY_PREFIX . 'fields_front' ] ) ) {
				return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-realestate' );
			}
			$metaboxes_form = $metaboxes[ WP_REALESTATE_AGENCY_PREFIX . 'fields_front' ];
			$post_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
	    } else {
	    	return WP_RealEstate_Template_Loader::get_template_part( 'misc/profile-form-normal' );
	    }

		if ( !$post_id ) {
			return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
		}

		wp_enqueue_script('google-maps');
		wp_enqueue_script('select2');
		wp_enqueue_style('select2');

		return WP_RealEstate_Template_Loader::get_template_part( 'misc/profile-form', array( 'post_id' => $post_id, 'metaboxes_form' => $metaboxes_form  ) );
	}

	public static function approve_user($atts) {
	    return WP_RealEstate_Template_Loader::get_template_part( 'misc/approve-user' );
	}
	
	public static function my_saved_search( $atts ) {
		if ( !is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

	    $user_id = get_current_user_id();
	    if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_vars = array(
		    'post_type' => 'saved_search',
		    'posts_per_page'    => get_option('posts_per_page'),
		    'paged'    			=> $paged,
		    'post_status' => 'publish',
		    'fields' => 'ids',
		    'author' => $user_id,
		);
		if ( isset($_GET['search']) ) {
			$query_vars['s'] = $_GET['search'];
		}
		if ( isset($_GET['orderby']) ) {
			switch ($_GET['orderby']) {
				case 'menu_order':
					$query_vars['orderby'] = array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
						'ID'         => 'DESC',
					);
					break;
				case 'newest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'DESC';
					break;
				case 'oldest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'ASC';
					break;
			}
		}
		$alerts = WP_RealEstate_Query::get_posts($query_vars);

		return WP_RealEstate_Template_Loader::get_template_part( 'misc/my-saved-searches', array( 'alerts' => $alerts ) );
	}

	public static function my_property_favorites( $atts ) {
		if ( !is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    $property_ids = WP_RealEstate_Favorite::get_property_favorites();
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/property-favorites', array( 'property_ids' => $property_ids ) );
	}

	public static function my_property_compare( $atts ) {
		
	    $property_ids = WP_RealEstate_Compare::get_compare_items();
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/property-compare', array( 'property_ids' => $property_ids ) );
	}

	public static function user_reviews( $atts ) {
		if ( !is_user_logged_in() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

		return WP_RealEstate_Template_Loader::get_template_part( 'misc/user-reviews' );
	}

	public static function agency_members( $atts ) {
		if ( !is_user_logged_in() || !WP_RealEstate_User::is_agency() ) {
		    return WP_RealEstate_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

	    return WP_RealEstate_Template_Loader::get_template_part( 'misc/agency-members' );
	}

	public static function properties( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_realestate_get_option('number_properties_per_page', 10)
		));
		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'property',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);
		$params = true;
		if ( WP_RealEstate_Abstract_Filter::has_filter() ) {
			$params = $_GET;
		} elseif (WP_RealEstate_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}

		$properties = WP_RealEstate_Query::get_posts($query_args, $params);
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/properties', array( 'properties' => $properties, 'atts' => $atts ) );
	}

	public static function agents( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_realestate_get_option('number_agents_per_page', 10)
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'agent',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);
		$params = true;
		if ( WP_RealEstate_Abstract_Filter::has_filter() ) {
			$params = $_GET;
		} elseif (WP_RealEstate_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		$agents = WP_RealEstate_Query::get_posts($query_args, $params);
		
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/agents', array( 'agents' => $agents, 'atts' => $atts ) );
	}

	public static function agencies( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_realestate_get_option('number_agencies_per_page', 10),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'agency',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);
		$params = true;
		if ( WP_RealEstate_Abstract_Filter::has_filter() ) {
			$params = $_GET;
		} elseif (WP_RealEstate_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		$agencies = WP_RealEstate_Query::get_posts($query_args, $params);
		
		return WP_RealEstate_Template_Loader::get_template_part( 'misc/agencies', array( 'agencies' => $agencies, 'atts' => $atts ) );
	}
}

WP_RealEstate_Shortcodes::init();
