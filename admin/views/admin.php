<?php
$plugin_data = get_plugin_data( WOOCOMMERCE_GOPAY_FULLPATH );
?>


<div class="wrap">
    <div class="woocommerce-gopay-menu">
        <h1><?php _e('Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN); ?></h1>
    </div>

    <div class="woocommerce-gopay-menu table-responsive">
        <table class="table table-striped table-bordered">
            <tr>
                <th>Plugin Name</th>
                <th>Version</th>
                <th>Description</th>
                <th>Author</th>
            </tr>
            <tr>
                <td><?php _e($plugin_data['Title'], WOOCOMMERCE_GOPAY_DOMAIN); ?></td>
                <td><?php _e($plugin_data['Version'], WOOCOMMERCE_GOPAY_DOMAIN); ?></td>
                <td><?php _e($plugin_data['Description'], WOOCOMMERCE_GOPAY_DOMAIN); ?></td>
                <td><?php _e($plugin_data['Author'], WOOCOMMERCE_GOPAY_DOMAIN); ?></td>
            </tr>
        </table>
    </div>

</div>