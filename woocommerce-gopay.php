<?php
/** 
 * The plugin bootstrap file
 * 
 * @link              https://argo22.com/
 * @since             1.0.0
 * @package           woocommerce-gopay
 * 
 * @wordpress-plugin
 * Plugin Name:       WooCommerce GoPay gateway
 * Plugin URI:        https://argo22.com/
 * Description:       WooCommerce and GoPay payment gateway integration
 * Version:           1.0.0
 * Author:            Argo22
 * Author URI:        https://argo22.com/
 * Text Domain:       woocommerce-gopay
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

/**
 * Constants.
 */
define('WOOCOMMERCE_GOPAY_VERSION', '1.0.0');
define('WOOCOMMERCE_GOPAY_DOMAIN', 'woocommerce-gopay');
define('WOOCOMMERCE_GOPAY_URL', plugin_dir_url( __FILE__ ));
define('WOOCOMMERCE_GOPAY_DIR', plugin_dir_path( __FILE__ ));
define('WOOCOMMERCE_GOPAY_BASENAME', plugin_basename( __FILE__ ));

// Check if WooCommerce is active
$message = __('WooCommerce GoPay gateway plugin requires WooCommerce to be active.', WOOCOMMERCE_GOPAY_DOMAIN);
if (function_exists('is_multisite') && is_multisite()) {
  include_once(ABSPATH . 'wp-admin/includes/plugin.php');

  if (!is_plugin_active('woocommerce/woocommerce.php')) { 
    exit($message);
  }
} else {
  if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit($message);
  }    
}

// Deactivate woocommerce gopay plugin if woocommerce is deactivated
register_deactivation_hook('woocommerce/woocommerce.php', 'woocommerce_deactivate_dependents');
/**
* When woocommerce is deactivated then deactivate woocommerce gopay as well
*/
function woocommerce_deactivate_dependents(){
  if(is_plugin_active(WOOCOMMERCE_GOPAY_BASENAME) ){
    add_action('update_option_active_plugins', 'woocommerce_gopay_deactivation');
  }
}

/**
* woocommerce gopay deactivation
*/
function woocommerce_gopay_deactivation(){
  deactivate_plugins(WOOCOMMERCE_GOPAY_BASENAME);
}

// Load files
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/gopaySDK/factory.php');
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/class-woocommerce-gopay-options.php');
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/class-woocommerce-gopay-notices.php');
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/class-woocommerce-gopay-activator.php');
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/class-woocommerce-gopay-deactivator.php');
require_once(WOOCOMMERCE_GOPAY_DIR . 'includes/class-woocommerce-gopay.php');



#load_plugin_textdomain(WOOCOMMERCE_GOPAY_DOMAIN, WOOCOMMERCE_GOPAY_DIR . '/languages');