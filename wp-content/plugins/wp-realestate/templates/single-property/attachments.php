<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);

$attachments = $meta_obj->get_post_meta('attachments');
?>
<?php if ( $meta_obj->check_post_meta_exist('attachments') && $attachments ) {
	// $admin_url = admin_url( 'admin-ajax.php' );
	$admin_url = WP_RealEstate_Ajax::get_endpoint('wp_realestate_ajax_download_attachment');
?>
	<div class="property-section property-attachments">
		<h3 class="title"><?php echo esc_html__( 'Attachments', 'wp-realestate' ); ?></h3>
		<div class="attachments-inner">
			<?php foreach ($attachments as $id => $attachment_url) {
		        $file_info = pathinfo($attachment_url);
		        if ( $file_info ) {
		            $download_url = add_query_arg(array('file_id' => $id), $admin_url);
		        ?>
		            <div class="attachment-item">
		                <span class="icon_type">
		                    <?php if ( !empty($file_info['extension']) ) {
		                        switch ($file_info['extension']) {
		                            case 'doc':
		                            case 'docx':
		                                ?>
		                                <i class="flaticon-document"></i>
		                                <?php
		                                break;
		                            
		                            case 'pdf':
		                                ?>
		                                <i class="flaticon-pdf"></i>
		                                <?php
		                                break;
		                            default:
		                                ?>
		                                <i class="flaticon-document"></i>
		                                <?php
		                                break;
		                        }
		                    } ?>
		                </span>
		                <a href="<?php echo esc_url($download_url); ?>" class="candidate-detail-attachment">
		                	<i class="flaticon-download"></i>
			                <?php if ( !empty($file_info['basename']) ) { ?>
			                    <span class="basename"><?php echo esc_html($file_info['basename']); ?></span>
			                <?php } ?>
			            </a>
		            </div>
		        <?php }
		    }?>
		</div>

		<?php do_action('wp-realestate-single-property-attachments', $post); ?>
	</div>
<?php }