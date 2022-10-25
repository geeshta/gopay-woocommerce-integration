<?php
/**
 * Plugin admin log
 *
 * @package   WooCommerce GoPay gateway
 * @author    GoPay
 * @link      https://www.gopay.com/
 * @copyright 2022 GoPay
 * @since     1.0.0
 */

$pagenum          = filter_input( INPUT_GET, 'pagenum', FILTER_VALIDATE_INT );
$log_table_filter = filter_input( INPUT_GET, 'log_table_filter' );

global $wpdb;
$rows = $wpdb->get_results(
	sprintf(
		"SELECT COUNT(*) as num_rows FROM %s%s WHERE UPPER(CONCAT(order_id, transaction_id, message, created_at, log_level, log))
                REGEXP '[\w\W]*%s[\w\W]*'",
		$wpdb->prefix,
		WOOCOMMERCE_GOPAY_LOG_TABLE_NAME,
		strtoupper( $log_table_filter )
	)
);

$results_per_page = 20;
$number_of_rows   = $rows[0]->num_rows;
$number_of_pages  = ceil( $number_of_rows / $results_per_page );

if ( null === $pagenum || false === $pagenum ) {
	$pagenum = 1;
}

$page_pagination = ( $pagenum - 1 ) * $results_per_page;
$log_data        = $page_pagination >= 0 ? $wpdb->get_results(
	sprintf(
		"SELECT * FROM %s%s WHERE UPPER(CONCAT(order_id, transaction_id, message, created_at, log_level, log))
                REGEXP '[\w\W]*%s[\w\W]*' ORDER BY created_at DESC LIMIT %d,%d",
		$wpdb->prefix,
		WOOCOMMERCE_GOPAY_LOG_TABLE_NAME,
		strtoupper( $log_table_filter ),
		$page_pagination,
		$results_per_page
	)
) : array();

?>

<div class="wrap">
	<div class="woocommerce-gopay-menu">
		<h1><?php echo wp_kses_post( __( 'Woocommerce GoPay gateway', 'woocommerce-gopay' ) ); ?></h1>
	</div>

	<div class="woocommerce-gopay-menu">
		<table>
			<thead>
				<tr>
					<th><?php echo wp_kses_post( __( 'Id', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Order id', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Transaction id', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Message', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Created at', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Log level', 'woocommerce-gopay' ) ); ?></th>
					<th><?php echo wp_kses_post( __( 'Log', 'woocommerce-gopay' ) ); ?></th>
				</tr>
			</thead>
			<tbody id="log_table_body">
				<?php
				foreach ( $log_data as $log ) {
					$order_log = wc_get_order( $log->order_id );
					if ( is_object( $order_log ) && $order_log instanceof \Automattic\WooCommerce\Admin\Overrides\Order
					) {
						$order_url = ! empty( $order_log->get_edit_order_url() ) ? $order_log->get_edit_order_url() :
							'#';
					} else {
						$order_url = '#';
					}
					$log_decoded = json_decode( $log->log );
					$gw_url      = ( ! empty( $log_decoded->json ) &&
										property_exists( $log_decoded->json, 'gw_url' ) ) ?
										$log_decoded->json->gw_url : '#';

					echo wp_kses_post( '<tr>' );
					echo wp_kses_post( '<td>' . esc_attr( $log->id ) . '</td>' );
					echo wp_kses_post( '<td><a href="' . esc_attr( $order_url ) . '">' . esc_attr( $log->order_id ) .
                        '</a></td>' );
					echo wp_kses_post( '<td><a href="' . esc_attr( $gw_url ) . '">' . esc_attr( $log->transaction_id
                        ) . '</a></td>' );
					echo wp_kses_post( '<td>' . esc_attr( $log->message ) . '</td>' );
					echo wp_kses_post( '<td>' . esc_attr( $log->created_at ) . ' (GMT)</td>' );
					echo wp_kses_post( '<td>' . esc_attr( $log->log_level ) . '</td>' );
					echo wp_kses( '<td><a href="#" onClick="openPopup(' .
						wp_kses_post( htmlspecialchars( $log->log, ENT_QUOTES ) ) . ');">Open log</a></td>',
                    array( 'td' => array(), 'a' => array( 'href' => 1, 'onclick' => 1 ) ) );
					echo wp_kses_post( '</tr>' );
				}
				?>
			</tbody>
		</table>

		<form action="">
			<label for="page"></label>
			<input type="hidden" id="page" name="page" value="woocommerce-gopay-menu-log">
			<label for="log_table_filter"><?php _e( 'Filter table by any column:', 'woocommerce-gopay' ); ?></label>
			<input type="text" id="log_table_filter" name="log_table_filter"
				   placeholder="<?php echo wp_kses_post( __( 'Search here', 'woocommerce-gopay' ) ); ?>">
			<input type="submit" value="<?php echo wp_kses_post( __( 'Search', 'woocommerce-gopay' ) ); ?>">
		</form>

		<?php
		if ( ! empty( $log_data ) ) {
			?>

		<div id="woocommerce-gopay-menu-popup" class="woocommerce-gopay-menu-popup">
			<div class="woocommerce-gopay-menu-close" onclick="closePopup();"></div>
		</div>

		<nav>
			<ul class="woocommerce-gopay-menu-pagination">
				<li class="woocommerce-gopay-menu-<?php echo wp_kses_post( $pagenum > 1 ? 'enabled' : 'disabled' ); ?>">
					<a href="<?php echo wp_kses_post( add_query_arg( 'pagenum', $pagenum - 1 ) ); ?>">Previous</a>
				</li>
				<?php
				if ( $number_of_pages > 10 ) {
					$start = max( $pagenum - 5, 1 );
					$stop  = $start + 10;

					if ( $stop > $number_of_pages ) {
						$start = $number_of_pages - 10;
						$stop  = $number_of_pages;
					}

					$pages_log = range( $start, $stop );
				} else {
					$pages_log = range( 1, $number_of_pages );
				}

				foreach ( $pages_log as $page_log ) {
					echo wp_kses( '<li class="woocommerce-gopay-menu-' .
						( $pagenum == $page_log ? 'active' : 'inactive' ) . '"><a href = "'
						. wp_kses_post( add_query_arg( 'pagenum', $page_log ) ) . '">' . wp_kses_post( $page_log ) . ' </a>',
                    array( 'li' => array( 'class' => 1 ), 'a' => array( 'href' => 1 ) ) );
				}
				?>
				<li class="woocommerce-gopay-menu-<?php echo wp_kses_post( $pagenum < $number_of_pages ? 'enabled' : 'disabled' ); ?>">
					<a href="<?php echo wp_kses_post( add_query_arg( 'pagenum', $pagenum + 1 ) ); ?>">Next</a>
				</li>
			</ul>
		</nav>
		<form action="">
			<label for="page"></label>
			<input type="hidden" id="page" name="page" value="woocommerce-gopay-menu-log">
			<label for="pagenum">Page (
			<?php
			echo wp_kses_post( $pagenum ) . ' of ' . wp_kses_post( $number_of_pages );
			?>
			):</label>
			<input type="number" id="pagenum" name="pagenum" min="1" max="
			<?php
			echo wp_kses_post( $number_of_pages );
			?>
			"
				   style="width: 65px;">
			<input type="submit" value="<?php echo wp_kses_post( __( 'Go to', 'woocommerce-gopay' ) ); ?>">
		</form>

			<?php
		}
		?>

	</div>
</div>
