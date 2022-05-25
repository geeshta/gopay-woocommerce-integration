<?php

/**
 * WooCommerce GoPay gateway lists of supported
 * options default
 * Lists of supported options for languages,
 * payment methods, banks, shipping methods,
 * countries and currencies
 *
 * @package   WooCommerce GoPay gateway
 * @author    argo22
 * @link      https://www.argo22.com
 * @copyright 2022 argo22
 * @since     1.0.0
 */

class Woocommerce_Gopay_Options
{

	/**
	 * Return supported currencies that
	 * can be used in the gateway
	 *
	 * @return array
	 */
	public static function supported_currencies(): array
	{
		return array(
			'CZK' => __( 'Czech koruna', WOOCOMMERCE_GOPAY_DOMAIN ),
			'EUR' => __( 'Euro', WOOCOMMERCE_GOPAY_DOMAIN ),
			'PLN' => __( 'Polish złoty', WOOCOMMERCE_GOPAY_DOMAIN ),
			'USD' => __( 'United States dollar', WOOCOMMERCE_GOPAY_DOMAIN ),
			'GBP' => __( 'Pound sterling', WOOCOMMERCE_GOPAY_DOMAIN ),
			'HUF' => __( 'Hungarian forint', WOOCOMMERCE_GOPAY_DOMAIN ),
			'RON' => __( 'Romanian lei', WOOCOMMERCE_GOPAY_DOMAIN ),
			'BGN' => __( 'Bulgarian lev', WOOCOMMERCE_GOPAY_DOMAIN ),
			'HRK' => __( 'Croatian kuna', WOOCOMMERCE_GOPAY_DOMAIN ),
		);
	}

