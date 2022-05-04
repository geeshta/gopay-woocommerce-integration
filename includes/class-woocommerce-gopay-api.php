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
     * Instance of the class.
     *
     * @since 1.0.0
     */
    protected static $instance = null;

    /**
     * Constructor for the plugin GoPay api
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->options = get_option('woocommerce_' . WOOCOMMERCE_GOPAY_ID . '_settings');
        $this->gopay = $this->auth_GoPay();
    }

    /**
     * Get Woocommerce_Gopay_API instance if it exists
     * or create a new one.
     *
     * @since 1.0.0
     * @return Woocommerce_Gopay_API Instance
     */
    public static function instance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * GoPay authentication
     *
     * @return \GoPay\Payments object
     * @since  1.0.0
     */
    private function auth_GoPay()
    {

        // Change it - compare with supported languages
        $language = get_locale() ? strtoupper(
            explode("_", get_locale())[0]) : GoPay\Definition\Language::ENGLISH;

        $gopay = GoPay\payments([
            "goid" => $this->options["goid"],
            "clientId" => $this->options["client_id"],
            "clientSecret" => $this->options["client_secret"],
            "isProductionMode" => $this->options["test"] == "yes" ? false : true,
            "scope" => GoPay\Definition\TokenScope::ALL,
            "language" => $language,
            "timeout" => 30,
        ]);

        return $gopay;
    }

    /**
     * Get items info
     *
     * @param object $order order detail.
     * @return array
     * @since 1.0.0
     */
    private function get_items($order)
    {
        $items = array();
        foreach ($order->get_items() as $item) {

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
     * @param string $gopay_payment_method payment method.
     * @param array $order order detail.
     * @param string $return_url url to be used when redirect from GoPay.
     * @param string $end_date the end date of recurrence
     * @return \GoPay\Http\Response
     * @since 1.0.0
     */
    public function create_payment($gopay_payment_method, $order, $end_date, $is_retry)
    {
        $simplified = $this->options['simplified_payment_method'] == "yes" ? true : false;

        $allowed_swifts = [];
        if (array_key_exists($gopay_payment_method, Woocommerce_Gopay_Options::supported_banks())) {
            $allowed_swifts = [$gopay_payment_method];
            $gopay_payment_method = "BANK_ACCOUNT";
        }

        if (empty($order->get_meta('_GoPay_payment_method', true)) || !$is_retry) {
            if (!$simplified && isset($gopay_payment_method)) {
                $default_payment_instrument = $gopay_payment_method;
            } else {
                $default_payment_instrument = "";
            }
        } else {
            $default_payment_instrument = $order->get_meta('_GoPay_payment_method', true);
            $allowed_swifts = !empty($order->get_meta('_GoPay_payment_method', true)) ?
                [$order->get_meta('_GoPay_bank_swift', true)] : $allowed_swifts;
        }

        $items = $this->get_items($order);

        $notification_url = add_query_arg(array('gopay-api' => WOOCOMMERCE_GOPAY_ID . '_notification',
            'order_id' => $order->get_id()), get_site_url());
        $return_url = add_query_arg(array('gopay-api' => WOOCOMMERCE_GOPAY_ID . '_return',
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

        if (!empty($default_payment_instrument)) {
            $payer = [
                'default_payment_instrument' => $default_payment_instrument,
                'allowed_payment_instruments' => [$default_payment_instrument],
                'allowed_swifts' => $allowed_swifts,
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

        $response = $this->gopay->createPayment($data);

        return $this->decode_response($response);
    }

    /**
     * GoPay create recurrence
     *
     * @param object $order order detail.
     * @return \GoPay\Http\Response
     * @since 1.0.0
     */
    public function create_recurrence($order)
    {
        $parent_order = Woocommerce_Gopay_Subscriptions::get_parent_order($order);
        $GoPay_Transaction_id = $parent_order->get_meta('GoPay_Transaction_id', true);

        $data = [
            'amount' => $order->get_total() * 100,
            'currency' => $order->get_currency(),
            'order_number' => $order->get_order_number(),
            'order_description' => 'subscription',
            'items' => $this->get_items($order),
            'additional_params' => [['name' => 'invoicenumber', 'value' => $order->get_order_number()]]
        ];

        $response = $this->gopay->createRecurrence($GoPay_Transaction_id, $data);

        return $this->decode_response($response);

    }

    /**
     * GoPay cancel recurrence
     *
     * @param object $subscription subscription detail.
     * @return \GoPay\Http\Response
     * @since 1.0.0
     */
    public function cancel_recurrence($subscription)
    {
        $GoPay_Transaction_id = $subscription->get_parent()->get_meta('GoPay_Transaction_id', true);
        $response = $this->gopay->voidRecurrence($GoPay_Transaction_id);

        return $this->decode_response($response);
    }

    /**
     * GoPay get enabled payments methods
     *
     * @param string $currency
     * @return array
     * @since  1.0.0
     */
    public function get_enabled_payment_methods($currency)
    {
        $enabledPayments = $this->gopay->getPaymentInstruments($this->options["goid"], $currency);

        $paymentInstruments = array();
        if ($enabledPayments->statusCode == 200) {
            foreach ($enabledPayments->json["enabledPaymentInstruments"] as $key => $paymentMethod) {
                if ($paymentMethod["paymentInstrument"] == "BANK_ACCOUNT") {
                    $paymentInstruments[$paymentMethod["paymentInstrument"]] = array(
                        "image" => $paymentMethod["image"]["normal"], "swifts" => array());
                    $enabledSwifts = $paymentMethod["enabledSwifts"];
                    foreach ($enabledSwifts as $_ => $bank) {
                        $paymentInstruments[$paymentMethod["paymentInstrument"]]["swifts"][$bank["swift"]] = array(
                            "label" => $bank["label"]["cs"], "image" => $bank["image"]["normal"]);
                    }
                } else {
                    $paymentInstruments[$paymentMethod["paymentInstrument"]] = array("image" => $paymentMethod["image"]["normal"]);
                }
            }
        }

        return $paymentInstruments;
    }

    /**
     * GoPay get enabled swifts
     *
     * @return array
     * @since  1.0.0
     */
    public function get_enabled_swifts()
    {
        $swifts = [];
        foreach (["CZK", "EUR", "PLN"] as $key => $currency) {
            $paymentInstruments = $this->get_enabled_payment_methods($currency);

            if (array_key_exists('BANK_ACCOUNT', $paymentInstruments)) {
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
     * @param string $order_id
     * @param string $GoPay_Transaction_id
     * @since  1.0.0
     */
    public function check_payment_status($order_id, $GoPay_Transaction_id)
    {
        $response = $this->gopay->getStatus($GoPay_Transaction_id);

        $orders = wc_get_orders(array(
            'limit' => 1,
            'meta_key' => 'GoPay_Transaction_id',
            'meta_compare' => $GoPay_Transaction_id,
        ));

        if (!empty($orders)) {
            $order = $orders[0];
        } else {
            return;
        }

        // Save log
        $log = [
            'order_id' => $order->get_id(),
            'transaction_id' => $response->statusCode == 200 ? $response->json['id'] : '0',
            'message' => $response->statusCode == 200 ? 'Checking payment status' : 'Error checking payment status',
            'log_level' => $response->statusCode == 200 ? 'INFO' : 'ERROR',
            'log' => $response
        ];
        Woocommerce_Gopay_Log::insert_log($log);

        if ($response->statusCode != 200) {
            return;
        }

        switch ($response->json['state']) {
            case 'PAID':
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

                $order->save();
                wp_redirect($order->get_checkout_order_received_url());

                break;
            case 'PAYMENT_METHOD_CHOSEN':
            case 'AUTHORIZED':
            case 'CREATED':
                wp_redirect($order->get_checkout_order_received_url());

                break;
            case 'TIMEOUTED':
            case 'CANCELED':
                $order->set_status('failed');
                $order->save();
                wp_redirect($order->get_checkout_order_received_url());

                break;
            case 'REFUNDED':
                $order->set_status('refunded');
                $order->save();

                break;
        }
    }

    /**
     * Get status of the transaction
     *
     * @since  1.0.0
     */
    public function get_status($order_id)
    {
        $GoPay_Transaction_id = get_post_meta($order_id, 'GoPay_Transaction_id', true);
        $response = $this->gopay->getStatus($GoPay_Transaction_id);

        return $this->decode_response($response);
    }

    /**
     * Refund payment
     *
     * @param int $transaction_id
     * @param string $amount
     * @return \GoPay\Http\Response $response
     * @since  1.0.0
     */
    public function refund_payment($transaction_id, $amount)
    {
        $response = $this->gopay->refundPayment($transaction_id, $amount);

        return $this->decode_response($response);
    }

    /**
     * Decode GoPay response and add raw body if
     * different from json property
     *
     * @param \GoPay\Http\Response $response
     * @since  1.0.0
     */
    private function decode_response($response)
    {
        $not_identical= (json_decode($response->__toString(), true) != $response->json) ||
            (empty($response->__toString()) != empty($response->json));

        if ($not_identical) {
            $response->{"raw_body"} = filter_var(str_replace("\n", " ",
                $response->__toString()), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        return $response;
    }
}