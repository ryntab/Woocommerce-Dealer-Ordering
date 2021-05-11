<?php

namespace App;

/**
 * Checkout Page Handler
 */
class Mailer

{

    function __construct()
    {
        add_action('wp_ajax_send_customer_alert', [$this, 'send_customer_register_alert'], 1, 1); 

    }

    //Email sent when a serial is initially added for an order.
    public function send_customer_register_alert($id){
        global $wpdb;

        $orderID = $_REQUEST['order_ID'] ?: $id;
        //$customerEmail = $_REQUEST['customer_Email'];
        //$customerName = $_REQUEST['customer_Name'];

        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_warranties` WHERE `order_id` = '$orderID'"));

        $variables = array();

        $variables['title'] = "Register your Paramotor for warranty!";
        $variables['subtitle'] = "Description! Leave Blank to hide";
        $variables['key'] = strVal($result[0]->order_serial);
        $variables['buttonText'] = "Register";
        $variables['buttonLink'] = get_home_url().'/my-account/?serialKey='.$result[0]->order_serial;

        $template = file_get_contents(BASEPLUGIN_INCLUDES . '/Emails/serialAlert.html');

        foreach($variables as $key => $value)
        {
            $template = str_replace('{{ '.$key.' }}', $value, $template);
        }

        $to = 'ryantaber17@gmail.com';
        $subject = 'Register your paramotor for warranty on Gravity Paramotors!';
        $headers = array('Content-Type: text/html; charset=UTF-8','From: My Site Name <support@example.com>');
        
        wp_mail( $to, $subject, $template, $headers );
      
        
        Mailer::update_sent_email_date($orderID);

        $wpdb->update('wp_dealer_customers', array('alert_sent' => date('Y-m-d H:i:s')), array('order_id' => $orderID));
        
        wp_send_json(true);
    }

    //Email sent when serial is updated.
    public function send_customer_serial_update($id){
        global $wpdb;
    
        $orderID = $_REQUEST['order_ID'] ?: $id;
        //$customerEmail = $_REQUEST['customer_Email'];
        //$customerName = $_REQUEST['customer_Name'];
    
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_warranties` WHERE `order_id` = '$orderID'"));
    
        $variables = array();
    
        $variables['title'] = "Your warranty registration serial was updated!";
        $variables['subtitle'] = "If you have yet to register your paramotor order for warranty be sure to use this new registration serial.";
        $variables['key'] = strVal($result[0]->order_serial);
        $variables['buttonText'] = "Register";
        $variables['buttonLink'] = get_home_url().'/my-account/?serialKey='.$result[0]->order_serial;
    
        $template = file_get_contents(BASEPLUGIN_INCLUDES . '/Emails/serialAlert.html');
    
        foreach($variables as $key => $value)
        {
            $template = str_replace('{{ '.$key.' }}', $value, $template);
        }
    
        $to = 'ryantaber17@gmail.com';
        $subject = 'Your warranty registration key from Gravity Paramotors was updated!';
        $headers = array('Content-Type: text/html; charset=UTF-8','From: Glidesports.com <support@glidersports.com>');
        
        wp_mail( $to, $subject, $template, $headers );
      
        
        Mailer::update_sent_email_date($orderID);
    
        $wpdb->update('wp_dealer_customers', array('alert_sent' => date('Y-m-d H:i:s')), array('order_id' => $orderID));
        
        wp_send_json($result[0]->order_serial);
    }

    public function update_sent_email_date($orderID){
        global $wpdb;
        $wpdb->update('wp_dealer_customers', array('alert_sent' => date('Y-m-d')), array('order_id' => $orderID));
    }
    
}
