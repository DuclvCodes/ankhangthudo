<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


$materials = get_the_terms($post->ID, 'property_material');
?>

<?php if ( ! empty( $materials ) ) : ?>
    <div class="property-section property-amenities">
        <h3 class="title"><?php echo esc_html__('Materials', 'wp-realestate'); ?></h3>
        <ul class="columns-gap list-check">
            <?php foreach ( $materials as $material ) : ?>
                
                <li class="yes"><?php echo esc_html( $material->name ); ?></li>
                
            <?php endforeach; ?>
        </ul>

        <?php do_action('wp-realestate-single-property-materials', $post); ?>
    </div><!-- /.property-amenities -->
<?php endif; ?>