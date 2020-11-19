<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$args = array('#customer_login', '#customer_register');
$action = isset($_COOKIE['homeo_login_register']) && in_array($_COOKIE['homeo_login_register'], $args) ? $_COOKIE['homeo_login_register'] : '#customer_login';
if (isset($_GET['ac']) && $_GET['ac'] == 'register') {
	$action = '#customer_register';
}
?>
<?php wc_print_notices(); ?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<div class="user row">
		<div id="customer_login" class="register_login_wrapper col-xs-12 col-sm-6 <?php echo trim($action == '#customer_login' ? 'active' : ''); ?>">
			<div class="box-white-theme">
				<h2 class="title"><?php esc_html_e( 'Login', 'homeo' ); ?></h2>
				<form method="post" class="login" role="form">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="form-group form-row form-row-wide">
						<input type="text" placeholder="<?php esc_attr_e( 'Username or email address', 'homeo' ); ?>" class="form-control" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
					</p>
					<p class="form-group form-row form-row-wide">
						<input placeholder="<?php esc_attr_e( 'Password', 'homeo' ); ?>" class="form-control" type="password" name="password" id="password" />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<div class="form-group form-row">
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<div class="form-group action-group clearfix">
							<span class="inline pull-left">
								<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e( 'Remember me', 'homeo' ); ?>
							</span>
							<span class="lost_password pull-right">
								<a class="text-theme" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'homeo' ); ?></a>
							</span>
						</div>
						<input type="submit" class="btn btn-theme btn-block" name="login" value="<?php esc_attr_e( 'sign in', 'homeo' ); ?>" />
					</div>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

				</form>
			</div>
		</div>
		
		<div id="customer_register" class="content-register register_login_wrapper col-xs-12 col-sm-6 <?php echo trim($action == '#customer_register' ? 'active' : ''); ?>">
			<div class="box-white-theme">
				<h2 class="title"><?php esc_html_e( 'Register', 'homeo' ); ?></h2>
				<form method="post" class="register widget" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="form-group form-row form-row-wide">
							<input type="text" placeholder="<?php esc_attr_e( 'Username', 'homeo' ); ?>" class="form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
						</p>

					<?php endif; ?>

					<p class="form-group form-row form-row-wide">
						<input type="email" placeholder="<?php esc_attr_e( 'Email address', 'homeo' ); ?>" class="form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="form-group form-row form-row-wide">
							<input type="password" placeholder="<?php esc_attr_e( 'Password', 'homeo' ); ?>" class="form-control" name="password" id="reg_password" />
						</p>

					<?php else : ?>

						<p><?php esc_html_e( 'A password will be sent to your email address.', 'homeo' ); ?></p>

					<?php endif; ?>


					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="form-group form-row wrapper-submit">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="btn btn-primary btn-block" name="register" value="<?php esc_attr_e( 'Register', 'homeo' ); ?>"><?php esc_html_e( 'Register', 'homeo' ); ?></button>
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>