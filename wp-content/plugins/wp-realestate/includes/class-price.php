<?php
/**
 * Price
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Price {
	
	/**
	 * Formats price
	 *
	 * @access public
	 * @param $price
	 * @return bool|string
	 */
	public static function format_price( $price, $show_null = false ) {
		if ( empty( $price ) || ! is_numeric( $price ) ) {
			if ( !$show_null ) {
				return false;
			}
			$price = 0;
		}

		$price = WP_RealEstate_Mixes::format_number( $price );

		$currency_index = 0;
		$symbol = wp_realestate_get_option('currency_symbol', '$');
		$currency_position = wp_realestate_get_option('currency_position', 'before');

		$currency_symbol = ! empty( $symbol ) ? '<span class="suffix">'.$symbol.'</span>' : '<span class="suffix">$</span>';
		$currency_show_symbol_after = $currency_position == 'after' ? true : false;

		if ( ! empty( $currency_symbol ) ) {
			if ( $currency_show_symbol_after ) {
				$price = '<span class="price-text">'.$price.'</span>' . $currency_symbol;
			} else {
				$price = $currency_symbol . '<span class="price-text">'.$price.'</span>';
			}
		}

		return $price;
	}
}
