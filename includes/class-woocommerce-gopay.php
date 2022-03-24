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

add_action('plugins_loaded', 'init_woocommerce_gopay_gateway');

function init_woocommerce_gopay_gateway(){

  class Woocommerce_Gopay_Gateway extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway
     *
     * @since  1.0.0
     */
    public function __construct(){

      $this->id = 'wc_gopay_gateway';
      // to show an image next to the gateway’s name on the frontend.
      $this->icon = apply_filters('woocommerce_gopay_icon', WOOCOMMERCE_GOPAY_URL . 'includes/assets/gopay.png');
      $this->has_fields = false;
      $this->method_title = __('GoPay payment gateway', WOOCOMMERCE_GOPAY_DOMAIN);
      $this->method_description = __('Take payments via GoPay payment gateway.', WOOCOMMERCE_GOPAY_DOMAIN);

      $this->init_form_fields();
      $this->init_settings();

      $this->title = $this->get_option('title');
      $this->description = $this->get_option('description');
      $this->goid = $this->get_option('goid');
      $this->client_id = $this->get_option('client_id');
      $this->client_secret = $this->get_option('client_secret');
      $this->test = $this->get_option('test');
      $this->instructions = $this->get_option('instructions');
      $this->enable_shipping_methods = $this->get_option('enable_shipping_methods', array());
      $this->enable_countries = $this->get_option('enable_countries', array());
      $this->enable_gopay_payment_methods = $this->get_option('enable_gopay_payment_methods', array());
      $this->enable_banks = $this->get_option('enable_banks', array());

      #add_filter('woocommerce_currencies', array('Woocommerce_Gopay_Options', 'supported_currencies'));
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
      add_filter('woocommerce_payment_complete_order_status', array($this, 'complete_order_status'), 10, 3);
    }

    /**
     * Gateway Settings Form Fields in Admin.
     *
     * @since  1.0.0
     */
    public function init_form_fields(){

      $supported_countries = Woocommerce_Gopay_Options::supported_countries();
      $supported_shipping_methods = Woocommerce_Gopay_Options::supported_shipping_methods();
      $supported_payment_methods = Woocommerce_Gopay_Options::supported_payment_methods();
      $supported_banks = Woocommerce_Gopay_Options::supported_banks();

      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'checkbox',
          'label' => __('Enable GoPay payment gateway', WOOCOMMERCE_GOPAY_DOMAIN),
          'default' => 'yes'
        ),
        'title' => array(
          'title' => __('Title', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'text',
          'description' => __('Name of the payment method that is displayed at the checkout', WOOCOMMERCE_GOPAY_DOMAIN),
          'default' => __('GoPay', WOOCOMMERCE_GOPAY_DOMAIN),
          'css' => 'width: 500px;'
        ),
        'description' => array(
          'title' => __('Description', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'textarea',
          'description' => __('Description of the payment method that is displayed at the checkout', WOOCOMMERCE_GOPAY_DOMAIN),
          'default' => __( 'Payment via GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN),
          'css' => 'width: 500px; min-height: 100px;'
        ),
        'goid' => array(
          'title' => __('GoId', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'client_id' => array(
          'title' => __('Client Id', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'client_secret' => array(
          'title' => __('Client secret', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'test' => array(
          'title' => __('Test mode', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'checkbox',
          'label' => __('Enable GoPay payment gateway test mode', WOOCOMMERCE_GOPAY_DOMAIN),
          'default' => 'yes'
        ),
        'enable_shipping_methods' => array(
          'title' => __('Enable shipping methods', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $supported_shipping_methods,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'enable_countries' => array(
          'title' => __('Enable countries', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $supported_countries,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'simplified_payment_method' => array(
          'title' => __('Payment method selection', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'checkbox',
          'label' => __('Enable simplified payment method selection', WOOCOMMERCE_GOPAY_DOMAIN),
          'description' => __('If enabled, customers cannot choose any specific payment method at the checkout but they have to select the payment method once the GoPay payment gateway is invoked.', WOOCOMMERCE_GOPAY_DOMAIN)
        ),
        'enable_gopay_payment_methods' => array(
          'title' => __('Enable GoPay payment methods', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $supported_payment_methods,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'enable_banks' => array(
          'title' => __('Enable banks', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $supported_banks,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'payment_retry' => array(
          'title' => __('Payment retry payment method', WOOCOMMERCE_GOPAY_DOMAIN),
          'type' => 'checkbox',
          'label' => __('Enable payment retry using the same payment metyhod', WOOCOMMERCE_GOPAY_DOMAIN),
          'description' => __('If enabled, payment retry of a failed payment will be done using the same payment method that was selected when customer was placing an order.', WOOCOMMERCE_GOPAY_DOMAIN)
        ),
      );

    }

    /**
     * Is the gateway available based on the restrictions
     * of countries and shipping methods.
     * 
     * @since  1.0.0
     * @return bool
     */
    public function is_available() {
      if(!empty(WC()->customer)){
        // Check countries
        $shipping_country = WC()->cart->get_customer()->get_shipping_country();
        $billing_country = WC()->cart->get_customer()->get_billing_country();

        if (empty($this->enable_countries) || (empty($shipping_country) && empty($billing_country))) {
          return false;
        }

        if(!in_array($shipping_country, $this->enable_countries) && !in_array($billing_country, $this->enable_countries)) {
          return false;
        }
        // end

        // Check shipping methods
        if (is_page(wc_get_page_id('checkout')) && !empty(get_query_var('order-pay'))) {
          $order_id = absint(get_query_var('order-pay'));
          $order = wc_get_order($order_id);

          $shippings = $order->get_items('shipping');
          foreach($shippings as $shipping){
            var_dump(strval($shipping->get_method_title()));
            $shipping_method = $shipping->get_method_title();
          }
          $chosen_shipping_method = WC()->session->get('chosen_shipping_methods');
          var_dump(strval($chosen_shipping_method));
        }
      }

      return parent::is_available();
    }

    /**
     * Process payment.
     * 
     * @since  1.0.0
     * @param  int $order_id Order ID.
     */
    public function process_payment($order_id) {
      $order = wc_get_order($order_id);

      if ($order->get_total() > 0) {
        $this->gopay_payment_processing($order);
      }
    }

    /**
     * GoPay gateway process payment.
     * 
     * @since  1.0.0
     * @param  int $order_id Order ID.
     */
    private function gopay_payment_processing($order) {
      $total = floatval($order->get_total());
      var_dump($total);
    }

    /**
     * Message order received page.
     * @since  1.0.0
     */
    public function thankyou_page() {
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
    public function complete_order_status($status, $order_id, $order = false) {
      if ($order && 'wc_gopay_gateway' === $order->get_payment_method()) {
        return 'completed';
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
  function add_woocommerce_gopay_gateway($methods) {
    $methods[] = 'Woocommerce_Gopay_Gateway';
    return $methods;
  }
  add_filter('woocommerce_payment_gateways', 'add_woocommerce_gopay_gateway');

}