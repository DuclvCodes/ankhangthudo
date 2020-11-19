<?php
/**
 * Mixes
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Mixes {
	
	public static function is_allowed_to_remove( $user_id, $item_id ) {
		$item = get_post( $item_id );

		if ( ! empty( $item->post_author ) ) {
			return $item->post_author == $user_id ;
		}

		return false;
	}
	
	public static function redirect($redirect_url) {
		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/' );
		}

		wp_redirect( $redirect_url );
		exit();
	}

	public static function get_email_content($type, $args) {
		if ( $type == 'new_message' ) {
			$content = wp_private_message_get_option('user_notice_add_new_message_content');
			$message_content = get_post_field('post_content', $args['message_id']);
		} else {
			$content = wp_private_message_get_option('user_notice_replied_message_content');
			$message_content = get_post_field('post_content',$args['reply_id']);
		}
		if ( empty($content) ) {
			return $content;
		}
		$website_name = get_bloginfo( 'name' );
		$website_url = home_url();
		$message_subject = get_the_title($args['message_id']);
		
		$user_name = $args['user']->display_name;
		$dashboard_id = wp_private_message_get_option('message_dashboard_page_id');
		$dashboard_link = get_permalink($dashboard_id);
		$message_dashboard = $dashboard_link;
		$message_detail_url = add_query_arg( 'id', $post->ID, remove_query_arg( 'id', $dashboard_link ) );

		$content = str_replace('{{message_content}}', $message_content, $content);
		$content = str_replace('{{website_name}}', $website_name, $content);
		$content = str_replace('{{website_url}}', $website_url, $content);
		$content = str_replace('{{message_subject}}', $message_subject, $content);
		$content = str_replace('{{user_name}}', $user_name, $content);
		$content = str_replace('{{message_dashboard}}', $message_dashboard, $content);
		$content = str_replace('{{message_detail_url}}', $message_detail_url, $content);
		return $content;
	}

	public static function get_current_user_id() {
		$user_id = get_current_user_id();
		return apply_filters('wp-private-message-get-current-user-id', $user_id);
	}
}
