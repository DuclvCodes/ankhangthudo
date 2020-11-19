<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$materials = get_categories( array(
    'taxonomy'      => 'property_material',
    'hide_empty'    => false,
) );
?>

<?php if ( ! empty( $materials ) ) : ?>
    <div class="property-section property-amenities">
        <h3 class="title"><?php echo esc_html__('Materials', 'wp-realestate'); ?></h3>
        <ul class="columns-gap list-check">
            <?php foreach ( $materials as $material ) : ?>
                <?php $has_term = has_term( $material->term_id, 'property_material' ); ?>

                <li <?php if ( $has_term ) : ?>class="yes"<?php else : ?>class="no"<?php endif; ?>><?php echo esc_html( $material->name ); ?></li>
                
            <?php endforeach; ?>
        </ul>

        <?php do_action('wp-realestate-single-property-materials', $post); ?>
    </div><!-- /.property-amenities -->
<?php endif; ?>