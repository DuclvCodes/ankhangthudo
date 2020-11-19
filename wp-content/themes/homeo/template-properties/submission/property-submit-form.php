<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="property-submission-form-wrapper">
	<h1 class="title-profile"><?php echo esc_html__('Add New Property','homeo') ?></h1>
	<div class="box-submit">
		<?php if ( sizeof($form_obj->errors) ) : ?>
			
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<div class="alert alert-danger margin-bottom-15">
					<?php echo wp_kses_post( $message ); ?>
				</div>
			<?php
			}
			?>
		<?php endif; ?>

		<?php if ( sizeof($form_obj->success_msg) ) : ?>
			<?php foreach ( $form_obj->success_msg as $message ) { ?>
				<div class="alert alert-success margin-bottom-15">
					<?php echo wp_kses_post( $message ); ?>
				</div>
			<?php
			}
			?>
		<?php endif; ?>
		
		<?php
			echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
				'form_format' => '<form action="' . $form_obj->get_form_action() . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="property_id" value="'.$property_id.'"><input type="hidden" name="'.$form_obj->get_form_name().'" value="'.$form_obj->get_form_name().'"><input type="hidden" name="submit_step" value="'.$step.'"><input type="hidden" name="object_id" value="%2$s">%3$s
					<div class="submit-button-wrapper">
						<button type="submit" name="submit-cmb-property" value="%4$s" class="btn btn-theme btn-inverse border-2">%4$s</button>
					</div>
					</form>',
				'save_button' => $submit_button_text,
			) );
		?>
	</div>
</div>