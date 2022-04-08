<?php

/**
 * WooCommerce GoPay API
 *
 * Connect to GoPay API using the GoPay's PHP SDK
 *
 * @package WooCommerce GoPay gateway
 * @author argo22
 * @link https://www.argo22.com
 * @copyright 2022 argo22
 * @since 1.0.0
 */

class Woocommerce_Gopay_API
{
    /**
     * Constructor for the plugin GoPay api
     *
     * @since 1.0.0
     */
    public function __construct()
    {
    }

    /**
     * GoPay authentication
     *
     * @since  1.0.0
     * @param  array $options gopay gateway settings.
     * @return GoPay object
     */
    private static function auth_GoPay($options){
        $gopay = GoPay\payments([
            "goid" => $options["goid"],
            "clientId" => $options["client_id"],
            "clientSecret" => $options["client_secret"],
            "isProductionMode" => $options["test"] == "yes" ? false : true,
            "scope" => GoPay\Definition\TokenScope::ALL,
            "language" => GoPay\Definition\Language::ENGLISH,
            "timeout" => 30,
        ]);

        return $gopay;
    }

    /**
     * GoPay create payment
     *
     * @since  1.0.0
     * @param  string $gopay_payment_method payment method.
     * @param  array $order order detail.
     * @param  string $return_url url to be used when redirect from GoPay.
     * @return response
     */
    public static function create_payment($gopay_payment_method, $order, $return_url){
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        if (isset($gopay_payment_method)) {
            $default_payment_instrument = $gopay_payment_method;
        } else {
            $default_payment_instrument = "PAYMENT_CARD";
        }

        $items = array();
        foreach($order->get_items() as $item){
            $items[] = [
                'type' => 'ITEM', // Change it
                'name' => $item['name'],
                'product_url' => get_permalink($item['product_id']),
                'amount' => $item['total'] * 100,
                'count' => $item['quantity'],
                'vat_rate' => '0' // Change it - select the correct rate
            ];
        }

        $callback = [
            'return_url' => $return_url, // wc_get_checkout_url(),
            'notification_url' => get_home_url() // Change it
        ];

        $contact = [
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'email' => $order->get_billing_email(),
            'phone_number' => $order->get_billing_phone(),
            'city' => $order->get_billing_city(),
            'street' => $order->get_billing_address_1(),
            'postal_code' => $order->get_billing_postcode(),
            'country_code' => Woocommerce_Gopay_Options::iso2_to_iso3()[$order->get_billing_country()]
        ];

        $simplified = $options['simplified_payment_method'] == "yes" ? true : false;
        if (!$simplified) {
            $payer = [
                'default_payment_instrument' => $default_payment_instrument,
                'allowed_payment_instruments' => [$default_payment_instrument],
                'allowed_swifts' => $options['enable_banks_' . get_woocommerce_currency()],
                'contact' => $contact
            ];
        } else {
            $payer = [
                'contact' => $contact
            ];
        }

        $additional_params = [
            ['name' => 'invoicenumber',
                'value' => $order->get_order_number()
            ]];

        $response = $gopay->createPayment([
            'payer' => $payer,
            'amount' => $order->get_total() * 100,
            'currency' => $order->get_currency(),
            'order_number' => $order->get_order_number(),
            'order_description' => 'order',
            'items' => $items,
            'additional_params' => $additional_params,
            'callback' => $callback,
            'lang' => GoPay\Definition\Language::ENGLISH // Change it to the one specified
        ]);

        return $response;
    }

    /**
     * GoPay get enabled payments methods
     *
     * @since  1.0.0
     * @param string $currency
     * @return array
     */
    public static function get_enabled_payment_methods($currency){
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        $enabledPayments = $gopay->getPaymentInstruments($options["goid"], $currency);

        $paymentInstruments = array();
        if ($enabledPayments->statusCode == 200){
            foreach ($enabledPayments->json["enabledPaymentInstruments"] as $key => $paymentMethod){
                if ($paymentMethod["paymentInstrument"] == "BANK_ACCOUNT"){
                    $paymentInstruments[$paymentMethod["paymentInstrument"]] = array(
                        "image" => $paymentMethod["image"]["normal"], "swifts" => array());
                    $enabledSwifts = $paymentMethod["enabledSwifts"];
                    foreach ($enabledSwifts as $_ => $bank) {
                        $paymentInstruments[$paymentMethod[
                            "paymentInstrument"]]["swifts"][$bank["swift"]] = $bank["label"]["cs"];
                    }
                } else {
                    $paymentInstruments[$paymentMethod[
                        "paymentInstrument"]] = array("image" => $paymentMethod["image"]["normal"]);
                }
            }
        }

        return $paymentInstruments;
    }

    /**
     * GoPay get enabled swifts
     *
     * @since  1.0.0
     * @return array
     */
    public static function get_enabled_swifts()
    {
        $swifts = [];
        foreach (["CZK", "EUR", "PLN"] as $currency => $value) {
            $paymentInstruments = self::get_enabled_payment_methods($currency);

            if (array_key_exists('BANK_ACCOUNT', $paymentInstruments)){
                foreach ($paymentInstruments["BANK_ACCOUNT"]["swifts"] as $swift => $description) {
                    $swifts[$swift] = $description;
                }
            }
        }

        return $swifts;
    }
}