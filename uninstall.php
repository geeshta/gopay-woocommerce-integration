<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://argo22.com/
 * @since      1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'woocommerce_gopay_log' );