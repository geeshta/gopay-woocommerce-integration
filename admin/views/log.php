<?php
global $wpdb;
$log_data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME . " ORDER BY created_at DESC");

$results_per_page = 20;
$number_of_rows = $wpdb->num_rows;
$number_of_pages = ceil($number_of_rows / $results_per_page);

if (!isset ($_GET['pagenum'])) {
    $pagenum = 1;
} else {
    $pagenum = filter_var($_GET['pagenum'], FILTER_VALIDATE_INT);
}

$page_pagination = ($pagenum - 1) * $results_per_page;
$log_data = array_slice($log_data, $page_pagination, $results_per_page);

?>

<div class="wrap">
    <div class="woocommerce-gopay-menu">
        <h1><?php _e('Woocommerce GoPay gateway', WOOCOMMERCE_GOPAY_DOMAIN); ?></h1>
    </div>

    <div class="woocommerce-gopay-menu">
        <table>
            <tr>
                <th>Id</th>
                <th>Order id</th>
                <th>Transaction id</th>
                <th>Message</th>
                <th>Created at</th>
                <th>Log level</th>
                <th>Log</th>
            </tr>
            <?php
            foreach ($log_data as $_ => $log) {
                $order = wc_get_order($log->order_id);
                $order_url = ($order && $order->get_edit_order_url()) ? $order->get_edit_order_url() : "#";
                $log_decoded = json_decode($log->log);
                $gw_url = (!empty($log_decoded) && property_exists($log_decoded, "gw_url")) ? $log_decoded->gw_url : "#";
                $gmt_offset = $log->gmt_offset > 0 ? '+' . $log->gmt_offset : $log->gmt_offset;

                echo '<tr>';
                echo '<td>' . $log->id . '</td>';
                echo '<td><a href="' . $order_url . '">' . $log->order_id . '</a></td>';
                echo '<td><a href="' . $gw_url . '">' . $log->transaction_id . '</a></td>';
                echo '<td>' . $log->message . '</td>';
                echo '<td>' . $log->created_at . ' (UTC' . $gmt_offset . ')</td>';
                echo '<td>' . $log->log_level . '</td>';
                echo '<td><a href="#" onClick="openPopup(' . htmlspecialchars($log->log,ENT_QUOTES) . ');">Open log</a></td>';
                echo '</tr>';
            }
            ?>
        </table>

        <div id="popup" class="popup">
            <div class="close" onclick="closePopup();"></div>
        </div>

        <nav>
            <ul class="pagination">
                <li class="<?php echo $pagenum > 1 ? 'enabled' : 'disabled' ?>">
                    <a href="<?php echo add_query_arg('pagenum', $pagenum - 1) ?>" tabindex="-1">Previous</a>
                </li>
                <?php
                for ($page = 1; $page <= $number_of_pages; $page++) {
                    echo '<li class="' . ($pagenum == $page ? 'active' : 'inactive') . '"><a href = "'
                        . add_query_arg('pagenum', $page) . '">' . $page . ' </a>';
                }
                ?>
                <li class="<?php echo $pagenum < $number_of_pages ? 'enabled' : 'disabled' ?>">
                    <a href="<?php echo add_query_arg('pagenum', $pagenum + 1) ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>
</div>