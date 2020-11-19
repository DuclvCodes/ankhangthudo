<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h1 class="title-profile"><?php esc_html_e('Change Password', 'homeo'); ?></h1>
<div class="box-white-dashboard max-600">
	<form method="post" action="" class="change-password-form">
		<div class="clearfix">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group">
						<label for="change-password-form-old-password"><?php echo esc_html__( 'Old password', 'homeo' ); ?></label>
						<input id="change-password-form-old-password" class="form-control" type="password" name="old_password" required="required">
					</div><!-- /.form-control -->
				</div>
				<div class="col-xs-12">
					<div class="form-group">
						<label for="change-password-form-new-password"><?php echo esc_html__( 'New password', 'homeo' ); ?></label>
						<input id="change-password-form-new-password" class="form-control" type="password" name="new_password" required="required" minlength="8">
					</div><!-- /.form-control -->
				</div>
				<div class="col-xs-12">
					<div class="form-group">
						<label for="change-password-form-retype-password"><?php echo esc_html__( 'Retype password', 'homeo' ); ?></label>
						<input id="change-password-form-retype-password" class="form-control" type="password" name="retype_password" required="required" minlength="8">
					</div><!-- /.form-control -->
				</div>
			</div>
		</div>
		<button type="submit" name="change_password_form" class="button btn btn-theme"><?php echo esc_html__( 'Change Password', 'homeo' ); ?></button>
	</form>
</div>