<?php
/**
 * Messages to be loaded in the admin dashboard as notices
 *
 * @link       https://argo22.com/
 * @since      1.0.0
 *
 * @package    woocommerce-gopay
 * @subpackage woocommerce-gopay/includes
 * @author    argo22
 */
class Woocommerce_Gopay_Notices {

  /**
  * WooCommerce is requirement
  */
  public static function wc_missing_notice_message() {
    $message = __('WooCommerce GoPay gateway plugin requires WooCommerce to be active.', WOOCOMMERCE_GOPAY_DOMAIN); 
    echo '<div class="' . esc_attr('notice notice-error') . '"><p>' . esc_html($message) . '</p></div>';
  }

  /**
  * PHP higher than 5.4 is requirement
  */
  public static function php_version_message() {
    $message = __('This plugin requires PHP Version 5.4 or greater.', WOOCOMMERCE_GOPAY_DOMAIN); 
    echo '<div class="' . esc_attr('notice notice-error') . '"><p>' . esc_html($message) . '</p></div>';
  }

}