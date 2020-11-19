<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('select2');
wp_enqueue_style('select2');
?>
<div class="login-form-wrapper">
	
	<?php if ( defined('HOMEO_DEMO_MODE') && HOMEO_DEMO_MODE ) { ?>
		<div class="sign-in-demo-notice">
			Username: <strong>agency</strong> or <strong>agent</strong><br>
			Password: <strong>demo</strong>
		</div>
	<?php } ?>
	
	<div id="login-form-wrapper" class="form-container box-white-dashboard form-login-register-inner">
		<form class="login-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="post">
			<?php do_action('login_form'); ?>
			<div class="form-group">
				<i class="flaticon-user"></i>
				<label for="username_or_email" class="hidden"><?php esc_html_e('Username Or Email', 'homeo'); ?></label>
                <sup class="required-field">*</sup>
				<input autocomplete="off" type="text" name="username" class="form-control" id="username_or_email" placeholder="<?php esc_attr_e('Enter username or email','homeo'); ?>">
			</div>
			<div class="form-group">
				<i class="flaticon-password"></i>
				<label for="login_password" class="hidden"><?php echo esc_html__('Password','homeo'); ?></label>
                <sup class="required-field">*</sup>
				<input name="password" type="password" class="password required form-control" id="login_password" placeholder="<?php esc_attr_e('Enter Password','homeo'); ?>">
			</div>
			<div class="row space-15">
				<div class="col-sm-6">
					<label for="user-remember-field">
						<input type="checkbox" name="remember" id="user-remember-field" value="true"> <?php echo esc_html__('Keep me signed in','homeo'); ?>
					</label>
				</div>
				<div class="col-sm-6 text-right">
					<a href="#forgot-password-form-wrapper" class="back-link" title="<?php esc_attr_e('Forgot Password','homeo'); ?>"><?php echo esc_html__("Lost Your Password?",'homeo'); ?></a>
				</div>
			</div>
			<div class="form-group no-margin">
				<input type="submit" class="btn btn-theme btn-block" name="submit" value="<?php esc_attr_e('Login','homeo'); ?>"/>
				<div class="register-info">
					<?php esc_html_e('Don\'t you have an account?', 'homeo'); ?>
					<a class="apus-user-register" href="#apus_register_form">
	                    <?php esc_html_e('Register', 'homeo'); ?>
	                </a>
                </div>
			</div>
			<?php
				wp_nonce_field('ajax-login-nonce', 'security_login');
			?>
		</form>
	</div>
	<!-- reset form -->
	<div id="forgot-password-form-wrapper" class="form-container">
		<form name="forgotpasswordform" class="forgotpassword-form" action="<?php echo esc_url( site_url('wp-login.php?action=lostpassword', 'login_post') ); ?>" method="post">
			<h3><?php echo esc_html__('Reset Password', 'homeo'); ?></h3>
			<div class="lostpassword-fields">
				<div class="form-group">
					<label for="lostpassword_username"><?php echo esc_html__('Username or E-mail','homeo'); ?></label>
            		<sup class="required-field">*</sup>
					<input type="text" name="user_login" class="user_login form-control" id="lostpassword_username" placeholder="<?php esc_attr_e('Enter Password','homeo'); ?>">
				</div>
				<?php
					do_action('lostpassword_form');
					wp_nonce_field('ajax-lostpassword-nonce', 'security_lostpassword');
				?>

				<?php if ( version_compare(WP_REALESTATE_PLUGIN_VERSION, '1.1.0', '>=') && WP_RealEstate_Recaptcha::is_recaptcha_enabled() ) { ?>
		            <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_realestate_get_option( 'recaptcha_site_key' )); ?>"></div>
		      	<?php } ?>

				<div class="form-group">
					<input type="submit" class="btn btn-theme btn-block" name="wp-submit" value="<?php esc_attr_e('Get New Password', 'homeo'); ?>" tabindex="100" />
					<input type="button" class="btn btn-danger btn-block btn-cancel" value="<?php esc_attr_e('Cancel', 'homeo'); ?>" tabindex="101" />
				</div>
			</div>
			<div class="lostpassword-link"><a href="#login-form-wrapper" class="back-link"><?php echo esc_html__('Back To Login', 'homeo'); ?></a></div>
		</form>
	</div>
</div>