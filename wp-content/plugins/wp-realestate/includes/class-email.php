<?php
/**
 * Price
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Email {
	
	public static $emails_vars;

	public static function init() {
		// Ajax endpoints.
		add_action( 'wre_ajax_wp_realestate_ajax_contact_form',  array(__CLASS__,'process_send_contact') );
		
		// compatible handlers.
		add_action( 'wp_ajax_wp_realestate_ajax_contact_form',  array(__CLASS__,'process_send_contact') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_contact_form',  array(__CLASS__,'process_send_contact') );
	}

	public static function wp_mail( $author_email, $subject, $content, $headers, $attachments = null) {
		if ( !preg_match( '%<html[>\s].*</html>%is', $content ) ) {
			$header = apply_filters( 'wp-realestate-mail-html-header',
				'<!doctype html>
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset='.get_bloginfo( 'charset' ).'" />
			<title>' . esc_html( $subject ) . '</title>
			</head>
			<body>
			', $subject );

			$footer = apply_filters( 'wp-realestate-mail-html-footer',
						'</body>
			</html>' );

			$content = $header . wpautop( $content ) . $footer;
		}
		
		return wp_mail( $author_email, $subject, $content, $headers, $attachments );
	}

	public static function process_send_contact() {
		$is_form_filled = ! empty( $_POST['email'] ) && ! empty( $_POST['name'] ) && ! empty( $_POST['message'] ) && ! empty( $_POST['post_id'] );
		if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_RealEstate_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$is_form_filled = false;
			}
		}
		$post_type = get_post_type( $_POST['post_id'] );
		$author_email = get_option('admin_email');
		$subject = $content = '';
		if ( $post_type == 'agent' ) {
			$author_email = get_post_meta( $_POST['post_id'], WP_REALESTATE_AGENT_PREFIX.'email', true );
			$subject = wp_realestate_get_option('contact_form_notice_subject');
			$content = wp_realestate_get_option('contact_form_notice_content');
		} elseif ( $post_type == 'agency' ) {
		    $author_email = get_post_meta( $_POST['post_id'], WP_REALESTATE_AGENCY_PREFIX.'email', true );
		    $subject = wp_realestate_get_option('contact_form_notice_subject');
			$content = wp_realestate_get_option('contact_form_notice_content');
		} elseif ( $post_type == 'property' ) {
			$author_id = get_post_field ('post_author', $_POST['post_id'] );
			if ( WP_RealEstate_User::is_agent($author_id) ) {
				$agent_id = WP_RealEstate_User::get_agent_by_user_id($author_id);
				$author_email = get_post_meta( $agent_id, WP_REALESTATE_AGENT_PREFIX.'email', true );
			} elseif ( WP_RealEstate_User::is_agency($author_id) ) {
				$agency_id = WP_RealEstate_User::get_agency_by_user_id($author_id);
				$author_email = get_post_meta( $agency_id, WP_REALESTATE_AGENCY_PREFIX.'email', true );
			} else {
				$author_email = get_the_author_meta( 'user_email' , $author_id );
			}

			$property_title = get_the_title($_POST['post_id']);
			$property_url = get_permalink($_POST['post_id']);


			$subject = wp_realestate_get_option('property_contact_form_notice_subject');
			$content = wp_realestate_get_option('property_contact_form_notice_content');

			$subject = str_replace('{{property_title}}', $property_title, $subject);
			$content = str_replace('{{property_title}}', $property_title, $content);
			$content = str_replace('{{property_url}}', $property_url, $content);
		}

		if ( $is_form_filled && $author_email ) {
			
	        $email = sanitize_text_field( $_POST['email'] );
	        $phone = sanitize_text_field( $_POST['phone'] );
	        $user_name = sanitize_text_field( $_POST['name'] );
	        $message = sanitize_text_field( $_POST['message'] );

	        $subject = str_replace('{{user_name}}', $user_name, $subject);

	        
	        $content = str_replace('{{user_name}}', $user_name, $content);
	        $content = str_replace('{{website_url}}', home_url(), $content);
	        $content = str_replace('{{website_name}}', get_bloginfo( 'name' ), $content);
	        $content = str_replace('{{email}}', $email, $content);
	        $content = str_replace('{{phone}}', $phone, $content);
	        $content = str_replace('{{message}}', $message, $content);
	        
	        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email, $email );
	        
	        $result = false;
			$result = WP_RealEstate_Email::wp_mail( $author_email, $subject, $content, $headers );
	        if ( $result ) {
	        	$return = array( 'status' => true, 'msg' => esc_html__('Your message has been successfully sent.', 'wp-realestate') );
	        } else {
	        	$return = array( 'status' => false, 'msg' => esc_html__('An error occurred when sending an email.', 'wp-realestate') );
	        }
	    } else {
	    	$return = array( 'status' => false, 'msg' => esc_html__('Form has been not filled correctly.', 'wp-realestate') );
	    }
	    echo wp_json_encode($return);
	   	exit;
	}

	public static function emails_vars() {
		self::$emails_vars = apply_filters( 'wp-realestate-emails-vars', array(
			'admin_notice_add_new_listing' => array(
				'subject' => array( 'property_title' ),
				'content' => array( 'property_title', 'property_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_updated_listing' => array(
				'subject' => array( 'property_title' ),
				'content' => array( 'property_title', 'property_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_expiring_listing' => array(
				'subject' => array( 'property_title' ),
				'content' => array( 'property_title', 'property_url', 'website_url', 'website_name', 'property_admin_edit_url' )
			),
			'user_notice_expiring_listing' => array(
				'subject' => array( 'property_title' ),
				'content' => array( 'property_title', 'property_url', 'website_url', 'website_name', 'dashboard_url', 'my_properties' )
			),
			'saved_search_notice' => array(
				'subject' => array( 'saved_search_title' ),
				'content' => array( 'saved_search_title', 'properties_found', 'website_url', 'website_name', 'email_frequency_type', 'properties_saved_search_url' )
			),
			'contact_form_notice' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'message', 'email', 'phone', 'website_url', 'website_name' )
			),
			'property_contact_form_notice' => array(
				'subject' => array( 'user_name', 'property_url' ),
				'content' => array( 'user_name', 'property_title', 'property_url', 'message', 'email', 'phone', 'website_url', 'website_name' )
			),
			'user_register_auto_approve' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'login_url', 'website_url', 'website_name' )
			),
			'user_register_need_approve' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'approve_url', 'website_url', 'website_name' )
			),
			'user_register_approved' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'login_url', 'dashboard_url', 'website_url', 'website_name' )
			),
			'user_register_denied' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'website_url', 'website_name' )
			),
			'user_reset_password' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'new_password', 'website_url', 'website_name' )
			),
		));
		return self::$emails_vars;
	}

	public static function display_email_vars($key, $type = 'subject') {
		self::emails_vars();
		$output = '';
		if ( !empty(self::$emails_vars[$key][$type]) ) {
			$i = 1;
			foreach (self::$emails_vars[$key][$type] as $value) {
				$output .= '{{'.$value.'}}'.($i < count(self::$emails_vars[$key][$type]) ? ', ' : '');
				$i++;
			}
		}
		return $output;
	}

	public static function render_email_vars($args, $key, $type = 'subject') {
		self::emails_vars();
		$output = wp_realestate_get_option($key.'_'.$type);
		if ( !empty(self::$emails_vars[$key][$type]) ) {
			$vars = self::$emails_vars[$key][$type];
			foreach ($vars as $var) {
				if ( strpos($output, '{{'.$var.'}}') !== false ) {
					if ( isset($args[$var]) ) {
						$value = $args[$var];
					} elseif ( is_callable( array('WP_RealEstate_Email', $var) ) ) {
						$value = call_user_func( array('WP_RealEstate_Email', $var), $args );
					} else {
						$value = apply_filters('wp-realestate-render-email-var-'.$var, '', $args);
					}
					$output = str_replace('{{'.$var.'}}', $value, $output);
				}
			}
		}
		return apply_filters( 'wp-realestate-render-emails-vars', $output, $args, $key, $type );
	}

	public static function property_title($args) {
		$output = '';
		if ( isset($args['property']) && !empty($args['property']->post_title) ) {
			$output = $args['property']->post_title;
		}
		return $output;
	}

	public static function property_url($args) {
		$output = '';
		if ( !empty($args['property']) ) {
			$output = get_permalink($args['property']);
		}
		return $output;
	}

	public static function website_url($args) {
		$output = home_url();
		
		return $output;
	}

	public static function website_name($args) {
		$output = get_bloginfo( 'name' );
		
		return $output;
	}

	public static function dashboard_url($args) {
		$output = '';
		$page_id = wp_realestate_get_option('user_dashboard_page_id');
		if ( !empty($page_id) ) {
			$output = get_permalink($page_id);
		} else {
			$output = home_url();
		}
		return $output;
	}

	public static function login_url($args) {
		$page_id = wp_realestate_get_option('login_register_page_id');
		if ( !empty($page_id) ) {
			$output = get_permalink($page_id);
		} else {
			$output = home_url();
		}
		return $output;
	}
	
	public static function my_properties($args) {
		$output = '';
		$my_properties_page_id = wp_realestate_get_option('my_properties_page_id');
		$output = get_permalink($my_properties_page_id);
		return $output;
	}

	public static function property_admin_edit_url($args) {
		$output = '';
		if ( !empty($args['property']) ) {
			$output = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $args['property']->ID ) );
		}
		return $output;
	}

	public static function author($args) {
		$output = '';
		if ( !empty($args['property']) && !empty($args['property']->post_author) ) {
			$output = get_the_author_meta( 'display_name', $args['property']->post_author );
		}
		return $output;
	}

	public static function agent_name($args) {
		$output = '';
		if ( isset($args['agent']) && !empty($args['agent']->post_title) ) {
			$output = $args['agent']->post_title;
		}
		return $output;
	}

	public static function approve_url($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->ID) ) {
			$approve_user_page_id = wp_realestate_get_option('approve_user_page_id');
			$admin_url = get_permalink($approve_user_page_id);

			$user_id = $args['user_obj']->ID;
            $code = get_user_meta($user_id, 'account_approve_key', true);
			$output = add_query_arg(array('action' => 'wp_realestate_approve_user', 'user_id' => $user_id, 'approve-key' => $code), $admin_url);
		}
		return $output;
	}

	public static function user_name($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->display_name) ) {
			$output = $args['user_obj']->display_name;
		}
		return $output;
	}
	
	public static function user_email($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->user_email) ) {
			$output = $args['user_obj']->user_email;
		}
		return $output;
	}

}

WP_RealEstate_Email::init();