<?php
$user = wp_get_current_user();
$data = get_userdata( $user->ID );
$avatar = get_the_author_meta( '_user_avatar', $user->ID );
$avatar_url = wp_get_attachment_image_src($avatar, 'full');
$address = get_the_author_meta( '_address', $user->ID );
$phone = get_the_author_meta( '_phone', $user->ID );

wp_enqueue_script( 'wp-realestate-ajax-file-upload' );
?>
<div class="profile-form-normal-wrapper">
	<h1 class="title"><?php _e( 'Edit Profile', 'wp-realestate' ) ; ?></h1>

	<?php if ( ! empty( $_SESSION['messages'] ) ) : ?>

		<ul class="messages">
			<?php foreach ( $_SESSION['messages'] as $message ) { ?>
				<?php
				$status = !empty( $message[0] ) ? $message[0] : 'success';
				if ( !empty( $message[1] ) ) {
				?>
				<li class="message_line <?php echo esc_attr( $status ) ?>">
					<?php echo wp_kses_post( $message[1] ); ?>
				</li>
			<?php
				}
			}
			unset( $_SESSION['messages'] );
			?>
		</ul>

	<?php endif; ?>
	<form method="post" action="" class="change-profile-form">

		<div class="left-inner">
	        <h3 class="sub"><?php echo esc_html__( 'Change Avatar', 'wp-realestate' ); ?></h3>
	        <div class="wp-realestate-uploaded-files">
	            <?php if (  $avatar ) { ?>
	                <div class="wp-realestate-uploaded-file">
	                    <?php
	                    $image_src = wp_get_attachment_image_src( absint( $avatar ) );
	                    $image_src = $image_src ? $image_src[0] : '';

	                    $extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );

	                    if ( 3 !== strlen( $extension ) || in_array( $extension, array( 'jpg', 'gif', 'png', 'jpeg', 'jpe' ) ) ) : ?>
	                        <span class="wp-realestate-uploaded-file-preview"><img src="<?php echo esc_url( $image_src ); ?>" /> <a class="wp-realestate-remove-uploaded-file" href="#"><?php _e( 'remove', 'wp-realestate' ); ?></i></a></span>
	                    <?php else : ?>
	                        <span class="wp-realestate-uploaded-file-name"><code><?php echo esc_html( basename( $image_src ) ); ?></code> <a class="wp-realestate-remove-uploaded-file" href="#"><?php _e( 'remove', 'wp-realestate' ); ?></a></span>
	                    <?php endif; ?>

	                    <input type="hidden" class="input-text" name="user_avatar" value="<?php echo esc_attr( $avatar ); ?>" />
	                </div>
	            <?php } ?>
	        </div>
	        <input id="upload-image-avarta" class="widefat wp-realestate-file-upload input-text hidden" name="user_avatar" type="file" value="<?php echo esc_attr($avatar); ?>" data-file_types="jpg|jpeg|jpe|gif|png"/>

	        <label class="label-can-drag">
				<div class="form-group group-upload">
			        <div class="upload-file-btn">
		            	<span><?php esc_html_e('Upload Image', 'wp-realestate'); ?></span>
			        </div>
			    </div>
			</label>
	    </div>
		<div class="row">
	        <div class="form-group col-sm-6 col-xs-12">
                <input id="change-profile-form-first-name" type="text" name="first_name" placeholder="<?php  esc_attr_e( 'First name', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $data->first_name ) ? esc_attr( $data->first_name ) : ''; ?>">
	        </div><!-- /.form-group -->
	        <div class="form-group col-sm-6 col-xs-12">
	            
                <input id="change-profile-form-last-name" type="text" name="last_name" placeholder="<?php esc_attr_e( 'Last name', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $data->last_name ) ? esc_attr( $data->last_name ) : ''; ?>">
	            
	        </div><!-- /.form-group -->
	        <div class="form-group col-sm-6 col-xs-12">
                <input id="change-profile-form-email" type="email" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $data->user_email ) ? esc_attr( $data->user_email ) : ''; ?>"  required="required">
	        </div><!-- /.form-group -->

	        <div class="form-group col-sm-6 col-xs-12">
                <input id="change-profile-form-phone" type="text" name="phone" placeholder="<?php esc_attr_e( 'Phone', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $phone ) ? esc_attr( $phone ) : ''; ?>">
	        </div><!-- /.form-group -->
	        <div class="form-group col-sm-6 col-xs-12">
                <input id="change-profile-form-address" type="text" name="address" placeholder="<?php esc_attr_e( 'Address', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $address ) ? esc_attr( $address ) : ''; ?>">
	        </div><!-- /.form-group -->

	        <div class="form-group col-sm-6 col-xs-12">
                <input id="change-profile-form-url" type="text" name="url" placeholder="<?php esc_attr_e( 'Website', 'wp-realestate' ); ?>" class="form-control" value="<?php echo ! empty( $data->url ) ? esc_attr( $data->url ) : ''; ?>">
	        </div><!-- /.form-group -->
	    </div>

	    <div class="form-group">
            <textarea id="change-profile-form-about" placeholder="<?php esc_attr_e( 'Biographical Info', 'wp-realestate' ); ?>" class="form-control" name="description" cols="70" rows="5"><?php echo ! empty( $data->description ) ? esc_attr( $data->description ) : ''; ?></textarea>
	    </div><!-- /.form-group -->

	    <?php wp_nonce_field('edit-profile-normal-nonce', 'edit-profile-normal'); ?>

	    <button type="submit" name="change_profile_form" class="button btn btn-theme"><?php echo esc_html__( 'Update', 'wp-realestate' ); ?></button>
	</form>
</div>