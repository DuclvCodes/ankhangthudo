<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'agents' => $agents
);
?>

<?php
	echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/archive-inner', $args);
?>

<?php echo WP_RealEstate_Template_Loader::get_template_part('loop/agent/pagination', array('agents' => $agents) ); ?>

