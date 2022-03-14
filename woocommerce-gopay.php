<?php
/**
 * Plugin Name:       WooCommerce GoPay gateway
 * Plugin URI:        https://argo22.com/
 * Description:       WooCommerce and GoPay payment gateway integration
 * Version:           0.0.1
 * Author:            Argo22
 * Author URI:        https://argo22.com/
 * Text Domain:       
 * License:           
 * License URI:       
 * Domain Path:       /languages
 * WC requires at least: 0.0
 * WC tested up to: 0.0
 */

// If this file is called directly, abort.
// Preventing direct access to your WordPress.
if (!defined('WPINC')) {
  die;
}

// Check if WooCommerce is active
if (function_exists('is_multisite') && is_multisite()) {
  include_once(ABSPATH . 'wp-admin/includes/plugin.php');

  if ( !is_plugin_active( 'woocommerce/woocommerce.php')) {
    return;
  }
} else {
  if (!in_array('woocommerce/woocommerce.php',
      apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
  }    
}

require_once(plugin_dir_path( __FILE__ ) . 'includes/gopaySDK/factory.php');