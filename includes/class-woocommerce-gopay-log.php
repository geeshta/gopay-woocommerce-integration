<?php

/**
 * WooCommerce GoPay gateway log
 * Insert log into database
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      https://www.argo22.com
 * @copyright 2022 argo22
 * @since     1.0.0
 */

class Woocommerce_Gopay_Log
{

	/**
	 * Constructor for the plugin log
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
	}

	/**
	 * Insert log into the database
	 *
	 * @param array $log
	 *
	 * @since  1.0.0
	 */
	public static function insert_log( array $log )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME;
		$data       = array(
						'order_id'          => $log['order_id'],
						'transaction_id'    => $log['transaction_id'],
						'message'           => $log['message'],
						'created_at'        => gmdate( 'Y-m-d H:i:s' ),
						'gmt_offset'        => get_option( 'gmt_offset' ),
						'log_level'         => $log['log_level'],
						'log'               => json_encode( $log['log'] ),
					);
		$where      = array(
						'order_id'          => $log['order_id'],
						'transaction_id'    => $log['transaction_id'],
						'message'           => $log['message'],
					);

		$response   = $wpdb->update( $table_name, $data, $where );
		if ( $response === false || $response < 1 ) {
			$wpdb->insert( $table_name, $data );
		}
	}
}
