<?php

global $wpdb;
$rows = $wpdb->get_results( 'SELECT COUNT(*) as num_rows FROM ' . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME );

$results_per_page = 20;
$number_of_rows   = $rows[0]->num_rows;
$number_of_pages  = ceil( $number_of_rows / $results_per_page );

$pagenum = filter_input( INPUT_GET, 'pagenum', FILTER_VALIDATE_INT );
if ( $pagenum === null || $pagenum === false ) {
	$pagenum = 1;
}

$page_pagination = ( $pagenum - 1 ) * $results_per_page;
$log_data        = $page_pagination >= 0 ? $wpdb->get_results(
	sprintf(
		'SELECT * FROM %s%s ORDER BY created_at DESC LIMIT %d,%d',
		$wpdb->prefix,
		WOOCOMMERCE_GOPAY_LOG_TABLE_NAME,
		$page_pagination,
		$results_per_page
	)
) : array();

?>

<div class="wrap">
	<div class="woocommerce-gopay-menu">
		<h1><?php _e( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ); ?></h1>
	</div>

	<div class="woocommerce-gopay-menu">
		<table>
            <thead>
                <tr>
                    <th><?php _e( 'Id', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Order id', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Transaction id', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Message', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Created at', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Log level', 'woocommerce-gopay' ); ?></th>
                    <th><?php _e( 'Log', 'woocommerce-gopay' ); ?></th>
                </tr>
            </thead>
            <tbody id="log_table_body">
                <?php
                foreach ( $log_data as $log ) {
                    $order = wc_get_order( $log->order_id );
                    if ( is_object( $order ) && $order instanceof \Automattic\WooCommerce\Admin\Overrides\Order ) {
                        $order_url = ! empty( $order->get_edit_order_url() ) ? $order->get_edit_order_url() : '#';
                    } else {
                        $order_url = '#';
                    }
                    $log_decoded = json_decode( $log->log );
                    $gw_url      = ( ! empty( $log_decoded->json ) &&
                                        property_exists( $log_decoded->json, 'gw_url' ) ) ?
                                        $log_decoded->json->gw_url : '#';

                    echo '<tr>';
                    echo '<td>' . esc_attr( $log->id ) . '</td>';
                    echo '<td><a href="' . esc_attr( $order_url ) . '">' . esc_attr( $log->order_id ) . '</a></td>';
                    echo '<td><a href="' . esc_attr( $gw_url ) . '">' . esc_attr( $log->transaction_id ) . '</a></td>';
                    echo '<td>' . esc_attr( $log->message ) . '</td>';
                    echo '<td>' . esc_attr( $log->created_at ) . ' (GMT)</td>';
                    echo '<td>' . esc_attr( $log->log_level ) . '</td>';
                    echo '<td><a href="#" onClick="openPopup(' .
                        htmlspecialchars( $log->log, ENT_QUOTES ) . ');">Open log</a></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
		</table>

        <b>Filter table by any column:
            <input id="log_table_filter" type="text" onkeyup="searchTable();" placeholder="Search here">
        </b>

        <?php
        if ( !empty( $log_data ) ) {
        ?>

		<div id="woocommerce-gopay-menu-popup" class="woocommerce-gopay-menu-popup">
			<div class="woocommerce-gopay-menu-close" onclick="closePopup();"></div>
		</div>

		<nav>
			<ul class="woocommerce-gopay-menu-pagination">
				<li class="woocommerce-gopay-menu-<?php echo $pagenum > 1 ? 'enabled' : 'disabled'; ?>">
					<a href="<?php echo add_query_arg( 'pagenum', $pagenum - 1 ); ?>">Previous</a>
				</li>
				<?php
				if ( $number_of_pages > 10 ) {
					$start = max( $pagenum - 5, 1 );
					$stop  = $start + 10;

					if ( $stop > $number_of_pages ) {
						$start = $number_of_pages - 10;
						$stop  = $number_of_pages;
					}

					$pages = range( $start, $stop );
				} else {
					$pages = range( 1, $number_of_pages );
				}

				foreach ( $pages as $page ) {
					echo '<li class="woocommerce-gopay-menu-' .
						( $pagenum == $page ? 'active' : 'inactive' ) . '"><a href = "'
						. add_query_arg( 'pagenum', $page ) . '">' . $page . ' </a>';
				}
				?>
				<li class="woocommerce-gopay-menu-<?php echo $pagenum < $number_of_pages ? 'enabled' : 'disabled'; ?>">
					<a href="<?php echo add_query_arg( 'pagenum', $pagenum + 1 ); ?>">Next</a>
				</li>
			</ul>
		</nav>
		<form action="">
			<label for="page"></label>
			<input type="hidden" id="page" name="page" value="woocommerce-gopay-menu-log">
			<label for="pagenum">Page (<?php echo $pagenum . ' of ' . $number_of_pages; ?>):</label>
			<input type="number" id="pagenum" name="pagenum" min="1" max="<?php echo $number_of_pages; ?>"
				   style="width: 65px;">
			<input type="submit" value="<?php echo _e( 'Go to', 'woocommerce-gopay' ); ?>">
		</form>

		<?php
            }
		?>

	</div>
</div>
