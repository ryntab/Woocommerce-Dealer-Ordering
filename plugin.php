<?php
/*
Plugin Name: Woo Dealer Ordering 🔥‍
Plugin URI: https://example.com/
Description: Allow dealers to make orders on behalf of their customers. Providing a dashboard for managment built with Vue.js
Version: 0.1
Author: Ryan Taber
Author URI: https://github.com/ryntab
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: baseplugin
Domain Path: /languages
*/

/**
 * Copyright (c) 2021 Ryan Taber (email: ryantaber17@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Woo_Dealer_Ordering class
 *
 * @class Woo_Dealer_Ordering The class that holds the entire Woo_Dealer_Ordering plugin
 */
final class Woo_Dealer_Ordering {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Woo_Dealer_Ordering class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_activation_hook( __FILE__, array( $this, 'DBsetup' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the Woo_Dealer_Ordering() class
     *
     * Checks for an existing Woo_Dealer_Ordering() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Woo_Dealer_Ordering();
        }

        return $instance;
    }

    public function gitUpdater(){
    include_once('updater.php');
        if (is_admin()) {
            $config = array(
                'slug' => plugin_basename(__FILE__),
                'proper_folder_name' => 'Woo-Dealer-Customers',
                'api_url' => 'https://api.github.com/repos/ryntab/Woocommerce-Warranty-Registration/',
                'raw_url' => 'https://raw.github.com/ryntab/Woocommerce-Warranty-Registration/master/',
                'github_url' => 'https://github.com/ryntab/Woocommerce-Warranty-Registration/',
                'zip_url' => 'https://github.com/ryntab/Woocommerce-Warranty-Registration/zipball/master',
                'sslverify' => true,
                'requires' => '3.0',
                'tested' => '3.3',
                'readme' => 'README.md',
                'access_token' => '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',
            );
            new WP_GitHub_Updater($config);
        }
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'BASEPLUGIN_VERSION', $this->version );
        define( 'BASEPLUGIN_FILE', __FILE__ );
        define( 'BASEPLUGIN_PATH', dirname( BASEPLUGIN_FILE ) );
        define( 'BASEPLUGIN_INCLUDES', BASEPLUGIN_PATH . '/includes' );
        define( 'BASEPLUGIN_URL', plugins_url( '', BASEPLUGIN_FILE ) );
        define( 'BASEPLUGIN_ASSETS', BASEPLUGIN_URL . '/assets' );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'baseplugin_installed' );

        if ( ! $installed ) {
            update_option( 'baseplugin_installed', time() );
        }

        update_option( 'baseplugin_version', BASEPLUGIN_VERSION );
    }

    public function DBsetup(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `{$wpdb->base_prefix}dealer_customers` (
		order_id INT UNSIGNED NULL, 
        dealer_id INT NULL,
        customer_user_id INT UNSIGNED NULL, 
		customer_first_name text NULL,
        customer_last_name text NULL,
        customer_email text NULL,
        customer_address text NULL,     
		warranty_claimed BOOL NULL, 
        alert_sent DATE NULL, 
		PRIMARY KEY  (order_id)) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once BASEPLUGIN_INCLUDES . '/Api.php';


        require_once BASEPLUGIN_INCLUDES . '/Mailer.php';


        require_once BASEPLUGIN_INCLUDES . '/Assets.php';


        require_once BASEPLUGIN_INCLUDES . '/Checkout.php';
        

        require_once BASEPLUGIN_INCLUDES . '/Order.php';
        

        if ( $this->is_request( 'admin' ) ) {
            require_once BASEPLUGIN_INCLUDES . '/Admin.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once BASEPLUGIN_INCLUDES . '/Frontend.php';
        }

        if ( $this->is_request( 'ajax' ) ) {
            // require_once BASEPLUGIN_INCLUDES . '/class-ajax.php';
        }

    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'init', array( $this, 'init_classes' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin'] = new App\Admin();
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['frontend'] = new App\Frontend();
        }

        if ( $this->is_request( 'ajax' ) ) {
            // $this->container['ajax'] =  new App\Ajax();
        }

        $this->container['order'] = new App\Order();
        $this->container['checkout'] = new App\Checkout();
        $this->container['api'] = new App\Api();
        $this->container['assets'] = new App\Assets();
        $this->container['mailer'] = new App\Mailer();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'baseplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'checkout' :
                return is_checkout();

            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

} // Woo_Dealer_Ordering

$baseplugin = Woo_Dealer_Ordering::init();
