<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="register-form-wrapper">
  	<div class="container-form">
      	<form name="registerForm" method="post" class="register-form box-white-dashboard form-login-register-inner">
      		
			<div class="form-group">
				<i class="flaticon-user"></i>
				<input type="text" class="form-control" name="username" id="register-username" placeholder="<?php esc_attr_e('User Name','homeo'); ?>">
			</div>
			<div class="form-group">
				<i class="flaticon-envelope"></i>
				<input type="text" class="form-control" name="email" id="register-email" placeholder="<?php esc_attr_e('Email','homeo'); ?>">
			</div>
			<div class="form-group">
				<i class="flaticon-password"></i>
				<input type="password" class="form-control" name="password" id="password" placeholder="<?php esc_attr_e('Password','homeo'); ?>">
			</div>

			<div class="form-group">
				<i class="flaticon-password"></i>
				<input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="<?php esc_attr_e('Re-enter Password','homeo'); ?>">
			</div>

			<div class="form-group">
				<i class="flaticon-user-1"></i>
				<select class="form-control" name="role">
					<option value=""><?php esc_html_e('Select Role', 'homeo'); ?></option>
					<option value="subscriber"><?php esc_html_e('User', 'homeo'); ?></option>

					<?php if ( homeo_get_config('register_form_enable_agent', true) ) { ?>
						<option value="wp_realestate_agent"><?php esc_html_e('Agent', 'homeo'); ?></option>
					<?php } ?>

					<?php if ( homeo_get_config('register_form_enable_agency', true) ) { ?>
						<option value="wp_realestate_agency"><?php esc_html_e('Agency', 'homeo'); ?></option>
					<?php } ?>
				</select>
			</div>

			<?php wp_nonce_field('ajax-register-nonce', 'security_register'); ?>

			<?php if ( WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) { ?>
	            <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_realestate_get_option( 'recaptcha_site_key' )); ?>"></div>
	      	<?php } ?>

	      	<?php
	      		$page_id = wp_realestate_get_option('terms_conditions_page_id');
	      		if ( !empty($page_id) ) {
	      			$page_url = get_permalink($page_id);
	      			?>
		      	<div class="form-group">
					<label for="register-terms-and-conditions">
						<input type="checkbox" name="terms_and_conditions" value="on" id="register-terms-and-conditions" required>
						<?php
							echo sprintf(wp_kses(__('I have read and accept the <a href="%s">Terms and Privacy Policy</a>', 'homeo'), array('a' => array('href' => array())) ), esc_url($page_url));
						?>
					</label>
				</div>
			<?php } ?>

			<div class="form-group no-margin">
				<button type="submit" class="btn btn-theme btn-block" name="submitRegister">
					<?php echo esc_html__('Sign Up', 'homeo'); ?>
				</button>

				<div class="login-info">
					<?php esc_html_e('Already have an account?', 'homeo'); ?>
					<a class="apus-user-login" href="#apus_login_forgot_form">
	                    <?php esc_html_e('Login', 'homeo'); ?>
	                </a>
                </div>
			</div>

			<?php do_action('register_form'); ?>
      	</form>
    </div>
</div>