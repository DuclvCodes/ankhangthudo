<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('select2');
wp_enqueue_style('select2');
?>
<div class="login-form-wrapper">
	
	<div id="login-form-wrapper" class="form-container">
		<form class="login-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="post">
			<div class="form-group">
				<label for="username_or_email"><?php esc_html_e('Username Or Email', 'wp-realestate'); ?></label>
                <sup class="required-field">*</sup>
				<input autocomplete="off" type="text" name="username" class="form-control" id="username_or_email" placeholder="<?php esc_attr_e('Enter username or email','wp-realestate'); ?>">
			</div>
			<div class="form-group">
				<label for="login_password"><?php echo esc_html__('Password','wp-realestate'); ?></label>
                <sup class="required-field">*</sup>
				<input name="password" type="password" class="password required form-control" id="login_password" placeholder="<?php esc_attr_e('Enter Password','wp-realestate'); ?>">
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="user-remember-field">
							<input type="checkbox" name="remember" id="user-remember-field" value="true"> <?php echo esc_html__('Keep me signed in','wp-realestate'); ?>
						</label>
					</div>
				</div>
				<div class="col-sm-6">
					<p>
						<a href="#forgot-password-form-wrapper" class="back-link" title="<?php esc_attr_e('Forgot Password','wp-realestate'); ?>"><?php echo esc_html__("Lost Your Password?",'wp-realestate'); ?></a>
					</p>
				</div>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-theme btn-block" name="submit" value="<?php esc_attr_e('Login','wp-realestate'); ?>"/>
			</div>
			<?php
				do_action('login_form');
				wp_nonce_field('ajax-login-nonce', 'security_login');
			?>
		</form>
	</div>
	<!-- reset form -->
	<div id="forgot-password-form-wrapper" class="form-container">
		<form name="forgotpasswordform" class="forgotpassword-form" action="<?php echo esc_url( site_url('wp-login.php?action=lostpassword', 'login_post') ); ?>" method="post">
			<h3><?php echo esc_html__('Reset Password', 'wp-realestate'); ?></h3>
			<div class="lostpassword-fields">
				<div class="form-group">
					<label for="lostpassword_username"><?php echo esc_html__('Username or E-mail','wp-realestate'); ?></label>
            		<sup class="required-field">*</sup>
					<input type="text" name="user_login" class="user_login form-control" id="lostpassword_username" placeholder="<?php esc_attr_e('Enter Password','wp-realestate'); ?>">
				</div>
				<?php
					do_action('lostpassword_form');
					wp_nonce_field('ajax-lostpassword-nonce', 'security_lostpassword');
				?>
				<div class="form-group">
					<input type="submit" class="btn btn-theme btn-block" name="wp-submit" value="<?php esc_attr_e('Get New Password', 'wp-realestate'); ?>" tabindex="100" />
					<input type="button" class="btn btn-danger btn-block btn-cancel" value="<?php esc_attr_e('Cancel', 'wp-realestate'); ?>" tabindex="101" />
				</div>
			</div>
			<div class="lostpassword-link"><a href="#login-form-wrapper" class="back-link"><?php echo esc_html__('Back To Login', 'wp-realestate'); ?></a></div>
		</form>
	</div>
</div>
