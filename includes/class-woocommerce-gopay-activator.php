<?php

/**
 * Plugin activation
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      https://www.argo22.com
 * @copyright 2022 argo22
 * @since     1.0.0
 */
class Woocommerce_Gopay_Activator {


	/**
	 * Run when plugin is activated
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		 self::create_log_table();
		// update num of decimals to be 2 by default (number of decimals of GoPay)
		// Rounded amounts can cause error when trying to refund an order
		update_option( 'woocommerce_price_num_decimals', 2 );
	}

	/**
	 * Create log table if it does not exist
	 *
	 * @since 1.0.0
	 */
	private static function create_log_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = 'CREATE TABLE ' . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME . " (
                id bigint(255) NOT NULL AUTO_INCREMENT,
                order_id bigint(255) NOT NULL,
                transaction_id bigint(255) NOT NULL,
                message varchar(50) NOT NULL,
                created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                log_level varchar(100) NOT NULL,
                log JSON NOT NULL,
                CONSTRAINT order_transaction_state_unique UNIQUE(order_id, transaction_id, message),
                PRIMARY KEY  (id)
                ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		maybe_create_table( $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME, $sql );
	}
}
