<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'properties' => $properties
);

echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', $args);

echo WP_RealEstate_Template_Loader::get_template_part('loop/property/pagination', array('properties' => $properties));
