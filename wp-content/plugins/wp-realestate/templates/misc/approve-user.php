<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="approve-user-wrapper">
	<?php if ( isset($_SESSION['approve_user_msg']) ) { ?>
		<div class="alert <?php echo esc_attr($_SESSION['approve_user_msg']['error'] ? 'text-danger' : 'text-info'); ?>">
			<h3><?php echo $_SESSION['approve_user_msg']['msg']; ?></h3>
		</div>
	<?php
		unset($_SESSION['approve_user_msg']);
	}
	?>
</div>
