<?php
/**
 * Plugin deactivation
 *
 * @link       https://argo22.com/
 * @since      1.0.0
 *
 * @package    woocommerce-gopay
 * @subpackage woocommerce-gopay/includes
 * @author    argo22
 */
class Woocommerce_Gopay_Deactivator {

  /**
   * Deactivation
   *
   * @since 1.0.0
   */
  public static function deactivate() {
      #self::delete_log_table();
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