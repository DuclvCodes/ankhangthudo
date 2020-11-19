<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_RealEstate_Property_Meta::get_instance($post->ID);

$virtual_tour = $meta_obj->get_post_meta('virtual_tour');
?>
<?php if ( $meta_obj->check_post_meta_exist('virtual_tour') && $virtual_tour ) { ?>
	<div class="property-section property-virtual_tour">
		<h3><?php echo esc_html__( 'Virtual Tour', 'wp-realestate' ); ?></h3>
		<div class="virtual_tour-embed-wrapper">
			<?php echo $virtual_tour; ?>
		</div>

		<?php do_action('wp-realestate-single-property-virtual-tour', $post); ?>
	</div>
<?php }