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
     * When a subscription is added to the cart then check cart add
     *
     * Check if only one subscription was added to the cart
     * without any other products/subscriptions
     *
     * @since  1.0.0
     * @param  bool $valid
     * @param  int $product_id product id
     * @param  int $quantity quantity of the item
     * @return bool
     */
    public static function subscriptions_check_add_to_cart($valid, $product_id, $quantity) {
        remove_filter("woocommerce_add_to_cart_validation",
            array("WC_Subscriptions_Cart_Validator", "maybe_empty_cart"));

        if (WC()->cart->get_cart_contents_count() != 0 &&
            (WC_Subscriptions_Product::is_subscription(end( WC()->cart->cart_contents)['product_id']) ||
            WC_Subscriptions_Product::is_subscription($product_id))
        ) {

            wc_add_notice(__("Products and subscriptions can not be purchased at the same time and " .
                                    "only one subscription per checkout is possible.",
                WOOCOMMERCE_GOPAY_DOMAIN), "notice");
            return false;
        }

        return true;
    }

    /**
     * When a subscription is added to the cart then check cart update
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
                    WOOCOMMERCE_GOPAY_DOMAIN), "notice");
                return false;
            }
        }

        return true;
    }

    /**
     * Redirect to the shop page if subscription was included
     * into the cart with any other product/subscription
     *
     * @since  1.0.0
     * @return string
     */
    public static function redirect_to_shop() {
        return get_permalink(wc_get_page_id('shop'));
    }

    /**
     * Get subscription data from order
     *
     * @since  1.0.0
     * @param  object $order
     * @return object
     */
    public static function get_subscription_data($order) {

        $is_subscriptions_plugin_active = in_array("woocommerce-subscriptions/woocommerce-subscriptions.php",
            apply_filters("active_plugins", get_option("active_plugins")));
        if ($is_subscriptions_plugin_active) {
            $order_id = $order->get_id();
            $is_subscription = (wcs_is_subscription($order_id) ||
                wcs_order_contains_subscription($order_id) ||
                wcs_order_contains_renewal($order_id));

            if ($is_subscription) {
                $subscription = wcs_get_subscriptions_for_order($order_id);
                $subscription_id = json_decode(end($subscription))->id;

                return wcs_get_subscription($subscription_id);
            }
        }

        return [];
    }

}