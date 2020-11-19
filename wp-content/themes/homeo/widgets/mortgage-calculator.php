<?php
extract( $args );
extract( $instance );

$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

if ( function_exists('wp_realestate_get_option') ) {
	$currency_symbol = wp_realestate_get_option('currency_symbol');
} else {
	$currency_symbol = '$';
}

?>

<div class="apus-mortgage-calculator">
	<div class="form-group">
		<input class="form-control" id="apus_mortgage_property_price" type="text" placeholder="<?php esc_html_e('Price', 'homeo'); ?>">
		<span class="unit"><?php echo esc_attr($currency_symbol); ?></span>
	</div>
	<div class="form-group">
		<input class="form-control" id="apus_mortgage_deposit" type="text" placeholder="<?php esc_html_e('Deposit', 'homeo'); ?>">
		<span class="unit"><?php echo esc_attr($currency_symbol); ?></span>
	</div>
	<div class="form-group">
		<input class="form-control" id="apus_mortgage_interest_rate" type="text" placeholder="<?php esc_html_e('Rate', 'homeo'); ?>">
		<span class="unit"><?php esc_html_e('%', 'homeo'); ?></span>
	</div>
	<div class="form-group">
		<input class="form-control" id="apus_mortgage_term_years" type="text" placeholder="<?php esc_html_e('Loan Term', 'homeo'); ?>">
		<span class="unit"><?php esc_html_e('Year', 'homeo'); ?></span>
	</div>
	<button id="btn_mortgage_get_results" class="btn btn-theme-second btn-block"><?php esc_html_e('Calculate', 'homeo'); ?></button>
	<div class="apus_mortgage_results"></div>
</div>