<?php

namespace App;

/**
 * Checkout Page Handler
 */
class Checkout
{
    function __construct() {
        add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'render_fields' ], 5, 1 );
        add_action( 'woocommerce_checkout_order_processed', [ $this, 'send_field_data' ], 99, 3 );
        add_action( 'woocommerce_checkout_process', [ $this, 'check_field_data' ], 5 );
    }

    /**
     * Render checkout fields
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
   
    
    //Add custom fields to the checkout page
    public function render_fields($checkout)
    {
        $user = wp_get_current_user();
        if (in_array('GravityDealer1', (array) $user->roles)) {
            echo '<h3>' . __('Customer Details') . '</h3>';
            woocommerce_form_field(
                'customer_first_name',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => array(
                        'form-row form-row-first'
                    ),
                    'label' => __('Customer First Name'),
                ),
                $checkout->get_value('customer_first_name')
            );

            woocommerce_form_field(
                'customer_last_name',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => array(
                        'form-row form-row-last'
                    ),
                    'label' => __('Customer Last Name'),
                ),
                $checkout->get_value('customer_last_name')
            );

            woocommerce_form_field(
                'customer_email',
                array(
                    'required' => true,
                    'type' => 'email',
                    'class' => array(
                        'form-row form-row-wide validate-required validate-email'
                    ),
                    'label' => __('Customer Email'),
                ),
                $checkout->get_value('customer_email')
            );

            woocommerce_form_field(
                'customer_country',
                array(
                    'type'      => 'country',
                    'class'     => array('chzn-drop', 'form-row form-row-wide'),
                    'label'     => __('Country'),
                    'placeholder' => __('Choose Your Customers Country.'),
                    'required'  => true,
                    'clear'     => true
                ),
                $checkout->get_value('customer_country')
            );

            woocommerce_form_field(
                'customer_street_address',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => array(
                        'form-row address-field validate-required form-row-wide'
                    ),
                    'label' => __('Customer Street Address'),
                ),
                $checkout->get_value('customer_street_address')
            );

            woocommerce_form_field(
                'customer_town',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => array(
                        'form-row address-field validate-required form-row-wide'
                    ),
                    'label' => __('Customer Town / City *'),
                ),
                $checkout->get_value('customer_town')
            );

            woocommerce_form_field(
                'customer_state',
                array(
                    'type'      => 'state',
                    'class'     => array('chzn-drop', 'form-row form-row-wide'),
                    'label'     => __('State'),
                    'placeholder' => __('Choose Your Customers State.'),
                    'required'  => true,
                    'clear'     => true
                ),
                $checkout->get_value('customer_state')
            );

            woocommerce_form_field(
                'customer_zip',
                array(
                    'required' => true,
                    'type' => 'text',
                    'class' => array(
                        'form-row form-row-wide validate-required'
                    ),
                    'label' => __('Customer Postal Code'),
                ),
                $checkout->get_value('customer_zip')
            );
        }
    }

    //Update the order meta with field value
    public function send_field_data($order_id, $posted_data, $order)
    {
        global $wpdb;
        $user = wp_get_current_user();
        $customer = get_user_by( 'email', sanitize_text_field($_POST['customer_email']));

        $address = $_POST['customer_street_address']. ', ' . $_POST['customer_town'] . ' ' . $_POST['customer_state'] . ', ' . $_POST['customer_zip'] . ', ' . $_POST['customer_country']; 

        $wpdb->insert
        ('wp_dealer_customers', array(
        'order_id' => $order_id,
        'dealer_id' => $user->ID,
        'customer_user_id' => $customer->ID, 
        'customer_first_name' =>  sanitize_text_field($_POST['customer_first_name']), 
        'customer_last_name' => sanitize_text_field($_POST['customer_last_name']), 
        'customer_email' => sanitize_text_field($_POST['customer_email']), 
        'customer_address' => $address, 
        'warranty_claimed' => Null)
        );   
    }

    //Alert handling
    public function check_field_data()
    {
        // Check if set, if its not set add an error.
        $user = wp_get_current_user();
        if (in_array('GravityDealer1', (array) $user->roles)) {
            if (!$_POST['customer_first_name']) {
                wc_add_notice(__('Please fill in your customers first name.'), 'error');
            }
            if (!$_POST['customer_last_name']) {
                wc_add_notice(__('Please fill in your customers last name.'), 'error');
            }
            if (!$_POST['customer_email']) {
                wc_add_notice(__('Please fill in your customers email.'), 'error');
            }
            if (!$_POST['customer_country']) {
                wc_add_notice(__('Please fill in your customers country.'), 'error');
            }
            if (!$_POST['customer_street_address']) {
                wc_add_notice(__('Please fill in your customers street address.'), 'error');
            }
            if (!$_POST['customer_town']) {
                wc_add_notice(__('Please fill in your customers town.'), 'error');
            }
            if (!$_POST['customer_state']) {
                wc_add_notice(__('Please fill in your customers state or providence.'), 'error');
            }
            if (!$_POST['customer_zip']) {
                wc_add_notice(__('Please fill in your customers postal code.'), 'error');
            }
        }
    }
}
