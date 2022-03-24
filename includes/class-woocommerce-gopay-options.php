<?php

/**
 * WooCommerce GoPay gateway lists of supported
 * options default
 * 
 * Lists of supported options for languages,
 * payment methods, banks, shipping methods,
 * countries and currencies 
 * 
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      www.argo22.com
 * @copyright 2022 argo22
 */

class Woocommerce_Gopay_Options {

  /**
   * Return supported currencies that
   * can be used in the gateway 
   * 
   * @return array
   */
  public static function supported_currencies() {
    return [
        'CZK' => __('Czech koruna', WOOCOMMERCE_GOPAY_DOMAIN),
        'EUR' => __('Euro', WOOCOMMERCE_GOPAY_DOMAIN),
        'PLN' => __('Polish złoty', WOOCOMMERCE_GOPAY_DOMAIN),
        'USD' => __('United States dollar', WOOCOMMERCE_GOPAY_DOMAIN),
        'GBP' => __('Pound sterling', WOOCOMMERCE_GOPAY_DOMAIN),
        'HUF' => __('Hungarian forint', WOOCOMMERCE_GOPAY_DOMAIN),
        'RON' => __('Romanian lei', WOOCOMMERCE_GOPAY_DOMAIN),
        'BGN' => __('Bulgarian lev', WOOCOMMERCE_GOPAY_DOMAIN),
        'HRK' => __('Croatian kuna', WOOCOMMERCE_GOPAY_DOMAIN)
      ];
  }

  /**
   * Return supported countries where
   * the gateway can be available 
   * 
   * @return array
   */
  public static function supported_countries() {
    return WC()->countries->get_allowed_countries();
  }

   /**
   * Return supported shipping methods that
   * the gateway can use
   * 
   * @return array
   */
  public static function supported_shipping_methods() {
    return array_reduce(WC()->shipping->load_shipping_methods(),
      function ($supported_shipping_methods, $shipping_method) {
        $supported_shipping_methods[$shipping_method->id] = $shipping_method->get_method_title();
        return $supported_shipping_methods;
      }, array()); 
  }

  /**
   * Return supported payment methods that
   * the gateway can use
   * 
   * @return array
   */
  public static function supported_payment_methods() {
    return [
        'PAYMENT_CARD' => __('Payment card', WOOCOMMERCE_GOPAY_DOMAIN),
        'BANK_ACCOUNT' => __('Bank account', WOOCOMMERCE_GOPAY_DOMAIN),
        'GPAY' => __('Google Pay', WOOCOMMERCE_GOPAY_DOMAIN),
        'APPLE_PAY' => __('Apple Pay', WOOCOMMERCE_GOPAY_DOMAIN),
        'GOPAY' => __('GoPay wallet', WOOCOMMERCE_GOPAY_DOMAIN),
        'PAYPAL' => __('PayPal wallet', WOOCOMMERCE_GOPAY_DOMAIN),
        'MPAYMENT' => __('mPlatba (mobile payment)', WOOCOMMERCE_GOPAY_DOMAIN),
        'PRSMS' => __('Premium SMS', WOOCOMMERCE_GOPAY_DOMAIN),
        'PAYSAFECARD' => __('PaySafeCard coupon', WOOCOMMERCE_GOPAY_DOMAIN),
        'BITCOIN' => __('Bitcoin wallet', WOOCOMMERCE_GOPAY_DOMAIN),
        'CLICK_TO_PAY' => __('Click to Pay', WOOCOMMERCE_GOPAY_DOMAIN)
      ];
  }

