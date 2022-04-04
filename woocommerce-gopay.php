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
if (!defined("WPINC")) {
  die();
}

/**
 * Constants.
 */
define("WOOCOMMERCE_GOPAY_VERSION", "1.0.0");
define("WOOCOMMERCE_GOPAY_DOMAIN", "woocommerce-gopay");
define("WOOCOMMERCE_GOPAY_URL", plugin_dir_url(__FILE__));
define("WOOCOMMERCE_GOPAY_DIR", plugin_dir_path(__FILE__));
define("WOOCOMMERCE_GOPAY_BASENAME", plugin_basename(__FILE__));
define("TABLE_NAME", "woocommerce_gopay_log");

// Check if WooCommerce is active
$message = __(
  "WooCommerce GoPay gateway plugin requires WooCommerce to be active.",
  WOOCOMMERCE_GOPAY_DOMAIN
);
if (function_exists("is_multisite") && is_multisite()) {
  include_once ABSPATH . "wp-admin/includes/plugin.php";

  if (!is_plugin_active("woocommerce/woocommerce.php")) {
    exit($message);
  }
} else {
  if (
    !in_array(
      "woocommerce/woocommerce.php",
      apply_filters("active_plugins", get_option("active_plugins"))
    )
  ) {
    exit($message);
  }
}

// Deactivate woocommerce gopay plugin if woocommerce is deactivated
register_deactivation_hook(
  "woocommerce/woocommerce.php",
  "woocommerce_deactivate_dependents"
);
/**
 * When woocommerce is deactivated then deactivate woocommerce gopay as well
 */
function woocommerce_deactivate_dependents()
{
  if (is_plugin_active(WOOCOMMERCE_GOPAY_BASENAME)) {
    add_action(
      "update_option_active_plugins",
      "woocommerce_gopay_deactivation"
    );
  }
}

/**
 * woocommerce gopay deactivation
 */
function woocommerce_gopay_deactivation()
{
  deactivate_plugins(WOOCOMMERCE_GOPAY_BASENAME);
}

// Load files
require_once WOOCOMMERCE_GOPAY_DIR .
    "vendor/autoload.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "admin/class-woocommerce-gopay-admin.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-log.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-options.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-notices.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-activator.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-deactivator.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay.php";

// Register activation/deactivation hook
register_activation_hook(__FILE__, array("Woocommerce_Gopay_Activator", "activate"));
register_deactivation_hook(__FILE__, array("Woocommerce_Gopay_Deactivator", "deactivate"));

// Load Woocommerce GoPay gateway admin page
if (is_admin() && (!defined( 'DOING_AJAX') || !DOING_AJAX)) {
    new Woocommerce_Gopay_Admin_Menu();
}

// Scheduling a task to be executed and check if order was paid
// Change it - Create a new class file/class to hold the functions below
function gopay_cron_schedules($schedules){
    if(!isset($schedules["10sec"])){
        $schedules["10sec"] = array(
            'interval' => 10,
            'display' => __('Once every 10 seconds',
                WOOCOMMERCE_GOPAY_DOMAIN)
        );
    }
    return $schedules;
}
add_filter('cron_schedules','gopay_cron_schedules');
if(!wp_next_scheduled('wc_gopay_check_status', array(false))){
    wp_schedule_event(time(), '10sec', 'wc_gopay_check_status', array(false));
}
function check_payment_status(){
    $options = get_option('woocommerce_wc_gopay_gateway_settings');
    $test = ($options['test'] == "yes") ? false : true;

    $gopay = GoPay\payments([
        "goid" => $options['goid'],
        "clientId" => $options['client_id'],
        "clientSecret" => $options['client_secret'],
        "isProductionMode" => $test,
        "scope" => GoPay\Definition\TokenScope::ALL,
        "language" => GoPay\Definition\Language::ENGLISH,
        "timeout" => 30,
    ]);

    $orders = wc_get_orders(array(
            'limit'=>-1,
            'type'=> 'shop_order',
            'status'=> array('wc-processing', 'wc-pending')
        )
    );
    foreach ($orders as $order){
        $GoPay_Transaction_id = get_post_meta($order->get_id(), 'GoPay_Transaction_id', true);
        $response = $gopay->getStatus($GoPay_Transaction_id);
        if ($response->json['state'] == 'PAID'){
            // Check if all products are either virtual or downloadable
            $all_virtual_downloadable = true;
            foreach ($order->get_items() as $item) {
                $product = wc_get_product($item["product_id"]);
                if (!$product->is_virtual() && !$product->is_downloadable()) {
                    $all_virtual_downloadable = false;
                    break;
                }
            }

            if ($all_virtual_downloadable) {
                $order->set_status('completed');
            } else {
                $order->set_status('processing');
            }
        }
        $order->save();
    }
}
add_action('wc_gopay_check_status', 'check_payment_status');

#load_plugin_textdomain(WOOCOMMERCE_GOPAY_DOMAIN, WOOCOMMERCE_GOPAY_DIR . '/languages');