	/**
	 * Return supported languages that
	 * can be used in the gateway
	 *
	 * @return array
	 */
	public static function supported_languages(): array
	{
		return array(
			'CS' => __( 'Czech', WOOCOMMERCE_GOPAY_DOMAIN ),
			'SK' => __( 'Slovak', WOOCOMMERCE_GOPAY_DOMAIN ),
			'EN' => __( 'English', WOOCOMMERCE_GOPAY_DOMAIN ),
			'DE' => __( 'German', WOOCOMMERCE_GOPAY_DOMAIN ),
			'RU' => __( 'Russian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'PL' => __( 'Polish', WOOCOMMERCE_GOPAY_DOMAIN ),
			'HU' => __( 'Hungarian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'RO' => __( 'Romanian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'BG' => __( 'Bulgarian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'HR' => __( 'Croatian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'IT' => __( 'Italian', WOOCOMMERCE_GOPAY_DOMAIN ),
			'FR' => __( 'French', WOOCOMMERCE_GOPAY_DOMAIN ),
			'ES' => __( 'Spanish', WOOCOMMERCE_GOPAY_DOMAIN ),
			'UK' => __( 'Ukrainian', WOOCOMMERCE_GOPAY_DOMAIN ),
		);
	}

	/**
	 * Return countries as keys and the language spoken
	 * in the country as values.
	 * If country has more than one spoken language than
	 * the one with the highest number of speakers is returned.
	 *
	 * @return array
	 */
	public static function country_to_language(): array
	{
		// Extracted from geonames.org (http://download.geonames.org/export/dump/countryInfo.txt)
		return array(
			'AD' => 'CA', 'AE' => 'AR', 'AF' => 'FA', 'AG' => 'EN', 'AI' => 'EN', 'AL' => 'SQ', 'AM' => 'HY',
			'AO' => 'PT', 'AR' => 'ES', 'AS' => 'EN', 'AT' => 'DE', 'AU' => 'EN', 'AW' => 'NL', 'AX' => 'SV',
			'AZ' => 'AZ', 'BA' => 'BS', 'BB' => 'EN', 'BD' => 'BN', 'BE' => 'NL', 'BF' => 'FR', 'BG' => 'BG',
			'BH' => 'AR', 'BI' => 'FR', 'BJ' => 'FR', 'BL' => 'FR', 'BM' => 'EN', 'BN' => 'MS', 'BO' => 'ES',
			'BQ' => 'NL', 'BR' => 'PT', 'BS' => 'EN', 'BT' => 'DZ', 'BW' => 'EN', 'BY' => 'BE', 'BZ' => 'EN',
			'CA' => 'EN', 'CC' => 'MS', 'CD' => 'FR', 'CF' => 'FR', 'CG' => 'FR', 'CH' => 'DE', 'CI' => 'FR',
			'CK' => 'EN', 'CL' => 'ES', 'CM' => 'EN', 'CN' => 'ZH', 'CO' => 'ES', 'CR' => 'ES', 'CU' => 'ES',
			'CV' => 'PT', 'CW' => 'NL', 'CX' => 'EN', 'CY' => 'EL', 'CZ' => 'CS', 'DE' => 'DE', 'DJ' => 'FR',
			'DK' => 'DA', 'DM' => 'EN', 'DO' => 'ES', 'DZ' => 'AR', 'EC' => 'ES', 'EE' => 'ET', 'EG' => 'AR',
			'EH' => 'AR', 'ER' => 'AA', 'ES' => 'ES', 'ET' => 'AM', 'FI' => 'FI', 'FJ' => 'EN', 'FK' => 'EN',
			'FM' => 'EN', 'FO' => 'FO', 'FR' => 'FR', 'GA' => 'FR', 'GB' => 'EN', 'GD' => 'EN', 'GE' => 'KA',
			'GF' => 'FR', 'GG' => 'EN', 'GH' => 'EN', 'GI' => 'EN', 'GL' => 'KL', 'GM' => 'EN', 'GN' => 'FR',
			'GP' => 'FR', 'GQ' => 'ES', 'GR' => 'EL', 'GS' => 'EN', 'GT' => 'ES', 'GU' => 'EN', 'GW' => 'PT',
			'GY' => 'EN', 'HK' => 'ZH', 'HN' => 'ES', 'HR' => 'HR', 'HT' => 'HT', 'HU' => 'HU', 'ID' => 'ID',
			'IE' => 'EN', 'IL' => 'HE', 'IM' => 'EN', 'IN' => 'EN', 'IO' => 'EN', 'IQ' => 'AR', 'IR' => 'FA',
			'IS' => 'IS', 'IT' => 'IT', 'JE' => 'EN', 'JM' => 'EN', 'JO' => 'AR', 'JP' => 'JA', 'KE' => 'EN',
			'KG' => 'KY', 'KH' => 'KM', 'KI' => 'EN', 'KM' => 'AR', 'KN' => 'EN', 'KP' => 'KO', 'KR' => 'KO',
			'XK' => 'SQ', 'KW' => 'AR', 'KY' => 'EN', 'KZ' => 'KK', 'LA' => 'LO', 'LB' => 'AR', 'LC' => 'EN',
			'LI' => 'DE', 'LK' => 'SI', 'LR' => 'EN', 'LS' => 'EN', 'LT' => 'LT', 'LU' => 'LB', 'LV' => 'LV',
			'LY' => 'AR', 'MA' => 'AR', 'MC' => 'FR', 'MD' => 'RO', 'ME' => 'SR', 'MF' => 'FR', 'MG' => 'FR',
			'MH' => 'MH', 'MK' => 'MK', 'ML' => 'FR', 'MM' => 'MY', 'MN' => 'MN', 'MO' => 'ZH', 'MP' => 'FIL',
			'MQ' => 'FR', 'MR' => 'AR', 'MS' => 'EN', 'MT' => 'MT', 'MU' => 'EN', 'MV' => 'DV', 'MW' => 'NY',
			'MX' => 'ES', 'MY' => 'MS', 'MZ' => 'PT', 'NA' => 'EN', 'NC' => 'FR', 'NE' => 'FR', 'NF' => 'EN',
			'NG' => 'EN', 'NI' => 'ES', 'NL' => 'NL', 'NO' => 'NO', 'NP' => 'NE', 'NR' => 'NA', 'NU' => 'NIU',
			'NZ' => 'EN', 'OM' => 'AR', 'PA' => 'ES', 'PE' => 'ES', 'PF' => 'FR', 'PG' => 'EN', 'PH' => 'TL',
			'PK' => 'UR', 'PL' => 'PL', 'PM' => 'FR', 'PN' => 'EN', 'PR' => 'EN', 'PS' => 'AR', 'PT' => 'PT',
			'PW' => 'PAU', 'PY' => 'ES', 'QA' => 'AR', 'RE' => 'FR', 'RO' => 'RO', 'RS' => 'SR', 'RU' => 'RU',
			'RW' => 'RW', 'SA' => 'AR', 'SB' => 'EN', 'SC' => 'EN', 'SD' => 'AR', 'SS' => 'EN', 'SE' => 'SV',
			'SG' => 'CMN', 'SH' => 'EN', 'SI' => 'SL', 'SJ' => 'NO', 'SK' => 'SK', 'SL' => 'EN', 'SM' => 'IT',
			'SN' => 'FR', 'SO' => 'SO', 'SR' => 'NL', 'ST' => 'PT', 'SV' => 'ES', 'SX' => 'NL', 'SY' => 'AR',
			'SZ' => 'EN', 'TC' => 'EN', 'TD' => 'FR', 'TF' => 'FR', 'TG' => 'FR', 'TH' => 'TH', 'TJ' => 'TG',
			'TK' => 'TKL', 'TL' => 'TET', 'TM' => 'TK', 'TN' => 'AR', 'TO' => 'TO', 'TR' => 'TR', 'TT' => 'EN',
			'TV' => 'TVL', 'TW' => 'ZH', 'TZ' => 'SW', 'UA' => 'UK', 'UG' => 'EN', 'UM' => 'EN', 'US' => 'EN',
			'UY' => 'ES', 'UZ' => 'UZ', 'VA' => 'LA', 'VC' => 'EN', 'VE' => 'ES', 'VG' => 'EN', 'VI' => 'EN',
			'VN' => 'VI', 'VU' => 'BI', 'WF' => 'WLS', 'WS' => 'SM', 'YE' => 'AR', 'YT' => 'FR', 'ZA' => 'ZU',
			'ZM' => 'EN', 'ZW' => 'EN', 'CS' => 'CU', 'AN' => 'NL',
		);
	}

	/**
	 * Get languages by country
	 *
	 * @param string $country country code
	 * @return array
	 */
	public static function get_languages_by_country( $country )
	{
		$locales = ResourceBundle::getLocales('');

		$matches = array();
		foreach ( $locales as $key => $locale ) {
			if ( Locale::getRegion( $locale ) == $country ) {
				$matches[ Locale::getPrimaryLanguage( $locale ) ][] = $locale;
			}
		}

		return $matches;

	}

	/**
	 * Return supported countries where
	 * the gateway can be available
	 *
	 * @return array
	 */
	public static function supported_countries(): array
	{
		return !empty( WC()->countries ) ? WC()->countries->get_allowed_countries() : array();
	}

	/**
	 * Return supported shipping methods that
	 * the gateway can use
	 *
	 * @return array
	 */
	public static function supported_shipping_methods(): array
	{
		if ( empty( WC()->countries ) ) {
			return array();
		}

		return array_reduce(
			WC()->shipping->load_shipping_methods(),
			function ( $supported_shipping_methods, $shipping_method ) {
				$supported_shipping_methods[ $shipping_method->id ] = __( $shipping_method->get_method_title(),
					WOOCOMMERCE_GOPAY_DOMAIN );
				return $supported_shipping_methods;
			},
			array()
		);
	}

	/**
	 * Return supported payment methods that
	 * the gateway can use
	 *
	 * @return array
	 */
	public static function supported_payment_methods(): array
	{
		// Supported payment methods according to https://doc.gopay.com/#payment-instrument
		$payment_methods = array(
			'PAYMENT_CARD'  => array( 'label' => __( 'Payment card', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'BANK_ACCOUNT'  => array( 'label' => __( 'Bank account', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'GPAY'          => array( 'label' => __( 'Google Pay', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'APPLE_PAY'     => array( 'label' => __( 'Apple Pay', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'GOPAY'         => array( 'label' => __( 'GoPay wallet', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'PAYPAL'        => array( 'label' => __( 'PayPal wallet', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'MPAYMENT'      => array( 'label' => __( 'mPlatba (mobile payment)', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'PRSMS'         => array( 'label' => __( 'Premium SMS', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'PAYSAFECARD'   => array( 'label' => __( 'PaySafeCard coupon', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'BITCOIN'       => array( 'label' => __( 'Bitcoin wallet', WOOCOMMERCE_GOPAY_DOMAIN ) ),
			'CLICK_TO_PAY'  => array( 'label' => __( 'Click to Pay', WOOCOMMERCE_GOPAY_DOMAIN ) ),
		);

		$options = get_option( 'woocommerce_wc_gopay_gateway_settings' ,  array() );
		$key = 'option_gopay_payment_methods';

		return !empty( $options ) && array_key_exists( $key, $options ) && !empty( $options[ $key ] ) ?
			$options[ $key ] : $payment_methods ;
	}

	/**
	 * Return supported banks for bank payment that
	 * the gateway can use
	 *
	 * @return array
	 */
	public static function supported_banks(): array
	{

		// Supported banks according to https://doc.gopay.com/#swift
		$banks = array(
			'GIBACZPX'      => array(
				'label' => __( 'Česká Spořitelna', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'KOMBCZPP'      => array(
				'label' => __( 'Komerční Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'RZBCCZPP'      => array(
				'label' => __( 'Raiffeisenbank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'FIOBCZPP'      => array(
				'label' => __( 'FIO Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'BACXCZPP'      => array(
				'label' => __( 'UniCredit Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'BREXCZPP'      => array(
				'label' => __( 'mBank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'CEKOCZPP'      => array(
				'label' => __( 'ČSOB', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'CEKOCZPP-ERA'  => array(
				'label' => __( 'Poštovní Spořitelna', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'AGBACZPP'      => array(
				'label' => __( 'Moneta Money Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'AIRACZPP'      => array(
				'label' => __( 'AirBank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'EQBKCZPP'      => array(
				'label' => __( 'EQUA Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'INGBCZPP'      => array(
				'label' => __( 'ING Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'EXPNCZPP'      => array(
				'label' => __( 'Expobank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'OBKLCZ2X'      => array(
				'label' => __( 'OberBank AG', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'SUBACZPP'      => array(
				'label' => __( 'Všeobecná Úvěrová Banka - pobočka Praha', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'CZ' ),
			'TATRSKBX'      => array(
				'label' => __( 'Tatra Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'SUBASKBX'      => array(
				'label' => __( 'Všeobecná Úverová Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'UNCRSKBX'      => array(
				'label' => __( 'UniCredit Bank SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'GIBASKBX'      => array(
				'label' => __( 'Slovenská Sporiteľňa', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'POBNSKBA'      => array(
				'label' => __( 'Poštová Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'OTPVSKBX'      => array(
				'label' => __( 'OTP Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'KOMASK2X'      => array(
				'label' => __( 'Prima Banka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'CITISKBA'      => array(
				'label' => __( 'Citibank Europe', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'FIOZSKBA'      => array(
				'label' => __( 'FIO Banka SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'INGBSKBX'      => array(
				'label' => __( 'ING Wholesale Banking SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'BREXSKBX'      => array(
				'label' => __( 'mBank SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'JTBPSKBA'      => array(
				'label' => __( 'J&T Banka SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'OBKLSKBA'      => array(
				'label' => __( 'OberBank AG SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'BSLOSK22'      => array(
				'label' => __( 'Privatbanka', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'BFKKSKBB'      => array(
				'label' => __( 'BKS Bank AG SK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'SK' ),
			'GBGCPLPK'      => array(
				'label' => __( 'Getin Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'NESBPLPW'      => array(
				'label' => __( 'Nest Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'VOWAPLP9'      => array(
				'label' => __( 'Volkswagen Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'CITIPLPX'      => array(
				'label' => __( 'Citi handlowy', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'WBKPPLPP'      => array(
				'label' => __( 'Santander', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'BIGBPLPW'      => array(
				'label' => __( 'Millenium Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'EBOSPLPW'      => array(
				'label' => __( 'Bank Ochrony Srodowiska', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'PKOPPLPW'      => array(
				'label' => __( 'Pekao Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'PPABPLPK'      => array(
				'label' => __( 'BNP Paribas', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'BPKOPLPW'      => array(
				'label' => __( 'OWSZECHNA KASA OSZCZEDNOSCI BANK POLSK', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'AGRIPLPR'      => array(
				'label' => __( 'Credit Agricole Banka Polska', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'GBGCPLPK-NOB'  => array(
				'label' => __( 'Noble Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'POLUPLPR'      => array(
				'label' => __( 'BPS/Bank Nowy BFG', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'BREXPLPW'      => array(
				'label' => __( 'mBank PL', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'INGBPLPW'      => array(
				'label' => __( 'ING Bank PL', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'ALBPPLPW'      => array(
				'label' => __( 'Alior', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'IEEAPLPA'      => array(
				'label' => __( 'IdeaBank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'POCZPLP4'      => array(
				'label' => __( 'Pocztowy24', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'IVSEPLPP'      => array(
				'label' => __( 'Plus Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'TOBAPLPW'      => array(
				'label' => __( 'Toyota Bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => 'PL' ),
			'OTHERS'        => array( 'label' => __( 'Another bank', WOOCOMMERCE_GOPAY_DOMAIN ),
				'country' => ''),
		);

		$options = get_option( 'woocommerce_wc_gopay_gateway_settings' ,  array() );
		$key = 'option_gopay_banks';

		return !empty( $options ) && array_key_exists( $key, $options ) && !empty( $options[ $key ] ) ?
			$options[ $key ] : $banks ;
	}

	/**
	 * Return iso 2 as keys and iso 3 equivalence as values
	 *
	 * @return array
	 */
	public static function iso2_to_iso3(): array
	{
		// Extracted from geonames.org (http://download.geonames.org/export/dump/countryInfo.txt)
		return array(
			'AD' => 'AND', 'AE' => 'ARE', 'AF' => 'AFG', 'AG' => 'ATG', 'AI' => 'AIA',
			'AL' => 'ALB', 'AM' => 'ARM', 'AO' => 'AGO', 'AQ' => 'ATA', 'AR' => 'ARG',
			'AS' => 'ASM', 'AT' => 'AUT', 'AU' => 'AUS', 'AW' => 'ABW', 'AX' => 'ALA',
			'AZ' => 'AZE', 'BA' => 'BIH', 'BB' => 'BRB', 'BD' => 'BGD', 'BE' => 'BEL',
			'BF' => 'BFA', 'BG' => 'BGR', 'BH' => 'BHR', 'BI' => 'BDI', 'BJ' => 'BEN',
			'BL' => 'BLM', 'BM' => 'BMU', 'BN' => 'BRN', 'BO' => 'BOL', 'BQ' => 'BES',
			'BR' => 'BRA', 'BS' => 'BHS', 'BT' => 'BTN', 'BV' => 'BVT', 'BW' => 'BWA',
			'BY' => 'BLR', 'BZ' => 'BLZ', 'CA' => 'CAN', 'CC' => 'CCK', 'CD' => 'COD',
			'CF' => 'CAF', 'CG' => 'COG', 'CH' => 'CHE', 'CI' => 'CIV', 'CK' => 'COK',
			'CL' => 'CHL', 'CM' => 'CMR', 'CN' => 'CHN', 'CO' => 'COL', 'CR' => 'CRI',
			'CU' => 'CUB', 'CV' => 'CPV', 'CW' => 'CUW', 'CX' => 'CXR', 'CY' => 'CYP',
			'CZ' => 'CZE', 'DE' => 'DEU', 'DJ' => 'DJI', 'DK' => 'DNK', 'DM' => 'DMA',
			'DO' => 'DOM', 'DZ' => 'DZA', 'EC' => 'ECU', 'EE' => 'EST', 'EG' => 'EGY',
			'EH' => 'ESH', 'ER' => 'ERI', 'ES' => 'ESP', 'ET' => 'ETH', 'FI' => 'FIN',
			'FJ' => 'FJI', 'FK' => 'FLK', 'FM' => 'FSM', 'FO' => 'FRO', 'FR' => 'FRA',
			'GA' => 'GAB', 'GB' => 'GBR', 'GD' => 'GRD', 'GE' => 'GEO', 'GF' => 'GUF',
			'GG' => 'GGY', 'GH' => 'GHA', 'GI' => 'GIB', 'GL' => 'GRL', 'GM' => 'GMB',
			'GN' => 'GIN', 'GP' => 'GLP', 'GQ' => 'GNQ', 'GR' => 'GRC', 'GS' => 'SGS',
			'GT' => 'GTM', 'GU' => 'GUM', 'GW' => 'GNB', 'GY' => 'GUY', 'HK' => 'HKG',
			'HM' => 'HMD', 'HN' => 'HND', 'HR' => 'HRV', 'HT' => 'HTI', 'HU' => 'HUN',
			'ID' => 'IDN', 'IE' => 'IRL', 'IL' => 'ISR', 'IM' => 'IMN', 'IN' => 'IND',
			'IO' => 'IOT', 'IQ' => 'IRQ', 'IR' => 'IRN', 'IS' => 'ISL', 'IT' => 'ITA',
			'JE' => 'JEY', 'JM' => 'JAM', 'JO' => 'JOR', 'JP' => 'JPN', 'KE' => 'KEN',
			'KG' => 'KGZ', 'KH' => 'KHM', 'KI' => 'KIR', 'KM' => 'COM', 'KN' => 'KNA',
			'KP' => 'PRK', 'KR' => 'KOR', 'KW' => 'KWT', 'KY' => 'CYM', 'KZ' => 'KAZ',
			'LA' => 'LAO', 'LB' => 'LBN', 'LC' => 'LCA', 'LI' => 'LIE', 'LK' => 'LKA',
			'LR' => 'LBR', 'LS' => 'LSO', 'LT' => 'LTU', 'LU' => 'LUX', 'LV' => 'LVA',
			'LY' => 'LBY', 'MA' => 'MAR', 'MC' => 'MCO', 'MD' => 'MDA', 'ME' => 'MNE',
			'MF' => 'MAF', 'MG' => 'MDG', 'MH' => 'MHL', 'MK' => 'MKD', 'ML' => 'MLI',
			'MM' => 'MMR', 'MN' => 'MNG', 'MO' => 'MAC', 'MP' => 'MNP', 'MQ' => 'MTQ',
			'MR' => 'MRT', 'MS' => 'MSR', 'MT' => 'MLT', 'MU' => 'MUS', 'MV' => 'MDV',
			'MW' => 'MWI', 'MX' => 'MEX', 'MY' => 'MYS', 'MZ' => 'MOZ', 'NA' => 'NAM',
			'NC' => 'NCL', 'NE' => 'NER', 'NF' => 'NFK', 'NG' => 'NGA', 'NI' => 'NIC',
			'NL' => 'NLD', 'NO' => 'NOR', 'NP' => 'NPL', 'NR' => 'NRU', 'NU' => 'NIU',
			'NZ' => 'NZL', 'OM' => 'OMN', 'PA' => 'PAN', 'PE' => 'PER', 'PF' => 'PYF',
			'PG' => 'PNG', 'PH' => 'PHL', 'PK' => 'PAK', 'PL' => 'POL', 'PM' => 'SPM',
			'PN' => 'PCN', 'PR' => 'PRI', 'PS' => 'PSE', 'PT' => 'PRT', 'PW' => 'PLW',
			'PY' => 'PRY', 'QA' => 'QAT', 'RE' => 'REU', 'RO' => 'ROU', 'RS' => 'SRB',
			'RU' => 'RUS', 'RW' => 'RWA', 'SA' => 'SAU', 'SB' => 'SLB', 'SC' => 'SYC',
			'SD' => 'SDN', 'SE' => 'SWE', 'SG' => 'SGP', 'SH' => 'SHN', 'SI' => 'SVN',
			'SJ' => 'SJM', 'SK' => 'SVK', 'SL' => 'SLE', 'SM' => 'SMR', 'SN' => 'SEN',
			'SO' => 'SOM', 'SR' => 'SUR', 'SS' => 'SSD', 'ST' => 'STP', 'SV' => 'SLV',
			'SX' => 'SXM', 'SY' => 'SYR', 'SZ' => 'SWZ', 'TC' => 'TCA', 'TD' => 'TCD',
			'TF' => 'ATF', 'TG' => 'TGO', 'TH' => 'THA', 'TJ' => 'TJK', 'TK' => 'TKL',
			'TL' => 'TLS', 'TM' => 'TKM', 'TN' => 'TUN', 'TO' => 'TON', 'TR' => 'TUR',
			'TT' => 'TTO', 'TV' => 'TUV', 'TW' => 'TWN', 'TZ' => 'TZA', 'UA' => 'UKR',
			'UG' => 'UGA', 'UM' => 'UMI', 'US' => 'USA', 'UY' => 'URY', 'UZ' => 'UZB',
			'VA' => 'VAT', 'VC' => 'VCT', 'VE' => 'VEN', 'VG' => 'VGB', 'VI' => 'VIR',
			'VN' => 'VNM', 'VU' => 'VUT', 'WF' => 'WLF', 'WS' => 'WSM', 'XK' => 'XKX',
			'YE' => 'YEM', 'YT' => 'MYT', 'ZA' => 'ZAF', 'ZM' => 'ZMB', 'ZW' => 'ZWE',
		);
	}
}
