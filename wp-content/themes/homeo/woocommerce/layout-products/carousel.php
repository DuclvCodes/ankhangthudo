<?php
$product_item = isset($product_item) ? $product_item : 'inner';
$show_nav = isset($show_nav) ? $show_nav : false;
$show_smalldestop = isset($show_smalldestop) ? $show_smalldestop : false;
$show_pagination = isset($show_pagination) ? $show_pagination : false;
$rows = isset($rows) ? $rows : 1;
$columns = isset($columns) ? $columns : 3;
$products = isset($products) ? $products : '';

$small_cols = $columns <= 1 ? 1 : 2; 
$slick_top = (!empty($slick_top)) ? $slick_top : '';
?>
<div class="slick-carousel products <?php echo esc_attr($slick_top); ?>" data-carousel="slick" data-items="<?php echo esc_attr($columns); ?>"
    <?php echo trim($columns >= 8 ? 'data-large="6"' : ''); ?> 
    <?php echo trim($columns >= 5 ? 'data-medium="4" data-large="4" ' : ''); ?> 
    <?php echo trim(($columns >= 4 && $product_item == 'inner-list-small')? 'data-medium="3" data-large="3" ' : ''); ?> 
    <?php echo trim( ($columns >= 2 && ($product_item == 'inner' || $product_item == 'inner-noborder')) ? 'data-extrasmall="2"' : 'data-extrasmall="1"'); ?> 
    data-smallmedium="<?php echo esc_attr($small_cols); ?>"

    data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>">

    <?php wc_set_loop_prop( 'loop', 0 ); ?>
    <?php $i = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>
        <div class="item">
            <div class="product clearfix">
                <?php wc_get_template_part( 'item-product/'.$product_item ); ?>
            </div>
        </div>
    <?php $i++; endwhile; ?>
</div>
<?php wp_reset_postdata(); ?>