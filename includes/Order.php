<?php

namespace App;

/**
 * Checkout Page Handler
 */
class Order

{
    function __construct()
    {
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'render_admin_order_fields'], 5);
        add_action('add_meta_boxes', [$this, 'render_admin_order_meta_box']);
    }

    //Display Order Meta [Old Version]
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
            return $order;
        }
    }

    //Admin Order: Add metabox to order view
    public function render_admin_order_meta_box()
    {
        add_meta_box('woocommerce-customer-details', __('Dealers Customer'), [$this, 'render_customer_details_meta_box'], 'shop_order', 'normal', 'high');
    }

    //Admin Order: Add metabox content function
    function render_customer_details_meta_box($order)
    {
        global $post;
        global $wpdb;
        

        $id =  intVal($post->ID);
        $customer = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_dealer_customers` WHERE `order_id` = '$id'"));
        $warranty = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_warranties` WHERE `order_id` = '$id'"));

        $first .= $customer[0]->customer_first_name;
        $last .= $customer[0]->customer_last_name;
        $email .= $customer[0]->customer_email;
        $address .= $customer[0]->customer_address;
        $customer_ID .= $customer[0]->customer_user_id;


        if ($warranty[0] == Null){
            $warrantyAlert = '<span class="warrantyAlert noserial">No Serial Active</span>';
            $warrantyClaimed = false;
        } else if ($warranty[0]->claimed_at != Null) {
            $warrantyClaimed = true;
            $warrantyAlert = '<span class="warrantyAlert claimed">Warranty Claimed</span>';
        } else if ($warranty[0]->claimed_at == Null) {
            $warrantyClaimed = false;
            $warrantyAlert = '<span class="warrantyAlert unclaimed">Warranty Not Claimed</span>';
        }

        function alert_customer($warrantyClaimed, $warranty){
            if ($warrantyClaimed == false and $warranty[0] != Null){
                $earlier = new \DateTime($warranty[0]->registered_at);
                $later = new \DateTime();
                $daysSince = $later->diff($earlier);
                if ($daysSince->d > 4) {
                    $html .= '<div style="width: 100%;" class="order_alert_customer" id="order_data">';
                    $html .= '<h3>Its been: '.$daysSince->d.' days since a warranty alert email was sent to the customer. They have still yet to register their order for warranty. Consider sending a reminder email!</h3>';
                    $html .= '</div>';
                    $html .= '<div id="order_data" class="emailer_send_alert">';
                    $html .= '<button type="submit" class="button save_order button-primary" name="save" value="Update">Send a reminder Email</button>';
                    $html .= '</div>';
                    return $html;
                }    
            }

        }
        
        function get_user_initials($first, $last){
            if ($first || $last){
                $userInitials = '<span class="customer-avatar">' . substr($first, 0, 1) . ' ' . substr($last, 0, 1) . '</span>';
            } else {
                $userInitials = '<span class="customer-avatar">- -</span>';
            }
            return $userInitials;
        }

        function get_matched_user($customer_ID){
            if ($customer_ID){
                $matchedUser =  '<p>Account ID: </p><a target="_blank" href="'.get_home_url().'/wp-admin/user-edit.php?user_id='.$customer_ID.'">View matched user '.$customer_ID.'<span class="dashicons dashicons-external"></span></a>';
            } else {
                $matchedUser = '<p>No Matched User</p>';
            }
            return $matchedUser;
        }


        $html .= '<div style="width: 50%; display: inline-block; vertical-align: top;" id="order_data" class="order_data_column">';
        $html .= get_user_initials($first, $last);
        $html .= $warrantyAlert;
        $html .= get_matched_user($customer_ID);
        $html .= '</div>';

        $html .= '<div style="width: 50%; display: inline-block;" id="order_data" class="order_data_column">';
        $html .= '<h3>Billing</h3>';
        $html .= '<p>First Name: ' . $first . '</p>';
        $html .= '<p>Last Name: ' . $last . '</p>';
        $html .= '<p>Email: ' . $email . '</p>';
        $html .= '<p>Address: ' . $address . '</p>';
        $html .= '</div>';

        $html .= alert_customer($warrantyClaimed, $warranty);
        echo $html;
        ?>
        <style>
        .customer-avatar{
            font-size: 12px;
            background-color: #efefef;
            padding: 20px;
            text-align: center;
            height: 60px;
            font-weight: 900;
            width: 60px;
            color: #cacaca;
            display: block;
            border-radius: 50%;
        }
        .warrantyAlert {
            background-color: #efefef;
            padding: 5px 10px;
            color: #cacaca;
            display: block;
            width: fit-content;
            border-radius: 5px;
            margin: 5px 0px 5px -5px !important;
        }
        .warrantyAlert.unclaimed{
            background-color: #ffeddb !important;
            color: #ffa330 !important;
        }
        .warrantyAlert.claimed {
            background-color: #d0ffe4 !important;
            color: #2ecc71 !important;
        }
        .warrantyAlert.noserial {
            background-color: #cbe3ff !important;
            color: #167df0 !important;
        }
        </style>
        <?php
    }
}
