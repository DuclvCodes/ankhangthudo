<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'agencies' => $agencies
);
?>

<?php
	echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/archive-inner', $args);
?>

<?php echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/pagination', array('agencies' => $agencies) ); ?>

