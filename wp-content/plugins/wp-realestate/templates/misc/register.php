<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="register-form-wrapper">
  	<div class="container-form">
      	<form name="registerForm" method="post" class="register-form">

      		<div class="form-group">
				<div class="role-tabs">
					<label for="wp_realestate_role_subscriber"><input id="wp_realestate_role_subscriber" type="radio" name="role" value="" checked="checked"><?php esc_html_e('User', 'wp-realestate'); ?></label>
					<label for="wp_realestate_role_agent"><input id="wp_realestate_role_agent" type="radio" name="role" value="wp_realestate_agent"><?php esc_html_e('Agent', 'wp-realestate'); ?></label>
					<label for="wp_realestate_role_agency"><input id="wp_realestate_role_agency" type="radio" name="role" value="wp_realestate_agency"><?php esc_html_e('Agency', 'wp-realestate'); ?></label>
				</div>
			</div>

			<div class="form-group">
				<label for="register-username"><?php esc_html_e('Username', 'wp-realestate'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="username" id="register-username" placeholder="<?php esc_attr_e('Enter Username','wp-realestate'); ?>">
			</div>
			<div class="form-group">
				<label for="register-email"><?php esc_html_e('Email', 'wp-realestate'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="email" id="register-email" placeholder="<?php esc_attr_e('Enter Email','wp-realestate'); ?>">
			</div>
			<div class="form-group">
				<label for="password"><?php esc_html_e('Password', 'wp-realestate'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="password" id="password" placeholder="<?php esc_attr_e('Enter Password','wp-realestate'); ?>">
			</div>
			<div class="form-group">
				<label for="confirmpassword"><?php esc_html_e('Confirm Password', 'wp-realestate'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="<?php esc_attr_e('Enter Password','wp-realestate'); ?>">
			</div>

			<?php wp_nonce_field('ajax-register-nonce', 'security_register'); ?>

			<?php if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) { ?>
	            <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_realestate_get_option( 'recaptcha_site_key' )); ?>"></div>
	      	<?php } ?>

			<div class="form-group">
				<button type="submit" class="btn btn-second btn-block" name="submitRegister">
					<?php echo esc_html__('Register now', 'wp-realestate'); ?>
				</button>
			</div>

			<?php do_action('register_form'); ?>
      	</form>
    </div>

</div>
