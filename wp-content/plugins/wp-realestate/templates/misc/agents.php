<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/archive-inner', array('agents' => $agents));

	echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/pagination', array('agents' => $agents));
?>