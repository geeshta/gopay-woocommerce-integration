<?php
/**
 * Plugin admin menu
 *
 * @package   WooCommerce GoPay gateway
 * @author    GoPay
 * @link      https://www.gopay.com/
 * @copyright 2022 GoPay
 * @since     1.0.0
 */

$plugin_data  = get_plugin_data( WOOCOMMERCE_GOPAY_FULLPATH );
$settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gopay_gateway' );
?>


<div class="wrap">
	<div class="woocommerce-gopay-menu">
		<h1><?php echo wp_kses_post( __( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ) ); ?></h1>
	</div>

	<div class="woocommerce-gopay-menu">
		<table>
			<tr>
				<th><?php echo wp_kses_post( __( 'Plugin Name', 'woocommerce-gopay' ) ); ?></th>
				<th><?php echo wp_kses_post( __( 'Version', 'woocommerce-gopay' ) ); ?></th>
				<th><?php echo wp_kses_post( __( 'Description', 'woocommerce-gopay' ) ); ?></th>
				<th><?php echo wp_kses_post( __( 'Author', 'woocommerce-gopay' ) ); ?></th>
				<th><?php echo wp_kses_post( __( 'Settings', 'woocommerce-gopay' ) ); ?></th>
			</tr>
			<tr>
				<td><a href="https://github.com/argo22packages/gopay-woocommerce-integration">
                    <?php echo wp_kses_post( __( 'GoPay gateway', 'woocommerce-gopay' ) ); ?></a></td>
				<td><?php echo wp_kses_post( __( '1.0', 'woocommerce-gopay' ) ); ?></td>
				<td><?php echo wp_kses_post( __( 'WooCommerce and GoPay payment gateway integration', 'woocommerce-gopay' ) ); ?></td>
				<td><a href="https://www.gopay.com/"><?php echo wp_kses_post( __( 'GoPay', 'woocommerce-gopay' ) ); ?></a></td>
				<?php
				echo wp_kses_post(
					'<td><a href="' . $settings_url . '">' . __(
						'Settings',
						'woocommerce-gopay'
					) . '</a></td>'
				)
				?>
			</tr>
		</table>
	</div>

</div>
