<?php

/**
 * Messages to be loaded in the admin dashboard as notices
 *
 * @package WooCommerce GoPay gateway
 * @author argo22
 * @link https://www.argo22.com
 * @copyright 2022 argo22
 * @since 1.0.0
 */
class Woocommerce_Gopay_Notices
{

    /**
     * WooCommerce is requirement
     */
    public static function wc_missing_notice_message()
    {
        $message = __('WooCommerce GoPay gateway plugin requires WooCommerce to be active.', WOOCOMMERCE_GOPAY_DOMAIN);
        echo '<div class="' . esc_attr('notice notice-error') . '"><p>' . esc_html($message) . '</p></div>';
    }

    /**
     * PHP higher than 7.4 is requirement
     */
    public static function php_version_message()
    {
        $message = __('This plugin requires PHP Version 7.4 or greater.', WOOCOMMERCE_GOPAY_DOMAIN);
        echo '<div class="' . esc_attr('notice notice-error') . '"><p>' . esc_html($message) . '</p></div>';
    }

}