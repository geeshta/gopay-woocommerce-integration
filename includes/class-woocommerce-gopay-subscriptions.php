<?php

/**
 * WooCommerce GoPay gateway subscriptions.
 *
 * Deal with woocommerce subscriptions plugin.
 *
 * @package WooCommerce GoPay gateway
 * @author argo22
 * @link https://www.argo22.com
 * @copyright 2022 argo22
 * @since 1.0.0
 */

class Woocommerce_Gopay_Subscriptions {

    /**
     * If subscriptions then check cart add
     *
     * Check if only one subscription was added to the cart
     * without any product purchased at the same time
     *
     * @since  1.0.0
     * @param  bool $valid
     * @param  int $product_id product id
     * @param  int $quantity quantity of the item
     * @return bool
     */
    public static function subscriptions_check_add_to_cart($valid, $product_id, $quantity) {
//        error_log(print_r([$valid, $product_id, $quantity, WC()->cart->get_cart_contents_count()], true));
//        error_log(print_r($GLOBALS['wp_filter']['woocommerce_add_to_cart_validation'],true));
//        remove_all_filters('woocommerce_add_to_cart_validation');
//        remove_all_filters("woocommerce_update_cart_validation");

        if (WC()->cart->get_cart_contents_count() != 0 &&
            (WC_Subscriptions_Product::is_subscription(end( WC()->cart->cart_contents)['product_id']) ||
            WC_Subscriptions_Product::is_subscription($product_id))
        ) {

            error_log("test");
            wc_add_notice(__("Products and subscriptions can not be purchased at the same time and " .
                                    "only one subscription per checkout is possible.",
                WOOCOMMERCE_GOPAY_DOMAIN), "error");
            return false;
        }

        return true;
    }

    /**
     * If subscriptions then check cart update
     *
     * Check if only one subscription was added to the cart
     *
     * @since  1.0.0
     * @param  bool $passed
     * @param  string $cart_item_key
     * @param  array $values values of the item
     * @param  int $quantity quantity of the item
     * @return bool
     */
    public static function subscriptions_check_cart_update($passed, $cart_item_key, $values, $quantity) {

        if ($quantity > 1) {
            if (WC_Subscriptions_Product::is_subscription($values['product_id'])) {
                wc_add_notice(__("Only one recurring payment/subscription per checkout is possible",
                    WOOCOMMERCE_GOPAY_DOMAIN), "error");
                return false;
            }
        }

        return true;
    }

}