<?php
/**
 * Private Message
 *
 * @package    wp-private-message
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Private_Message_Message {
	
	public static function init() {
		add_action( 'wp_ajax_wp_private_message_send_message',  array(__CLASS__,'process_send_message') );
		add_action( 'wp_ajax_nopriv_wp_private_message_send_message',  array(__CLASS__,'process_send_message') );

		add_action( 'wp_ajax_wp_private_message_reply_message',  array(__CLASS__,'process_reply_message') );
		add_action( 'wp_ajax_nopriv_wp_private_message_reply_message',  array(__CLASS__,'process_reply_message') );

		add_action( 'wp_ajax_wp_private_message_choose_message',  array(__CLASS__,'process_choose_message') );
		add_action( 'wp_ajax_nopriv_wp_private_message_choose_message',  array(__CLASS__,'process_choose_message') );

		add_action( 'wp_ajax_wp_private_message_search_message',  array(__CLASS__,'process_search_message') );
		add_action( 'wp_ajax_nopriv_wp_private_message_search_message',  array(__CLASS__,'process_search_message') );

		add_action( 'wp_ajax_wp_private_message_message_loadmore',  array(__CLASS__,'process_message_loadmore') );
		add_action( 'wp_ajax_nopriv_wp_private_message_message_loadmore',  array(__CLASS__,'process_message_loadmore') );

		add_action( 'wp_ajax_wp_private_message_replied_loadmore',  array(__CLASS__,'process_replied_loadmore') );
		add_action( 'wp_ajax_nopriv_wp_private_message_replied_loadmore',  array(__CLASS__,'process_replied_loadmore') );

		add_action( 'wp_ajax_wp_private_message_delete_message',  array(__CLASS__,'process_delete_message') );
		add_action( 'wp_ajax_nopriv_wp_private_message_delete_message',  array(__CLASS__,'process_delete_message') );
	}
	
	public static function process_send_message() {
		$return = array();
		if ( !isset( $_POST['wp-private-message-send-message-nonce'] ) || ! wp_verify_nonce( $_POST['wp-private-message-send-message-nonce'], 'wp-private-message-send-message' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Sorry, your nonce did not verify.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to send a message.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$recipient = !empty($_POST['recipient']) ? $_POST['recipient'] : '';
		$user = get_user_by('id', $recipient);

		if ( empty($user->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Recipient did not exists.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$user_id = WP_Private_Message_Mixes::get_current_user_id();
		$message_id = self::insert_message($user_id, $recipient);
		
        if ( $message_id ) {
        	// Send Email
        	if ( wp_private_message_get_option('user_notice_add_new_message') ) {
	        	$sender_info = get_userdata($user_id);
	        	$email_from = $sender_info->user_email;

				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_from, $email_from );
				$email_to = $user->user_email;
				$subject = wp_private_message_get_option('user_notice_add_new_message_subject');
				$content = WP_Private_Message_Mixes::get_email_content('new_message', array(
					'message_id' => $message_id,
					'sender_info' => $sender_info,
					'user' => $user,
				));
				
				wp_mail( $email_to, $subject, $content, $headers );
			}

	        $return = array( 'status' => true, 'msg' => esc_html__('Sent message successful.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Send message error.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function insert_message($user_id, $recipient) {
		$message_args = array(
            'post_title' => isset($_POST['subject']) ? $_POST['subject'] : '',
            'post_type' => 'private_message',
            'post_content' => isset($_POST['message']) ? $_POST['message'] : '',
            'post_status' => 'publish',
            'post_author' => $user_id,
        );
		$message_args = apply_filters('wp-private-message-add-message-data', $message_args);
		do_action('wp-private-message-before-add-message');

        // Insert the post into the database
        $message_id = wp_insert_post($message_args);
        if ( $message_id ) {
        	update_post_meta($message_id, '_read_'.$user_id, 'yes');
	        update_post_meta($message_id, '_sender', $user_id);
	        update_post_meta($message_id, '_recipient', $recipient);
	        
	        do_action('wp-private-message-after-add-message', $message_id, $recipient, $user_id);
	    }

	    return $message_id;
	}

	public static function process_reply_message() {
		$return = array();
		if ( !isset( $_POST['wp-private-message-reply-message-nonce'] ) || ! wp_verify_nonce( $_POST['wp-private-message-reply-message-nonce'], 'wp-private-message-reply-message' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Sorry, your nonce did not verify.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to reply a message.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$parent = !empty($_POST['parent']) ? $_POST['parent'] : '';
		$post = get_post($parent);

		if ( empty($post) || $post->post_type !== 'private_message' ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message did not exists.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$user_id = WP_Private_Message_Mixes::get_current_user_id();
		$reply_args = array(
            'post_title' => 'RE: '.$post->post_title,
            'post_type' => 'private_message',
            'post_content' => isset($_POST['message']) ? $_POST['message'] : '',
            'post_status' => 'publish',
            'post_parent' => $parent,
            'post_author' => $user_id,
        );
		$reply_args = apply_filters('wp-private-message-reply-message-data', $reply_args);
		do_action('wp-private-message-before-reply-message');

        // Insert the post into the database
        $reply_id = wp_insert_post($reply_args);

        if ( $reply_id ) {
        	do_action('wp-private-message-after-reply-message', $reply_id, $parent, $user_id);

        	$sender = get_post_meta($parent, '_sender', true);
        	$recipient = get_post_meta($parent, '_recipient', true);
        	$rpost = get_post($reply_id);
        	$output = WP_Private_Message_Template_Loader::get_template_part( 'reply-item', array('rpost' => $rpost ) );


        	if ( $user_id == $sender ) {
	        	delete_post_meta($post->ID, '_read_'.$recipient);
	        	$email_user_id = $recipient;
	        } else {
	        	delete_post_meta($post->ID, '_read_'.$sender);
	        	$email_user_id = $sender;
	        }

        	// Send Email
        	if ( wp_private_message_get_option('user_notice_add_new_message') ) {
	        	$sender_info = get_userdata($user_id);
	        	$email_from = $sender_info->user_email;
	        	$user = get_userdata($email_user_id);

				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_from, $email_from );
				$email_to = $user->user_email;
				$subject = wp_private_message_get_option('user_notice_replied_message_subject');
				$content = WP_Private_Message_Mixes::get_email_content('reply_message', array(
					'reply_id' => $reply_id,
					'sender_info' => $sender_info,
					'user' => $user,
				));
				
				wp_mail( $email_to, $subject, $content, $headers );
			}

	        $return = array( 'status' => true, 'msg' => $output );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Reply message error.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_choose_message() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-private-message-choose-message-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Sorry, your nonce did not verify.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$message_id = isset($_POST['message_id']) ? $_POST['message_id'] : '';
		if ( empty($message_id) || !($post = get_post($message_id)) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is not exactly', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		

		$output = WP_Private_Message_Template_Loader::get_template_part( 'reply-section', array( 'post' => $post ) );
	 	$return = array( 'status' => true, 'msg' => $output );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_search_message() {
		$return = array();
		if ( !isset( $_POST['wp-private-message-search-message-nonce'] ) || ! wp_verify_nonce( $_POST['wp-private-message-search-message-nonce'], 'wp-private-message-search-message' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Sorry, your nonce did not verify.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		$search_read = isset($_POST['search_read']) ? $_POST['search_read'] : '';

		ob_start();
		if ( $search || $search_read ) {
			$user_id = WP_Private_Message_Mixes::get_current_user_id();
			$args = array(
				'post_per_page' => wp_private_message_get_option('number_message_per_page', 10),
				'paged' => 1,
				'author' => $user_id,
				'search' => $search,
			);
			switch ($search_read) {
				case 'read':
					$args['meta_query'][] = array(
						array(
				    		'key'       => '_read_'.$user_id,
							'value'     => 'yes',
							'compare'   => '==',
				    	),
					);
					break;
				case 'unread':
					$args['meta_query'][] = array(
						'relation' => 'OR',
						array(
				    		'key'       => '_read_'.$user_id,
							'value'     => '',
							'compare'   => '==',
				    	),
				    	array(
							'key' => '_read_'.$user_id,
							'compare' => 'NOT EXISTS',
						)
					);
					break;
			}

			$loop = self::get_list_messages($args);

			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) : $loop->the_post();
					global $post;

					echo WP_Private_Message_Template_Loader::get_template_part( 'message-item', array( 'classes' => '', 'post' => $post ) );

				endwhile;
				wp_reset_postdata();
			} else {
				?>
				<li class="not-found"><?php esc_html_e('No message found', 'wp-private-message'); ?></li>
				<?php
			}
		}
		$output = ob_get_clean();
		$next_page = '';
		if ( $loop->max_num_pages >= 2 ) {
			$next_page = '<div class="loadmore-action">
							<a href="javascript:void(0);" class="loadmore-message-btn" data-paged="2">'.esc_html__( 'Load more', 'wp-private-message' ).'</a>
						</div>';
		}
		$return = array( 'status' => true, 'output' => $output, 'next_page' => $next_page );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_message_loadmore() {
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		$paged = isset($_POST['paged']) ? $_POST['paged'] : 2;
		
		do_action('wp-private-message-before-message-loadmore');

		ob_start();
		$user_id = WP_Private_Message_Mixes::get_current_user_id();
		$args = array(
			'post_per_page' => wp_private_message_get_option('number_message_per_page', 10),
			'paged' => $paged,
			'author' => $user_id,
			'search' => $search,
		);
		$loop = self::get_list_messages($args);

		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				global $post;

				echo WP_Private_Message_Template_Loader::get_template_part( 'message-item', array( 'classes' => '', 'post' => $post ) );

			endwhile;
			wp_reset_postdata();
		}
		$output = ob_get_clean();
		$next_page = 0;
		if ( $paged < $loop->max_num_pages ) {
			$next_page = $paged + 1;
		}
		$return = array( 'status' => true, 'next_page' => $next_page, 'output' => $output );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_replied_loadmore() {
		$paged = isset($_POST['paged']) ? $_POST['paged'] : 2;
		$parent = isset($_POST['parent']) ? $_POST['parent'] : '';
		if ( empty($parent) || !($pmessage = get_post($parent)) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is not exactly', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-private-message-before-replied-loadmore');

		$sender = get_post_meta($parent, '_sender', true);
		ob_start();
		$user_id = WP_Private_Message_Mixes::get_current_user_id();
		$args = array(
		    'post_per_page' => wp_private_message_get_option('number_replied_per_page', 25),
		    'paged' => $paged,
		    'parent' => $parent,
	  	);
	  	$reply_messages = WP_Private_Message_Message::get_list_reply_messages($args);

	  	$next_page = 0;
		if ( $paged < $reply_messages->max_num_pages ) {
			$next_page = $paged + 1;
		} else {
			echo WP_Private_Message_Template_Loader::get_template_part( 'reply-item', array( 'rpost' => $pmessage ) );
		}

		if ( $reply_messages->have_posts() ) {
			$posts = $reply_messages->posts;
        	$posts = array_reverse($posts, true);
        	foreach ($posts as $post) {
        		echo WP_Private_Message_Template_Loader::get_template_part( 'reply-item', array( 'rpost' => $post ) );
        	}
		}
		
		
		$output = ob_get_clean();

		$return = array( 'status' => true, 'next_page' => $next_page, 'output' => $output );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_delete_message() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-private-message-delete-message-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Sorry, your nonce did not verify.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$message_id = isset($_POST['message_id']) ? $_POST['message_id'] : '';
		if ( empty($message_id) || !($post = get_post($message_id)) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is not exactly', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		do_action('wp-private-message-before-delete-message');

		// delete replied
		$args = array(
			'parent' => $message_id,
		);
		$replies = self::get_list_reply_messages($args);
		if ( $replies->have_posts() ) {
			foreach ($replies->posts as $post) {
				wp_delete_post( $post->ID, true );
			}
		}
		$delete_post = wp_delete_post( $message_id, true );
		if ( $delete_post ) {
			$return = array( 'status' => true, 'msg' => esc_html__('Message is successful', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is error.', 'wp-private-message') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function get_list_messages($params) {
		$params = wp_parse_args( $params, array(
			'post_per_page' => -1,
			'paged' => 1,
			'post_status' => 'publish',
			'post__in' => array(),
			'fields' => null, // ids
			'author' => null,
			'meta_query' => array(),
			'tax_query' => array(),
			'search' => '',
		));
		extract($params);

		$query_args = array(
			'post_type'         => 'private_message',
			'paged'         	=> $paged,
			'posts_per_page'    => $post_per_page,
			'post_status'       => $post_status,
			'order'       		=> 'DESC',
			'orderby'       	=> 'date',
			'post_parent'       		=> 0,
		);
		if ( !empty($post__in) ) {
	    	$query_args['post__in'] = $post__in;
	    }
	    if ( !empty($post__in) ) {
	    	$query_args['post__in'] = $post__in;
	    }

	    if ( !empty($search) ) {
	    	$query_args['s'] = $search;
	    }
	    if ( !empty($author) ) {
	    	$meta_query[] = array(
	    		'relation' => 'OR',
	    		array(
		    		'key'       => '_sender',
					'value'     => $author,
					'compare'   => '==',
		    	),
		    	array(
		    		'key'       => '_recipient',
					'value'     => $author,
					'compare'   => '==',
		    	),
	    	);
	    }
	    if ( !empty($meta_query) ) {
	    	$query_args['meta_query'] = $meta_query;
	    }
	    return new WP_Query( $query_args );
	}

	public static function get_list_reply_messages($params) {
		$params = wp_parse_args( $params, array(
			'post_per_page' => -1,
			'paged' => 1,
			'post_status' => 'publish',
			'post__in' => array(),
			'fields' => null, // ids
			'meta_query' => null,
			'tax_query' => null,
			'parent' => 0,
			'order' => 'DESC',
			'orderby' => 'modified',
		));
		extract($params);

		$query_args = array(
			'post_type'         => 'private_message',
			'paged'         	=> $paged,
			'posts_per_page'    => $post_per_page,
			'post_status'       => $post_status,
			'order'       		=> $order,
			'orderby'       	=> $orderby,
			'post_parent'       => $parent,
		);
		if ( !empty($post__in) ) {
	    	$query_args['post__in'] = $post__in;
	    }

	    if ( !empty($fields) ) {
	    	$query_args['fields'] = $fields;
	    }

	    return new WP_Query( $query_args );
	}
}
WP_Private_Message_Message::init();