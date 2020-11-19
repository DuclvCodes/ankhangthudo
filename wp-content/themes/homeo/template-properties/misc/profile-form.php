<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="profile-form-wrapper">
	<h1 class="title-profile"><?php esc_html_e( 'Edit Profile', 'homeo' ) ; ?></h1>
	<div class="box-white-dashboard">
		<?php if ( ! empty( $_SESSION['messages'] ) ) : ?>

			<?php foreach ( $_SESSION['messages'] as $message ) { ?>
				<?php
				$status = !empty( $message[0] ) ? $message[0] : 'success';
				if ( !empty( $message[1] ) ) {
				?>
				<div class="alert alert-<?php echo esc_attr( $status ) ?> margin-bottom-15">
					<?php echo wp_kses_post( $message[1] ); ?>
				</div>
			<?php
				}
			}
			unset( $_SESSION['messages'] );
			?>

		<?php endif; ?>

		<?php
			echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
				'form_format' => '<form action="' . esc_url(WP_RealEstate_Mixes::get_full_current_url()) . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb-profile" value="%4$s" class="button-primary btn btn-theme"></form>',
				'save_button' => esc_html__( 'Save Profile', 'homeo' ),
			) );
		?>
	</div>
</div>