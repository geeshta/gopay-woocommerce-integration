<?php
/**
 * Plugin deactivation
 *
 * @package WooCommerce GoPay gateway
 * @author argo22
 * @link https://www.argo22.com
 * @copyright 2022 argo22
 * @since 1.0.0
 */
class Woocommerce_Gopay_Deactivator {

  /**
   * Deactivation
   *
   * @since 1.0.0
   */
  public static function deactivate() {
      #self::delete_log_table();

      // Unschedule event
      wp_unschedule_event(wp_next_scheduled('wc_gopay_check_status', array(false)),
          'wc_gopay_check_status', array(false));
  }

    /**
     * Delete log table if it exists
     *
     * @since 1.0.0
     */
    private static function delete_log_table() {
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS " .  $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME);
    }

}