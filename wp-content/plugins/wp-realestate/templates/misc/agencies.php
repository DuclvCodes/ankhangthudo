<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/archive-inner', array('agencies' => $agencies));
	
	echo WP_RealEstate_Template_Loader::get_template_part('loop/agency/pagination', array('agencies' => $agencies));
?>