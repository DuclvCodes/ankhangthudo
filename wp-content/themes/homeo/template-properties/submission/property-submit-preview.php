<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $property_preview;
$property_preview = $post;

$property_layout = homeo_get_property_layout_type();
$property_layout = !empty($property_layout) ? $property_layout : 'v1';
?>
<div class="property-submission-preview-form-wrapper">
	<?php if ( sizeof($form_obj->errors) ) : ?>
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<div class="alert alert-danger margin-bottom-15">
					<?php echo wp_kses_post( $message ); ?>
				</div>
			<?php
			}
			?>
	<?php endif; ?>
	<form action="<?php echo esc_url($form_obj->get_form_action());?>" class="cmb-form" method="post" enctype="multipart/form-data" encoding="multipart/form-data">
		<input type="hidden" name="<?php echo esc_attr($form_obj->get_form_name()); ?>" value="<?php echo esc_attr($form_obj->get_form_name()); ?>">
		<input type="hidden" name="property_id" value="<?php echo esc_attr($property_id); ?>">
		<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
		<input type="hidden" name="object_id" value="<?php echo esc_attr($property_id); ?>">
		<?php wp_nonce_field('wp-realestate-property-submit-preview-nonce', 'security-property-submit-preview'); ?>
		<div class="wrapper-action-property">
			<button class="button btn btn-sm btn-theme" name="continue-submit-property"><?php esc_html_e('Submit Property', 'homeo'); ?></button>
			<button class="button btn btn-sm btn-warning" name="continue-edit-property"><?php esc_html_e('Edit Property', 'homeo'); ?></button>
		</div>
		

	</form>

	<?php
	$latitude = WP_RealEstate_Property::get_post_meta( $post->ID, 'map_location_latitude', true );
	$longitude = WP_RealEstate_Property::get_post_meta( $post->ID, 'map_location_longitude', true );
	?>
	<div class="single-property-wrapper single-listing-wrapper" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>">
		<?php
			if ( $property_layout !== 'v1' ) {
				echo WP_RealEstate_Template_Loader::get_template_part( 'content-single-property-'.$property_layout );
			} else {
				echo WP_RealEstate_Template_Loader::get_template_part( 'content-single-property' );
			}
		?>
	</div>
</div>
