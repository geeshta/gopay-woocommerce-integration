<?php
$plugin_data    = get_plugin_data( WOOCOMMERCE_GOPAY_FULLPATH );
$settings_url   = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gopay_gateway' );
?>


<div class="wrap">
    <div class="woocommerce-gopay-menu">
        <h1><?php _e( 'Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN ); ?></h1>
    </div>

    <div class="woocommerce-gopay-menu">
        <table>
            <tr>
                <th><?php _e( 'Plugin Name', WOOCOMMERCE_GOPAY_DOMAIN ) ?></th>
                <th><?php _e( 'Version', WOOCOMMERCE_GOPAY_DOMAIN ) ?></th>
                <th><?php _e( 'Description', WOOCOMMERCE_GOPAY_DOMAIN ) ?></th>
                <th><?php _e( 'Author', WOOCOMMERCE_GOPAY_DOMAIN ) ?></th>
                <th><?php _e( 'Settings', WOOCOMMERCE_GOPAY_DOMAIN ) ?></th>
            </tr>
            <tr>
                <td><?php _e( $plugin_data['Title'], WOOCOMMERCE_GOPAY_DOMAIN ); ?></td>
                <td><?php _e( $plugin_data['Version'], WOOCOMMERCE_GOPAY_DOMAIN ); ?></td>
                <td><?php _e( $plugin_data['Description'], WOOCOMMERCE_GOPAY_DOMAIN ); ?></td>
                <td><?php _e( $plugin_data['Author'], WOOCOMMERCE_GOPAY_DOMAIN ); ?></td>
				<?php
				echo '<td><a href="' . $settings_url . '">' . __( 'Settings', WOOCOMMERCE_GOPAY_DOMAIN ) . '</a></td>'
				?>
            </tr>
        </table>
    </div>

</div>