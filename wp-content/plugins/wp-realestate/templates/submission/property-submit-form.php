<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="property-submission-form-wrapper">
	
	<?php if ( sizeof($form_obj->errors) ) : ?>
		<ul class="messages errors">
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<li class="message_line danger">
					<?php echo wp_kses_post( $message ); ?>
				</li>
			<?php
			}
			?>
		</ul>
	<?php endif; ?>

	<?php if ( sizeof($form_obj->success_msg) ) : ?>
		<ul class="messages success">
			<?php foreach ( $form_obj->success_msg as $message ) { ?>
				<li class="message_line info">
					<?php echo wp_kses_post( $message ); ?>
				</li>
			<?php
			}
			?>
		</ul>
	<?php endif; ?>

	<?php
		echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
			'form_format' => '<form action="' . $form_obj->get_form_action() . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="property_id" value="'.$property_id.'"><input type="hidden" name="'.$form_obj->get_form_name().'" value="'.$form_obj->get_form_name().'"><input type="hidden" name="submit_step" value="'.$step.'"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb-property" value="%4$s" class="button-primary"></form>',
			'save_button' => $submit_button_text,
		) );
	?>
</div>
