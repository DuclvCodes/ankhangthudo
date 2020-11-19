<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$return = '';
$options = get_post_meta( $post->ID, $prefix.'options', true );
if ( is_array($value) && !empty($options) ) {
    foreach ($value as $v) {
        foreach ($options as $option) {
            if ( $v == $option['value'] ) {
                $return .= ' '.$option['text'].',';
            }
        }
    }
    $return = rtrim($return, ',');
}
echo $return;