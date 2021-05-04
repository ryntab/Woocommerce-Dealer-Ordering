<?php

namespace App;

/**
 * Checkout Page Handler
 */
class Order

{
    function __construct()
    {
        add_action('add_meta_boxes', [$this, 'render_admin_order_meta_box']);
    }

    //Admin Order: Add metabox to order view
    public function render_admin_order_meta_box()
    {
        add_meta_box('woocommerce-customer-details', __('Dealers Customer'), [$this, 'render_customer_details_meta_box'], 'shop_order', 'normal', 'high', 10, 1);
    }

    //Admin Order: Add metabox content function
    public function render_customer_details_meta_box($order)
    {
        global $post;
        global $wpdb;

        $id =  intVal($post->ID);

        $customer = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_dealer_customers` WHERE `order_id` = '$id'"));
        $warranty = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_warranties` WHERE `order_id` = '$id'"));

        $first = $customer[0]->customer_first_name ?: get_post_meta($id, 'customer_first_name', true);
        $last = $customer[0]->customer_last_name ?: get_post_meta($id, 'customer_last_name', true);
        $email = $customer[0]->customer_email ?: get_post_meta($id, 'customer_email', true);
        $customer_ID = $customer[0]->customer_user_id ?: null;
        $address = $customer[0]->customer_address ?: get_post_meta($id, 'customer_country', true) . get_post_meta($id, 'customer_street_address', true) . get_post_meta($id, 'customer_town', true) . get_post_meta($id, 'customer_state', true)  .  get_post_meta($id, 'customer_zip', true);
        


        if ($warranty[0] == Null){
            $warrantyAlert = '<span class="warrantyAlert noserial">No Serial Active</span>';
        } else if ($warranty[0]->claimed_at != Null) {
            $warrantyAlert = '<span class="warrantyAlert claimed">Warranty Claimed</span>';
        } else if ($warranty[0]->claimed_at == Null) {
            $warrantyAlert = '<span class="warrantyAlert unclaimed">Warranty Not Claimed</span>';
        }


        function alert_customer($warranty){
            
            if ($warranty[0]->registered_at != Null){
                $earlier = new \DateTime($warranty[0]->registered_at);
                $later = new \DateTime();
                $daysSince = $later->diff($earlier);
                
                if ($daysSince->d > get_option('wrs_remind_admin_email_again')) {
                    $html = NULL;
                    $html .= '<div style="width: 100%;" class="order_alert_customer">';
                    $html .= '<p>Its been: '.$daysSince->d.' days since a warranty alert email was sent to the customer. They have still yet to register their order for warranty. Consider sending a reminder email!</p>';
                    $html .= '</div>';
                    $html .= '<div class="emailer_send_alert">';
                    $html .= '<button type="submit" id="send-alert" class="button save_order button-primary">Send a reminder Email</button>';
                    $html .= '</div>';
                    return $html;
                }    
            }

        }
        
        function get_user_initials($first, $last){
            if ($first || $last){
                $userInitials = '<div class="customer-circle"><span class="customer-avatar">' . substr($first, 0, 1) . ' ' . substr($last, 0, 1) . '</span></div>';
            } else {
                $userInitials = '<div class="customer-circle"><span class="customer-avatar">- -</span></div>';
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

        $html = NULL;
        $html .= '<div style="padding: 20px"><div style="width: 50%; display: inline-block; vertical-align: top;">';
        $html .= get_user_initials($first, $last);
        $html .= $warrantyAlert;
        $html .= get_matched_user($customer_ID);
        $html .= '</div>';

        $html .= '<div style="width: 50%; display: inline-block;">';
        $html .= '<h3>Billing</h3>';
        $html .= '<p>First Name: '. $first .'</p>';
        $html .= '<p>Last Name: '. $last .'</p>';
        $html .= '<p>Email: '. $email .'</p>';
        $html .= '<p>Address: '. $address .'</p>';
        $html .= '</div>';
        $html .= alert_customer($warranty);
        $html .= '</div>';


  
        echo $html;
        ?>
        <style>
            .customer-circle {
                width: 60px;
                height: 60px;
            }
            .customer-avatar {
                font-size: 14px;
                background-color: #efefef;
                text-align: center;
                font-weight: 900;
                color: #cacaca;
                display: block;
                line-height: 60px;
                border-radius: 50%;
                height: 100%;
                width: 100%;
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
