<?php

class Woocommerce_Gopay_Log {

    /**
     * Constructor for the plugin log
     *
     * @since 1.0.0
     */
    public function __construct()
    {
    }

    public static function insert_log(){ // Change it - pass as arguments data to be saved
        global $wpdb;

        $response = $wpdb->insert($wpdb->prefix . TABLE_NAME,
            [
                'order_id' => 0,
                'transaction_id' => 0,
                'created_at' => gmdate('Y-m-d H:i:s'),
                'log_level' => 'TEST',
                'log' => 'INSERTED'
            ]);
    }
}