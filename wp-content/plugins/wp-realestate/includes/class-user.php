<?php
/**
 * User
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_User {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_user_roles' ) );
		add_action( 'init', array( __CLASS__, 'role_caps' ) );

		// Ajax endpoints.
		add_action( 'wre_ajax_wp_realestate_ajax_login',  array( __CLASS__, 'process_login' ) );
		add_action( 'wre_ajax_wp_realestate_ajax_forgotpass',  array( __CLASS__, 'process_forgot_password' ) );
		add_action( 'wre_ajax_wp_realestate_ajax_register',  array( __CLASS__, 'process_register' ) );
		
		add_action( 'wre_ajax_wp_realestate_ajax_change_password',  array(__CLASS__,'process_change_password') );
		add_action( 'wre_ajax_wp_realestate_ajax_resend_approve_account',  array(__CLASS__,'process_resend_approve_account') );

		// compatible handlers.
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_login',  array( __CLASS__, 'process_login' ) );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_forgotpass',  array( __CLASS__, 'process_forgot_password' ) );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_register',  array( __CLASS__, 'process_register' ) );
		
		add_action( 'wp_ajax_wp_realestate_ajax_change_password',  array(__CLASS__,'process_change_password') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_change_password',  array( __CLASS__, 'process_change_password' ) );

		//
		add_action( 'user_register', array( __CLASS__, 'registration_save' ), 10, 1 );
		add_action( 'cmb2_after_init', array( __CLASS__, 'process_change_profile' ) );
		add_action( 'wp', array( __CLASS__, 'process_change_profile_normal' ) );

		add_action( 'delete_user', array(__CLASS__,'process_delete_user'), 10, 2 );

		//
		add_filter( 'wp_authenticate_user', array( __CLASS__, 'admin_user_auth_callback' ), 11, 2 );

		// action
		add_action( 'load-users.php', array( __CLASS__, 'process_update_user_action' ) );
		add_filter( 'wp_realestate_new_user_approve_validate_status_update', array( __CLASS__, 'validate_status_update' ), 10, 3 );

		add_action( 'wp_realestate_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		add_action( 'wp_realestate_new_user_approve_deny_user', array( __CLASS__, 'deny_user' ) );
		
		// resend approve account
		add_action( 'wp_ajax_wp_realestate_ajax_resend_approve_account',  array(__CLASS__,'process_resend_approve_account') );
		add_action( 'wp_ajax_nopriv_wp_realestate_ajax_resend_approve_account',  array(__CLASS__,'process_resend_approve_account') );

		// Filters
		add_filter( 'user_row_actions', array( __CLASS__, 'user_table_actions' ), 10, 2 );
		add_filter( 'manage_users_columns', array( __CLASS__, 'add_column' ) );
		add_filter( 'manage_users_custom_column', array( __CLASS__, 'status_column' ), 10, 3 );

		add_action( 'restrict_manage_users', array( __CLASS__, 'status_filter' ), 10, 1 );
		add_action( 'pre_user_query', array( __CLASS__, 'filter_by_status' ) );

		// approve user
		add_action( 'wp', array( __CLASS__, 'process_approve_user' ) );


		// backend user profile
		add_action( 'show_user_profile', array( __CLASS__, 'backend_user_profile_fields') );
		add_action( 'edit_user_profile', array( __CLASS__, 'backend_user_profile_fields') );
		// backend save user profile
		add_action( 'personal_options_update', array( __CLASS__, 'backend_save_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'backend_save_user_profile_fields' ) );

		// get avatar
		add_filter('get_avatar', array( __CLASS__, 'get_avatar'), 100, 5 );
	}

	public static function add_user_roles() {
	    add_role(
            'wp_realestate_agency', esc_html__('Agency', 'wp-realestate'), array(
		        'read' => false,
		        'edit_posts' => false,
		        'delete_posts' => false,
            )
	    );
	    add_role(
            'wp_realestate_agent', esc_html__('Agent', 'wp-realestate'), array(
		        'read' => false,
		        'edit_posts' => false,
		        'delete_posts' => false,
            )
	    );
	}

	public static function role_caps() {
	    if ( current_user_can('wp_realestate_agency') ) {
	        $subscriber = get_role('wp_realestate_agency');
		    $subscriber->add_cap('upload_files');
		    $subscriber->add_cap('edit_post');
		    $subscriber->add_cap('edit_published_pages');
		    $subscriber->add_cap('edit_others_pages');
		    $subscriber->add_cap('edit_others_posts');
	    }
	    
	    if ( current_user_can('wp_realestate_agent') ) {
		    $subscriber = get_role('wp_realestate_agent');
		    $subscriber->add_cap('upload_files');
		    $subscriber->add_cap('edit_post');
		    $subscriber->add_cap('edit_published_pages');
		    $subscriber->add_cap('edit_others_pages');
		    $subscriber->add_cap('edit_others_posts');
	    }

	    if ( current_user_can('subscriber') ) {
		    $subscriber = get_role('subscriber');
		    $subscriber->add_cap('upload_files');
		    $subscriber->add_cap('edit_post');
		    $subscriber->add_cap('edit_published_pages');
		    $subscriber->add_cap('edit_others_pages');
		    $subscriber->add_cap('edit_others_posts');
	    }
	}

	public static function is_agent_can_add_submission($user_id = null) {
		if ( empty($user_id) ) {
			$user_id = get_current_user_id();
		}
    	$return = self::is_agent($user_id);
		return apply_filters( 'wp-realestate-is-agent-can-add-submission', $return, $user_id );
	}

	public static function is_agent_can_edit_job($job_id) {
		$return = true;
		if ( ! is_user_logged_in() || ! $job_id ) {
			$return = false;
		} else {
			$job = get_post( $job_id );
			if ( ! $job || ( absint( $job->post_author ) !== get_current_user_id() && ! current_user_can( 'edit_post', $job_id ) ) ) {
				$return = false;
			}
		}

		return apply_filters( 'wp-realestate-is-agent-can-edit-job', $return, $job_id );
	}

	public static function is_agent($user_id = null) {
		global $sitepress;
		if ( empty($user_id) && is_user_logged_in() ) {
	        $user_id = get_current_user_id();
	    }
	    $agent_id = get_user_meta($user_id, 'agent_id', true);
	    $agent_id = $agent_id > 0 ? $agent_id : 0;
	    if ($agent_id > 0) {
	    	$agent_id = WP_RealEstate_WPML::get_icl_object_id($agent_id, 'agent');

	        $post_agent = get_post($agent_id);
	        if ($post_agent && isset($post_agent->ID)) {
	            return true;
	        }
	    }
	    return false;
	}

	public static function is_agency($user_id = 0) {
	    if ( empty($user_id) && is_user_logged_in() ) {
	        $user_id = get_current_user_id();
	    }
	    $agency_id = get_user_meta($user_id, 'agency_id', true);
	    $agency_id = $agency_id > 0 ? $agency_id : 0;
	    if ($agency_id > 0) {
	    	$agency_id = WP_RealEstate_WPML::get_icl_object_id($agency_id, 'agency');

	        $post_agency = get_post($agency_id);
	        if ($post_agency && isset($post_agency->ID)) {
	            return true;
	        }
	    }
	    return false;
	}

	public static function get_user_by_agent_id($agent_id = 0) {
	    $user_id = get_post_meta($agent_id, WP_REALESTATE_AGENT_PREFIX . 'user_id', true);
	    $user_id = $user_id > 0 ? $user_id : 0;
	    $user_obj = get_user_by('ID', $user_id);
	    if ($user_obj) {
	        return $user_obj->ID;
	    }
	    return false;
	}

	public static function get_agent_by_user_id($user_id = 0) {
		global $sitepress;
	    $agent_id = get_user_meta($user_id, 'agent_id', true);
	    $agent_id = $agent_id > 0 ? $agent_id : 0;
	    if ($agent_id > 0) {
	    	$agent_id = WP_RealEstate_WPML::get_icl_object_id($agent_id, 'agent');

	        $post_agent = get_post($agent_id);
	        if ($post_agent) {
	            return $post_agent->ID;
	        }
	    }
	    return false;
	}

	public static function get_user_by_agency_id($agency_id = 0) {
	    $user_id = get_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX.'user_id', true);
	    $user_id = $user_id > 0 ? $user_id : 0;
	    $user_obj = get_user_by('ID', $user_id);
	    if ($user_obj) {
	        return $user_obj->ID;
	    }
	    return false;
	}

	public static function get_agency_by_user_id($user_id = 0) {
		global $sitepress;
	    $agency_id = get_user_meta($user_id, 'agency_id', true);
	    $agency_id = $agency_id > 0 ? $agency_id : 0;
	    if ($agency_id > 0) {
	    	$agency_id = WP_RealEstate_WPML::get_icl_object_id($agency_id, 'agency');

	        $post_agency = get_post($agency_id);
	        if ($post_agency) {
	            return $post_agency->ID;
	        }
	    }
	    return false;
	}

	public static function is_user_can_edit_property($property_id) {
		$return = true;
		if ( ! is_user_logged_in() || ! $property_id ) {
			$return = false;
		} else {
			$property = get_post( $property_id );
			if ( ! $property || ( absint( $property->post_author ) !== get_current_user_id() && ! current_user_can( 'edit_post', $property_id ) ) ) {
				$return = false;
			}
		}

		return apply_filters( 'wp-realestate-is-user-can-edit-property', $return, $property_id );
	}
	
	public static function process_login() {
   		check_ajax_referer( 'ajax-login-nonce', 'security_login' );
   		
   		$info = array();
   		
   		$info['user_login'] = isset($_POST['username']) ? $_POST['username'] : '';
	    $info['user_password'] = isset($_POST['password']) ? $_POST['password'] : '';
	    $info['remember'] = isset($_POST['remember']) ? true : false;
		
		if ( empty($info['user_login']) || empty($info['user_password']) ) {
            echo json_encode(array(
            	'status' => false,
            	'msg' => __('Please fill all form fields', 'wp-realestate')
            ));
            die();
        }

		if (filter_var($info['user_login'], FILTER_VALIDATE_EMAIL)) {
            $user_obj = get_user_by('email', $info['user_login']);
        } else {
            $user_obj = get_user_by('login', $info['user_login']);
        }
        $user_id = isset($user_obj->ID) ? $user_obj->ID : '0';
        $user_login_auth = self::get_user_status($user_id);
        if ( $user_login_auth == 'pending' && isset($user_obj->ID) ) {
            echo json_encode(array(
            	'status' => false,
            	'msg' => self::login_msg($user_obj)
            ));
            die();
        } elseif ( $user_login_auth == 'denied' && isset($user_obj->ID) ) {
        	echo json_encode(array(
            	'status' => false,
            	'msg' => __('Your account denied', 'wp-realestate')
            ));
            die();
        }

		$user_signon = wp_signon( $info, false );
	    if ( is_wp_error($user_signon) ){
			$result = json_encode(array('status' => false, 'msg' => esc_html__('Wrong username or password. Please try again!!!', 'wp-realestate')));
	    } else {
			wp_set_current_user($user_signon->ID);
	        $result = json_encode( array( 'status' => true, 'msg' => esc_html__('Signin successful, redirecting...', 'wp-realestate') ) );
	    }

   		echo trim($result);
   		die();
	}

	public static function process_forgot_password() {
		// First check the nonce, if it fails the function will break
	    check_ajax_referer( 'ajax-lostpassword-nonce', 'security_lostpassword' );
		
		if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_RealEstate_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$error = esc_html__( 'Captcha is not valid', 'wp-realestate' );

				echo json_encode(array('status' => false, 'msg' => $error));
				wp_die();
			}
		}
		
		global $wpdb;
		
		$account = isset($_POST['user_login']) ? $_POST['user_login'] : '';
		
		if( empty( $account ) ) {
			$error = esc_html__( 'Enter an username or e-mail address.', 'wp-realestate' );
		} else {
			if(is_email( $account )) {
				if( email_exists($account) ) {
					$get_by = 'email';
				} else {
					$error = esc_html__( 'There is no user registered with that email address.', 'wp-realestate' );			
				}
			} else if (validate_username( $account )) {
				if( username_exists($account) ) {
					$get_by = 'login';
				} else {
					$error = esc_html__( 'There is no user registered with that username.', 'wp-realestate' );				
				}
			} else {
				$error = esc_html__( 'Invalid username or e-mail address.', 'wp-realestate' );		
			}
		}	
		
		do_action('wp-realestate-process-forgot-password', $_POST);

		if ( empty($error) ) {
			if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
	            $user_obj = get_user_by('email', $account);
	        } else {
	            $user_obj = get_user_by('login', $account);
	        }
	        $user_id = isset($user_obj->ID) ? $user_obj->ID : '0';
	        $user_login_auth = self::get_user_status($user_id);
	        if ( $user_login_auth == 'pending' && isset($user_obj->ID) ) {
	            echo json_encode(array(
	            	'status' => false,
	            	'msg' => self::login_msg($user_obj)
	            ));
	            die();
	        } elseif ( $user_login_auth == 'denied' && isset($user_obj->ID) ) {
	            echo json_encode(array(
	            	'status' => false,
	            	'msg' => __('Your account denied.', 'wp-realestate')
	            ));
	            die();
	        }

			$random_password = wp_generate_password();
			$user = get_user_by( $get_by, $account );
			
			$update_user = wp_update_user( array( 'ID' => $user->ID, 'user_pass' => $random_password ) );
				
			if( $update_user ) {
				$from = get_option('admin_email');
				
				$email_to = $user->user_email;
				$subject = wp_realestate_get_option('user_reset_password_subject');
				$subject = str_replace('{{user_name}}', $user->display_name, $subject);

				$content = wp_realestate_get_option('user_reset_password_content');
				$content = str_replace('{{new_password}}', $random_password, $content);
				$content = str_replace('{{user_name}}', $user_name, $content);
				$content = str_replace('{{user_email}}', $email_to, $content);
				$content = str_replace('{{website_name}}', get_bloginfo( 'name' ), $content);
				$content = str_replace('{{website_url}}', home_url(), $content);

					
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $from );
					
				$mail = WP_RealEstate_Email::wp_mail( $email_to, $subject, $content, $headers );
				
				if( $mail ) {
					$success = esc_html__( 'Check your email address for you new password.', 'wp-realestate' );
				} else {
					$error = esc_html__( 'System is unable to send you mail containg your new password.', 'wp-realestate' );						
				}
			} else {
				$error =  esc_html__( 'Oops! Something went wrong while updating your account.', 'wp-realestate' );
			}
		}
	
		if ( ! empty( $error ) ) {
			echo json_encode( array('status'=> false, 'msg'=> $error) );
		}
				
		if ( ! empty( $success ) ) {
			echo json_encode( array('status' => true, 'msg'=> $success ) );	
		}
		die();
	}

	public static function process_register() {
		global $reg_errors;

		check_ajax_referer( 'ajax-register-nonce', 'security_register' );
		
        self::registration_validation( $_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirmpassword'] );
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {

	 		$userdata = array(
		        'user_login' => sanitize_user( $_POST['username'] ),
		        'user_email' => sanitize_email( $_POST['email'] ),
		        'user_pass' => $_POST['password'],
		        'role' => 'subscriber',
	        );
	 		if ( !empty($_POST['role']) ) {
	        	$userdata['role'] = $_POST['role'];
	        }

	        $user_id = wp_insert_user( $userdata );
	        if ( ! is_wp_error( $user_id ) ) {

	        	if ( wp_realestate_get_option('users_requires_approval', 'auto') != 'auto' ) {
	        		$user_data = get_userdata($user_id);
	        		$jsondata = array(
	            		'status' => true,
	            		'msg' => self::register_msg($user_data),
	            		'redirect' => false
	            	);
	        	} else {
	        		$jsondata = array(
	        			'status' => true,
	        			'msg' => esc_html__( 'You have registered, redirecting ...', 'wp-realestate' ),
	        			'redirect' => true
	        		);
	        		wp_set_auth_cookie($user_id);
	        	}
	        } else {
		        $jsondata = array('status' => false, 'msg' => esc_html__( 'Register user error!', 'wp-realestate' ) );
		    }
	    } else {
	    	$jsondata = array('status' => false, 'msg' => implode(', <br>', $reg_errors->get_error_messages()) );
	    }
	    echo json_encode($jsondata);
	    exit;
	}

	public static function registration_validation( $username, $email, $password, $confirmpassword ) {
		global $reg_errors;
		$reg_errors = new WP_Error;

		if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_RealEstate_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$reg_errors->add('field', esc_html__( 'Captcha is not valid', 'wp-realestate' ) );
			}
		}

		$page_id = wp_realestate_get_option('terms_conditions_page_id');
		if ( !empty($page_id) ) {
			if ( empty($_POST['terms_and_conditions']) ) {
				$reg_errors->add('field', esc_html__( 'Terms and Conditions are required', 'wp-realestate' ) );
			}
		}
		
		if ( empty( $username ) || empty( $password ) || empty( $email ) || empty( $confirmpassword ) ) {
		    $reg_errors->add('field', esc_html__( 'Required form field is missing', 'wp-realestate' ) );
		}

		if ( 4 > strlen( $username ) ) {
		    $reg_errors->add( 'username_length', esc_html__( 'Username too short. At least 4 characters is required', 'wp-realestate' ) );
		}

		if ( username_exists( $username ) ) {
	    	$reg_errors->add('user_name', esc_html__( 'The username already exists!', 'wp-realestate' ) );
		}

		if ( ! validate_username( $username ) ) {
		    $reg_errors->add( 'username_invalid', esc_html__( 'The username you entered is not valid', 'wp-realestate' ) );
		}

		if ( 5 > strlen( $password ) ) {
	        $reg_errors->add( 'password', esc_html__( 'Password length must be greater than 5', 'wp-realestate' ) );
	    }

	    if ( $password != $confirmpassword ) {
	        $reg_errors->add( 'password', esc_html__( 'Password must be equal Confirm Password', 'wp-realestate' ) );
	    }

	    if ( !is_email( $email ) ) {
		    $reg_errors->add( 'email_invalid', esc_html__( 'Email is not valid', 'wp-realestate' ) );
		}

		if ( email_exists( $email ) ) {
		    $reg_errors->add( 'email', esc_html__( 'Email Already in use', 'wp-realestate' ) );
		}
	}

	public static function registration_save($user_id) {
        $action = isset($_POST['action']) && $_POST['action'] != '' ? $_POST['action'] : '';
        $user_role = isset($_POST['role']) && $_POST['role'] != '' ? $_POST['role'] : '';
        $user_obj = get_user_by('ID', $user_id);


        if ($user_role == 'wp_realestate_agency') {
        	$post_title = str_replace(array('-', '_'), array(' ', ' '), $user_obj->display_name);
        	$display_name = $user_obj->display_name;

            $post_args = array(
                'post_title' => $post_title,
                'post_type' => 'agency',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $user_id,
            );

            if ( wp_realestate_get_option('users_requires_approval', 'auto') != 'auto' && $action == 'wp_realestate_ajax_register' ) {
            	$post_args['post_status'] = 'pending';
            }

            // Insert the post into the database
            $agency_id = wp_insert_post($post_args);

            update_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX . 'user_id', $user_id);
            update_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX . 'display_name', $display_name);
            update_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX . 'email', $user_obj->user_email);

            update_post_meta($agency_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));

            // custom fields saving
            do_action('wp_realestate_signup_custom_fields_save', 'agency', $agency_id);

            //
            update_user_meta($user_id, 'agency_id', $agency_id);
        } elseif ( $user_role == 'wp_realestate_agent' ) {
            $post_args = array(
                'post_title' => str_replace(array('-', '_'), array(' ', ' '), $user_obj->display_name),
                'post_type' => 'agent',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $user_id,
            );
            $post_status = 'publish';
            if ( wp_realestate_get_option('users_requires_approval', 'auto') != 'auto' && $action == 'wp_realestate_ajax_register' ) {
            	$post_status = 'pending';
            }
            $post_args['post_status'] = $post_status;
            
            // Insert the post into the database
            $agent_id = wp_insert_post($post_args);
            
            update_post_meta($agent_id, WP_REALESTATE_AGENT_PREFIX . 'user_id', $user_id);
            update_post_meta($agent_id, WP_REALESTATE_AGENT_PREFIX . 'display_name', $user_obj->display_name);
            update_post_meta($agent_id, WP_REALESTATE_AGENT_PREFIX . 'email', $user_obj->user_email);

            update_post_meta($agent_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));
            
            // custom fields saving
            do_action('wp_realestate_signup_custom_fields_save', 'agent', $agent_id);
            
            update_user_meta($user_id, 'agent_id', $agent_id);
        }

        if ( wp_realestate_get_option('users_requires_approval', 'auto') != 'auto' && $action == 'wp_realestate_ajax_register' ) {
            $code = WP_RealEstate_Mixes::random_key();
            update_user_meta($user_id, 'account_approve_key', $code);
        	update_user_meta($user_id, 'user_account_status', 'pending');

        	if ( wp_realestate_get_option('users_requires_approval', 'auto') == 'email_approve' ) {
				$user_email = stripslashes( $user_obj->user_email );
			} else {
				$user_email = get_option( 'admin_email', false );
			}

			$subject = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'subject');
			$content = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'content');

			$email_from = get_option( 'admin_email', false );
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
			// send the mail
			WP_RealEstate_Email::wp_mail( $user_email, $subject, $content, $headers );
        } else {
        	$user_email = stripslashes( $user_obj->user_email );
        	$subject = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'subject');
			$content = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'content');

			$email_from = get_option( 'admin_email', false );
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
			// send the mail
			WP_RealEstate_Email::wp_mail( $user_email, $subject, $content, $headers );
        }

        do_action('wp_realestate_member_after_making_agent_or_agency', $user_id, $user_role);

        //remove user admin bar
        update_user_meta($user_id, 'show_admin_bar_front', false);
	}

	public static function process_delete_user($user_id, $reassign) {
		if ( self::is_agency($user_id) ) {
        	$agency_id = self::get_agency_by_user_id($user_id);

            wp_delete_post($agency_id);
        } elseif ( self::is_agent($user_id) ) {
        	$agent_id = self::get_agent_by_user_id($user_id);

            wp_delete_post($agent_id);
        }
	}

	public static function process_change_profile() {
		$user_id = get_current_user_id();
		$prefix = '';
		if ( WP_RealEstate_User::is_agent($user_id) ) {
	    	$prefix = WP_REALESTATE_AGENT_PREFIX;
	    	$post_id = WP_RealEstate_User::get_agent_by_user_id($user_id);
	    } elseif( WP_RealEstate_User::is_agency($user_id) ) {
	    	$prefix = WP_REALESTATE_AGENCY_PREFIX;
	    	$post_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
	    } else {
	    	return;
	    }

		if ( ! isset( $_POST['submit-cmb-profile'] ) || empty( $_POST[$prefix.'post_type'] ) || !in_array($_POST[$prefix.'post_type'], array('agency', 'agent') ) ) {
			return;
		}

		$redirect_url = get_permalink( wp_realestate_get_option('edit_profile_page_id') );

		$cmb = cmb2_get_metabox( $prefix . 'fields_front', $post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		$data = array(
			'ID'			=> $post_id,
			'post_title'    => sanitize_text_field( $_POST[ $prefix . 'title' ] ),
			'post_content'  => wp_kses( $_POST[ $prefix . 'description' ], '<b><strong><i><em><h1><h2><h3><h4><h5><h6><pre><code><span>' ),
		);

		do_action( 'wp-realestate-process-profile-before-change', $post_id, $prefix );

		$data = apply_filters('wp-realestate-process-profile-data', $data, $post_id, $prefix);
		
		$post_id = wp_update_post( $data );

		if ( ! empty( $post_id ) && ! empty( $_POST['object_id'] ) ) {
			$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of job ID

			$cmb->save_fields( $post_id, 'post', $_POST );

			// Create featured image
			$featured_image = get_post_meta( $post_id, $prefix . 'featured_image', true );
			if ( ! empty( $_POST[ 'current_' . $prefix . 'featured_image' ] ) ) {
				if ( !empty($featured_image) ) {
					if ( is_array($featured_image) ) {
						$img_id = $featured_image[0];
					} elseif ( is_integer($featured_image) ) {
						$img_id = $featured_image;
					} else {
						$img_id = WP_RealEstate_Image::get_attachment_id_from_url($featured_image);
					}
					set_post_thumbnail( $post_id, $img_id );
				} else {
					update_post_meta( $post_id, $prefix . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}
			} else {
				update_post_meta( $post_id, $prefix . 'featured_image', null );
				delete_post_thumbnail( $post_id );
			}

			do_action( 'wp-realestate-process-profile-after-change', $post_id, $prefix );

			$_SESSION['messages'][] = array( 'success', __( 'Profile has been successfully updated.', 'wp-realestate' ) );

		} else {
			$_SESSION['messages'][] = array( 'danger', __( 'Can not update profile', 'wp-realestate' ) );
		}

		WP_RealEstate_Mixes::redirect( $redirect_url );
		exit();
	}

	public static function process_change_password() {
		$old_password = sanitize_text_field( $_POST['old_password'] );
		$new_password = sanitize_text_field( $_POST['new_password'] );
		$retype_password = sanitize_text_field( $_POST['retype_password'] );

		if ( empty( $old_password ) || empty( $new_password ) || empty( $retype_password ) ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'All fields are required.', 'wp-realestate' ) ));
			die();
		}

		if ( $new_password != $retype_password ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'New and retyped password are not same.', 'wp-realestate' ) ));
			die();
		}

		$user = wp_get_current_user();
		if ( ! wp_check_password( $old_password, $user->data->user_pass, $user->ID ) ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'Your old password is not correct.', 'wp-realestate' ) ));
			die();
		}

		do_action('wp-realestate-process-change-password', $_POST);

		wp_set_password( $new_password, $user->ID );
		echo json_encode(array('status' => true, 'msg'=> __( 'Your password has been successfully changed.', 'wp-realestate' ) ));
		die();
	}


	public static function process_resend_approve_account() {
		$user_login = isset($_POST['login']) ? $_POST['login'] : '';
		
		if ( empty($user_login) ) {
            echo json_encode(array(
            	'status' => false,
            	'msg' => __('Username or Email not exactly.', 'wp-realestate')
            ));
            die();
        }

		if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
            $user_obj = get_user_by('email', $user_login);
        } else {
            $user_obj = get_user_by('login', $user_login);
        }
        if ( !empty($user_obj->ID) ) {
	        $user_login_auth = self::get_user_status($user_obj->ID);
	        if ( $user_login_auth == 'pending' ) {
	        	if ( wp_realestate_get_option('users_requires_approval', 'auto') == 'email_approve' ) {
					$user_email = stripslashes( $user_obj->user_email );
				} else {
					$user_email = get_option( 'admin_email', false );
				}

				$subject = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'subject');
				$content = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'content');

				$email_from = get_option( 'admin_email', false );
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

				// send the mail
				$result = WP_RealEstate_Email::wp_mail( $user_email, $subject, $content, $headers );
				if ( $result ) {
					echo json_encode(array(
		            	'status' => true,
		            	'msg' => __('Sent a email successfully.', 'wp-realestate')
		            ));
		            die();
				} else {
					echo json_encode(array(
		            	'status' => false,
		            	'msg' => __('Send a email error.', 'wp-realestate')
		            ));
		            die();
		        }
	        }
        }
        echo json_encode(array(
        	'status' => false,
        	'msg' => __('Your account is not available.', 'wp-realestate')
        ));
        die();
	}

	public static function admin_user_auth_callback($user, $password = '') {
    	global $pagenow;
	    
	    $status = self::get_user_status($user->ID);
	    $message = false;
		switch ( $status ) {
			case 'pending':
				$pending_message = self::login_msg($user);
				$message = new WP_Error( 'pending_approval', $pending_message );
				break;
			case 'denied':
				$denied_message = __('Your account denied.', 'wp-realestate');
				$message = new WP_Error( 'denied_access', $denied_message );
				break;
			case 'approved':
				$message = $user;
				break;
		}

	    return $message;
	}

	public static function process_approve_user() {
		$post = get_post();

		if ( is_object( $post ) ) {
			if ( strpos( $post->post_content, '[wp_realestate_approve_user]' ) !== false ) {
				
				$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
				$code = isset($_GET['approve-key']) ? $_GET['approve-key'] : 0;
				if ( !$user_id ) {
					$error = array(
						'error' => true,
						'msg' => __('The user is not exists.', 'wp-realestate')
					);

				}
				$user = get_user_by('ID', $user_id);
				if ( empty($user) ) {
					$error = array(
						'error' => true,
						'msg' => __('The user is not exists.', 'wp-realestate')
					);
				} else {
					$user_code = get_user_meta($user_id, 'account_approve_key', true);
					if ( $code != $user_code ) {
						$error = array(
							'error' => true,
							'msg' => __('Code is not exactly.', 'wp-realestate')
						);
					}
				}

				if ( empty($error) ) {
					$return = self::update_user_status($user_id, 'approve');
					$error = array(
						'error' => false,
						'msg' => __('Your account approved.', 'wp-realestate')
					);
					$_SESSION['approve_user_msg'] = $error;
				} else {
					$_SESSION['approve_user_msg'] = $error;
				}
			}
		}
	}

	public static function approve_user( $user_id ) {
		$user = get_user_by('ID', $user_id);

		wp_cache_delete( $user->ID, 'users' );
		wp_cache_delete( $user->data->user_login, 'userlogins' );

		$user_email = stripslashes( $user->data->user_email );

		$subject = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user), 'user_register_approved', 'subject');
		$content = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user), 'user_register_approved', 'content');

		$email_from = get_option( 'admin_email', false );
		$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
		// send the mail
		WP_RealEstate_Email::wp_mail( $user_email, $subject, $content, $headers );

		// change usermeta tag in database to approved
		update_user_meta( $user->ID, 'user_account_status', 'approved' );
		update_user_meta( $user->ID, 'account_approve_key', '' );

		// agent | agency
		if ( self::is_agent($user->ID) ) {
			$agent_id = self::get_agent_by_user_id($user->ID);
			$data_args = array(
				'post_status' => 'publish',
				'ID' => $agent_id
			);
			remove_action( 'wp_realestate_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
			remove_action('denied_to_publish', array( 'WP_RealEstate_Post_Type_Agent', 'process_denied_to_publish' ) );
			remove_action('pending_to_publish', array( 'WP_RealEstate_Post_Type_Agent', 'process_denied_to_publish' ) );
			$agent_id = wp_update_post( $data_args, true );
			add_action( 'denied_to_publish', array( 'WP_RealEstate_Post_Type_Agent', 'process_pending_to_publish' ) );
			add_action( 'pending_to_publish', array( 'WP_RealEstate_Post_Type_Agent', 'process_pending_to_publish' ) );
			add_action( 'wp_realestate_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		} elseif ( self::is_agency($user->ID) ) {
			$agency_id = self::get_agency_by_user_id($user->ID);
			$data_args = array(
				'post_status' => 'publish',
				'ID' => $agency_id
			);
			remove_action( 'wp_realestate_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
			remove_action('denied_to_publish', array( 'WP_RealEstate_Post_Type_Agency', 'process_denied_to_publish' ) );
			remove_action('pending_to_publish', array( 'WP_RealEstate_Post_Type_Agency', 'process_denied_to_publish' ) );
			$agency_id = wp_update_post( $data_args, true );
			add_action( 'denied_to_publish', array( 'WP_RealEstate_Post_Type_Agency', 'process_pending_to_publish' ) );
			add_action( 'pending_to_publish', array( 'WP_RealEstate_Post_Type_Agency', 'process_pending_to_publish' ) );
			add_action( 'wp_realestate_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		}

		do_action( 'wp-realestate-new_user_approve_user_approved', $user );
	}

	public static function deny_user( $user_id ) {
		$user = get_user_by('ID', $user_id);

		$user_email = stripslashes( $user->data->user_email );

		$subject = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user), 'user_register_denied', 'subject');
		$content = WP_RealEstate_Email::render_email_vars(array('user_obj' => $user), 'user_register_denied', 'content');

		$email_from = get_option( 'admin_email', false );
		$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
		// send the mail
		WP_RealEstate_Email::wp_mail( $user_email, $subject, $content, $headers );

		update_user_meta( $user->ID, 'user_account_status', 'denied' );

		// agent | agency
		if ( self::is_agent($user->ID) ) {
			$agent_id = self::get_agent_by_user_id($user->ID);
			$data_args = array(
				'post_status' => 'denied',
				'ID' => $agent_id
			);
			$agent_id = wp_update_post( $data_args, true );
		} elseif ( self::is_agency($user->ID) ) {
			$agency_id = self::get_agency_by_user_id($user->ID);

			$data_args = array(
				'post_status' => 'denied',
				'ID' => $agency_id
			);
			$agency_id = wp_update_post( $data_args, true );
		}

		do_action( 'wp-realestate-new_user_approve_user_denied', $user );
	}

	public static function get_user_status( $user_id ) {
		$user_status = get_user_meta( $user_id, 'user_account_status', true );

		if ( empty( $user_status ) ) {
			$user_status = 'approved';
		}

		return $user_status;
	}

	public static function update_user_status( $user, $status ) {
		$user_id = absint( $user );
		if ( !$user_id ) {
			return false;
		}

		if ( !in_array( $status, array( 'approve', 'deny' ) ) ) {
			return false;
		}

		$do_update = apply_filters( 'wp_realestate_new_user_approve_validate_status_update', true, $user_id, $status );
		if ( !$do_update ) {
			return false;
		}

		// where it all happens
		do_action( 'wp_realestate_new_user_approve_' . $status . '_user', $user_id );
		do_action( 'wp_realestate_new_user_approve_user_status_update', $user_id, $status );

		return true;
	}

	public static function process_update_user_action() {
		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'approve', 'deny' ) ) && !isset( $_GET['new_role'] ) ) {
			check_admin_referer( 'wp-realestate' );

			$sendback = remove_query_arg( array( 'approved', 'denied', 'deleted', 'ids', 'wp-realestate-status-query-submit', 'new_role' ), wp_get_referer() );
			if ( !$sendback ) {
				$sendback = admin_url( 'users.php' );
			}

			$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			$status = sanitize_key( $_GET['action'] );
			$user = absint( $_GET['user'] );

			self::update_user_status( $user, $status );

			if ( $_GET['action'] == 'approve' ) {
				$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );
			} else {
				$sendback = add_query_arg( array( 'denied' => 1, 'ids' => $user ), $sendback );
			}

			wp_redirect( $sendback );
			exit;
		}
	}

	public static function validate_status_update( $do_update, $user_id, $status ) {
		$current_status = self::get_user_status( $user_id );

		if ( $status == 'approve' ) {
			$new_status = 'approved';
		} else {
			$new_status = 'denied';
		}

		if ( $current_status == $new_status ) {
			$do_update = false;
		}

		return $do_update;
	}

	/**
	 * Add the approve or deny link where appropriate.
	 *
	 * @uses user_row_actions
	 * @param array $actions
	 * @param object $user
	 * @return array
	 */
	public static function user_table_actions( $actions, $user ) {
		if ( $user->ID == get_current_user_id() ) {
			return $actions;
		}

		if ( is_super_admin( $user->ID ) ) {
			return $actions;
		}

		$user_status = self::get_user_status( $user->ID );

		$approve_link = add_query_arg( array( 'action' => 'approve', 'user' => $user->ID ) );
		$approve_link = remove_query_arg( array( 'new_role' ), $approve_link );
		$approve_link = wp_nonce_url( $approve_link, 'wp-realestate' );

		$deny_link = add_query_arg( array( 'action' => 'deny', 'user' => $user->ID ) );
		$deny_link = remove_query_arg( array( 'new_role' ), $deny_link );
		$deny_link = wp_nonce_url( $deny_link, 'wp-realestate' );

		$approve_action = '<a href="' . esc_url( $approve_link ) . '">' . __( 'Approve', 'wp-realestate' ) . '</a>';
		$deny_action = '<a href="' . esc_url( $deny_link ) . '">' . __( 'Deny', 'wp-realestate' ) . '</a>';

		if ( $user_status == 'pending' ) {
			$actions[] = $approve_action;
			$actions[] = $deny_action;
		} else if ( $user_status == 'approved' ) {
			$actions[] = $deny_action;
		} else if ( $user_status == 'denied' ) {
			$actions[] = $approve_action;
		}

		return $actions;
	}

	/**
	 * Add the status column to the user table
	 *
	 * @uses manage_users_columns
	 * @param array $columns
	 * @return array
	 */
	public static function add_column( $columns ) {
		$the_columns['user_status'] = __( 'Status', 'wp-realestate' );

		$newcol = array_slice( $columns, 0, -1 );
		$newcol = array_merge( $newcol, $the_columns );
		$columns = array_merge( $newcol, array_slice( $columns, 1 ) );

		return $columns;
	}

	/**
	 * Show the status of the user in the status column
	 *
	 * @uses manage_users_custom_column
	 * @param string $val
	 * @param string $column_name
	 * @param int $user_id
	 * @return string
	 */
	public static function status_column( $val, $column_name, $user_id ) {
		switch ( $column_name ) {
			case 'user_status' :
				$status = self::get_user_status( $user_id );
				if ( $status == 'approved' ) {
					$status_i18n = __( 'approved', 'wp-realestate' );
				} else if ( $status == 'denied' ) {
					$status_i18n = __( 'denied', 'wp-realestate' );
				} else if ( $status == 'pending' ) {
					$status_i18n = __( 'pending', 'wp-realestate' );
				}
				return $status_i18n;
				break;

			default:
		}

		return $val;
	}

	/**
	 * Add a filter to the user table to filter by user status
	 *
	 * @uses restrict_manage_users
	 */
	public static function status_filter( $which ) {
		$id = 'wp_realestate_filter-' . $which;

		$filter_button = submit_button( __( 'Filter', 'wp-realestate' ), 'button', 'wp-realestate-status-query-submit', false, array( 'id' => 'wp-realestate-status-query-submit' ) );
		$filtered_status = null;
		if ( ! empty( $_REQUEST['wp_realestate_filter-top'] ) || ! empty( $_REQUEST['wp_realestate_filter-bottom'] ) ) {
			$filtered_status = esc_attr( ( ! empty( $_REQUEST['wp_realestate_filter-top'] ) ) ? $_REQUEST['wp_realestate_filter-top'] : $_REQUEST['wp_realestate_filter-bottom'] );
		}
		$statuses = array('pending', 'approved', 'denied');
		?>
		<label class="screen-reader-text" for="<?php echo $id ?>"><?php _e( 'View all users', 'wp-realestate' ); ?></label>
		<select id="<?php echo $id ?>" name="<?php echo $id ?>" style="float: none; margin: 0 0 0 15px;">
			<option value=""><?php _e( 'View all users', 'wp-realestate' ); ?></option>
		<?php foreach ( $statuses as $status ) : ?>
			<option value="<?php echo esc_attr( $status ); ?>"<?php selected( $status, $filtered_status ); ?>><?php echo esc_html( $status ); ?></option>
		<?php endforeach; ?>
		</select>
		<?php echo apply_filters( 'wp_realestate_filter_button', $filter_button ); ?>
		<style>
			#wp-realestate-status-query-submit {
				float: right;
				margin: 2px 0 0 5px;
			}
		</style>
	<?php
	}

	/**
	 * Modify the user query if the status filter is being used.
	 *
	 * @uses pre_user_query
	 * @param $query
	 */
    public static function filter_by_status( $query ) {
		global $wpdb;

		if ( !is_admin() ) {
			return;
		}
		
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( isset( $screen ) && 'users' != $screen->id ) {
			return;
		}
		$filter = null;
		if ( ! empty( $_REQUEST['wp_realestate_filter-top'] ) || ! empty( $_REQUEST['wp_realestate_filter-bottom'] ) ) {
			$filter = esc_attr( ( ! empty( $_REQUEST['wp_realestate_filter-top'] ) ) ? $_REQUEST['wp_realestate_filter-top'] : $_REQUEST['wp_realestate_filter-bottom'] );
		}
		if ( $filter != null ) {

			$query->query_from .= " INNER JOIN {$wpdb->usermeta} ON ( {$wpdb->users}.ID = $wpdb->usermeta.user_id )";

			if ( 'approved' == $filter ) {
				$query->query_fields = "DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->users}.ID";
				$query->query_from .= " LEFT JOIN {$wpdb->usermeta} AS mt1 ON ({$wpdb->users}.ID = mt1.user_id AND mt1.meta_key = 'user_account_status')";
				$query->query_where .= " AND ( ( $wpdb->usermeta.meta_key = 'user_account_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = 'approved' ) OR mt1.user_id IS NULL )";
			} else {
				$query->query_where .= " AND ( ($wpdb->usermeta.meta_key = 'user_account_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = '{$filter}') )";
			}
		}
	}

	public static function register_msg($user) {
		$requires_approval = wp_realestate_get_option('users_requires_approval', 'auto');

		if ( $requires_approval == 'email_approve' ) {
			return __('Registration complete. Before you can login, you must active your account sent to your email address.', 'wp-realestate');
		} elseif ( $requires_approval == 'admin_approve' ) {
			return __('Registration complete. Your account has to be confirmed by an administrator before you can login', 'wp-realestate');
		} else {
			return __('Your account has to be confirmed yet.', 'wp-realestate');
		}
	}
	
	public static function login_msg($user) {
		$requires_approval = wp_realestate_get_option('users_requires_approval', 'auto');
		
		if ( $requires_approval == 'email_approve' ) {
			return sprintf(__('Account account has not confirmed yet, you must active your account with the link sent to your email address. If you did not receive this email, please check your junk/spam folder. <a href="javascript:void(0);" class="wp-realestate-resend-approve-account-btn" data-login="%s">Click here</a> to resend the activation email.', 'wp-realestate'), $user->user_login );
		} elseif ( $requires_approval == 'admin_approve' ) {
			return __('Your account has to be confirmed by an administrator before you can login.', 'wp-realestate');
		} else {
			return __('Your account has to be confirmed yet.', 'wp-realestate');
		}
	}


	public static function backend_user_profile_fields( $user ) {
		$data = get_userdata( $user->ID );
		$avatar = get_the_author_meta( '_user_avatar', $user->ID );
		$avatar_url = wp_get_attachment_image_src($avatar, 'full');
		
		$address = get_the_author_meta( '_address', $user->ID );
		$phone = get_the_author_meta( '_phone', $user->ID );
		?>
		<h3><?php esc_html_e( 'User Profile', 'wp-realestate' ); ?></h3>
		<table class="form-table">
			<tbody>
				
				<tr>
					<th>
						<label ><?php esc_html_e( 'Avatar', 'wp-realestate' ); ?></label>
					</th>
					<td>
						<div class="screenshot-user avatar-screenshot">
				            <?php if ( !empty($avatar_url[0]) ) { ?>
				                <img src="<?php echo esc_url($avatar_url[0]); ?>" alt="<?php esc_attr_e( 'Avatar', 'wp-realestate' ); ?>" />
				            <?php } ?>
				        </div>
				        <input class="widefat upload_image" name="user_avatar" type="hidden" value="<?php echo esc_attr($avatar); ?>" />
				        <div class="upload_image_action">
				            <input type="button" class="button radius-3x btn btn-theme user-add-image" value="<?php esc_attr_e( 'Add Avatar', 'wp-realestate' ); ?>">
				            <input type="button" class="button radius-3x btn btn-theme-second user-remove-image" value="<?php esc_attr_e( 'Remove Avatar', 'wp-realestate' ); ?>">
				        </div>
					</td>
				</tr>
				<tr>
					<th>
						<label for="lecturer_mobile"><?php esc_html_e( 'Address', 'wp-realestate' ); ?></label>
					</th>
					<td>
						<input id="change-profile-form-address" type="text" name="address" class="form-control" value="<?php echo ! empty( $address ) ? esc_attr( $address ) : ''; ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label for="lecturer_mobile"><?php esc_html_e( 'Phone', 'wp-realestate' ); ?></label>
					</th>
					<td>
						<input id="change-profile-form-phone" type="text" name="phone" class="form-control" value="<?php echo ! empty( $phone ) ? esc_attr( $phone ) : ''; ?>">
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	public static function backend_save_user_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$keys = array(
			'user_avatar', 'address', 'phone'
		);

		foreach ($keys as $key) {
			$value = isset($_POST[$key]) ? sanitize_text_field( $_POST[$key] ) : '';
			update_user_meta( $user_id, '_'.$key, $value );
		}
	}

	public static function process_change_profile_normal() {
		if ( !isset( $_POST['change_profile_form'] ) ) {
			return;
		}

		$user = wp_get_current_user();

		$email = isset($_POST['email']) ? sanitize_email( $_POST['email'] ) : '';

		$general_keys = array( 'first_name', 'last_name', 'description', 'url' );
		$keys = array(
			'current_user_avatar', 'address', 'phone'
		);

		if ( empty( $email ) ) {
			$_SESSION['messages'][] = array( 'danger', __( 'E-mail is required.', 'wp-realestate' ) );
			return;
		}

		do_action('wp-realestate-before-change-profile-normal');

		update_user_meta( $user->ID, 'user_email', $email );

		$result = wp_update_user( array(
			'ID'            => $user->ID,
			'user_email'    => $email,
		) );
		if ( $result ) {
			foreach ($general_keys as $key) {
				$value = isset($_POST[$key]) ? sanitize_text_field( $_POST[$key] ) : '';
				update_user_meta( $user->ID, $key, $value );
			}

			foreach ($keys as $key) {
				$value = isset($_POST[$key]) ? sanitize_text_field( $_POST[$key] ) : '';
				if ( $key == 'current_user_avatar' ) {
					$attachment_id = WP_RealEstate_Image::create_attachment($value);
					update_user_meta( $user->ID, '_user_avatar', $attachment_id );
				} else {
					update_user_meta( $user->ID, '_'.$key, $value );
				}
			}
			$_SESSION['messages'][] = array( 'success', __( 'Profile has been successfully updated.', 'wp-realestate' ) );
		} else {
			$_SESSION['messages'][] = array( 'danger', __( 'Can not update profile.', 'wp-realestate' ) );
		}
	}

	public static function get_avatar($avatar, $id_or_email='', $size='', $default='', $alt='') {
	    if (is_object($id_or_email)) {
	        
	        $avatar_id = get_the_author_meta( '_user_avatar', $id_or_email->ID );
	        if ( !empty($avatar_id) ) {
	            $avatar_url = wp_get_attachment_image_src($avatar_id, 'thumbnail');
	            if ( !empty($avatar_url[0]) ) {
	                $avatar = '<img src="'.esc_url($avatar_url[0]).'" width="'.esc_attr($size).'" height="'.esc_attr($size).'" alt="'.esc_attr($alt).'" class="avatar avatar-'.esc_attr($size).' wp-user-avatar wp-user-avatar-'.esc_attr($size).' photo avatar-default" />';
	            }
	        }
	    } else {
	        $avatar_id = get_the_author_meta( '_user_avatar', $id_or_email );
	        if ( !empty($avatar_id) ) {
	            $avatar_url = wp_get_attachment_image_src($avatar_id, 'thumbnail');
	            if ( !empty($avatar_url[0]) ) {
	                $avatar = '<img src="'.esc_url($avatar_url[0]).'" width="'.esc_attr($size).'" height="'.esc_attr($size).'" alt="'.esc_attr($alt).'" class="avatar avatar-'.esc_attr($size).' wp-user-avatar wp-user-avatar-'.esc_attr($size).' photo avatar-default" />';
	            }
	        }
	    }
	    return $avatar;
	}
}

WP_RealEstate_User::init();