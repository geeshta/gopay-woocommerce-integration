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

    public static function insert_log($log){
        global $wpdb;

        $response = $wpdb->insert($wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME,
            [
                'order_id' => $log['order_id'],
                'transaction_id' => $log['transaction_id'],
                'created_at' => gmdate('Y-m-d H:i:s'),
                'gmt_offset' => get_option('gmt_offset'),
                'log_level' => $log['log_level'],
                'log' => json_encode($log['log'])
            ]);

        if ($response) {
            error_log("LOG INSERTED");
        }
    }
}