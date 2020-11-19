<?php
/**
 * Review
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Review {
	
	protected static $post_ids = array ();

	public static function init() {
		add_filter( 'comments_template', array( __CLASS__, 'comments_template_loader') );

		add_action( 'comment_post', array( __CLASS__, 'save_rating_comment'), 10, 3 );

		add_action( 'comment_unapproved_to_approved', array( __CLASS__,'save_ratings_average'), 10 );
		add_action( 'comment_approved_to_unapproved', array( __CLASS__,'save_ratings_average'), 10 );
		add_action( 'comment_approved_to_trash', array( __CLASS__,'save_ratings_average'), 10 );
		add_action( 'comment_trash_to_approved', array( __CLASS__,'save_ratings_average'), 10 );
		add_action( 'comment_approved_to_spam', array( __CLASS__,'save_ratings_average'), 10 );
		add_action( 'comment_spam_to_approved', array( __CLASS__,'save_ratings_average'), 10 );

		add_action( 'comment_form_top', array( __CLASS__, 'comment_rating_fields' ) );
	}

	public static function review_enable($post_id = null) {
	    if ( empty($post_id) ) {
	        $post_id = get_the_ID();
	    }
	    $post_type = get_post_type();
	    if ( $post_type == 'agent' || $post_type == 'agency' || $post_type == 'property' ) {
	    	if ( ! comments_open($post_id) || wp_realestate_get_option('enable_'.$post_type.'_review', 'on') != 'on' ) {
		        return false;
		    }
		    return true;
	    }
	    return false;
	}

	public static function comments_template_loader($template) {
	    if ( get_post_type() === 'agent') {
	        return WP_RealEstate_Template_Loader::locate('single-agent/reviews');
	    } elseif ( get_post_type() === 'agency' ) {
	    	return WP_RealEstate_Template_Loader::locate('single-agency/reviews');
	    } elseif ( get_post_type() === 'property' ) {
	    	return WP_RealEstate_Template_Loader::locate('single-property/reviews');
	    }
	    return $template;
	}
	
	// comment list
	public static function agent_comments( $comment, $args, $depth ) {
	    echo WP_RealEstate_Template_Loader::get_template_part( 'single-agent/review', array('comment' => $comment, 'args' => $args, 'depth' => $depth) );
	}

	public static function agency_comments( $comment, $args, $depth ) {
	    echo WP_RealEstate_Template_Loader::get_template_part( 'single-agency/review', array('comment' => $comment, 'args' => $args, 'depth' => $depth) );
	}

	public static function property_comments( $comment, $args, $depth ) {
	    echo WP_RealEstate_Template_Loader::get_template_part( 'single-property/review', array('comment' => $comment, 'args' => $args, 'depth' => $depth) );
	}

	public static function user_reviews( $comment, $args, $depth ) {
	    echo WP_RealEstate_Template_Loader::get_template_part( 'misc/user-review-item', array('comment' => $comment, 'args' => $args, 'depth' => $depth) );
	}

	// add comment meta
	public static function save_rating_comment( $comment_id, $comment_approved, $commentdata ) {
	    $post_type = get_post_type($commentdata['comment_post_ID']);
	    if ( $post_type == 'agent' || $post_type == 'agency' || $post_type == 'property' ) {
	    	if ( isset($_POST['rating']) ) {

	    		update_comment_meta( $comment_id, '_rating', $_POST['rating'] );
	    		if ( is_array($_POST['rating']) ) {
			        $total = 0;
			        foreach ($_POST['rating'] as $key => $value) {
			            $total += intval($value);
			        }
			        $avg = round($total/count($_POST['rating']),2);
			    } else {
			    	$avg = $_POST['rating'];
			    }
		        update_comment_meta( $comment_id, '_rating_avg', $avg );

		        //add_comment_meta( $comment_id, '_rating', $_POST['rating'] );
		        
		        if ( $commentdata['comment_approved'] ) {
			        $average_rating = self::get_total_rating( $commentdata['comment_post_ID'] );
		        	update_post_meta( $commentdata['comment_post_ID'], '_average_rating', $average_rating );

		        	$avg_ratings = self::get_total_ratings($commentdata['comment_post_ID']);
        			update_post_meta( $commentdata['comment_post_ID'], '_average_ratings', $avg_ratings );
		        }
		    }
	    }
	}

	public static function save_ratings_average($comment) {
		$post_id = $comment->comment_post_ID;
	    $post_type = get_post_type($post_id);

	    if ( $post_type == 'agent' || $post_type == 'agency' || $post_type == 'property' ) {
	        $average_rating = self::get_total_rating( $post_id );
	        update_post_meta( $post_id, '_average_rating', $average_rating );

	        $avg_ratings = self::get_total_ratings($post_id);
        	update_post_meta( $post_id, '_average_ratings', $avg_ratings );
	    }
	}

	public static function get_ratings_average($post_id) {
	    return get_post_meta( $post_id, '_average_rating', true );
	}

	public static function get_review_comments( $args = array() ) {
	    $args = wp_parse_args( $args, array(
	        'status' => 'approve',
	        'post_id' => '',
	        'user_id' => '',
	        'post_type' => 'agent',
	        'number' => 0
	    ));
	    extract($args);

	    $cargs = array(
	        'status' => 'approve',
	        'post_type' => $post_type,
	        'number' => $number,
	        'meta_query' => array(
	            array(
	               'key' => '_rating',
	               'value' => 0,
	               'compare' => '>',
	            )
	        )
	    );
	    if ( !empty($post_id) ) {
	        $cargs['post_id'] = $post_id;
	    }
	    if ( !empty($user_id) ) {
	        $cargs['user_id'] = $user_id;
	    }

	    $comments = get_comments( $cargs );
	    
	    return $comments;
	}

	public static function get_total_reviews( $post_id ) {
		$post_type = get_post_type($post_id);
	    $args = array( 'post_id' => $post_id, 'post_type' => $post_type );
	    $comments = self::get_review_comments($args);

	    if (empty($comments)) {
	        return 0;
	    }
	    
	    return count($comments);
	}

	public static function get_total_rating( $post_id ) {
		$post_type = get_post_type($post_id);
	    $args = array( 'post_id' => $post_id, 'post_type' => $post_type );
	    $comments = self::get_review_comments($args);
	    if (empty($comments)) {
	        return 0;
	    }
	    $total_review = 0;
	    foreach ($comments as $comment) {
	        $rating = intval( get_comment_meta( $comment->comment_ID, '_rating_avg', true ) );
	        if ($rating) {
	            $total_review += (int)$rating;
	        }
	    }
	    return round($total_review/count($comments),2);
	}

	public static function get_total_ratings( $post_id ) {
	    $args = array( 'post_id' => $post_id );
	    $comments = self::get_review_comments($args);
	    if (empty($comments)) {
	        return;
	    }
	    $reviews = array();
	    foreach ($comments as $comment) {
	        $ratings = get_comment_meta( $comment->comment_ID, '_rating', true );

	        if ( !empty($ratings) && is_array($ratings) ) {
	            foreach ($ratings as $category => $value) {
	                if ( isset($reviews[$category]) ) {
	                    $reviews[$category] = $reviews[$category] + $value;
	                } else {
	                    $reviews[$category] = $value;
	                }
	            }
	        }
	    }
	    if ( !empty($reviews) ) {
	        foreach ($reviews as $category => $total) {
	            $reviews[$category] = round($total/count($comments),2);
	        }
	    }
	    
	    return $reviews;
	}

	public static function get_total_rating_by_user( $user_id, $post_type ) {
	    $args = array( 'user_id' => $user_id, 'post_type' => $post_type );
	    $comments = self::get_review_comments($args);

	    if (empty($comments)) {
	        return 0;
	    }
	    $total_review = 0;
	    foreach ($comments as $comment) {
	        $rating = intval( get_comment_meta( $comment->comment_ID, '_rating_avg', true ) );
	        if ($rating) {
	            $total_review += (int)$rating;
	        }
	    }
	    return $total_review/count($comments);
	}

	public static function get_detail_ratings( $post_id ) {
	    global $wpdb;
	    $comment_ratings = $wpdb->get_results( $wpdb->prepare(
	        "
	            SELECT cm2.meta_value AS rating, COUNT(*) AS quantity FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->comments AS c ON (p.ID = c.comment_post_ID AND c.comment_approved=1)
	            INNER JOIN $wpdb->commentmeta AS cm2 ON cm2.comment_id = c.comment_ID AND cm2.meta_key=%s
	            WHERE p.ID=%d
	            GROUP BY cm2.meta_value",
	            '_rating',
	            $post_id
	        ), OBJECT_K
	    );
	    return $comment_ratings;
	}

    public static function get_comments( $args = array(), $post_ids = array() ) {
        if ( array () !== $post_ids ) {
            self::$post_ids = $post_ids;
            add_filter( 'comments_clauses', array( __CLASS__, 'filter_where_clause' ) );
        }
        return get_comments( $args );
    }

    public static function filter_where_clause( $q ) {
        $ids       = implode( ', ', self::$post_ids );
        $_where_in = " AND comment_post_ID IN ( $ids )";

        if ( FALSE !== strpos( $q['where'], ' AND comment_post_ID =' ) ) {
            $q['where'] = preg_replace(
                '~ AND comment_post_ID = \d+~',
                $_where_in,
                $q['where']
            );
        } else {
            $q['where'] .= $_where_in;
        }

        remove_filter( 'comments_clauses', array( __CLASS__, 'filter_where_clause' ) );
        return $q;
    }


	public static function comment_rating_fields ($default_val = array()) {
		global $post;

		if ( !in_array($post->post_type, array('agency', 'agent', 'property')) ) {
			return;
		}

	    $html = '';
	    ob_start();
	    
	    $categories = wp_realestate_get_option($post->post_type.'_review_category');
	    if ( self::review_enable() ) {
	    	if ( empty($categories) ) {
	    		$categories = array(
	    			array(
	    				'key' => 'default-key',
	    				'name' => '',
	    			)
	    		);
	    	}
	        ?>
	        <div class="rating-wrapper comment-form-rating">
	        <?php
		        foreach ($categories as $category) {
		            $value = isset($default_val[$category['key']]) ? $default_val[$category['key']] : 5;
		            ?>
		            <div class="rating-inner">
		                <div class="comment-form-rating">
		                	<?php if ( !empty($category['name']) ) { ?>
			                    <span class="subtitle"><?php echo esc_html($category['name']); ?></span>
			                <?php } ?>
		                    <ul class="review-stars">
		                        <?php
		                            for ($i=1; $i <= 5; $i++) { 
		                            	?>
		                                <li><span class="fas fa-star <?php echo esc_attr($i <= $value ? 'active' : ''); ?>"></span></li>
		                                <?php
		                            }
		                        ?>
		                    </ul>
		                    <input type="hidden" value="<?php echo esc_attr($value); ?>" name="rating[<?php echo esc_html($category['key']); ?>]" class="rating">
		                </div>
		            </div>
		            <?php
		        }
	        ?>
	        </div>
	        <?php
	    }

	    $html = ob_get_clean();
	    echo $html;
	}

	public static function print_review( $rate, $type = '', $nb = 0 ) {
	    ?>
	    <div class="review-stars-rated-wrapper">
	        <div class="review-stars-rated">
	            <ul class="review-stars">
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	            </ul>
	            
	            <ul class="review-stars filled"  style="<?php echo esc_attr( 'width: ' . ( $rate * 20 ) . '%' ) ?>" >
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	                <li><span class="fas fa-star"></span></li>
	            </ul>
	        </div>
	        <?php if ($type == 'detail') { ?>
	            <span class="nb-review"><?php echo sprintf(_n('%d Review', '%d Reviews', $nb, 'wp-realestate'), $nb); ?></span>
	        <?php } elseif ($type == 'list') { ?>
	            <span class="nb-review"><?php echo sprintf(_n('(%d Review)', '(%d Reviews)', $nb, 'wp-realestate'), $nb); ?></span>
	        <?php } ?>
	    </div>
	    <?php
	}

}

WP_RealEstate_Review::init();