<?php

/**
 * WooCommerce GoPay gateway
 * 
 * Initialize the payment gateway between WooCommerce and GoPay
 * 
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      www.argo22.com
 * @copyright 2022 argo22
 */

add_action('plugins_loaded', 'init_woocommerce_gopay_gateway');

function init_woocommerce_gopay_gateway(){

  class WC_GoPay_Payment_Gateway extends WC_Payment_Gateway {

    public $domain;

    /**
     * Constructor for the gateway
     *
     * @access public
     */
    public function __construct(){

      $this->id = 'wc_gopay_gateway';
      $this->domain = 'gopay_payment';
      $this->method_title = 'GoPay payment gateway';
      $this->has_fields = false;
      $this->method_description = __('Take payments via GoPay payment gateway.', $this->domain);

      $this->init_form_fields();
      $this->init_settings();
      $this->title = $this->settings['title'];
      $this->description = $this->settings['description'];
      $this->icon = $this->settings['icon'];
      $this->test = $this->settings['test'];

      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Gateway Settings Form Fields in Admin.
     *  
     * @access public
     */
    public function init_form_fields(){

      $allowed_countries = [];
      $allowed_shipping_methods = [];

      if (is_admin()) {
        $allowed_countries = WC()->countries->get_allowed_countries();

        foreach (WC()->shipping->load_shipping_methods() as $shipping_method) {
          $allowed_shipping_methods[$shipping_method->id] = $shipping_method->get_method_title();
        }
      }

      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', $this->domain),
          'type' => 'checkbox',
          'label' => __('Enable GoPay payment gateway', $this->domain),
          'default' => 'yes'
        ),
        'title' => array(
          'title' => __('Title', $this->domain),
          'type' => 'text',
          'description' => __('Name of the payment method that is displayed at the checkout', $this->domain),
          'default' => __('GoPay', $this->domain),
          'css' => 'width: 500px;'
        ),
        'description' => array(
          'title' => __('Description', $this->domain),
          'type' => 'textarea',
          'description' => __('Description of the payment method that is displayed at the checkout', $this->domain),
          'default' => __( 'Payment via GoPay gateway', $this->domain),
          'css' => 'width: 500px; min-height: 100px;'
        ),
        'goid' => array(
          'title' => __('GoId', $this->domain),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'client_id' => array(
          'title' => __('Client Id', $this->domain),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'client_secret' => array(
          'title' => __('Client secret', $this->domain),
          'type' => 'text',
          'css' => 'width: 500px;'
        ),
        'test' => array(
          'title' => __('Test mode', $this->domain),
          'type' => 'checkbox',
          'label' => __('Enable GoPay payment gateway test mode', $this->domain),
          'default' => 'yes'
        ),
        'enable_shipping_methods' => array(
          'title' => __('Enable shipping methods', $this->domain),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $allowed_shipping_methods,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'enable_countries' => array(
          'title' => __('Enable countries', $this->domain),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => $allowed_countries,
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'simplified_payment_method' => array(
          'title' => __('Payment method selection', $this->domain),
          'type' => 'checkbox',
          'label' => __('Enable simplified payment method selection', $this->domain),
          'description' => __('If enabled, customers cannot choose any specific payment method at the checkout but they have to select the payment method once the GoPay payment gateway is invoked.', $this->domain)
        ),
        'enable_gopay_payment_methods' => array(
          'title' => __('Enable GoPay payment methods', $this->domain),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => [], // change it
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'enable_banks' => array(
          'title' => __('Enable banks', $this->domain),
          'type' => 'multiselect',
          'class' => 'chosen_select',
          'options' => [], // change it
          'desc_tip' => true,
          'css' => 'width: 500px; min-height: 50px;'
        ),
        'payment_retry' => array(
          'title' => __('Payment retry payment method', $this->domain),
          'type' => 'checkbox',
          'label' => __('Enable payment retry using the same payment metyhod', $this->domain),
          'description' => __('If enabled, payment retry of a failed payment will be done using the same payment method that was selected when customer was placing an order.', $this->domain)
        ),
      );

    }

  }

  /**
   *  Add the Gateway to WooCommerce
   * 
   * @param array $methods
   * @return array
   */
  function add_wc_gopay_payment_gateway($methods) {
    $methods[] = 'WC_GoPay_Payment_Gateway';
    return $methods;
  }
  add_filter('woocommerce_payment_gateways', 'add_wc_gopay_payment_gateway');

}