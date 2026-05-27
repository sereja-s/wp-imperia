=== Hide Address Fields for WooCommerce ===
Contributors: condless
Tags: Local pikcup, Billing fields
Requires at least: 5.2
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WooCommerce plugin for hiding the billing address fields on checkout based on the selected shipping/payment methods.

== Description ==

WooCommerce plugin for hiding the billing address fields on checkout based on the selected shipping/payment methods.

[Documentation](https://en.condless.com/hide-address-fields-for-woocommerce/) | [Contact](https://en.condless.com/contact/)

= How To Use =
1. Plugin Settings: Choose for which payment/shipping methods the billing address fields will be hidden.

= How It Works =
* Each time the customer will modify his shipping/payment methods on checkout, the plugin will show/hide the billing address fields accordingly.

= Features =
* **Conditions**: Choose which shipping methods and payment methods will cause the billing address fields to be hidden (for example local pickup and cash).
* **Billing Address Fields**: Choose which billing address fields to hide when conditions are met.
* **Top Options**: The shipping/payment options can be moved to the billing details section on checkout.
* **Customer Address**: The address of the customer from previous orders will be kept in his user details after creating an order with no billing address fields.
* **Shipping Options Shortcode**: Display the shipping options anywhere using the [haf_shipping_options] shortcode.

== Installation ==

= Minimum Requirements =
WordPress 5.2 or greater
PHP 7.0 or greater
WooCommerce 3.4 or greater

= Automatic installation =
1. Go to your Dashboard => Plugins => Add new
1. In the search form write: Condless
1. When the search return the result, click on the Install Now button

= Manual Installation =
1. Download the plugin from this page clicking on the Download button
1. Go to your Dashboard => Plugins => Add new
1. Now select Upload Plugin button
1. Click on Select file button and select the file you just download
1. Click on Install Now button and the Activate Plugin

== Screenshots ==
1. Hide Address Fields Plugin Settings
1. Hidden Address Fields on Checkout for Local pickup
1. Visible Address Fields on Checkout for Flat rate shipping

== Frequently Asked Questions ==

= How to hide the billing address fields on checkout for Local pickup regardless of the payment method? =

Select Local pickup in the 'Shipping methods' option and all of the methods in the 'Payment methods' option.

= Why the billing address fields aren't hidden? =

You must select both shipping methods and payment methods in the plugin settings, and select the equivalent methods on checkout.
The billing_country and address_1 fields must not be disabled on checkout.
The plugin doesn't support the WooCommerce Cart/Checkout Blocks.

== Changelog ==

= 1.2.2 - May 8, 2025 =
* Enhancement - WordPress version compatibility

= 1.2.1 - November 12, 2024 =
* Enhancement - WordPress version compatibility

= 1.2 - May 22, 2024 =
* Enhancement - WooCommerce version compatibility

= 1.1.9 - March 1, 2024 =
* Enhancement - WordPress version compatibility

= 1.1.8 - October 12, 2023 =
* Enhancement - WooCommerce version compatibility

= 1.1.7 - June 30, 2023 =
* Enhancement - WooCommerce version compatibility

= 1.1.6 - March 18, 2023 =
* Enhancement - WooCommerce version compatibility

= 1.1.5 - December 22, 2022 =
* Enhancement - WooCommerce version compatibility

= 1.1.4 - August 19, 2022 =
* Enhancement - WooCommerce version compatibility

= 1.1.3 - June 1, 2022 =
* Enhancement - WooCommerce version compatibility

= 1.1.2 - April 10, 2022 =
* Dev - shipping options shortcode

= 1.1.1 - February 27, 2022 =
* Dev - Set defaults fields to hide for previous version compatibility

= 1.1 - February 26, 2022 =
* Dev - Shipping/Payment methods position

= 1.0.9 - February 08, 2022 =
* Enhancement - Toggle the shipping fields properly

= 1.0.8 - December 25, 2021 =
* Enhancement - WooCommerce version compatibility

= 1.0.7 - October 20, 2021 =
* Enhancement - WooCommerce version compatibility

= 1.0.6 - July 28, 2021 =
* Dev - WP Compatibility

= 1.0.5 - June 30, 2021 =
* Dev - WP Compatibility

= 1.0.4 - April 7, 2021 =
* Enhancement - WooCommerce version compatibility

= 1.0.3 - March 12, 2021 =
* Enhancement - WooCommerce version compatibility

= 1.0.2 - February 13, 2021 =
* Feature - Trigger action when hiding the fields

= 1.0.1 - December 23, 2020 =
* Feature - Filter if to hide the fields

= 1.0 - October 07, 2020 =
* Initial release
