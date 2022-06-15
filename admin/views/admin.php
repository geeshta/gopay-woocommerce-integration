<?php
$plugin_data  = get_plugin_data( WOOCOMMERCE_GOPAY_FULLPATH );
$settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gopay_gateway' );
?>


<div class="wrap">
	<div class="woocommerce-gopay-menu">
		<h1><?php _e( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ); ?></h1>
	</div>

	<div class="woocommerce-gopay-menu">
		<table>
			<tr>
				<th><?php _e( 'Plugin Name', 'woocommerce-gopay' ); ?></th>
				<th><?php _e( 'Version', 'woocommerce-gopay' ); ?></th>
				<th><?php _e( 'Description', 'woocommerce-gopay' ); ?></th>
				<th><?php _e( 'Author', 'woocommerce-gopay' ); ?></th>
				<th><?php _e( 'Settings', 'woocommerce-gopay' ); ?></th>
			</tr>
			<tr>
				<td><?php _e( $plugin_data['Title'], 'woocommerce-gopay' ); ?></td>
				<td><?php _e( $plugin_data['Version'], 'woocommerce-gopay' ); ?></td>
				<td><?php _e( $plugin_data['Description'], 'woocommerce-gopay' ); ?></td>
				<td><?php _e( $plugin_data['Author'], 'woocommerce-gopay' ); ?></td>
				<?php
				echo '<td><a href="' . $settings_url . '">' . __( 'Settings', 'woocommerce-gopay' ) . '</a></td>'
				?>
			</tr>
		</table>
	</div>

</div>
