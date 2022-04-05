<?php
/**
 * Plugin activation
 *
 * @link       https://argo22.com/
 * @since      1.0.0
 *
 * @package    woocommerce-gopay
 * @subpackage woocommerce-gopay/includes
 * @author    argo22
 */
class Woocommerce_Gopay_Activator {

  /**
   * Run when plugin is activated
   *
   * @since 1.0.0
   */
  public static function activate() {
    self::create_log_table();
  }

  /**
   * Create log table if it does not exist
   *
   * @since 1.0.0
   */
  private static function create_log_table() {
      global $wpdb;

      $charset_collate = $wpdb->get_charset_collate();

      $sql = "CREATE TABLE " . $wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME . " (
                id bigint(255) NOT NULL AUTO_INCREMENT,
                order_id bigint(255) NOT NULL,
                transaction_id bigint(255) NOT NULL,
                created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                gmt_offset int NOT NULL,
                log_level varchar(100) NOT NULL,
                log JSON NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      maybe_create_table($wpdb->prefix . WOOCOMMERCE_GOPAY_LOG_TABLE_NAME, $sql);
    }

}