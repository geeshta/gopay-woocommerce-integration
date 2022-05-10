<?php

/**
 * WooCommerce GoPay gateway subscriptions.
 * Deal with woocommerce subscriptions plugin.
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      https://www.argo22.com
 * @copyright 2022 argo22
 * @since     1.0.0
 */

class Woocommerce_Gopay_Subscriptions
{

	/**
	 * Constructor for the gateway
	 *
	 * @since  1.0.0
	 */
	public static function subscriptions_actions_filters()
	{
		// Disable multiple checkout option
		add_action( 'plugins_loaded', array( 'Woocommerce_Gopay_Subscriptions',
			'disable_subscriptions_multiple_purchase' ) );
		add_action(
			'update_option_woocommerce_subscriptions_multiple_purchase',
			array( 'Woocommerce_Gopay_Subscriptions', 'disable_subscriptions_multiple_purchase' )
		);
		add_action(
			'add_option_woocommerce_subscriptions_multiple_purchase',
			array( 'Woocommerce_Gopay_Subscriptions', 'disable_subscriptions_multiple_purchase' )
		);

		// When a subscription is added to the cart check if any other product/subscriptions was included
		add_filter(
			'woocommerce_add_to_cart_validation',
			array( 'Woocommerce_Gopay_Subscriptions', 'subscriptions_check_add_to_cart' ),
			9,
			3
		);
		add_filter(
			'woocommerce_update_cart_validation',
			array( 'Woocommerce_Gopay_Subscriptions', 'subscriptions_check_cart_update' ),
			10,
			4
		);
		add_filter(
			'woocommerce_cart_redirect_after_error',
			array( 'Woocommerce_Gopay_Subscriptions', 'redirect_to_shop' )
		);

		// Process/Cancel subscription payments
		add_action(
			'woocommerce_scheduled_subscription_payment_' . WOOCOMMERCE_GOPAY_ID,
			array( 'Woocommerce_Gopay_Subscriptions', 'process_subscription_payment' ),
			5,
			2
		);
		add_action(
			'woocommerce_subscription_status_updated',
			array( 'Woocommerce_Gopay_Subscriptions', 'cancel_subscription_payment' ),
			4,
			3
		);
	}

	/**
	 * When a subscription is added to the cart then check cart add
	 * Check if only one subscription was added to the cart
	 * without any other products/subscriptions
	 *
	 * @param bool $valid
	 * @param int  $product_id product id
	 * @param int  $quantity   quantity of the item
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function subscriptions_check_add_to_cart( bool $valid, int $product_id, int $quantity ): bool
	{
		remove_filter(
			'woocommerce_add_to_cart_validation',
			array( 'WC_Subscriptions_Cart_Validator', 'maybe_empty_cart' )
		);

		if ( WC()->cart->get_cart_contents_count() != 0 &&
			( WC_Subscriptions_Product::is_subscription( end( WC()->cart->cart_contents )['product_id'] ) ||
				WC_Subscriptions_Product::is_subscription( $product_id ) )
		) {
			wc_add_notice( __(
				'Products and subscriptions can not be purchased at the same time and ' .
				'only one subscription per checkout is possible.',
				WOOCOMMERCE_GOPAY_DOMAIN
			), 'notice' );
			return false;
		}

		return true;
	}

	/**
	 * When a subscription is added to the cart then check cart update
	 * Check if only one subscription was added to the cart
	 *
	 * @param bool   $passed
	 * @param string $cart_item_key
	 * @param array  $values   values of the item
	 * @param int    $quantity quantity of the item
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function subscriptions_check_cart_update( bool   $passed,
	                                                 string $cart_item_key, array $values, int $quantity ): bool
	{
		if ( $quantity > 1 ) {
			if ( WC_Subscriptions_Product::is_subscription( $values['product_id'] ) ) {
				wc_add_notice( __(
					'Only one recurring payment/subscription per checkout is possible',
					WOOCOMMERCE_GOPAY_DOMAIN
				), 'notice' );
				return false;
			}
		}

		return true;
	}

	/**
	 * Redirect to the shop page if subscription was included
	 * into the cart with any other product/subscription
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public static function redirect_to_shop(): string
	{
		return get_permalink( wc_get_page_id( 'shop' ) );
	}

	/**
	 * Get subscription data from order
	 *
	 * @param object $order
	 *
	 * @return array|false|WC_Subscription
	 * @since  1.0.0
	 */
	public static function get_subscription_data( $order )
	{
		$is_subscriptions_plugin_active = in_array(
			'woocommerce-subscriptions/woocommerce-subscriptions.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		);
		if ( $is_subscriptions_plugin_active ) {
			$order_id           = $order->get_id();
			$is_subscription    = ( wcs_is_subscription( $order_id ) ||
				wcs_order_contains_subscription( $order_id ) ||
				wcs_order_contains_renewal( $order_id ) );

			if ( $is_subscription ) {
				$subscription       = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'any' ) );
				$subscription_id    = json_decode( end( $subscription ) )->id;

				return wcs_get_subscription( $subscription_id );
			}
		}

