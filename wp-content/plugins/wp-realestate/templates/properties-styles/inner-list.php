<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$agents = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'agents', true );

$types = get_the_terms( $post->ID, 'property_type' );
$address = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'address', true );
$home_area = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'home_area', true );
$beds = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'beds', true );
$baths = get_post_meta( $post->ID, WP_REALESTATE_PROPERTY_PREFIX . 'baths', true );

$price = WP_RealEstate_Property::get_price_html($post->ID);

?>

<?php do_action( 'wp_realestate_before_property_content', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
        <div class="agent-thumbnail">
            <?php echo get_the_post_thumbnail( $post, 'thumbnail' ); ?>

            <?php if ( $types ) { ?>
                <?php foreach ($types as $term) { ?>
                    <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="property-information">
    	
		<?php the_title( sprintf( '<h2 class="entry-title property-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

        <?php if ( $address ) { ?>
            <div class="property-location"><?php echo $address; ?></div>
        <?php } ?>
        <div class="property-date">
            <?php echo sprintf( __('%s ago', 'wp-realestate'), human_time_diff(get_the_time('U'), current_time('timestamp')) ); ?> 
        </div>
        
        <div class="property-metas">
            <?php
                if ( $home_area ) {
                    echo sprintf(__('%d Home Area', 'wp-realestate'), $home_area);
                }
                if ( $beds ) {
                    echo sprintf(__('%d Beds', 'wp-realestate'), $beds);
                }
                if ( $baths ) {
                    echo sprintf(__('%d Baths', 'wp-realestate'), $baths);
                }
            ?>
        </div>

        <div class="property-metas-bottom">
            <div class="property-date-author">
                <?php
                    if ( !empty($agents) && is_array($agents) ) {
                        $agent = $agents[0];
                        echo get_the_title($agent);
                    } elseif ( !empty($post->post_author) ) {
                        $userdata = get_userdata($post->post_author);
                        echo $userdata->display_name;
                    }
                ?>
            </div>
            <?php if ( $price ) { ?>
                <div class="property-price"><?php echo $price; ?></div>
            <?php } ?>
        </div>

	</div>
</article><!-- #post-## -->

<?php do_action( 'wp_realestate_after_property_content', $post->ID ); ?>