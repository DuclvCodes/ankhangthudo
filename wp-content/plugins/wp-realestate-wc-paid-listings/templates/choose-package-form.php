<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$form      = WP_RealEstate_Submit_Form::get_instance();
$property_id    = $form->get_property_id();
$step      = $form->get_step();
$form_name = $form->get_form_name();

$user_id = get_current_user_id();
$user_packages = WP_RealEstate_Wc_Paid_Listings_Mixes::get_packages_by_user($user_id, true);
$packages = WP_RealEstate_Wc_Paid_Listings_Submit_Form::get_products();

?>
<form method="post" id="property_package_selection">
	<?php if ( empty($property_id) || WP_RealEstate_User::is_user_can_edit_property( $property_id ) ) { ?>
		<div class="property_property_packages_title">
			<input type="hidden" name="property_id" value="<?php echo esc_attr( $property_id ); ?>" />

			<input type="hidden" name="<?php echo esc_attr($form_name); ?>" value="<?php echo esc_attr($form_name); ?>">
			<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
			<input type="hidden" name="object_id" value="<?php echo esc_attr($property_id); ?>">

			<?php wp_nonce_field('wp-realestate-property-submit-package-nonce', 'security-property-submit-package'); ?>

			<h2><?php esc_html_e( 'Choose a package', 'wp-realestate-wc-paid-listings' ); ?></h2>
		</div>
		<div class="property_types">
			<?php echo WP_RealEstate_Wc_Paid_Listings_Template_Loader::get_template_part('user-packages', array('user_packages' => $user_packages) ); ?>
			<?php echo WP_RealEstate_Wc_Paid_Listings_Template_Loader::get_template_part('packages', array('packages' => $packages) ); ?>
		</div>
	<?php } else { ?>
		<div class="text-warning">
			<?php esc_html_e('Sorry, you can\'t post a listing.', 'wp-realestate-wc-paid-listings'); ?>
		</div>
	<?php } ?>
</form>