		return array();
	}

	/**
	 * Get parent order
	 *
	 * @param object $order
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_parent_order( $order ): ?WC_Order
	{
		$subscription = ( new Woocommerce_Gopay_Subscriptions )->get_subscription_data( $order );

		if ( !empty( $subscription ) ) {
			return $subscription->get_parent();
		}

		return array();
	}

	/**
	 * Is subscription present in the cart
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function cart_contains_subscription(): bool
	{
		foreach ( WC()->cart->get_cart() as $item ) {
			$product = wc_get_product( $item['product_id'] );
			if ( class_exists( 'WC_Subscriptions_Product' ) &&
				WC_Subscriptions_Product::is_subscription( $product ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Process subscription payment when triggered
	 * by the action on the anniversary
	 * of the original purchase or when triggered
	 * off-schedule by 3rd party code or
	 * store manager actions
	 *
	 * @param float  $renewal_total
	 * @param object $renewal_order
	 *
	 * @since  1.0.0
	 */
	public static function process_subscription_payment( float $renewal_total, $renewal_order )
	{
		$renewal_order->update_status( 'pending' );
		$response = Woocommerce_Gopay_API::create_recurrence( $renewal_order );

		if ( $response->statusCode == 200 ) {
			$renewal_order->update_meta_data( 'GoPay_Transaction_id', $response->json['id'] );
		} else {
			$renewal_order->update_status( 'failed' );
		}
		$renewal_order->save();

		$log = array(
			'order_id'          => $renewal_order->get_id(),
			'transaction_id'    => $response->statusCode == 200 ? $response->json['id'] : 0,
			'message'           => $response->statusCode == 200 ?
								'Recurrence of previously created payment executed' : 'Recurring payment error',
			'log_level'         => $response->statusCode == 200 ? 'INFO' : 'ERROR',
			'log'               => $response,
		);
		Woocommerce_Gopay_Log::insert_log( $log );
	}

	/**
	 * Cancel subscription payment when
	 * the status is changed
	 *
	 * @param object $subscription
	 * @param string $new_status
	 * @param string $old_status
	 *
	 * @since  1.0.0
	 */
	public static function cancel_subscription_payment( $subscription, string $new_status, string $old_status )
	{
		$status_to_cancel = array( 'cancelled', 'expired', 'pending-cancel' );
		$gopay_status     = Woocommerce_Gopay_API::get_status( $subscription->get_parent()->get_id() );
		if ( in_array( $new_status, $status_to_cancel ) &&
			$gopay_status->json['recurrence']['recurrence_state'] == 'STARTED' ) {
			$response   = Woocommerce_Gopay_API::cancel_recurrence( $subscription );
			$status     = Woocommerce_Gopay_API::get_status( $subscription->get_parent()->get_id() );

			$order = $subscription->order;
			if ( $response->statusCode == 200 ) {
				$order->set_status( 'cancelled' );
				$order->save();
			} else {
				$subscription->set_status( 'on-hold' );
				$subscription->save();
			}

			$log = array(
				'order_id'          => $order->get_id(),
				'transaction_id'    => $response->statusCode == 200 ? $response->json['id'] :
									( $status->statusCode == 200 ? $status->json['id'] : 0 ),
				'message'           => $response->statusCode == 200 ?
									'Recurrence of previously created payment cancelled' : 'Cancel recurrence error',
				'log_level'         => $response->statusCode == 200 ? 'INFO' : 'ERROR',
				'log'               => $response->statusCode != 200 ? $response : $status,
			);
			Woocommerce_Gopay_Log::insert_log( $log );
		}
	}

	/**
	 * Disable woocommerce subscriptions multiple purchase option
	 *
	 * @since  1.0.0
	 */
	public static function disable_subscriptions_multiple_purchase()
	{
		if ( !get_option( WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase' ) ||
			get_option( WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase' ) == 'yes' ) {
			add_action( 'admin_notices', array( 'Woocommerce_Gopay_Subscriptions', 'admin_notice_error' ) );
			update_option( WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase', 'no' );
		}
	}

	/**
	 * Show an error message about mixed checkout option was disabled
	 *
	 * @since  1.0.0
	 */
	public static function admin_notice_error()
	{
		$message = __(
			'WooCommerce GoPay gateway plugin requires WooCommerce Subscriptions Mixed Checkout option to be disabled.',
			WOOCOMMERCE_GOPAY_DOMAIN
		);
		echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
	}
}
