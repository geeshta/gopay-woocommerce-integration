<?php
/**
 * The plugin bootstrap file
 *
 * @link                 https://argo22.com/
 * @since                1.0.0
 * @package              woocommerce-gopay
 * @wordpress-plugin
 * Plugin Name:          WooCommerce GoPay gateway
 * Plugin URI:           https://github.com/argo22packages/gopay-woocommerce-integration
 * Description:          WooCommerce and GoPay payment gateway integration
 * Version:              1.0.0
 * Author:               Argo22
 * Author URI:           https://argo22.com/
 * Text Domain:          woocommerce-gopay
 * License:              GPLv2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:          /languages
 * WC requires at least: 6.0
 * WC tested up to:      6.3
 */

// If this file is called directly, abort.
// Preventing direct access to your WordPress.
if ( !defined( 'WPINC' ) ) {
	die();
}

/**
 * Constants.
 */
define( 'WOOCOMMERCE_GOPAY_DOMAIN', 'woocommerce-gopay' );
define( 'WOOCOMMERCE_GOPAY_ID', 'wc_gopay_gateway' );
define( 'WOOCOMMERCE_GOPAY_FULLPATH', __FILE__ );
define( 'WOOCOMMERCE_GOPAY_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_GOPAY_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_GOPAY_BASENAME', plugin_basename( __FILE__ ) );
define( 'WOOCOMMERCE_GOPAY_BASENAME_DIR', dirname( plugin_basename( __FILE__ ) ) );
define( 'WOOCOMMERCE_GOPAY_LOG_TABLE_NAME', 'woocommerce_gopay_log' );

// Check requirements
require WOOCOMMERCE_GOPAY_DIR .
	'check_requirements.php';

// Load files
require_once WOOCOMMERCE_GOPAY_DIR .
	'vendor/autoload.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'admin/class-woocommerce-gopay-admin.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-log.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-options.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-activator.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-deactivator.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-api.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay-subscriptions.php';
require_once WOOCOMMERCE_GOPAY_DIR .
	'includes/class-woocommerce-gopay.php';

// Register activation/deactivation hook
register_activation_hook( __FILE__, array( 'Woocommerce_Gopay_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Woocommerce_Gopay_Deactivator', 'deactivate' ) );

// Check if Woocommerce GoPay Gateway was instantiated
add_action( 'plugins_loaded', array( 'Woocommerce_Gopay_Gateway', 'get_instance' ) );
// Load text domain for translations
add_action( 'init', array( 'Woocommerce_Gopay_Gateway', 'load_textdomain' ), 99 );
