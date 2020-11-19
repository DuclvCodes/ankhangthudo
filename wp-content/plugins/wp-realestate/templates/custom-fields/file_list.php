<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$return = '';
if ( !empty($value) ) {
    $return = '<ul class="custom-field-file_list">';
    foreach ($value as $id => $v) {
       $return .= '<li><a href="'.esc_url($v).'">'. wp_get_attachment_image( $id , 'thumbnail' ).'</a></li>';
    }
    $return .= '</ul>';
}
echo $return;