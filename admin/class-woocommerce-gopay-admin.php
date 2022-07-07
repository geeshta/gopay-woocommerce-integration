<?php
/**
 * WooCommerce GoPay admin menu
 * Initialize plugin admin menu
 *
 * @package   WooCommerce GoPay gateway
 * @author    GoPay
 * @link      https://www.gopay.com/
 * @copyright 2022 GoPay
 * @since     1.0.0
 */

/**
 * Admin menu
 *
 * @since  1.0.0
 */
class Woocommerce_Gopay_Admin_Menu {


	/**
	 * Constructor for the plugin admin menu
	 *
	 * @since 1.0.0
	 */
	public static function create_menu_actions() {
		add_action( 'admin_menu', array( 'Woocommerce_Gopay_Admin_Menu', 'create_menu' ) );
		add_action( 'admin_enqueue_scripts', array( 'Woocommerce_Gopay_Admin_Menu', 'admin_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( 'Woocommerce_Gopay_Admin_Menu', 'admin_enqueue_scripts' ) );
	}

	/**
	 * Admin enqueue styles
	 *
	 * @since 1.0.0
	 */
	public static function admin_enqueue_styles() {
		wp_enqueue_style(
			WOOCOMMERCE_GOPAY_DOMAIN . '-menu-styles',
			WOOCOMMERCE_GOPAY_URL . 'admin/css/menu.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Admin enqueue scripts
	 *
	 * @since 1.0.0
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_script(
			WOOCOMMERCE_GOPAY_DOMAIN . '-menu-scripts',
			WOOCOMMERCE_GOPAY_URL . 'admin/js/menu.js',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Create Woocommerce GoPay gateway admin page
	 *
	 * @since 1.0.0
	 */
	public static function create_menu() {
		if ( ! defined( 'WOOCOMMERCE_GOPAY_ADMIN_MENU' ) ) {
			add_menu_page(
				__( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ),
				__( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ),
				'manage_woocommerce',
				'woocommerce-gopay-menu'
			);

			add_submenu_page(
				'woocommerce-gopay-menu',
				__( 'Woocommerce GoPay info', 'woocommerce-gopay' ),
				__( 'Info', 'woocommerce-gopay' ),
				'manage_woocommerce',
				'woocommerce-gopay-menu',
				array( 'Woocommerce_Gopay_Admin_Menu', 'load_admin_info_page' )
			);

			add_submenu_page(
				'woocommerce-gopay-menu',
				__( 'Woocommerce GoPay log', 'woocommerce-gopay' ),
				__( 'Log', 'woocommerce-gopay' ),
				'manage_woocommerce',
				'woocommerce-gopay-menu-log',
				array( 'Woocommerce_Gopay_Admin_Menu', 'load_admin_log_page' )
			);

			define( 'WOOCOMMERCE_GOPAY_ADMIN_MENU', true );
		}
	}

	/**
	 * Load admin page
	 *
	 * @since 1.0.0
	 */
	public static function load_admin_info_page() {
		include_once 'views/admin.php';
	}

	/**
	 * Load admin page
	 *
	 * @since 1.0.0
	 */
	public static function load_admin_log_page() {
		include_once 'views/log.php';
	}
}
