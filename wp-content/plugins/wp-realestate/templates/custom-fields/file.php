<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$return = '<ul class="custom-field-file_list">';
$return .= '<li><img src="'.esc_url($value).'" alt=""></li>';
$return .= '</ul>';
echo $return;