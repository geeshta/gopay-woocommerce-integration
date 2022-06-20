<?php

/**
 * Plugin deactivation
 *
 * @package   WooCommerce GoPay gateway
 * @author    GoPay
 * @link      https://www.gopay.com/
 * @copyright 2022 GoPay
 * @since     1.0.0
 */
class Woocommerce_Gopay_Deactivator {


	/**
	 * Deactivation
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// self::delete_log_table();
	}

	/**
	 * Delete log table if it exists
	 *
	 * @since 1.0.0
	 */
	private static function delete_log_table() {
		global $wpdb;

		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME );
	}
}
