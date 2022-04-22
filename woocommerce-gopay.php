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
define("WOOCOMMERCE_GOPAY_DOMAIN", "woocommerce-gopay");
define("WOOCOMMERCE_GOPAY_ID", "wc_gopay_gateway");
define("WOOCOMMERCE_GOPAY_FULLPATH", __FILE__);
define("WOOCOMMERCE_GOPAY_URL", plugin_dir_url(__FILE__));
define("WOOCOMMERCE_GOPAY_DIR", plugin_dir_path(__FILE__));
define("WOOCOMMERCE_GOPAY_BASENAME", plugin_basename(__FILE__));
define("WOOCOMMERCE_GOPAY_LOG_TABLE_NAME", "woocommerce_gopay_log");

/**
 * Check if plugin is active
 */
function check_is_plugin_active($path) {
    if (function_exists("is_multisite") && is_multisite()) {
        include_once ABSPATH . "wp-admin/includes/plugin.php";

        if (is_plugin_active($path)) {
            return true;
        }
    } else {
        if (in_array($path, apply_filters("active_plugins", get_option("active_plugins")))) {
            return true;
        }
    }

    return false;
}

// Check if WooCommerce is active
$message = __(
  "WooCommerce GoPay gateway plugin requires WooCommerce to be active.",
  WOOCOMMERCE_GOPAY_DOMAIN
);
if (!check_is_plugin_active("woocommerce/woocommerce.php")) {
    exit($message);
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
  if (check_is_plugin_active(WOOCOMMERCE_GOPAY_BASENAME)) {
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
    "includes/class-woocommerce-gopay-api.php";
require_once WOOCOMMERCE_GOPAY_DIR .
    "includes/class-woocommerce-gopay-subscriptions.php";
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
/**
 * Cron execute every 10 seconds
 */
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
add_action('wc_gopay_check_status', array('Woocommerce_Gopay_API', 'check_payment_status'));

// Check if WooCommerce Subscriptions is active
if (check_is_plugin_active("woocommerce-subscriptions/woocommerce-subscriptions.php")) {

    // Disable multiple checkout option
    add_action('plugins_loaded', 'disable_subscriptions_multiple_purchase');
    add_action('update_option_woocommerce_subscriptions_multiple_purchase',
        'disable_subscriptions_multiple_purchase');
    add_action('add_option_woocommerce_subscriptions_multiple_purchase',
        'disable_subscriptions_multiple_purchase');

    // When a subscription is added to the cart check if any other product/subscriptions was included
    add_filter('woocommerce_add_to_cart_validation',
        array('Woocommerce_Gopay_Subscriptions', 'subscriptions_check_add_to_cart'), 9, 3);
    add_filter('woocommerce_update_cart_validation',
        array('Woocommerce_Gopay_Subscriptions', 'subscriptions_check_cart_update'), 10, 4);
    add_filter('woocommerce_cart_redirect_after_error',
        array('Woocommerce_Gopay_Subscriptions', 'redirect_to_shop'));

    // Process/Cancel subscription payments
    add_action('woocommerce_scheduled_subscription_payment_' . WOOCOMMERCE_GOPAY_ID,
        array('Woocommerce_Gopay_Subscriptions', 'process_subscription_payment'), 5, 2);
    add_action('woocommerce_subscription_status_updated',
        array('Woocommerce_Gopay_Subscriptions', 'cancel_subscription_payment'), 4, 3);
    add_action('woocommerce_scheduled_subscription_payment_retry',
        array('Woocommerce_Gopay_Subscriptions', 'retry_subscription_payment'), 20, 1);
}

/**
 * Disable woocommerce subscriptions multiple purchase option
 */
function disable_subscriptions_multiple_purchase() {
    if (!get_option(WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase') ||
        get_option(WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase') == 'yes') {
        add_action('admin_notices', 'admin_notice_error');
        update_option(WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase', 'no');
    }
}

/**
 * Show an error message about mixed checkout option was disabled
 */
function admin_notice_error() {
    $message = __(
        "WooCommerce GoPay gateway plugin requires WooCommerce Subscriptions Mixed Checkout option to be disabled.",
        WOOCOMMERCE_GOPAY_DOMAIN
    );
    echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
}

// TEST
/**
 * Add template redirect
 *
 * @since 1.0.0
 */
add_action('template_redirect', 'check_status_gopay_redirect');
function check_status_gopay_redirect() {
//    global $wp;
//
//    $log = [
//        'order_id' => 0,
//        'transaction_id' => 0,
//        'log_level' => home_url( $wp-> ),
//        'log' => $_GET
//    ];
//    Woocommerce_Gopay_Log::insert_log($log);
}
// END

#load_plugin_textdomain(WOOCOMMERCE_GOPAY_DOMAIN, WOOCOMMERCE_GOPAY_DIR . '/languages');
