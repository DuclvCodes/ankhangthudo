<?php
/**
 * Social: Facebook
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

/*
 * Import the Facebook SDK and load all the classes
 */
require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/facebook-sdk/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;


class WP_RealEstate_Social_Facebook {
	
	private $app_id;

    private $app_secret;

    private $callback_url;

    private $access_token;

    private $facebook_user_datas;

    private $redirect_url;

	private static $_instance = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		// Start session
        if (!session_id()) {
            session_start();
        }

        $this->app_id = wp_realestate_get_option( 'facebook_api_app_id' );
        $this->app_secret = wp_realestate_get_option( 'facebook_api_app_secret' );

        $this->callback_url = admin_url('admin-ajax.php?action=wp_realestate_facebook_login');
        if ( $this->is_facebook_login_enabled() ) {
            // Ajax endpoints.
            add_action( 'wre_ajax_wp_realestate_facebook_login', array( $this, 'process_facebook_login' ) );

            // compatible handlers.
            add_action( 'wp_ajax_wp_realestate_facebook_login', array( $this, 'process_facebook_login' ) );
            add_action( 'wp_ajax_nopriv_wp_realestate_facebook_login', array( $this, 'process_facebook_login' ) );

            add_action( 'login_form', array( $this, 'display_login_btn') );
        }
	}

    public function is_facebook_login_enabled() {
        if ( wp_realestate_get_option('enable_facebook_login') && ! empty( $this->app_id ) && ! empty( $this->app_secret ) ) {
            return true;
        }

        return false;
    }

	public function process_facebook_login() {
        
        // We start the connection
        $fb = $this->init_api();

        // We save the token in our instance
        $this->access_token = $this->get_token($fb);

        // We get the user details
        $this->facebook_user_datas = $this->get_user_details($fb);

        // We first try to login the user
        $this->login_user();

        // Otherwise, we create a new account
        $this->create_user();

        if ( empty($this->redirect_url) ) {
        	$user_dashboard_page_id = wp_realestate_get_option('user_dashboard_page_id');
        	$this->redirect_url = $user_dashboard_page_id > 0 ? get_permalink($user_dashboard_page_id) : home_url('/');
        }

        // Redirect the user
        WP_RealEstate_Mixes::redirect($this->redirect_url);
    }

    private function init_api() {
        $facebook = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => 'v2.10',
            'persistent_data_handler' => 'session'
        ]);

        return $facebook;
    }

    private function get_login_url() {
        $fb = $this->init_api();
        $helper = $fb->getRedirectLoginHelper();

        // Optional permissions
        $permissions = ['email'];

        $url = $helper->getLoginUrl( $this->callback_url, $permissions );

        return esc_url($url);
    }

    private function get_token($fb) {

        // Assign the Session variable for Facebook
        $_SESSION['FBRLH_state'] = $_GET['state'];

        // Load the Facebook SDK helper
        $helper = $fb->getRedirectLoginHelper();

        // Try to get an access token
        try {
            $accessToken = $helper->getAccessToken(admin_url('admin-ajax.php?action=wp_realestate_facebook_login'));
        }
        // When Graph returns an error
        catch (FacebookResponseException $e) {
            $error = __('Graph returned an error: ', 'wp-realestate') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }
        // When validation fails or other local issues
        catch (FacebookSDKException $e) {
            $error = __('Facebook SDK returned an error: ', 'wp-realestate') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we don't got a token, it means we had an error
        if (!isset($accessToken)) {
            // Report our errors

            set_transient('wp_realestate_facebook_message', $message, 60 * 60 * 24 * 30);

            // Redirect
            WP_RealEstate_Mixes::redirect($this->redirect_url);
        }

        return $accessToken->getValue();
    }

    /**
     * Get user details through the Facebook API
     *
     * @link https://developers.facebook.com/docs/facebook-login/permissions#reference-public_profile
     * @param $fb Facebook
     * @return \Facebook\GraphNodes\GraphUser
     */
    private function get_user_details($fb) {
        try {
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,link', $this->access_token);
        } catch (FacebookResponseException $e) {
            $message = __('Graph returned an error: ', 'wp-realestate') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } catch (FacebookSDKException $e) {
            $message = __('Facebook SDK returned an error: ', 'wp-realestate') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we caught an error
        if (isset($message)) {
            // Report our errors
            set_transient('wp_realestate_facebook_message', $message, 60 * 60 * 24 * 30);

            // Redirect
            WP_RealEstate_Mixes::redirect($this->redirect_url);
        }

        return $response->getGraphUser();
    }

    private function login_user() {
        $wp_users = get_users(array(
            'meta_key' => 'wp_realestate_facebook_id',
            'meta_value' => $this->facebook_user_datas['id'],
            'number' => 1,
            'count_total' => false,
            'fields' => 'ids',
        ));

        if (empty($wp_users[0])) {
            return false;
        }

        wp_set_auth_cookie($wp_users[0]);

        do_action('wp_realestate_after_facebook_login', $wp_users[0]);

        WP_RealEstate_Mixes::redirect($this->redirect_url);
    }

    private function create_user() {
        $fb_user = $this->facebook_user_datas;

        $_social_user_obj = get_user_by('email', $fb_user['email']);
        if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
            update_user_meta($_social_user_obj->ID, 'wp_realestate_facebook_id', $fb_user['id']);
            $this->login_user();
        }

        // Create an username
        $username = sanitize_user(str_replace(' ', '_', strtolower($this->facebook_user_datas['name'])));

        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);
        }

        ///
        $userdata = array(
	        'user_login' => sanitize_user( $username ),
	        'user_email' => sanitize_email( $fb_user['email'] ),
	        'user_pass' => wp_generate_password(),
        );
        $userdata = apply_filters('wp-realestate-facebook-login-userdata', $userdata, $fb_user);

        $user_id = wp_insert_user( $userdata );
        if ( ! is_wp_error( $user_id ) ) {
        	
            update_user_meta($user_id, 'first_name', $fb_user['first_name']);
            update_user_meta($user_id, 'last_name', $fb_user['last_name']);
            update_user_meta($user_id, 'user_url', $fb_user['link']);
            update_user_meta($user_id, 'wp_realestate_facebook_id', $fb_user['id']);

        	do_action('wp_realestate_after_facebook_login', $user_id);

			wp_set_auth_cookie($user_id);
        } else {
	        set_transient('wp_realestate_facebook_message', $user_id->get_error_message(), 60 * 60 * 24 * 30);
            echo $user_id->get_error_message();
            die;
	    }
    }

    public function display_message() {
        if ( get_transient('wp_realestate_facebook_message') ) {
            $message = get_transient('wp_realestate_facebook_message');
            echo '<div class="alert alert-danger facebook-message">' . $message . '</div>';
            delete_transient('wp_realestate_facebook_message');
        }
    }

    public function display_login_btn() {
        if ( is_user_logged_in() ) {
            return;
        }
    	ob_start();
        $this->display_message();
    	?>
    	<div class="facebook-login-btn-wrapper">
    		<a class="facebook-login-btn" href="<?php echo esc_url($this->get_login_url()); ?>"><i class="fab fa-facebook-f"></i> <?php esc_html_e('Login with Facebook', 'wp-realestate'); ?></a>
    	</div>
    	<?php
    	$output = ob_get_clean();
    	echo apply_filters('wp-realestate-facebook-login-btn', $output, $this);
    }
}

function wp_realestate_social_facebook() {
    WP_RealEstate_Social_Facebook::get_instance();
}
add_action( 'init', 'wp_realestate_social_facebook' );