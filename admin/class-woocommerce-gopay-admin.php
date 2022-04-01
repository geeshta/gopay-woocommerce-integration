<?php

class Woocommerce_Gopay_Admin_Menu {

    public function __construct($options)
    {
        $this->$options = $options;
    }

    public function create_menu(){
        add_menu_page(
            __('Woocommerce gopay', WOOCOMMERCE_GOPAY_DOMAIN),
            __('Info', WOOCOMMERCE_GOPAY_DOMAIN),
            'plugin_info',
            'woocommerce-gopay-menu'
        );
    }
}