  /**
   * Return supported banks for bank payment that
   * the gateway can use
   * 
   * @return array
   */
  public static function supported_banks() {
    return [
      'GIBACZPX' => __('Česká Spořitelna', WOOCOMMERCE_GOPAY_DOMAIN),
      'KOMBCZPP' => __('Komerční Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'RZBCCZPP' => __('Raiffeisenbank', WOOCOMMERCE_GOPAY_DOMAIN),
      'FIOBCZPP' => __('FIO Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'BACXCZPP' => __('UniCredit Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'BREXCZPP' => __('mBank', WOOCOMMERCE_GOPAY_DOMAIN),
      'CEKOCZPP' => __('ČSOB', WOOCOMMERCE_GOPAY_DOMAIN),
      'CEKOCZPP-ERA' => __('Poštovní Spořitelna', WOOCOMMERCE_GOPAY_DOMAIN),
      'AGBACZPP' => __('Moneta Money Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'AIRACZPP' => __('AirBank', WOOCOMMERCE_GOPAY_DOMAIN),
      'EQBKCZPP' => __('EQUA Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'INGBCZPP' => __('ING Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'EXPNCZPP' => __('Expobank', WOOCOMMERCE_GOPAY_DOMAIN),
      'OBKLCZ2X' => __('OberBank AG', WOOCOMMERCE_GOPAY_DOMAIN),
      'SUBACZPP' => __('Všeobecná Úvěrová Banka - pobočka Praha', WOOCOMMERCE_GOPAY_DOMAIN),
      'BPPFCZP1' => __('Hello! Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'TATRSKBX' => __('Tatra Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'SUBASKBX' => __('Všeobecná Úverová Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'UNCRSKBX' => __('UniCredit Bank SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'GIBASKBX' => __('Slovenská Sporiteľňa', WOOCOMMERCE_GOPAY_DOMAIN),
      'POBNSKBA' => __('Poštová Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'OTPVSKBX' => __('OTP Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'KOMASK2X' => __('Prima Banka', WOOCOMMERCE_GOPAY_DOMAIN),
      'CITISKBA' => __('Citibank Europe', WOOCOMMERCE_GOPAY_DOMAIN),
      'FIOZSKBA' => __('FIO Banka SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'INGBSKBX' => __('ING Wholesale Banking SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'BREXSKBX' => __('mBank SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'JTBPSKBA' => __('J&T Banka SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'OBKLSKBA' => __('OberBank AG SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'BSLOSK22' => __('Privatbanka', WOOCOMMERCE_GOPAY_DOMAIN),
      'BFKKSKBB' => __('BKS Bank AG SK', WOOCOMMERCE_GOPAY_DOMAIN),
      'GBGCPLPK' => __('Getin Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'NESBPLPW' => __('Nest Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'VOWAPLP9' => __('Volkswagen Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'CITIPLPX' => __('Citi handlowy', WOOCOMMERCE_GOPAY_DOMAIN),
      'WBKPPLPP' => __('Santander', WOOCOMMERCE_GOPAY_DOMAIN),
      'BIGBPLPW' => __('Millenium Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'EBOSPLPW' => __('Bank Ochrony Srodowiska', WOOCOMMERCE_GOPAY_DOMAIN),
      'PKOPPLPW' => __('Pekao Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'PPABPLPK' => __('BNP Paribas', WOOCOMMERCE_GOPAY_DOMAIN),
      'BPKOPLPW' => __('OWSZECHNA KASA OSZCZEDNOSCI BANK POLSK', WOOCOMMERCE_GOPAY_DOMAIN),
      'AGRIPLPR' => __('Credit Agricole Banka Polska', WOOCOMMERCE_GOPAY_DOMAIN),
      'GBGCPLPK-NOB' => __('Noble Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'POLUPLPR' => __('BPS/Bank Nowy BFG', WOOCOMMERCE_GOPAY_DOMAIN),
      'BREXPLPW' => __('mBank PL', WOOCOMMERCE_GOPAY_DOMAIN),
      'INGBPLPW' => __('ING Bank PL', WOOCOMMERCE_GOPAY_DOMAIN),
      'ALBPPLPW' => __('Alior', WOOCOMMERCE_GOPAY_DOMAIN),
      'IEEAPLPA' => __('IdeaBank', WOOCOMMERCE_GOPAY_DOMAIN),
      'POCZPLP4' => __('Pocztowy24', WOOCOMMERCE_GOPAY_DOMAIN),
      'IVSEPLPP' => __('Plus Bank', WOOCOMMERCE_GOPAY_DOMAIN),
      'TOBAPLPW' => __('Toyota Bank', WOOCOMMERCE_GOPAY_DOMAIN)
    ];
  }

}



