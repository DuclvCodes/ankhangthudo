<?php
/**
 * Property Yelp
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Property_Yelp {

	private static $_instance = null;
	private $API_KEY = null;
	private $API_HOST = "https://api.yelp.com";
	private $SEARCH_PATH = "/v3/businesses/search";
	private $BUSINESS_PATH = "/v3/businesses/";
	private $SEARCH_LIMIT = 3;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->API_KEY = wp_realestate_get_option('api_settings_yelp_app_secret');
	}

	public function request($host, $path, $url_params = array()) {
	    // Send Yelp API Call
	    try {
	        $curl = curl_init();
	        if (FALSE === $curl)
	            throw new Exception('Failed to initialize');

	        $url = $host . $path . "?" . http_build_query($url_params);

	        curl_setopt_array($curl, array(
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => true,  // Capture response.
	            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
	            CURLOPT_MAXREDIRS => 10,
	            CURLOPT_TIMEOUT => 30,
	            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	            CURLOPT_CUSTOMREQUEST => "GET",
	            CURLOPT_HTTPHEADER => array(
	                "authorization: Bearer " . $this->API_KEY ,
	                "cache-control: no-cache",
	            ),
	        ));

	        $response = curl_exec($curl);

	        if (FALSE === $response)
	            throw new Exception(curl_error($curl), curl_errno($curl));
	        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	        if (200 != $http_status)
	            throw new Exception($response, $http_status);

	        curl_close($curl);
	    } catch(Exception $e) {
	        trigger_error(sprintf(
	            'Curl failed with error #%d: %s',
	            $e->getCode(), $e->getMessage()),
	            E_USER_ERROR);
	    }

	    return $response;
	}

	public function search($term, $location = '', $latitude = '', $longitude = '') {
	    $url_params = array();
	    
	    $url_params['term'] = $term;
	    if ( $location ) {
		    $url_params['location'] = $location;
		}
		if ( $latitude ) {
		    $url_params['latitude'] = $latitude;
		}
		if ( $longitude ) {
		    $url_params['longitude'] = $longitude;
		}
	    $url_params['limit'] = $this->SEARCH_LIMIT;
	    
	    return $this->request($this->API_HOST, $this->SEARCH_PATH, $url_params);
	}

	public function get_business($business_id) {
	    $business_path = $this->BUSINESS_PATH . urlencode($business_id);
	    
	    return $this->request($this->API_HOST, $business_path);
	}

	public function query_api($term, $location = '', $latitude = '', $longitude = '') {     
	    $response = json_decode($this->search($term, $location, $latitude, $longitude));
	    return $response->businesses;
	}

	public static function get_yelp_categories() {
        return apply_filters( 'wp-realestate-get-yelp-categories', array(
            'food' => esc_html__('Food', 'wp-realestate'),
            'nightlife' => esc_html__('Nightlife', 'wp-realestate'),
            'restaurants' => esc_html__('Restaurants', 'wp-realestate'),
            'shopping' => esc_html__('Shopping', 'wp-realestate'),
            'active-life' => esc_html__('Active Life', 'wp-realestate'),
            'arts-entertainment' => esc_html__('Arts & Entertainment', 'wp-realestate'),
            'automotive' => esc_html__('Automotive', 'wp-realestate'),
            'beauty-spas' => esc_html__('Beauty & Spas', 'wp-realestate'),
            'education' => esc_html__('Education', 'wp-realestate'),
            'event-planning-services' => esc_html__('Event Planning & Services', 'wp-realestate'),
            'health-medical' => esc_html__('Health & Medical', 'wp-realestate'),
            'home-services' => esc_html__('Home Services', 'wp-realestate'),
            'local-services' => esc_html__('Local Services', 'wp-realestate'),
            'financial-services' => esc_html__('Financial Services', 'wp-realestate'),
            'hotels-travel' => esc_html__('Hotels & Travel', 'wp-realestate'),
            'local-flavor' => esc_html__('Local Flavor', 'wp-realestate'),
            'mass-media' => esc_html__('Mass Media', 'wp-realestate'),
            'pets' => esc_html__('Pets', 'wp-realestate'),
            'professional-services' => esc_html__('Professional Services', 'wp-realestate'),
            'public-services-govt' => esc_html__('Public Services & Government', 'wp-realestate'),
            'real-estate' => esc_html__('Real Estate', 'wp-realestate'),
            'religious-organizations' => esc_html__('Religious Organizations', 'wp-realestate'),
        ));
    }

    public static function get_yelp_star_img($star) {
		switch ($star) {
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
				$class = 'regular_'.$star.'.png';
				break;
			case '1.5':
				$class = 'regular_1_half.png';
				break;
			case '2.5':
				$class = 'regular_2_half.png';
				break;
			case '3.5':
				$class = 'regular_3_half.png';
				break;
			case '4.5':
				$class = 'regular_4_half.png';
				break;
			default:
				$class = 'regular_0.png';
				break;
		}
		return apply_filters( 'homesweet_get_yelp_star_img', $class, $star );
	}
}
