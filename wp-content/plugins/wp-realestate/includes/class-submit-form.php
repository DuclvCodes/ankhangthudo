<?php
/**
 * Submit Form
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Submit_Form extends WP_RealEstate_Abstract_Form {
	public $form_name = 'wp_realestate_property_submit_form';
	

	private static $_instance = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_action( 'wp', array( $this, 'process' ) );

		$this->get_steps();

		if ( !empty( $_REQUEST['submit_step'] ) ) {
			$step = is_numeric( $_REQUEST['submit_step'] ) ? max( absint( $_REQUEST['submit_step'] ), 0 ) : array_search( intval( $_REQUEST['submit_step'] ), array_keys( $this->steps ), true );
			$this->step = $step;
		}

		$this->property_id = ! empty( $_REQUEST['property_id'] ) ? absint( $_REQUEST['property_id'] ) : 0;

		if ( ! WP_RealEstate_User::is_user_can_edit_property( $this->property_id ) ) {
			$this->property_id = 0;
		}

		add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );
	}

	public function get_steps() {
		$this->steps = apply_filters( 'wp_realestate_submit_property_steps', array(
			'submit'  => array(
				'view'     => array( $this, 'form_output' ),
				'handler'  => array( $this, 'submit_process' ),
				'priority' => 10,
			),
			'preview' => array(
				'view'     => array( $this, 'preview_output' ),
				'handler'  => array( $this, 'preview_process' ),
				'priority' => 20,
			),
			'done'    => array(
				'before_view' => array( $this, 'done_handler' ),
				'view'     => array( $this, 'done_output' ),
				'priority' => 30,
			)
		));

		uasort( $this->steps, array( 'WP_RealEstate_Mixes', 'sort_array_by_priority' ) );

		return $this->steps;
	}

	public function submit_process() {
		$prefix = WP_REALESTATE_PROPERTY_PREFIX;
		if ( ! isset( $_POST['submit-cmb-property'] ) || empty( $_POST[$prefix.'post_type'] ) || 'property' !== $_POST[$prefix.'post_type'] ) {
			return;
		}
		
		$cmb = cmb2_get_metabox( $prefix . 'fields_front' );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		// Setup and sanitize data
		if ( isset( $_POST[ $prefix . 'title' ] ) ) {
			$post_id = $this->property_id;

			$post_status = 'preview';
			if ( ! empty( $post_id ) ) {
				$old_post = get_post( $post_id );
				$post_date = $old_post->post_date;
				$old_post_status = get_post_status( $post_id );
				if ( $old_post_status === 'draft' ) {
					$post_status = 'preview';
				} else {
					$post_status = $old_post_status;
				}
			} else {
				$post_date = '';
			}

			$data = array(
				'post_title'     => sanitize_text_field( $_POST[ $prefix . 'title' ] ),
				'post_author'    => get_current_user_id(),
				'post_status'    => $post_status,
				'post_type'      => 'property',
				'post_date'      => $post_date,
				'post_content'   => wp_kses_post( $_POST[ $prefix . 'description' ] ),
				'comment_status' => 'open'
			);

			$new_post = true;
			if ( !empty( $post_id ) ) {
				$data['ID'] = $post_id;
				$new_post = false;
			}

			do_action( 'wp-realestate-process-submission-before-save', $post_id, $this );

			$data = apply_filters('wp-realestate-process-submission-data', $data, $post_id);
			
			$this->errors = $this->submission_validate($data);
			if ( sizeof($this->errors) ) {
				return;
			}

			$post_id = wp_insert_post( $data, true );

			if ( ! empty( $post_id ) ) {
				$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of property ID

				$cmb->save_fields( $post_id, 'post', $_POST );

				// Create featured image
				$featured_image = get_post_meta( $post_id, $prefix . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . $prefix . 'featured_image' ] ) ) {
					$img_id = get_post_meta( $post_id, $prefix . 'featured_image_img', true );
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

				// Floor plans
				if ( !empty($_POST[$prefix.'floor_plans_group']) ) {
					$floor_plans_group = $_POST[$prefix.'floor_plans_group'];
					if ( isset($_POST['current_'.$prefix.'floor_plans_group']) ) {
						foreach ($_POST['current_'.$prefix.'floor_plans_group'] as $gkey => $ar_value) {
							foreach ($ar_value as $ikey => $value) {
								if ( is_numeric($value) ) {
									$url = wp_get_attachment_url( $value );
									$floor_plans_group[$gkey][$ikey.'_id'] = $value;
									$floor_plans_group[$gkey][$ikey] = $url;
								} elseif ( ! empty( $value ) ) {
									$attach_id = WP_RealEstate_Image::create_attachment( $value, $post_id );
									$url = wp_get_attachment_url( $attach_id );
									$floor_plans_group[$gkey][$ikey.'_id'] = $attach_id;
									$floor_plans_group[$gkey][$ikey] = $url;
								}
							}
						}
						update_post_meta( $post_id, $prefix.'floor_plans_group', $floor_plans_group );
					}
				}

				do_action( 'wp-realestate-process-submission-after-save', $post_id );

				if ( $new_post ) {
					setcookie( 'property_add_new_update', 'new' );
				} else {
					setcookie( 'property_add_new_update', 'update' );
				}
				$this->property_id = $post_id;
				$this->step ++;

			} else {
				if( $new_post ) {
					$this->errors[] = __( 'Can not create property', 'wp-realestate' );
				} else {
					$this->errors[] = __( 'Can not update property', 'wp-realestate' );
				}
			}
		}

		return;
	}

	public function submission_validate( $data ) {
		$error = array();
		if ( empty($data['post_author']) ) {
			$error[] = __( 'Please login to submit property', 'wp-realestate' );
		}
		if ( empty($data['post_title']) ) {
			$error[] = __( 'Title is required.', 'wp-realestate' );
		}
		if ( empty($data['post_content']) ) {
			$error[] = __( 'Description is required.', 'wp-realestate' );
		}
		$error = apply_filters('wp-realestate-submission-validate', $error);
		return $error;
	}

	public function preview_output() {
		global $post;

		if ( $this->property_id ) {
			$post              = get_post( $this->property_id ); // WPCS: override ok.
			$post->post_status = 'preview';

			setup_postdata( $post );

			echo WP_RealEstate_Template_Loader::get_template_part( 'submission/property-submit-preview', array(
				'post_id' => $this->property_id,
				'property_id'         => $this->property_id,
				'step'           => $this->get_step(),
				'form_obj'           => $this,
			) );
			wp_reset_postdata();
		}
	}

	public function preview_process() {
		if ( ! $_POST ) {
			return;
		}

		if ( !isset( $_POST['security-property-submit-preview'] ) || ! wp_verify_nonce( $_POST['security-property-submit-preview'], 'wp-realestate-property-submit-preview-nonce' )  ) {
			$this->errors[] = esc_html__('Your nonce did not verify.', 'wp-realestate');
			return;
		}

		if ( isset( $_POST['continue-edit-property'] ) ) {
			$this->step --;
		} elseif ( isset( $_POST['continue-submit-property'] ) ) {
			$property = get_post( $this->property_id );

			if ( in_array( $property->post_status, array( 'preview', 'expired' ), true ) ) {
				// Reset expiry.
				delete_post_meta( $property->ID, WP_REALESTATE_PROPERTY_PREFIX.'expiry_date' );

				// Update property listing.
				$review_before = wp_realestate_get_option( 'submission_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$update_property                  = array();
				$update_property['ID']            = $property->ID;
				$update_property['post_status']   = apply_filters( 'wp_realestate_submit_property_post_status', $post_status, $property );
				$update_property['post_date']     = current_time( 'mysql' );
				$update_property['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_property['post_author']   = get_current_user_id();

				wp_update_post( $update_property );
			}

			$this->step ++;
		}
	}

	public function done_output() {
		$property = get_post( $this->property_id );
		
		echo WP_RealEstate_Template_Loader::get_template_part( 'submission/property-submit-done', array(
			'post_id' => $this->property_id,
			'property'	  => $property,
		) );
	}

	public function done_handler() {
		do_action( 'wp_realestate_property_submit_done', $this->property_id );
		
		if ( ! empty( $_COOKIE['property_add_new_update'] ) ) {
			$property_add_new_update = $_COOKIE['property_add_new_update'];

			if ( wp_realestate_get_option('admin_notice_add_new_listing') ) {
				$property = get_post($this->property_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_from, $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'admin_notice_add_new_listing', 'subject');
				$content = WP_RealEstate_Email::render_email_vars(array('property' => $property), 'admin_notice_add_new_listing', 'content');
				
				WP_RealEstate_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
			
			setcookie( 'property_add_new_update', '', time() - HOUR_IN_SECONDS );
		}
	}
}

function wp_realestate_submit_form() {
	if ( ! empty( $_POST['wp_realestate_property_submit_form'] ) ) {
		WP_RealEstate_Submit_Form::get_instance();
	}
}

add_action( 'init', 'wp_realestate_submit_form' );