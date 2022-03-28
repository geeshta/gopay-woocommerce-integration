<?php

/**
 * WooCommerce GoPay gateway
 *
 * Initialize the payment gateway between WooCommerce and GoPay
 *
 * @link              https://argo22.com/
 * @since             1.0.0
 * @package           woocommerce-gopay
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      www.argo22.com
 * @copyright 2022 argo22
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
      $this->id = "wc_gopay_gateway";
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

      $this->supported_countries = Woocommerce_Gopay_Options::supported_countries();
      $this->supported_shipping_methods = Woocommerce_Gopay_Options::supported_shipping_methods();
      $this->supported_payment_methods = Woocommerce_Gopay_Options::supported_payment_methods();
      $this->supported_banks = Woocommerce_Gopay_Options::supported_banks();
      $this->iso2_to_iso3 = Woocommerce_Gopay_Options::iso2_to_iso3();

      $this->init_form_fields();
      $this->init_settings();

      $this->title = $this->get_option("title");
      $this->description = $this->get_option("description");
      $this->goid = $this->get_option("goid");
      $this->client_id = $this->get_option("client_id");
      $this->client_secret = $this->get_option("client_secret");
      $this->test = $this->get_option("test");
      $this->instructions = $this->get_option("instructions");
      $this->enable_shipping_methods = $this->get_option(
        "enable_shipping_methods",
        []
      );
      $this->enable_countries = $this->get_option("enable_countries", []);
      $this->enable_gopay_payment_methods = $this->get_option(
        "enable_gopay_payment_methods",
        []
      );
      $this->enable_banks = $this->get_option("enable_banks", []);

      #add_filter('woocommerce_currencies', array('Woocommerce_Gopay_Options', 'supported_currencies'));
      add_action("woocommerce_update_options_payment_gateways_" . $this->id, [
        $this,
        "process_admin_options",
      ]);
      add_action("woocommerce_thankyou_" . $this->id, [$this, "thankyou_page"]);
      add_filter(
        "woocommerce_payment_complete_order_status",
        [$this, "complete_order_status"],
        10,
        3
      );
    }

    /**
     * Gateway Settings Form Fields in Admin.
     *
     * @since  1.0.0
     */
    public function init_form_fields()
    {
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
            "Enable payment retry using the same payment metyhod",
            WOOCOMMERCE_GOPAY_DOMAIN
          ),
          "description" => __(
            "If enabled, payment retry of a failed payment will be done using the same payment method that was selected when customer was placing an order.",
            WOOCOMMERCE_GOPAY_DOMAIN
          ),
        ],
      ];
    }

    /**
     * Is the gateway available based on the restrictions
     * of countries and shipping methods.
     *
     * @since  1.0.0
     * @return bool
     */
    public function is_available()
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
            $chosen_shipping_methods[$key] = explode(":", $value)[0];
          }

          if (
            empty($chosen_shipping_methods) ||
            array_diff($chosen_shipping_methods, $this->enable_shipping_methods)
          ) {
            return false;
          }
        }
        //end check shipping methods
      }

      return parent::is_available();
    }

    /**
     * Payment fields.
     *
     * @since  1.0.0
     */
    public function payment_fields()
    {
      echo wpautop(wptexturize($this->description));

      $enabled_payment_methods = "";
      $checked = 'checked="checked"';
      foreach ($this->enable_gopay_payment_methods as $key => $payment_method) {
        $enabled_payment_methods .=
          '
        <div class="payment_method_wc_gopay_gateway_selection">
          <input class="payment_method_wc_gopay_gateway_input" name="gopay_payment_method" type="radio" id="' .
          $payment_method .
          '" value="' .
          $payment_method .
          '" ' .
          $checked .
          ' />
          <span>' .
          $this->supported_payment_methods[$payment_method] .
          '</span>
        </div>';
        $checked = "";
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
        $test = $this->test == "yes" ? true : false;

        $gopay = GoPay\payments([
            "goid" => $this->goid,
            "clientId" => $this->client_id,
            "clientSecret" => $this->client_secret,
            "isProductionMode" => $test,
            "scope" => GoPay\Definition\TokenScope::ALL,
            "language" => GoPay\Definition\Language::ENGLISH,
            "timeout" => 30,
        ]);

        error_log(print_r($gopay, true));

        if (isset($_POST["gopay_payment_method"])) {
            $default_payment_instrument = $_POST["gopay_payment_method"];
        } else {
            $default_payment_instrument = "PAYMENT_CARD";
        }

        $items = array();
        foreach($order->get_items() as $item){
            $items[] = [
                'type' => $item['type'],
                'name' => $item['name'],
                'product_url' => get_permalink($item['product_id']),
                'amount' => $item['total'],
                'count' => $item['quantity'],
                'vat_rate' => 'RATE_4' // Change it - select the correct rate
            ];
        }

        $callback = [
            'return_url' => add_query_arg(array(
                'order-pay' => $order->get_id(),
                'key' => $order->get_order_key()
            )),
            'notification_url' => 'https://wordpress.test/' // Change it
        ];

      $response = $gopay->createPayment([
        'payer' => [
            'default_payment_instrument' => $default_payment_instrument,
            'allowed_payment_instruments' => $this->enable_gopay_payment_methods,
            'default_swift' => 'FIOBCZPP', // Change it to be the one chosen by the user
            'allowed_swifts' => $this->enable_banks,
            'contact' => [
                'first_name' => $order->billing_first_name,
                'last_name' => $order->billing_last_name,
                'email' => $order->billing_email,
                'phone_number' => $order->billing_phone,
                'city' => $order->billing_city,
                'street' => $order->billing_address_1,
                'postal_code' => $order->billing_postcode,
                'country_code' => $this->iso2_to_iso3[$order->billing_country]
            ]
        ],
        'amount' => $order->total,
        'currency' => $order->get_currency(), // Change it - check if the currency is one of the allowed currencies
        'order_number' => $order->get_order_number(),
        'order_description' => 'order',
        'items' => $items,
        'additional_params' => [
          ['name' => 'invoicenumber',
            'value' => $order->get_order_number()
        ]],
        'callback' => $callback,
        'lang' => GoPay\Definition\Language::ENGLISH // Change it to the one specified
      ]);

      error_log(print_r($response, true));
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
      if ($order && "wc_gopay_gateway" === $order->get_payment_method()) {
        return "completed";
      }
      return $status;
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
