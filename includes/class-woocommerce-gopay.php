<?php

/**
 * WooCommerce GoPay gateway
 * Initialize the payment gateway between WooCommerce and GoPay
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      https://www.argo22.com
 * @copyright 2022 argo22
 * @since     1.0.0
 */

add_action( 'plugins_loaded', 'init_woocommerce_gopay_gateway' );

function init_woocommerce_gopay_gateway() {
	class Woocommerce_Gopay_Gateway extends WC_Payment_Gateway {

		/**
		 * Instance of the class.
		 *
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor for the gateway
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			 $this->id                = WOOCOMMERCE_GOPAY_ID;
			$this->icon               = apply_filters(
				'woocommerce_gopay_icon',
				WOOCOMMERCE_GOPAY_URL . 'includes/assets/images/gopay.png'
			);
			$this->has_fields         = false;
			$this->method_title       = __(
				'GoPay payment gateway',
				'woocommerce-gopay'
			);
			$this->method_description = __(
				'Take payments via GoPay payment gateway.',
				'woocommerce-gopay'
			);

			$this->enable_currencies          = Woocommerce_Gopay_Options::supported_currencies();
			$this->supported_languages        = Woocommerce_Gopay_Options::supported_languages();
			$this->supported_countries        = Woocommerce_Gopay_Options::supported_countries();
			$this->supported_shipping_methods = Woocommerce_Gopay_Options::supported_shipping_methods();
			$this->supported_payment_methods  = Woocommerce_Gopay_Options::supported_payment_methods();
			$this->supported_banks            = Woocommerce_Gopay_Options::supported_banks();
			$this->iso2_to_iso3               = Woocommerce_Gopay_Options::iso2_to_iso3();

			$this->init_form_fields();
			$this->init_settings();

			$this->title         = $this->get_option( 'title' );
			$this->description   = $this->get_option( 'description' );
			$this->goid          = $this->get_option( 'goid' );
			$this->client_id     = $this->get_option( 'client_id' );
			$this->client_secret = $this->get_option( 'client_secret' );
			$this->test          = ! $this->get_option( 'test' );
			$this->instructions  = $this->get_option( 'instructions' );

			$this->simplified_payment_method    = $this->get_option( 'simplified_payment_method' ) == 'yes';
			$this->payment_retry                = $this->get_option( 'payment_retry' ) == 'yes';
			$this->enable_countries             = $this->get_option( 'enable_countries', array() );
			$this->enable_gopay_payment_methods = $this->get_option( 'enable_gopay_payment_methods', array() );
			$this->enable_banks                 = $this->get_option( 'enable_banks', array() );
			$this->enable_shipping_methods      = $this->get_option( 'enable_shipping_methods', array() );

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
				'pre-orders',
			);

			add_action( 'admin_init', array( $this, 'update_payment_methods' ), 1 );
			add_action( 'update_payment_methods_and_banks', array( $this, 'check_enabled_on_GoPay' ), 1 );
			add_action( 'template_redirect', array( $this, 'check_status_gopay_redirect' ) );
			add_action( 'woocommerce_create_refund', array( $this, 'calculate_refund_amount' ), 10, 2 );
			add_action(
				'woocommerce_update_options_payment_gateways_' . $this->id,
				array(
					$this,
					'process_admin_options',
				)
			);
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			// add_filter( 'woocommerce_currencies', array( 'Woocommerce_Gopay_Options', 'supported_currencies' ) );
			add_filter(
				'woocommerce_payment_complete_order_status',
				array( $this, 'complete_order_status' ),
				10,
				3
			);
			add_filter(
				'woocommerce_thankyou_order_received_text',
				array( $this, 'thankyou_page' ),
				20,
				2
			);

			// Load Woocommerce GoPay gateway admin page
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				Woocommerce_Gopay_Admin_Menu::create_menu_actions();
			}

			// Check if WooCommerce Subscriptions is active
			if ( check_is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
				Woocommerce_Gopay_Subscriptions::subscriptions_actions_filters();
			}
		}

		/**
		 * Get Woocommerce_Gopay_Gateway instance if it exists
		 * or create a new one.
		 *
		 * @return Woocommerce_Gopay_Gateway|null Instance
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Load plugin textdomain
		 *
		 * @since 1.0.0
		 */
		public static function load_textdomain() {
			load_plugin_textdomain(
				WOOCOMMERCE_GOPAY_DOMAIN,
				false,
				WOOCOMMERCE_GOPAY_BASENAME_DIR . '/languages'
			);
		}

		/**
		 * Update payment methods and banks
		 *
		 * @since 1.0.0
		 */
		public function update_payment_methods() {
			if ( empty( $this->settings['goid'] ) ) {
				return;
			}

			if ( empty( $this->get_option( 'option_gopay_payment_methods' ) ) ||
				empty( $this->get_option( 'option_gopay_banks' ) ) ) {
				wp_schedule_single_event( time(), 'update_payment_methods_and_banks' );
			}

			if ( filter_input( INPUT_GET, 'page' ) == 'wc-settings' &&
				filter_input( INPUT_GET, 'section' ) == 'wc_gopay_gateway' ) {
				wp_schedule_single_event( time(), 'update_payment_methods_and_banks' );
			}
		}

		/**
		 * Check payment methods and banks that
		 * are enabled on GoPay account.
		 *
		 * @since 1.0.0
		 */
		function check_enabled_on_GoPay() {
			 $payment_methods = array();
			$banks            = array();
			foreach ( Woocommerce_Gopay_Options::supported_currencies() as $currency => $value ) {
				$supported       = Woocommerce_Gopay_API::check_enabled_on_GoPay( $currency );
				$payment_methods = $payment_methods + $supported[0];
				$banks           = $banks + $supported[1];

				$this->update_option( 'gopay_payment_methods_' . $currency, $supported[0] );
				$this->update_option( 'gopay_banks_' . $currency, $supported[1] );
			}

			if ( ! empty( $payment_methods ) ) {
				$this->update_option( 'option_gopay_payment_methods', $payment_methods );
			}
			if ( ! empty( $banks ) ) {
				if ( array_key_exists( 'OTHERS', $banks ) ) {
					// Send 'Others' to the end
					$other = $banks['OTHERS'];
					unset( $banks['OTHERS'] );
					$banks['OTHERS'] = $other;
				}

				$this->update_option( 'option_gopay_banks', $banks );
			}
		}

		/**
		 * Extract payment methods/banks from
		 * list of supported
		 *
		 * @param array $supported either payment methods or banks
		 * @return array
		 */
		private function extract_payment_methods( $supported ): array {
			foreach ( $supported as $key => $value ) {
				$supported[ $key ] = $value['label'] .
					( array_key_exists( 'country', $value ) ? ' ' . $value['country'] : '' );
			}

			return $supported;
		}

		/**
		 * Gateway Settings Form Fields in Admin.
		 *
		 * @since  1.0.0
		 */
		public function init_form_fields() {
			$this->init_settings();
			if ( empty( $this->settings['goid'] ) ||
				empty( $this->settings['client_id'] ) ||
				empty( $this->settings['client_secret'] ) ) {
				$this->update_option( 'enabled', 'no' );
			}

			$this->form_fields = array(
				'enabled'       => array(
					'title'   => __( 'Enable/Disable', 'woocommerce-gopay' ),
					'type'    => 'checkbox',
					'label'   => __(
						'Inform goid, client id and secret to enable GoPay payment gateway and load the other options',
						'woocommerce-gopay'
					),
					'css'     => 'display: none;',
					'default' => 'no',
				),
				'goid'          => array(
					'title' => __( 'GoId', 'woocommerce-gopay' ),
					'type'  => 'text',
					'css'   => 'width: 500px;',
				),
				'client_id'     => array(
					'title' => __( 'Client Id', 'woocommerce-gopay' ),
					'type'  => 'text',
					'css'   => 'width: 500px;',
				),
				'client_secret' => array(
					'title' => __( 'Client secret', 'woocommerce-gopay' ),
					'type'  => 'text',
					'css'   => 'width: 500px;',
				),
			);

			if ( ! empty( $this->settings['goid'] ) &&
				! empty( $this->settings['client_id'] ) &&
				! empty( $this->settings['client_secret'] ) ) {
				// Set default parameters
				if ( empty( $this->settings['enabled'] ) ) {
					$this->update_option( 'enabled', 'yes' );
				}
				if ( empty( $this->settings['title'] ) ) {
					$this->update_option( 'title', 'GoPay' );
				}
				if ( empty( $this->settings['description'] ) ) {
					$this->update_option( 'description', 'Payment via GoPay gateway' );
				}
				if ( empty( $this->settings['test'] ) ) {
					$this->update_option( 'test', 'yes' );
				}
				// end

				$this->form_fields = array(
					'enabled'                          => array(
						'title'   => __( 'Enable/Disable', 'woocommerce-gopay' ),
						'type'    => 'checkbox',
						'label'   => __(
							'Enable GoPay payment gateway',
							'woocommerce-gopay'
						),
						'default' => 'yes',
					),
					'title'                            => array(
						'title'       => __( 'Title', 'woocommerce-gopay' ),
						'type'        => 'text',
						'description' => __(
							'Name of the payment method that is displayed at the checkout',
							'woocommerce-gopay'
						),
						'default'     => __( 'GoPay', 'woocommerce-gopay' ),
						'css'         => 'width: 500px;',
					),
					'description'                      => array(
						'title'       => __( 'Description', 'woocommerce-gopay' ),
						'type'        => 'textarea',
						'description' => __(
							'Description of the payment method that is displayed at the checkout',
							'woocommerce-gopay'
						),
						'default'     => __(
							'Payment via GoPay gateway',
							'woocommerce-gopay'
						),
						'css'         => 'width: 500px; min-height: 100px;',
					),
					'goid'                             => array(
						'title' => __( 'GoId', 'woocommerce-gopay' ),
						'type'  => 'text',
						'css'   => 'width: 500px;',
					),
					'client_id'                        => array(
						'title' => __( 'Client Id', 'woocommerce-gopay' ),
						'type'  => 'text',
						'css'   => 'width: 500px;',
					),
					'client_secret'                    => array(
						'title' => __( 'Client secret', 'woocommerce-gopay' ),
						'type'  => 'text',
						'css'   => 'width: 500px;',
					),
					'test'                             => array(
						'title'   => __( 'Test mode', 'woocommerce-gopay' ),
						'type'    => 'checkbox',
						'label'   => __(
							'Enable GoPay payment gateway test mode',
							'woocommerce-gopay'
						),
						'default' => 'yes',
					),
					'default_language_gopay_interface' => array(
						'title'    => __( 'Default language of the GoPay interface', 'woocommerce-gopay' ),
						'type'     => 'select',
						'class'    => 'chosen_select',
						'options'  => $this->supported_languages,
						'desc_tip' => true,
						'default'  => 'EN',
						'css'      => 'width: 500px; min-height: 50px;',
					),
					'enable_shipping_methods'          => array(
						'title'    => __( 'Enable shipping methods', 'woocommerce-gopay' ),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->supported_shipping_methods,
						'desc_tip' => true,
						'css'      => 'width: 500px; min-height: 50px;',
					),
					'enable_countries'                 => array(
						'title'    => __( 'Enable countries', 'woocommerce-gopay' ),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->supported_countries,
						'desc_tip' => true,
						'css'      => 'width: 500px; min-height: 50px;',
					),
					'simplified_payment_method'        => array(
						'title'       => __( 'Payment method selection', 'woocommerce-gopay' ),
						'type'        => 'checkbox',
						'label'       => __(
							'Enable simplified payment method selection',
							'woocommerce-gopay'
						),
						'description' => __(
							'If enabled, customers cannot choose any specific payment method at the checkout' .
							' but they have to select the payment method once the GoPay payment gateway is invoked.',
							'woocommerce-gopay'
						),
					),
					'enable_gopay_payment_methods'     => array(
						'title'    => __(
							'Enable GoPay payment methods',
							'woocommerce-gopay'
						),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->extract_payment_methods( $this->supported_payment_methods ),
						'desc_tip' => true,
						'css'      => 'width: 500px; min-height: 50px;',
					),
					'enable_banks'                     => array(
						'title'    => __( 'Enable banks', 'woocommerce-gopay' ),
						'type'     => 'multiselect',
						'class'    => 'chosen_select',
						'options'  => $this->extract_payment_methods( $this->supported_banks ),
						'desc_tip' => true,
						'css'      => 'width: 500px; min-height: 50px;',
					),
					'payment_retry'                    => array(
						'title'       => __(
							'Payment retry payment method',
							'woocommerce-gopay'
						),
						'type'        => 'checkbox',
						'label'       => __(
							'Enable payment retry using the same payment method',
							'woocommerce-gopay'
						),
						'description' => __(
							'If enabled, payment retry of a failed payment will be done using the same payment method' .
							' that was selected when customer was placing an order.',
							'woocommerce-gopay'
						),
					),
				);
			}
		}

		/**
		 * Is the gateway available based on the restrictions
		 * of countries and shipping methods.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_available(): bool {
			if ( ! empty( WC()->customer ) ) {
				// Check countries
				$billing_country = WC()
					->cart->get_customer()
					->get_billing_country();

				if ( empty( $this->enable_countries ) || empty( $billing_country ) ||
					! in_array( $billing_country, (array) $this->enable_countries ) ) {
					return false;
				}
				// end check countries

				// Check currency matches one of the supported currencies
				if ( ! get_woocommerce_currency() || ! array_key_exists(
					get_woocommerce_currency(),
					$this->enable_currencies
				) ) {
					return false;
				}
				// end check currency

				// Check if all products are either virtual or downloadable
				$all_virtual_downloadable = true;
				foreach ( WC()->cart->get_cart() as $item ) {
					$product = wc_get_product( $item['product_id'] );
					if ( ! $product->is_virtual() && ! $product->is_downloadable() ) {
						$all_virtual_downloadable = false;
						break;
					}
				}

				if ( $all_virtual_downloadable ) {
					return parent::is_available();
				}
				// end check virtual or downloadable

				// Check shipping methods
				if ( is_page( wc_get_page_id( 'checkout' ) ) &&
					! empty( get_query_var( 'order-pay' ) )
				) {
					$order_id = absint( get_query_var( 'order-pay' ) );
					$order    = wc_get_order( $order_id );

					$items_shipping = $order->get_items( 'shipping' );
					foreach ( $items_shipping as $item_shipping ) {
						if ( ! in_array(
							$item_shipping->get_method_title(),
							(array) $this->enable_shipping_methods
						)
						) {
							return false;
						}
					}
				} else {
					$chosen_shipping_methods = array();
					foreach ( WC()->session->get( 'chosen_shipping_methods' ) as $key => $value ) {
						if ( ! is_null( $value ) ) {
							$chosen_shipping_methods[ $key ] = explode( ':', $value )[0];
						}
					}

					if ( empty( $chosen_shipping_methods ) ||
						array_diff( $chosen_shipping_methods, (array) $this->enable_shipping_methods )
					) {
						return false;
					}
				}
				// end check shipping methods
			}

			return parent::is_available();
		}

		/**
		 * Extract banks by country code
		 *
		 * @param string $country
		 * @since  1.0.0
		 */
		public function extract_banks_by_country() {
			$supported_banks = array();
			if ( ! empty( WC()->customer->get_billing_country() ) ) {

				$country = WC()->customer->get_billing_country();
				foreach ( Woocommerce_Gopay_Options::supported_banks() as $swift => $value ) {
					if ( $country == $value['country'] || $value['country'] == '' ) {
						$supported_banks[ $swift ] = $value;
					}
				}
			}

			return $supported_banks;
		}

		/**
		 * Payment fields.
		 *
		 * @since  1.0.0
		 */
		public function payment_fields() {
			echo wpautop( wptexturize( $this->description ) );

			$enabled_payment_methods = '';
			$checked                 = 'checked="checked"';
			$payment_retry           = ( $this->payment_retry &&
				is_page( wc_get_page_id( 'checkout' ) ) &&
				! empty( get_query_var( 'order-pay' ) ) );
			if ( ! $this->simplified_payment_method && ! $payment_retry ) {
				// Only supported by the currency
				$supported_payment_methods = $this->get_option(
					'gopay_payment_methods_' . get_woocommerce_currency(),
					array()
				);
				$supported_banks           = $this->get_option(
					'gopay_banks_' . get_woocommerce_currency(),
					array()
				);

				// All selected in the settings page
				$selected_payment_methods = $this->get_option( 'enable_gopay_payment_methods', array() );
				$selected_banks           = $this->get_option( 'enable_banks', array() );

				// Intersection of all selected and the supported by the currency
				$payment_methods = array_intersect_key(
					$supported_payment_methods,
					array_flip(
						$selected_payment_methods
					)
				);
				$banks           = array_intersect_key( $supported_banks, array_flip( $selected_banks ) );

				// Check if subscription - only card payment is enabled
				if ( Woocommerce_Gopay_Subscriptions::cart_contains_subscription() ) {
					if ( array_key_exists( 'PAYMENT_CARD', (array) $payment_methods ) ) {
						$payment_methods = array( 'PAYMENT_CARD' => $payment_methods['PAYMENT_CARD'] );
					} else {
						$payment_methods = array();
					}
				}

				$input =
					'
					<div class="payment_method_' . WOOCOMMERCE_GOPAY_ID . '_selection">
					<div>
					    <input class="payment_method_' . WOOCOMMERCE_GOPAY_ID .
						'_input" name="gopay_payment_method" type="radio" id="%s" value="%s" %s />
					    <span>%s</span>
					</div>
					<img src="%s" alt="ico" style="height: auto; width: auto; margin-left: auto;"/>
					</div>';

				foreach ( $payment_methods as $payment_method => $payment_method_label_image ) {
					if ( $payment_method == 'BANK_ACCOUNT' ) {
						foreach ( $banks as $bank => $bank_label_image ) {
							$span = __( $bank_label_image['label'], 'woocommerce-gopay' );
							$img  = array_key_exists( 'image', $bank_label_image ) ?
								$bank_label_image['image'] : '';

							$enabled_payment_methods .= sprintf(
								$input,
								$payment_method,
								$bank,
								$checked,
								$span,
								$img
							);
						}
						continue;
					}

					$span = __( $payment_method_label_image['label'], 'woocommerce-gopay' );
					$img  = array_key_exists( 'image', $payment_method_label_image ) ?
						$payment_method_label_image['image'] : '';

					$enabled_payment_methods .= sprintf(
						$input,
						$payment_method,
						$payment_method,
						$checked,
						$span,
						$img
					);

					$checked = '';
				}
			}

			echo $enabled_payment_methods;
		}

		/**
		 * Process payment.
		 *
		 * @param int $order_id Order ID.
		 *
		 * @since  1.0.0
		 */
		public function process_payment( $order_id ): array {
			$order = wc_get_order( $order_id );
			$order->set_status( 'pending' );
			$order->save();

			if ( ! $order->get_currency() || ! array_key_exists( $order->get_currency(), $this->enable_currencies ) ) {
				if ( ! wc_has_notice( __( 'Currency is not supported on GoPay', 'woocommerce-gopay' ), 'error' ) ) {
					wc_add_notice( __( 'Currency is not supported on GoPay', 'woocommerce-gopay' ), 'error' );
				}
				return array(
					'result'   => 'failed',
					'redirect' => wc_get_checkout_url(),
				);
			}

			// Check if total is equal to zero
			$subscription = Woocommerce_Gopay_Subscriptions::get_subscription_data( $order );
			if ( $order->get_total() == 0 ) {
				if ( empty( $subscription ) ) {
					foreach ( $order->get_items() as $item ) {
						$product = wc_get_product( $item['product_id'] );
						if ( ! $product->is_virtual() && ! $product->is_downloadable() ) {
							$order->set_status( 'processing' );
							break;
						}
					}

					if ( $order->get_status() != 'processing' ) {
						$order->set_status( 'completed' );
					}
					$order->save();
				}
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}

			$gopay_payment_method = filter_input( INPUT_POST, 'gopay_payment_method' );
			$is_retry             = $this->payment_retry &&
										is_page( wc_get_page_id( 'checkout' ) ) &&
										! empty( get_query_var( 'order-pay' ) );

			// Add GoPay payment method to order
			if ( $gopay_payment_method ) {
				if ( array_key_exists( $gopay_payment_method, Woocommerce_Gopay_Options::supported_banks() ) ) {
					$order->update_meta_data( '_GoPay_bank_swift', $gopay_payment_method );
					$order->update_meta_data( '_GoPay_payment_method', 'BANK_ACCOUNT' );
				} else {
					$order->update_meta_data( '_GoPay_payment_method', $gopay_payment_method );
				}
			}

			// GoPay API only considers cents
			// Rounding total to 2 decimals
			$order->set_total( wc_format_decimal( $order->get_total(), 2 ) );

			$response = Woocommerce_Gopay_API::create_payment(
				$gopay_payment_method,
				$order,
				! empty( $subscription ) ? $subscription->get_date( 'end' ) : '',
				$is_retry
			);

			if ( $response->statusCode != 200 ) {
				$log = array(
					'order_id'       => $order_id,
					'transaction_id' => 0,
					'message'        => 'Process payment error',
					'log_level'      => 'ERROR',
					'log'            => $response,
				);
				Woocommerce_Gopay_Log::insert_log( $log );
				if ( ! wc_has_notice(
					__(
						'Payment creation on GoPay not possible',
						'woocommerce-gopay'
					),
					'error'
				) ) {
					wc_add_notice(
						__(
							'Payment creation on GoPay not possible',
							'woocommerce-gopay'
						),
						'error'
					);
				}

				return array(
					'result'   => 'failed',
					'redirect' => wc_get_checkout_url(),
				);
			}

			// Add GoPay transaction id to order
			// $order->set_status('on-hold');
			$order->update_meta_data( 'GoPay_Transaction_id', $response->json['id'] );
			$order->save();

			// Save log
			$log = array(
				'order_id'       => $order_id,
				'transaction_id' => $response->json['id'],
				'message'        => 'Payment created',
				'log_level'      => 'INFO',
				'log'            => $response,
			);
			Woocommerce_Gopay_Log::insert_log( $log );

			return array(
				'result'   => 'success',
				'redirect' => $response->json['gw_url'],
			);
		}

		/**
		 * Process refund.
		 *
		 * @param int        $order_id ID.
		 * @param float|null $amount   amount.
		 * @param string     $reason   reason.
		 *
		 * @return boolean if succeeded, or a WP_Error object.
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ): bool {
			// GoPay API only considers cents
			// Rounding amount to be refunded to 2 decimals
			$amount         = wc_format_decimal( $amount, 2 );
			$transaction_id = get_post_meta( $order_id, 'GoPay_Transaction_id', true );
			$response       = Woocommerce_Gopay_API::refund_payment( $transaction_id, $amount * 100 );
			$status         = Woocommerce_Gopay_API::get_status( $order_id );

			$log = array(
				'order_id'       => $order_id,
				'transaction_id' => $transaction_id,
				'message'        => $status->statusCode == 200 ? ( $status->json['state'] == 'PARTIALLY_REFUNDED' ?
					'Payment partially refunded' : 'Payment refunded' ) : 'Payment refund executed',
				'log_level'      => 'INFO',
				'log'            => $status,
			);

			if ( $response->statusCode != 200 ) {
				$log['message']   = 'Process refund error';
				$log['log_level'] = 'ERROR';
				$log['log']       = $response;
				Woocommerce_Gopay_Log::insert_log( $log );

				return false;
			}
			Woocommerce_Gopay_Log::insert_log( $log );

			if ( $response->json['result'] == 'FINISHED' ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Calculate refund without rounding up/down.
		 *
		 * @param object $refund an object with refund info
		 * @param array  $args   refund's arguments
		 */
		public static function calculate_refund_amount( $refund, array $args ) {
			$amount = 0;
			if ( count( $args['line_items'] ) > 0 ) {
				foreach ( $args['line_items'] as $item_id => $item ) {
					$refund_total = $item['refund_total'];
					$refund_tax   = isset( $item['refund_tax'] ) ? array_sum( $item['refund_tax'] ) : 0;

					$amount += (float) $refund_total + (float) $refund_tax;
				}
			}

			$refund->set_amount( $amount );
			$refund->save();
		}

		/**
		 * Check Status of GoPay payment
		 *
		 * @since 1.0.0
		 */
		public function check_status_gopay_redirect() {
			 $gopay_api = filter_input( INPUT_GET, 'gopay-api' );
			$id         = filter_input( INPUT_GET, 'id' );
			if ( $gopay_api && $id ) {
				Woocommerce_Gopay_API::check_payment_status( $id );
			}
		}

		/**
		 * Message order received page.
		 *
		 * @since  1.0.0
		 */
		public function thankyou_page( $message, $order ) {
			 $message     = __( 'Thank you. Your order has been received.', 'woocommerce-gopay' );
			$subscription = Woocommerce_Gopay_Subscriptions::get_subscription_data( $order );
			if ( ! empty( $subscription ) && $order->get_total() == 0 ) {
				return $message . __(
					' Please pay for your subscription after the trial period.',
					'woocommerce-gopay'
				);
			}

			if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
				return $message . __(
					' However, we are still waiting for the confirmation or payment rejection.',
					'woocommerce-gopay'
				);
			}

			return $message;
		}

		/**
		 * Complete order status for orders.
		 *
		 * @param string         $status   Current order status.
		 * @param int            $order_id Order ID.
		 * @param WC_Order|false $order    Order object.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function complete_order_status( string $status, int $order_id, $order = false ): string {
			if ( $order && WOOCOMMERCE_GOPAY_ID === $order->get_payment_method() ) {
				return 'completed';
			}
			return $status;
		}

		/**
		 * Process admin options.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function process_admin_options(): bool {
			$saved = parent::process_admin_options();
			$this->init_form_fields();

			// Check payment methods and banks enabled on GoPay account
			if ( empty( $this->get_option( 'option_gopay_payment_methods', '' ) ) &&
				! empty( $this->get_option( 'goid', '' ) )
			) {
				$this->check_enabled_on_GoPay();
			}

			return $saved;
		}

		/**
		 * Enqueue styles
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style(
				WOOCOMMERCE_GOPAY_DOMAIN . '-payment-methods-styles',
				WOOCOMMERCE_GOPAY_URL . 'includes/assets/css/payment_methods.css'
			);
		}
	}

	/**
	 *  Add the Gateway to WooCommerce
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	function add_woocommerce_gopay_gateway( array $methods ): array {
		$methods[] = 'Woocommerce_Gopay_Gateway';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_woocommerce_gopay_gateway' );
}
