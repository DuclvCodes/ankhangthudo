<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo WP_RealEstate_Template_Loader::get_template_part('loop/property/archive-inner', array('properties' => $properties));
