<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$amenities = get_the_terms($post->ID, 'property_amenity');
?>

<?php if ( ! empty( $amenities ) ) : ?>
    <div class="property-section property-amenities">
        <h3 class="title"><?php echo esc_html__('Amenities', 'homeo'); ?></h3>
        <ul class="columns-gap list-check">
            <?php foreach ( $amenities as $amenity ) : ?>
                <li class="yes"><?php echo esc_html( $amenity->name ); ?></li>
                
            <?php endforeach; ?>
        </ul>

        <?php do_action('wp-realestate-single-property-amenities', $post); ?>
    </div><!-- /.property-amenities -->
<?php endif; ?>