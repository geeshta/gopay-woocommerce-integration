<?php

class Woocommerce_Gopay_Admin_Menu {

    /**
     * Constructor for the plugin admin menu
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        #$this->$options = $options;
        add_action('admin_menu', array($this, 'create_menu'));
    }

    /**
     * Create Woocommerce GoPay gateway admin page
     *
     * @since 1.0.0
     */
    public function create_menu(){
        if (!defined('WOOCOMMERCE_GOPAY_ADMIN_MENU')){

            add_menu_page(
                __('Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN),
                __('Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN),
                'manage_woocommerce',
                'woocommerce-gopay-menu'
            );

            add_submenu_page(
                'woocommerce-gopay-menu',
                __('Woocommerce GoPay info', WOOCOMMERCE_GOPAY_DOMAIN),
                __('Info', WOOCOMMERCE_GOPAY_DOMAIN),
                'manage_woocommerce',
                'woocommerce-gopay-menu',
                array( $this, 'load_admin_info_page' )
            );

            add_submenu_page(
                'woocommerce-gopay-menu',
                __('Woocommerce GoPay log', WOOCOMMERCE_GOPAY_DOMAIN),
                __('Log', WOOCOMMERCE_GOPAY_DOMAIN),
                'manage_woocommerce',
                'woocommerce-gopay-menu-log',
                array( $this, 'load_admin_log_page' )
            );

            define('WOOCOMMERCE_GOPAY_ADMIN_MENU', true);
        }
    }

    /**
     * Load admin page
     *
     * @since 1.0.0
     */
    public function load_admin_info_page() {
        include_once('views/admin.php');
    }

    /**
     * Load admin page
     *
     * @since 1.0.0
     */
    public function load_admin_log_page() {
        include_once('views/log.php');
    }
}