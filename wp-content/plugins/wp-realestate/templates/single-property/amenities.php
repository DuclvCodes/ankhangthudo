<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$amenities = get_categories( array(
    'taxonomy'      => 'property_amenity',
    'hide_empty'    => false,
) );
?>

<?php if ( ! empty( $amenities ) ) : ?>
    <div class="property-section property-amenities">
        <h3 class="title"><?php echo esc_html__('Amenities', 'wp-realestate'); ?></h3>
        <ul class="columns-gap list-check">
            <?php foreach ( $amenities as $amenity ) : ?>
                <?php $has_term = has_term( $amenity->term_id, 'property_amenity' ); ?>

                <li <?php if ( $has_term ) : ?>class="yes"<?php else : ?>class="no"<?php endif; ?>><?php echo esc_html( $amenity->name ); ?></li>
                
            <?php endforeach; ?>
        </ul>

        <?php do_action('wp-realestate-single-property-amenities', $post); ?>
    </div><!-- /.property-amenities -->
<?php endif; ?>