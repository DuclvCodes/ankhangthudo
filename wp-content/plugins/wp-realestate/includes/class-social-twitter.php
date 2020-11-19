<?php
/**
 * Social: Twitter
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_RealEstate_Social_Twitter {
    
    private $consumer_key = '';
    private $consumer_secret = '';
    private $access_token = '';
    private $token_secret = '';
    private $redirect_url = '';
    //
    private $twitter_user_datas;

    private static $_instance = null;

    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        $this->consumer_key = wp_realestate_get_option( 'twitter_api_consumer_key' );
        $this->consumer_secret = wp_realestate_get_option( 'twitter_api_consumer_secret' );
        $this->access_token = wp_realestate_get_option( 'twitter_api_access_token' );
        $this->token_secret = wp_realestate_get_option( 'twitter_api_token_secret' );

        if ( $this->is_twitter_login_enabled() ) {
            $user_dashboard_page_id = wp_realestate_get_option('user_dashboard_page_id');
            $this->redirect_url = $user_dashboard_page_id > 0 ? get_permalink($user_dashboard_page_id) : home_url('/');

            // Ajax endpoints.
            add_action('wre_ajax_wp_realestate_twitter', array($this, 'twitter_connect'));
            
            // compatible handlers.
            add_action('wp_ajax_wp_realestate_twitter', array($this, 'twitter_connect'));
            add_action('wp_ajax_nopriv_wp_realestate_twitter', array($this, 'twitter_connect'));

            add_action( 'login_form', array( $this, 'display_login_btn') );
            
            if ( isset($_GET['oauth_token']) && $_GET['oauth_token'] != '' ) {
                $this->process_twitter_login();
            }
        }
    }

    public function is_twitter_login_enabled() {
        if ( wp_realestate_get_option('enable_twitter_login') && ! empty( $this->consumer_key ) && ! empty( $this->consumer_secret ) ) {
            return true;
        }

        return false;
    }

    public function twitter_connect() {
        if ( !class_exists('TwitterOAuth') ) {
            require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/twitter/twitteroauth.php';
        }
        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;
        $twitter_oath_callback = home_url('/');
        if ($consumer_key != '' && $consumer_secret != '') {

            $connection = new TwitterOAuth($consumer_key, $consumer_secret);
            $request_token = $connection->getRequestToken($twitter_oath_callback);

            if (!empty($request_token)) {
                set_transient('oauth_token', $request_token['oauth_token'], (60 * 60 * 24));
                set_transient('oauth_token_secret', $request_token['oauth_token_secret'], (60 * 60 * 24));
                $token = $request_token['oauth_token'];
            }

            switch ($connection->http_code) {
                case 200:
                    $url = $connection->getAuthorizeURL($token);
                    wp_redirect($url);
                    break;
                default:
                    echo esc_html($connection->http_code);
                    esc_html_e('There is problem while connecting to twitter', 'wp-realestate');
            }
            exit();
        }
        wp_die();
    }

    public function process_twitter_login() {
        if (!class_exists('TwitterOAuth')) {
            require_once WP_REALESTATE_PLUGIN_DIR . 'libraries/twitter/twitteroauth.php';
        }
        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;

        $oauth_token = get_transient('oauth_token');
        $oauth_token_secret = get_transient('oauth_token_secret');
        if (!empty($oauth_token) && !empty($oauth_token_secret)) {
            $connection = new TwitterOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            set_transient('access_token', $access_token, (3600 * 60) * 24);
            delete_transient('oauth_token');
            delete_transient('oauth_token_secret');
        }

        if (200 == $connection->http_code) {
            set_transient('status', 'verified', (3600 * 60) * 24);
            
            $params = array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');
            
            $user = $connection->get('account/verify_credentials',$params);
            
            $this->twitter_user_datas = $user;

            // We first try to login the user
            $this->login_user();

            // Otherwise, we create a new account
            $this->create_user();

            WP_RealEstate_Mixes::redirect($this->redirect_url);
        } else {
            esc_html_e('There is problem while connecting to twitter', 'wp-realestate');
        }
        die;
    }

    private function login_user() {

        $wp_users = get_users(array(
            'meta_key' => 'wp_realestate_twitter_id',
            'meta_value' => $this->twitter_user_datas->id,
            'number' => 1,
            'count_total' => false,
            'fields' => 'ids',
        ));

        if (empty($wp_users[0])) {
            return false;
        }

        wp_set_auth_cookie($wp_users[0]);

        do_action('wp_realestate_after_twitter_login', $wp_users[0]);

        WP_RealEstate_Mixes::redirect($this->redirect_url);
    }

    private function create_user() {

        $twitter_user = $this->twitter_user_datas;
        
        $site_url = parse_url(site_url());
        $user_email = 'tw_' . md5($twitter_user->id) . '@' . $site_url['host'];

        if (isset($twitter_user->email)) {
            $user_email = $twitter_user->email;

            $_social_user_obj = get_user_by('email', $user_email);
            if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
                update_user_meta($_social_user_obj->ID, 'wp_realestate_twitter_id', $twitter_user->id);
                $this->login_user();
            }
        }

        // Create an username
        $username = sanitize_user(str_replace(' ', '_', strtolower($twitter_user->name)));

        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);
        }

        ///
        $userdata = array(
            'user_login' => sanitize_user( $username ),
            'user_email' => sanitize_email( $email ),
            'user_pass' => wp_generate_password(),
        );
        $userdata = apply_filters('wp-realestate-twitter-login-userdata', $userdata, $twitter_user);


        // Creating our user
        $user_id = wp_insert_user( $userdata );
        if ( ! is_wp_error( $user_id ) ) {
            
            $first_name = isset($twitter_user->first_name) ? $twitter_user->first_name : '';
            $last_name = isset($twitter_user->last_name) ? $twitter_user->last_name : '';

            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'wp_realestate_twitter_id', $twitter_user->id);

            do_action('wp_realestate_after_twitter_login', $user_id);

            wp_set_auth_cookie($user_id);
        } else {
            set_transient('wp_realestate_twitter_message', $user_id->get_error_message(), 60 * 60 * 24 * 30);
            echo $user_id->get_error_message();
            die;
        }
    }
    
    public static function get_login_url() {
        return admin_url('admin-ajax.php?action=wp_realestate_twitter');
    }

    public function display_message() {
        if ( get_transient('wp_realestate_twitter_message') ) {
            $message = get_transient('wp_realestate_twitter_message');
            echo '<div class="alert alert-danger twitter-message">' . $message . '</div>';
            delete_transient('wp_realestate_twitter_message');
        }
    }

    public function display_login_btn() {
        if ( is_user_logged_in() ) {
            return;
        }
        ob_start();
        $this->display_message();
        ?>
        <div class="twitter-login-btn-wrapper">
            <a class="twitter-login-btn" href="<?php echo esc_url($this->get_login_url()); ?>"><i class="fab fa-twitter"></i> <?php esc_html_e('Login with Twitter', 'wp-realestate'); ?></a>
        </div>
        <?php
        $output = ob_get_clean();
        echo apply_filters('wp-realestate-twitter-login-btn', $output, $this);
    }

}

function wp_realestate_social_twitter() {
    WP_RealEstate_Social_Twitter::get_instance();
}
add_action( 'init', 'wp_realestate_social_twitter' );