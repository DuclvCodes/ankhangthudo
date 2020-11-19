<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Homeo_Elementor_Transactions extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_transactions';
    }

	public function get_title() {
        return esc_html__( 'Apus Transactions', 'homeo' );
    }
    
	public function get_categories() {
        return [ 'homeo-elements' ];
    }

	protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'homeo' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'homeo' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'homeo' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'homeo' ),
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        ?>
        <h1 class="title-profile"><?php esc_html_e( 'Transactions', 'homeo' ) ; ?></h1>
        <div class="box-white-dashboard">
            <div class="inner-list">
                <?php if ($title!=''): ?>
                    <h2 class="title">
                        <?php echo esc_attr( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( ! is_user_logged_in() ) {
                    ?>
                        <div class="text-warning"><?php  esc_html_e( 'Please login to see this page.', 'homeo' ); ?></div>
                    <?php
                } else {
                    if ( get_query_var( 'paged' ) ) {
                        $paged = get_query_var( 'paged' );
                    } elseif ( get_query_var( 'page' ) ) {
                        $paged = get_query_var( 'page' );
                    } else {
                        $paged = 1;
                    }
                    $args = array(
                        'post_type' => 'shop_order',
                        'posts_per_page' => get_option('posts_per_page'),
                        'paged' => $paged,
                        'post_status' => array('wc-pending', 'wc-on-hold', 'wc-cancelled', 'wc-failed', 'wc-processing', 'wc-refunded', 'wc-completed'),
                        'order' => 'DESC',
                        'orderby' => 'ID',
                        'meta_query' => array(
                            array(
                                'meta_key' => '_customer_user',
                                'value' => get_current_user_id(),
                            )
                        )
                    );
                    $trans_loop = new WP_Query($args);
                    $total_trans = $trans_loop->found_posts;

                    if ( $trans_loop->have_posts() ) {
                    ?>
                        <div class="widget-user-packages <?php echo esc_attr($el_class); ?>">
                            <div class="widget-content table-responsive">
                                <table class="user-transactions">
                                    <thead>
                                        <tr>
                                            <td><?php esc_html_e('Order ID', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Package', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Amount', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Date', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Payment Mode', 'homeo'); ?></td>
                                            <td><?php esc_html_e('Status', 'homeo'); ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($trans_loop->have_posts()) : $trans_loop->the_post();
                                            global $post;
                                            $prefix = WP_REALESTATE_WC_PAID_LISTINGS_PREFIX;
                                            
                                            $trans_order_name = '';
                                            $trans_order_obj = wc_get_order($post->ID);
                                            $continue = false;
                                            foreach ( $trans_order_obj->get_items() as $item ) {
                                                $oproduct = wc_get_product( $item['product_id'] );
                                                if ( is_object($oproduct) && $oproduct->is_type( array( 'property_package' ) ) ) {
                                                    $trans_order_name = get_the_title($oproduct->get_ID());
                                                    $continue = true;
                                                }
                                            }
                                            if ( !$continue ) {
                                                continue;
                                            }
                                            $trans_order_price = $trans_order_obj->get_total();

                                            $order_price = wc_price($trans_order_price);

                                            $trans_status = $trans_order_obj->get_status();
                                            if ($trans_status == 'completed') {
                                                $status_txt = esc_html__('Successfull', 'homeo');
                                                $status_class = 'success';
                                            } else if ($trans_status == 'processing') {
                                                $status_txt = esc_html__('Processing', 'homeo');
                                                $status_class = 'pending';
                                            } else if ($trans_status == 'refunded') {
                                                $status_txt = esc_html__('Refunded', 'homeo');
                                                $status_class = 'pending';
                                            } else {
                                                $status_txt = esc_html__('Pending', 'homeo');
                                                $status_class = 'pending';
                                            }

                                            $order_date_obj = $trans_order_obj->get_date_created();
                                            $order_date_array = json_decode(json_encode($order_date_obj), true);
                                            $order_date = isset($order_date_array['date']) ? $order_date_array['date'] : '';

                                            $payment_mode = $trans_order_obj->get_payment_method();
                                            $payment_mode = $payment_mode != '' ? $payment_mode : '-';
                                            if ($payment_mode == 'cod') {
                                                $payment_mode = esc_html__('Cash on Delivery', 'homeo');
                                            }
                                        ?>
                                            <tr>
                                                <td class="id_property"><?php the_ID(); ?></td>
                                                <td class="title"><?php echo trim($trans_order_name); ?></td>
                                                <td><?php echo trim($order_price); ?></td>
                                                <td class="date"><?php echo trim($order_date != '' ? date_i18n(get_option('date_format'), strtotime($order_date)) : '-') ?></td>
                                                <td><?php echo trim($payment_mode) ?></td>
                                                <td><span class="action <?php echo esc_attr($status_class) ?>"><?php echo trim($status_txt); ?></span></td>
                                            </tr>
                                        <?php endwhile;
                                            wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>

                                <?php
                                WP_RealEstate_Mixes::custom_pagination( array(
                                    'max_num_pages' => $trans_loop->max_num_pages,
                                    'prev_text'     => '<i class=" ti-angle-left"></i>',
                                    'next_text'     => '<i class=" ti-angle-right"></i>',
                                    'wp_query' => $trans_loop
                                ));
                                ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="not-found"><?php esc_html_e('Don\'t have any items', 'homeo'); ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php }

}

Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Homeo_Elementor_Transactions );