=== GoPay for WooCommerce ===
Contributors: GoPay
Tags: WooCommerce, GoPay
Requires at least: 5.4
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce and GoPay payment gateway integration

== Description ==
This is the official plugin for connecting GoPay payment gateway to your e-shop. GoPay is already used by over 18,000 e-shops in the Czech Republic, Slovakia and all over the world. Start accepting payments today! 

= Plugin functions: =
* 56 payment methods including Google Pay, Apple Pay, Click to Pay and PSD2 bank transfers
* 9 currencies and 13 language localizations
* mobile and desktop payment gateway
* remember mode on the payment gateway - customer can remember payment card details and pay just by one click
* payment cancellation
* recurring payments
* payment restart 

== Installation ==
First of all, install WordPress and WooCommerce, then upload and configure the plugin by following the steps below:
1. Copy the plugin files to the '/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the Plugins screen in WordPress.
3. Configure the plugin by providing goid, client id and secret to load the other options (they can be found on your GoPay account).
4. Finally, choose the options you want to be available in the payment gateway (payment methods and banks must also be enabled in your GoPay account).

== Frequently Asked Questions ==

= How will I receive my payments? =
Successful payments will be automatically credited to the GoPay merchant account. We will send it from the merchant account to the registered bank account at the time of clearing.

= How often is clearing done? =
We offer 3 clearing frequencies - daily, weekly and monthly.

= Do I need to have a bank account to receive payments? =
Yes, it is necessary to register a bank account to receive a clearing.

= How do I know that the customer has successfully paid? =
After a successful payment, we send a notification about the change of the payment status. You can also check the payment status in your GoPay merchant account.

== Screenshots ==

1. Card payment - desktop version
2. Card payment - mobile version
3. Saved cards - desktop version
4. Saved cards - mobile version
5. Payment method selection - desktop version
6. Payment methods selection - mobile version

= Minimum requirements =
* WordPress 5.4
* PHP version 7.4
* WooCommerce version 5.0
* WooCommerce Subscriptions¹ 4.0

1 - WooCommerce Subscriptions must be installed if you need to deal with recurring payments.

== Changelog ==

= 1.0.0 =
WooCommerce and GoPay gateway integration.

= 1.0.1 =
Fixed variable products error

= 1.0.2 =
Fixed issues when enabled payment instruments was empty

= 1.0.3 =
Translation fix for payment options
