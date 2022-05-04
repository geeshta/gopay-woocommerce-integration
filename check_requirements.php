<?php

/**
 * Check if plugin is active
 */
function check_is_plugin_active($path)
{
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