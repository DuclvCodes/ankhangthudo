<?php

function homeo_realestate_paid_listing_template_folder_name($folder) {
	$folder = 'template-paid-listings';
	return $folder;
}
add_filter( 'wp-realestate-wc-paid-listings-theme-folder-name', 'homeo_realestate_paid_listing_template_folder_name', 10 );

