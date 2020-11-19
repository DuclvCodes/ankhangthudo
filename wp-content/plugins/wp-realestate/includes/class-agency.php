<?php
/**
 * Agent
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Agency {
	
	public static function init() {
		// Ajax endpoints.
		add_action( 'wre_ajax_wp_realestate_ajax_get_agents', array( __CLASS__, 'get_ajax_agents' ) );
		add_action( 'wre_ajax_wp_realestate_ajax_agency_add_agent', array( __CLASS__, 'add_agent' ) );
		add_action( 'wre_ajax_wp_realestate_ajax_agency_remove_agent', array( __CLASS__, 'remove_agent' ) );
		
		// compatible handlers.
		add_action( 'wp_ajax_wp_realestate_ajax_get_agents', array( __CLASS__, 'get_ajax_agents' ) );
		add_action( 'wp_ajax_wp_realestate_ajax_agency_add_agent', array( __CLASS__, 'add_agent' ) );
		add_action( 'wp_ajax_wp_realestate_ajax_agency_remove_agent', array( __CLASS__, 'remove_agent' ) );


		add_action( 'wp_realestate_before_agency_archive', array( __CLASS__, 'display_agencies_results_count_orderby_start' ), 5 );
		add_action( 'wp_realestate_before_agency_archive', array( __CLASS__, 'display_agencies_count_results' ), 10 );
		add_action( 'wp_realestate_before_agency_archive', array( __CLASS__, 'display_agencies_orderby' ), 15 );
		add_action( 'wp_realestate_before_agency_archive', array( __CLASS__, 'display_agencies_results_count_orderby_end' ), 100 );

		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
	}

	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'agency' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'agency' || $suppress_filters ) {
			return;
		}

		$limit = wp_realestate_get_option('number_agencies_per_page', 10);
		$query_vars = &$query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;
		
		return $query;
	}

	public static function get_ajax_agents() {
		$query_args = array(
			'post_type'         => 'agent',
			'paged'         	=> 1,
			'posts_per_page'    => 20,
			'post_status'       => 'publish',
			'orderby' => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			),
			'order' => 'DESC',
		);
		if ( !empty($_REQUEST['q']) ) {
			$query_args['s'] = $_REQUEST['q'];
		}
		$user_id = get_current_user_id();
		$agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
		$agents = self::get_post_meta($agency_id, 'agents', false);
		if ( !empty($agents) ) {
			$query_args['post__not_in'] = $agents;
		}
		$loop = new WP_Query( $query_args );
		$return = array();
		if ( $loop->have_posts() ) {
			foreach ($loop->posts as $post) {
				$return[] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
					'img' => get_the_post_thumbnail_url($post, 'thumbnail'),
				);
			}
		}
		echo json_encode($return);
		exit();
	}

	public static function add_agent() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-agency-add-agent-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$agent_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : '';
		if ( empty($agent_id) || !($post = get_post($agent_id)) || empty($post->post_type) || $post->post_type !== 'agent' ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Agent not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}

		do_action( 'wp-realestate-process-add-agent' );

		$user_id = get_current_user_id();
		$agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
		$agents = self::get_post_meta($agency_id, 'agents', false);
		$html = '';
		if ( !empty($agents) ) {
			if ( in_array($agent_id, $agents) ) {
				// return available
				$return = array( 'status' => false, 'msg' => esc_html__('Agent exists', 'wp-realestate') );
				echo wp_json_encode($return);
			   	exit;
			} else {
				add_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX.'agents', $agent_id);
                $post = get_post($agent_id);
                if ( $post ) {
	                setup_postdata( $GLOBALS['post'] =& $post );
	                $agent_style = apply_filters('wp-realestate-agent-inner-list-team', 'inner-list-team');
	                $html = WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/'.$agent_style );
	                wp_reset_postdata();
	            }

				$return = array( 'status' => true, 'msg' => esc_html__('Add agent to team successful', 'wp-realestate'), 'html' => $html );
				echo wp_json_encode($return);
			   	exit;
			}
		} else {
			add_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX.'agents', $agent_id);
			$post = get_post($agent_id);
            if ( $post ) {
				setup_postdata( $GLOBALS['post'] =& $post );
	            $agent_style = apply_filters('wp-realestate-agent-inner-list-team', 'inner-list-team');
	            $html = WP_RealEstate_Template_Loader::get_template_part( 'agents-styles/'.$agent_style );
	            wp_reset_postdata();
	        }

			$return = array( 'status' => true, 'msg' => esc_html__('Add agent to team successful', 'wp-realestate'), 'html' => $html );
			echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function remove_agent() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-realestate-agency-remove-agent-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-realestate') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$agent_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : '';
		if ( empty($agent_id) || !($post = get_post($agent_id)) || empty($post->post_type) || $post->post_type !== 'agent' ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Agent not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}

		do_action( 'wp-realestate-process-remove-agent' );

		$user_id = get_current_user_id();
		$agency_id = WP_RealEstate_User::get_agency_by_user_id($user_id);
		$agents = self::get_post_meta($agency_id, 'agents', false);
		if ( !empty($agents) && is_array($agents) ) {
			
		    delete_post_meta($agency_id, WP_REALESTATE_AGENCY_PREFIX.'agents', $agent_id);
			$return = array( 'status' => true, 'msg' => esc_html__('Remove agent from team successful', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;

		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Agent not found', 'wp-realestate') );
			echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_REALESTATE_AGENCY_PREFIX.$key, $single);
	}

	public static function update_post_meta($post_id, $key, $data) {
		return update_post_meta($post_id, WP_REALESTATE_AGENCY_PREFIX.$key, $data);
	}
	
	public static function display_agencies_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/results-count', $args);
	}

	public static function display_agencies_orderby() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/orderby');
	}

	public static function display_agencies_results_count_orderby_start() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/results-count-orderby-start');
	}

	public static function display_agencies_results_count_orderby_end() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/results-count-orderby-end');
	}
}

WP_RealEstate_Agency::init();