<?php

namespace App;

/**
 * Checkout Page Handler
 */
class Order
{
    function __construct() {
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'render_admin_order_fields' ], 5 );
    }

    //Display Order Meta
    public function render_admin_order_fields($order)
    {
        if ((get_post_meta($order->get_id(), 'customer_first_name', true))) {
            echo '<h3>Dealer Submitted Customer Details</h3><div class="address">';
            echo '<p><strong>Customer Name: </strong>' . get_post_meta($order->get_id(), 'customer_first_name', true) . ' ' . get_post_meta($order->get_id(), 'customer_last_name', true) . '</p>';
            echo '<p><strong>Email Adress: </strong>' . get_post_meta($order->get_id(), 'customer_email', true) . '</p>';
            echo '<p><strong>Address: </strong>' .
                get_post_meta($order->get_id(), 'customer_country', true) . '<br>' .
                get_post_meta($order->get_id(), 'customer_street_address', true) . '<br>' .
                get_post_meta($order->get_id(), 'customer_town', true) . ', ' .
                get_post_meta($order->get_id(), 'customer_state', true)  .  ' ' .
                get_post_meta($order->get_id(), 'customer_zip', true) . ' ' .
                '</p>';
            echo '</div>';
        }
    }
}
