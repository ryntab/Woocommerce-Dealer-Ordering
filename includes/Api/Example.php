<?php
namespace App\Api;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Example extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'dealer/api';
        $this->rest_base = 'orders';
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_dealer_orders' ),
                    'permission_callback' => array( $this, 'get_orders_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_dealer_orders( $request ) {
        global $wpdb;
        $order = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_dealer_customers`"));

        $data['page'] = 1;
        $data['results'] = $order;
        $data['total_pages'] = 1;
        $data['total_results'] = 4;
        



        return $data;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_orders_permissions_check( $request ) {
        return true;
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}
