<?php
global $wpdb;
$log_data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME . " ORDER BY created_at DESC");
?>

<div class="wrap">
    <div class="woocommerce-gopay-menu">
        <h1><?php _e('Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN); ?></h1>
    </div>

    <div class="woocommerce-gopay-menu table-responsive">
        <table class="table table-striped table-bordered">
            <tr>
                <th>Id</th>
                <th>Order id</th>
                <th>Transaction id</th>
                <th>Created at</th>
                <th>Log level</th>
                <th>Log</th>
            </tr>
            <?php
            foreach ($log_data as $_ => $log){
                $order = wc_get_order($log->order_id);
                $order_url = ($order && $order->get_edit_order_url()) ? $order->get_edit_order_url() : "#";
                $log_decoded = json_decode($log->log);
                $gw_url = (!empty($log_decoded) && property_exists($log_decoded, "gw_url")) ? $log_decoded->gw_url : "#";
                $gmt_offset = $log->gmt_offset > 0 ? '+'. $log->gmt_offset : $log->gmt_offset;

                echo '<tr>';
                echo '<td>' . $log->id . '</td>';
                echo '<td><a href="' . $order_url . '">' . $log->order_id . '</a></td>';
                echo '<td><a href="' . $gw_url . '">' . $log->transaction_id . '</a></td>';
                echo '<td>' . $log->created_at . ' (UTC' . $gmt_offset . ')</td>';
                echo '<td>' . $log->log_level . '</td>';
                echo '<td>' . $log->log . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>