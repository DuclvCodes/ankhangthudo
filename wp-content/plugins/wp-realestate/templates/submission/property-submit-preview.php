<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $property_preview;
$property_preview = $post;
?>
<div class="property-submission-preview-form-wrapper">
	<?php if ( sizeof($form_obj->errors) ) : ?>
		<ul class="messages">
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<li class="message_line danger">
					<?php echo wp_kses_post( $message ); ?>
				</li>
			<?php
			}
			?>
		</ul>
	<?php endif; ?>
	<form action="<?php echo esc_url($form_obj->get_form_action());?>" class="cmb-form" method="post" enctype="multipart/form-data" encoding="multipart/form-data">
		<input type="hidden" name="<?php echo esc_attr($form_obj->get_form_name()); ?>" value="<?php echo esc_attr($form_obj->get_form_name()); ?>">
		<input type="hidden" name="property_id" value="<?php echo esc_attr($property_id); ?>">
		<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
		<input type="hidden" name="object_id" value="<?php echo esc_attr($property_id); ?>">
		<?php wp_nonce_field('wp-realestate-property-submit-preview-nonce', 'security-property-submit-preview'); ?>

		<button class="button btn" name="continue-submit-property"><?php esc_html_e('Submit Property', 'wp-realestate'); ?></button>
		<button class="button btn" name="continue-edit-property"><?php esc_html_e('Edit Property', 'wp-realestate'); ?></button>

		<?php echo WP_RealEstate_Template_Loader::get_template_part( 'content-single-property' ); ?>
	</form>
</div>
