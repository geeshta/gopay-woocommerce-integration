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
    private static function auth_GoPay($options) {

        // Change it - compare with supported languages
        $language = get_locale() ? strtoupper(
            explode("_", get_locale())[0]) : GoPay\Definition\Language::ENGLISH;

        $gopay = GoPay\payments([
            "goid" => $options["goid"],
            "clientId" => $options["client_id"],
            "clientSecret" => $options["client_secret"],
            "isProductionMode" => $options["test"] == "yes" ? false : true,
            "scope" => GoPay\Definition\TokenScope::ALL,
            "language" => $language,
            "timeout" => 30,
        ]);

        return $gopay;
    }

    /**
     * Get items info
     *
     * @since 1.0.0
     * @param object $order order detail.
     * @return array
     */
    private static function get_items($order) {

        $items = array();
        foreach($order->get_items() as $item){

            $vat_rate = '0';
            if ($item->get_tax_status() == 'taxable') {
                $array = WC_Tax::get_base_tax_rates($item->get_tax_class());
                if (!empty($array)) {
                    $vat_rate = (string)end($array)['rate'];
                }
            }

            $items[] = [
                'type' => 'ITEM',
                'name' => $item['name'],
                'product_url' => get_permalink($item['product_id']),
                'amount' => $item['total'] * 100,
                'count' => $item['quantity'],
                'vat_rate' => $vat_rate
            ];
        }

        return $items;
    }

    /**
     * GoPay create payment
     *
     * @since 1.0.0
     * @param string $gopay_payment_method payment method.
     * @param array $order order detail.
     * @param string $return_url url to be used when redirect from GoPay.
     * @param string $end_date the end date of recurrence
     * @return response
     */
    public static function create_payment($gopay_payment_method, $order, $return_url, $end_date) {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        if (isset($gopay_payment_method)) {
            $default_payment_instrument = $gopay_payment_method;
        } else {
            $default_payment_instrument = "PAYMENT_CARD";
        }

        $items = self::get_items($order);

        $notification_url = add_query_arg(array('gopay-api' => WOOCOMMERCE_GOPAY_ID . '_notification',
                                                'order_id' => $order->get_id()), get_site_url());

        $callback = [
            'return_url' => $return_url,
            'notification_url' => $notification_url
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
                'allowed_swifts' => $options['enable_banks_' . $order->get_currency()],
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

        // Change it - compare with supported languages
        $language = get_locale() ? strtoupper(
            explode("_", get_locale())[0]) : GoPay\Definition\Language::ENGLISH;

        $data = [
            'payer' => $payer,
            'amount' => $order->get_total() * 100,
            'currency' => $order->get_currency(),
            'order_number' => $order->get_order_number(),
            'order_description' => 'order',
            'items' => $items,
            'additional_params' => $additional_params,
            'callback' => $callback,
            'lang' => $language
        ];

        if (!empty($end_date)) {
            $data["recurrence"] = [
                "recurrence_cycle" => "ON_DEMAND",
                "recurrence_date_to" => $end_date != 0 ? $end_date : date('Y-m-d', strtotime('+5 years'))];
        }

        $response = $gopay->createPayment($data);

        return $response;
    }

    /**
     * GoPay create recurrence
     *
     * @since 1.0.0
     * @param object $order order detail.
     * @return response
     */
    public static function create_recurrence($order) {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        $parent_order = Woocommerce_Gopay_Subscriptions::get_parent_order($order);
        $GoPay_Transaction_id = $parent_order->get_meta('GoPay_Transaction_id', true);

        $data = [
            'amount' => $order->get_total() * 100,
            'currency' => $order->get_currency(),
            'order_number' => $order->get_order_number(),
            'order_description' => 'subscription',
            'items' => self::get_items($order),
            'additional_params' => [['name' => 'invoicenumber', 'value' => $order->get_order_number()]]
        ];

        $response = $gopay->createRecurrence($GoPay_Transaction_id, $data);

        return $response;

    }

    /**
     * GoPay cancel recurrence
     *
     * @since 1.0.0
     * @param object $subscription subscription detail.
     * @return response
     */
    public static function cancel_recurrence($subscription) {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        $GoPay_Transaction_id = $subscription->get_parent()->get_meta('GoPay_Transaction_id', true);

        $response = $gopay->voidRecurrence($GoPay_Transaction_id);

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
        foreach (["CZK", "EUR", "PLN"] as $key => $currency) {
            $paymentInstruments = self::get_enabled_payment_methods($currency);

            if (array_key_exists('BANK_ACCOUNT', $paymentInstruments)){
                foreach ($paymentInstruments["BANK_ACCOUNT"]["swifts"] as $swift => $description) {
                    $swifts[$swift] = __($description, WOOCOMMERCE_GOPAY_DOMAIN);
                }
            }
        }

        return $swifts;
    }

    /**
     * Check payment status
     *
     * @since  1.0.0
     */
    public static function check_payment_status() {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        $orders = wc_get_orders(array(
                'limit'=>-1,
                'type'=> 'shop_order',
                'status'=> 'wc-pending' //array('wc-pending', 'wc-on-hold')
            )
        );
        foreach ($orders as $order) {
            $GoPay_Transaction_id = $order->get_meta('GoPay_Transaction_id', true);

            if (empty($GoPay_Transaction_id)) {
                continue;
            }

            $response = $gopay->getStatus($GoPay_Transaction_id);

            if ($response->json['state'] == 'PAID') {
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

                // Update retry status
                if (class_exists("WCS_Retry_Manager", false)) {
                    $retry = WCS_Retry_Manager::store()->get_last_retry_for_order(
                        wcs_get_objects_property($order, 'id'));
                    if (!empty($retry)) {
                        $retry->update_status('complete');
                    }
                }

                // Save log
                $log = [
                    'order_id' => $order->get_id(),
                    'transaction_id' => $response->json['id'],
                    'log_level' => 'INFO',
                    'log' => $response->json
                ];
                Woocommerce_Gopay_Log::insert_log($log);
            }
            $order->save();
        }
    }

    /**
     * Get status of the transaction
     *
     * @since  1.0.0
     */
    public static function get_status($order_id) {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);

        $GoPay_Transaction_id = get_post_meta($order_id, 'GoPay_Transaction_id', true);
        $response = $gopay->getStatus($GoPay_Transaction_id);

        // Change it - Handle any possible error and add log

        return $response;
    }

    /**
     * Refund payment
     *
     * @since  1.0.0
     * @param int $transaction_id
     * @param string $amount
     * @return json $response
     */
    public static function refund_payment($transaction_id, $amount) {
        $options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $gopay = self::auth_GoPay($options);
        $response = $gopay->refundPayment($transaction_id, $amount);

        // Change it - Handle any possible error and add log
        // Add message add_order_note and wc_add_notice

        return $response;
    }
}