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

class WP_RealEstate_Agent {
	
	public static function init() {
		add_action( 'wp_realestate_before_agent_archive', array( __CLASS__, 'display_agents_results_count_orderby_start' ), 5 );
		add_action( 'wp_realestate_before_agent_archive', array( __CLASS__, 'display_agents_count_results' ), 10 );
		add_action( 'wp_realestate_before_agent_archive', array( __CLASS__, 'display_agents_orderby' ), 15 );
		add_action( 'wp_realestate_before_agent_archive', array( __CLASS__, 'display_agents_results_count_orderby_end' ), 100 );
		
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
	}

	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'agent' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'agent' || $suppress_filters ) {
			return;
		}

		$limit = wp_realestate_get_option('number_agents_per_page', 10);
		$query_vars = &$query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;
		
		return $query;
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_REALESTATE_AGENT_PREFIX.$key, $single);
	}

	public static function update_post_meta($post_id, $key, $data) {
		return update_post_meta($post_id, WP_REALESTATE_AGENT_PREFIX.$key, $data);
	}
	
	public static function display_agents_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/results-count', $args);
	}

	public static function display_agents_orderby() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/orderby');
	}

	public static function display_agents_results_count_orderby_start() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/results-count-orderby-start');
	}

	public static function display_agents_results_count_orderby_end() {
		echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/results-count-orderby-end');
	}
}

WP_RealEstate_Agent::init();