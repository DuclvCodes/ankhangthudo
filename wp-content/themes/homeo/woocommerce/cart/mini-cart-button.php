<?php global $woocommerce; ?>
<div class="apus-topcart">
 	<div class="cart">
        <a class="dropdown-toggle mini-cart" data-toggle="dropdown" aria-expanded="true" role="button" aria-haspopup="true" data-delay="0" href="#" title="<?php esc_attr_e('View your shopping cart', 'homeo'); ?>">
            <i class="ti-shopping-cart"></i>
            <span class="count"><?php echo sprintf($woocommerce->cart->cart_contents_count); ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right"><div class="widget_shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
        </div></div>
    </div>
</div>