<?php

/**
 * WooCommerce GoPay gateway
 *
 * Initialize the payment gateway between WooCommerce and GoPay
 *
 * @package WooCommerce GoPay gateway
 * @author argo22
 * @link https://www.argo22.com
 * @copyright 2022 argo22
 * @since 1.0.0
 */

add_action("plugins_loaded", "init_woocommerce_gopay_gateway");

function init_woocommerce_gopay_gateway()
{
  class Woocommerce_Gopay_Gateway extends WC_Payment_Gateway
  {
    /**
     * Constructor for the gateway
     *
     * @since  1.0.0
     */
    public function __construct()
    {
      $this->id = WOOCOMMERCE_GOPAY_ID;
      // to show an image next to the gateway’s name on the frontend.
      $this->icon = apply_filters(
        "woocommerce_gopay_icon",
        WOOCOMMERCE_GOPAY_URL . "includes/assets/gopay.png"
      );
      $this->has_fields = false;
      $this->method_title = __(
        "GoPay payment gateway",
        WOOCOMMERCE_GOPAY_DOMAIN
      );
      $this->method_description = __(
        "Take payments via GoPay payment gateway.",
        WOOCOMMERCE_GOPAY_DOMAIN
      );
      $this->supports = array(
          'subscriptions',
          'products',
          'subscription_cancellation',
          'subscription_reactivation',
          'subscription_suspension',
          'subscription_amount_changes',
          'subscription_payment_method_change',
          'subscription_date_changes',
          'refunds',
          'pre-orders'
      );

      $this->enable_currencies = Woocommerce_Gopay_Options::supported_currencies();
      $this->supported_countries = Woocommerce_Gopay_Options::supported_countries();
      $this->supported_shipping_methods = Woocommerce_Gopay_Options::supported_shipping_methods();
      $this->supported_payment_methods = Woocommerce_Gopay_Options::supported_payment_methods();
      $this->supported_banks = Woocommerce_Gopay_Options::supported_banks();
      $this->iso2_to_iso3 = Woocommerce_Gopay_Options::iso2_to_iso3();

      $this->init_form_fields();
      $this->init_settings();

      add_action("woocommerce_update_options_payment_gateways_" . $this->id, [
          $this,
          "process_admin_options",
          ]
      );

      $this->title = $this->get_option("title");
      $this->description = $this->get_option("description");
      $this->goid = $this->get_option("goid");
      $this->client_id = $this->get_option("client_id");
      $this->client_secret = $this->get_option("client_secret");
      $this->test = !$this->get_option("test");
      $this->instructions = $this->get_option("instructions");
      $this->enable_shipping_methods = $this->get_option(
        "enable_shipping_methods",
        []
      );
      $this->enable_countries = $this->get_option("enable_countries", []);
      $this->simplified_payment_method = $this->get_option("simplified_payment_method") == "yes";
      $this->payment_retry = $this->get_option("payment_retry") == "yes";
      $this->enable_gopay_payment_methods = $this->get_option(
        "enable_gopay_payment_methods",
        []
      );
      $this->enable_banks = $this->get_option("enable_banks", []);

      #add_filter('woocommerce_currencies', array('Woocommerce_Gopay_Options', 'supported_currencies'));
      add_action('plugins_loaded', array('Woocommerce_Gopay_Admin_Menu', 'create_menu'));
      add_action("woocommerce_thankyou_" . $this->id, [$this, "thankyou_page"]);
      add_filter(
        "woocommerce_payment_complete_order_status",
        [$this, "complete_order_status"],
        10,
        3
      );
      add_filter("woocommerce_thankyou_order_received_text",
          array($this, "subscription_trial_period_thankyou_page"), 20, 2);
    }

    /**
     * Gateway Settings Form Fields in Admin.
     *
     * @since  1.0.0
     */
    public function init_form_fields()
    {

        $this->init_settings();
        if (empty($this->settings["goid"]) ||
            empty($this->settings["client_id"]) ||
            empty($this->settings["client_secret"])) {
            $this->update_option('enabled',"no");
        }

        $this->form_fields = [
            "enabled" => [
                "title" => __("Enable/Disable", WOOCOMMERCE_GOPAY_DOMAIN),
                "type" => "checkbox",
                "label" => __(
                    "Inform goid, client id and secret to enable the other options",
                    WOOCOMMERCE_GOPAY_DOMAIN
                ),
                "css" => "display: none;",
                "default" => "no",
            ],
            "goid" => [
                "title" => __("GoId", WOOCOMMERCE_GOPAY_DOMAIN),
                "type" => "text",
                "css" => "width: 500px;",
            ],
            "client_id" => [
                "title" => __("Client Id", WOOCOMMERCE_GOPAY_DOMAIN),
                "type" => "text",
                "css" => "width: 500px;",
            ],
            "client_secret" => [
                "title" => __("Client secret", WOOCOMMERCE_GOPAY_DOMAIN),
                "type" => "text",
                "css" => "width: 500px;",
            ]
        ];

        if (!empty($this->settings["goid"]) &&
            !empty($this->settings["client_id"]) &&
            !empty($this->settings["client_secret"])) {

            // Set default parameters
            if (empty($this->settings["enabled"])) {
                $this->update_option('enabled',"yes");
            }
            if (empty($this->settings["title"])) {
                $this->update_option('title',"GoPay");
            }
            if (empty($this->settings["description"])) {
                $this->update_option('description',"Payment via GoPay gateway");
            }
            if (empty($this->settings["test"])) {
                $this->update_option('test',"yes");
            }
            // end

            $this->form_fields = [
                "enabled" => [
                    "title" => __("Enable/Disable", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "checkbox",
                    "label" => __(
                        "Enable GoPay payment gateway",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "default" => "yes",
                ],
                "title" => [
                    "title" => __("Title", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "text",
                    "description" => __(
                        "Name of the payment method that is displayed at the checkout",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "default" => __("GoPay", WOOCOMMERCE_GOPAY_DOMAIN),
                    "css" => "width: 500px;",
                ],
                "description" => [
                    "title" => __("Description", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "textarea",
                    "description" => __(
                        "Description of the payment method that is displayed at the checkout",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "default" => __(
                        "Payment via GoPay gateway",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "css" => "width: 500px; min-height: 100px;",
                ],
                "goid" => [
                    "title" => __("GoId", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "text",
                    "css" => "width: 500px;",
                ],
                "client_id" => [
                    "title" => __("Client Id", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "text",
                    "css" => "width: 500px;",
                ],
                "client_secret" => [
                    "title" => __("Client secret", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "text",
                    "css" => "width: 500px;",
                ],
                "test" => [
                    "title" => __("Test mode", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "checkbox",
                    "label" => __(
                        "Enable GoPay payment gateway test mode",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "default" => "yes",
                ],
                "enable_shipping_methods" => [
                    "title" => __("Enable shipping methods", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "multiselect",
                    "class" => "chosen_select",
                    "options" => $this->supported_shipping_methods,
                    "desc_tip" => true,
                    "css" => "width: 500px; min-height: 50px;",
                ],
                "enable_countries" => [
                    "title" => __("Enable countries", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "multiselect",
                    "class" => "chosen_select",
                    "options" => $this->supported_countries,
                    "desc_tip" => true,
                    "css" => "width: 500px; min-height: 50px;",
                ],
                "simplified_payment_method" => [
                    "title" => __("Payment method selection", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "checkbox",
                    "label" => __(
                        "Enable simplified payment method selection",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "description" => __(
                        "If enabled, customers cannot choose any specific payment method at the checkout but they have to select the payment method once the GoPay payment gateway is invoked.",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                ],
                "enable_gopay_payment_methods" => [
                    "title" => __(
                        "Enable GoPay payment methods",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "type" => "multiselect",
                    "class" => "chosen_select",
                    "options" => $this->supported_payment_methods,
                    "desc_tip" => true,
                    "css" => "width: 500px; min-height: 50px;",
                ],
                "enable_banks" => [
                    "title" => __("Enable banks", WOOCOMMERCE_GOPAY_DOMAIN),
                    "type" => "multiselect",
                    "class" => "chosen_select",
                    "options" => $this->supported_banks,
                    "desc_tip" => true,
                    "css" => "width: 500px; min-height: 50px;",
                ],
                "payment_retry" => [
                    "title" => __(
                        "Payment retry payment method",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "type" => "checkbox",
                    "label" => __(
                        "Enable payment retry using the same payment method",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                    "description" => __(
                        "If enabled, payment retry of a failed payment will be done using the same payment method that was selected when customer was placing an order.",
                        WOOCOMMERCE_GOPAY_DOMAIN
                    ),
                ],
            ];
        }
    }

    /**
     * Is the gateway available based on the restrictions
     * of countries and shipping methods.
     *
     * @since  1.0.0
     * @return bool
     */
    public function is_available() // Change it - Add notice message when returning false
    {
      if (!empty(WC()->customer)) {
        // Check countries
        $shipping_country = WC()
          ->cart->get_customer()
          ->get_shipping_country();
        $billing_country = WC()
          ->cart->get_customer()
          ->get_billing_country();

        if (
          empty($this->enable_countries) ||
          (empty($shipping_country) && empty($billing_country))
        ) {
          return false;
        }

        if (
          !in_array($shipping_country, $this->enable_countries) &&
          !in_array($billing_country, $this->enable_countries)
        ) {
          return false;
        }
        // end check countries

        // Check if all products are either virtual or downloadable
        $all_virtual_downloadable = true;
        foreach (WC()->cart->get_cart() as $item) {
          $product = wc_get_product($item["product_id"]);
          if (!$product->is_virtual() && !$product->is_downloadable()) {
            $all_virtual_downloadable = false;
            break;
          }
        }

        if ($all_virtual_downloadable) {
          return parent::is_available();
        }
        //end check virtual or downloadable

        // Check shipping methods
        if (
          is_page(wc_get_page_id("checkout")) &&
          !empty(get_query_var("order-pay"))
        ) {
          $order_id = absint(get_query_var("order-pay"));
          $order = wc_get_order($order_id);

          $items_shipping = $order->get_items("shipping");
          foreach ($items_shipping as $item_shipping) {
            if (
              !in_array(
                $item_shipping->get_method_title(),
                $this->enable_shipping_methods
              )
            ) {
              return false;
            }
          }
        } else {
          $chosen_shipping_methods = [];
          foreach (
            WC()->session->get("chosen_shipping_methods")
            as $key => $value
          ) {
              if (!is_null($value)) {
                  $chosen_shipping_methods[$key] = explode(":", $value)[0];
              }
          }

          if (
            empty($chosen_shipping_methods) ||
            array_diff($chosen_shipping_methods, $this->enable_shipping_methods)
          ) {
            return false;
          }
        }
        //end check shipping methods

        // Check currency matches one of the supported currencies
        if (!get_woocommerce_currency() || !array_key_exists(get_woocommerce_currency(), $this->enable_currencies)) {
            // Change it - notice is showing more than once
            wc_add_notice(__('Currency is not supported on GoPay', WOOCOMMERCE_GOPAY_DOMAIN), "error");
            return false;
        }
        // end check currency
      }

      return parent::is_available();
    }

    /**
     * Payments methods enabled on GoPay account
     *
     * @since  1.0.0
     * @return array $supported_payment_methods
     */
    protected function update_payments_methods_enabled() {
        $paymentInstruments = Woocommerce_Gopay_API::get_enabled_payment_methods(get_woocommerce_currency());

        // Payment methods
        $supported_payment_methods = [];
        foreach ($paymentInstruments as $key => $value){
            $supported_payment_methods[$key] = $value;
            $supported_payment_methods[$key][
                "description"] = Woocommerce_Gopay_Options::supported_payment_methods()[$key];
        }
//        $supported_payment_methods = array_intersect_key(
//            Woocommerce_Gopay_Options::supported_payment_methods(), $paymentInstruments);
        $enable_gopay_payment_methods = $this->get_option("enable_gopay_payment_methods", []);
        $enable_gopay_payment_methods = array_intersect_key($supported_payment_methods,
            array_flip($enable_gopay_payment_methods));
        #$this->enable_gopay_payment_methods = array_keys($enable_gopay_payment_methods);
        $this->update_option('enable_gopay_payment_methods_' . get_woocommerce_currency(),
            array_keys($enable_gopay_payment_methods));

        // Banks
        if (array_key_exists("BANK_ACCOUNT", $enable_gopay_payment_methods)) {
            $supported_banks = array_intersect_key(
                Woocommerce_Gopay_Options::supported_banks(), $paymentInstruments["BANK_ACCOUNT"]["swifts"]);
            $enable_banks = $this->get_option("enable_banks", []);
            $enable_banks = array_intersect_key($supported_banks,
                array_flip($enable_banks));
            #$this->enable_banks = array_keys($enable_banks);
            $this->update_option('enable_banks_' . get_woocommerce_currency(), array_keys($enable_banks));
        } else {
            #$this->supported_banks = [];
            $this->update_option('enable_banks_' . get_woocommerce_currency(), []);
        }

        return $supported_payment_methods;
    }

    /**
     * Payment fields.
     *
     * @since  1.0.0
     */
    public function payment_fields()
    {
      echo wpautop(wptexturize($this->description));

      $supported_payment_methods = $this->update_payments_methods_enabled();

      $enabled_payment_methods = "";
      $checked = 'checked="checked"';
      $payment_retry = ($this->payment_retry &&
          is_page(wc_get_page_id("checkout")) &&
          !empty(get_query_var("order-pay")));
      if (!$this->simplified_payment_method && !$payment_retry) {
          $payment_methods = $this->get_option('enable_gopay_payment_methods_' . get_woocommerce_currency());

          // Check if subscription - only card payment is enabled
          if (Woocommerce_Gopay_Subscriptions::cart_contains_subscription()) {
              if (in_array("PAYMENT_CARD", $payment_methods)) {
                  $payment_methods = array("PAYMENT_CARD");
              } else {
                  $payment_methods = array();
              }
          }

          foreach ($payment_methods as $key => $payment_method) {
              if ($payment_method == "BANK_ACCOUNT" &&
                  empty($this->get_option('enable_banks_' . get_woocommerce_currency()))){
                  continue;
              }
              $enabled_payment_methods .=
                  '
                  <div class="payment_method_' . WOOCOMMERCE_GOPAY_ID . '_selection" style="border-bottom: 1px dashed; padding: 12px;">
                    <input class="payment_method_' . WOOCOMMERCE_GOPAY_ID .
                        '_input" name="gopay_payment_method" type="radio" id="' .
                        $payment_method . '" value="' . $payment_method . '" ' . $checked . ' />
                    <span>' . $supported_payment_methods[$payment_method]["description"] . '</span>
                    <img src="' . $supported_payment_methods[$payment_method]["image"] . '" alt="ico" style="height: auto; width: auto;"/>
                  </div>';
              $checked = "";
          }
      }

      echo $enabled_payment_methods;
    }

    /**
     * Process payment.
     *
     * @since  1.0.0
     * @param  int $order_id Order ID.
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        if (!$order->get_currency() || !array_key_exists($order->get_currency(), $this->enable_currencies)) {
            wc_add_notice(__('Currency is not supported on GoPay', WOOCOMMERCE_GOPAY_DOMAIN), "error");
            return [
                "result" => "failed",
                "redirect" => wc_get_checkout_url()
            ];
        }

        // Payment Retry
        if ($this->payment_retry &&
            is_page(wc_get_page_id("checkout")) &&
            !empty(get_query_var("order-pay"))
        ) {
            $response = Woocommerce_Gopay_API::get_status($order_id);
            return [
                "result" => "success",
                "redirect" => $response->json['gw_url']
            ];
        }

        // Check if total is equal to zero
        $subscription = Woocommerce_Gopay_Subscriptions::get_subscription_data($order);
        if ($order->get_total() == 0) {
            if (!empty($subscription)) {
                return [
                    "result" => "success",
                    "redirect" => $this->get_return_url($order)
                ];
            } else {
                foreach ($order->get_items() as $item) {
                    $product = wc_get_product($item["product_id"]);
                    if (!$product->is_virtual() && !$product->is_downloadable()) {
                        $order->set_status('processing');
                        break;
                    }
                }

                if ($order->get_status() != 'processing') {
                    $order->set_status('completed');
                }
                $order->save();

                return [
                    "result" => "success",
                    "redirect" => $this->get_return_url($order)
                ];
            }
        }

        $response = Woocommerce_Gopay_API::create_payment(
            array_key_exists("gopay_payment_method", $_POST) ?
                $_POST["gopay_payment_method"] : null, $order, $this->get_return_url($order),
            !empty($subscription) ? $subscription->get_date("end") : "");

        if ($response->statusCode != 200) {
            $log = [
                'order_id' => $order_id,
                'transaction_id' => 0,
                'log_level' => 'Error',
                'log' => $response->json
            ];
            Woocommerce_Gopay_Log::insert_log($log);

            wc_add_notice(__('Payment creation on GoPay not possible', WOOCOMMERCE_GOPAY_DOMAIN), "error");

            return [
                "result" => "failed",
                "redirect" => wc_get_checkout_url()
            ];
        }

        // Add GoPay transaction id to order (and subscription if it exists)
        #$order->set_status('on-hold');
        $order->update_meta_data('GoPay_Transaction_id', $response->json['id']);
        $order->save();
        if (!empty($subscription)) {
            $subscription->update_meta_data('GoPay_Transaction_id', $response->json['id']);
            $subscription->save();
        }

        // Save log
        $log = [
            'order_id' => $order_id,
            'transaction_id' => $response->json['id'],
            'log_level' => 'INFO',
            'log' => $response->json
        ];
        Woocommerce_Gopay_Log::insert_log($log);

        return [
            "result" => "success",
            "redirect" => $response->json['gw_url']
        ];
    }

    /**
     * Process refund.
     *
     * @param int $order_id ID.
     * @param float|null $amount amount.
     * @param string $reason reason.
     * @return boolean if succeeded, or a WP_Error object.
     */
    public function process_refund($order_id, $amount = null, $reason = '') {
        $transaction_id = get_post_meta($order_id, 'GoPay_Transaction_id', true);
        $response = Woocommerce_Gopay_API::refund_payment($transaction_id, $amount * 100);

        $log = [
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
            'log_level' => 'info',
            'log' => $response->json
        ];

        if ($response->statusCode != 200) {
            $log['log_level'] = 'Error';
            Woocommerce_Gopay_Log::insert_log($log);

            return false;
        }
        Woocommerce_Gopay_Log::insert_log($log);

        return true;
    }

    /**
     * Message order received page.
     * @since  1.0.0
     */
    public function thankyou_page()
    {
      if ($this->instructions) {
        echo wp_kses_post(wpautop(wptexturize($this->instructions)));
      }
    }

      /**
       * Message order received page for subscription in trial period.
       * @since  1.0.0
       */
      public function subscription_trial_period_thankyou_page($thank_you, $order)
      {
          $message = __("Thank you. Your order has been received.", WOOCOMMERCE_GOPAY_DOMAIN);
          $subscription = Woocommerce_Gopay_Subscriptions::get_subscription_data($order);
          if (!empty($subscription) && $order->get_total() == 0) {
              return $message . __(" Please pay for your subscription after the trial period.", WOOCOMMERCE_GOPAY_DOMAIN);
          }

          return $message;
      }

    /**
     * Complete order status for orders.
     *
     * @since  1.0.0
     * @param  string         $status Current order status.
     * @param  int            $order_id Order ID.
     * @param  WC_Order|false $order Order object.
     * @return string
     */
    public function complete_order_status($status, $order_id, $order = false)
    {
      if ($order &&  WOOCOMMERCE_GOPAY_ID === $order->get_payment_method()) {
        return "completed";
      }
      return $status;
    }

    /**
     * Process admin options.
     *
     * @since  1.0.0
     * @return string
     */
    public function process_admin_options() {
        $saved = parent::process_admin_options();
        $this->init_form_fields();

        return $saved;
    }
  }

  /**
   *  Add the Gateway to WooCommerce
   *
   * @param array $methods
   * @return array
   */
  function add_woocommerce_gopay_gateway($methods)
  {
    $methods[] = "Woocommerce_Gopay_Gateway";
    return $methods;
  }
  add_filter("woocommerce_payment_gateways", "add_woocommerce_gopay_gateway");
}
