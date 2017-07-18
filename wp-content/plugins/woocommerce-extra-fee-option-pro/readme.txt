=== Plugin Name ===
Contributors: terrytsang
Plugin Name: WooCommerce Extra Fee Options PRO
Plugin URI:  http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/
Tags: woocommerce, extra fee, minimum order, service charge, e-commerce, payment, shipping, product, category
Requires at least: 4.0
Tested up to: 3.7.1
Stable tag: 1.0.3
Version: 1.0.3
License: Unlimited Sites

== Description ==

A WooCommerce plugin that allow user to add multiple extra fee for any order with flexible options.

In WooCommerce Settings Panel, there will be a new submenu link called 'Extra Fee Options PRO' where you can:

*   Enabled / Disabled the extra fee option row
*   "Label" - choose the name for the extra fee
*   "Amount" - set the amount to be charged for the order
*   "Type" - there are 2 types (Fixed Rate or Cart % Fee)
*   "Taxable" - the fee will be taxable or not
*   "Minimum Order" - apply fee when cart total is less or equal than this amount
*   "Cart Items" - you can set cart item range for the fee
*   "Payment" - apply to certain payment gateway only
*   "Shipping" - apply to certain shipping method only

= Features =

*   Implement multiple extra fee for any order with more options
*   2 languages available : English UK (en_GB) and Chinese (zh_CN)

= IMPORTANT NOTES =
*   Do use POEdit and open 'wc-extra-fee-option-pro.pot' file and save the file as wc-extra-fee-option-pro-[language code].po, then put that into languages folder for this plugin.
*   Support WooCommerce 2.0.x only and not yet for WooCommerce 1.x

== Installation ==

1. Upload the entire *woocommerce-extra-fee-option-pro* folder to the */wp-content/plugins/* directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce Settings panel at left sidebar menu and update the options at Tab *Extra Fee Options PRO* there.
4. That's it. You're ready to go and cheers!

== Screenshots ==

1. [screenhot-1.png] Screenshot Admin WooCommerce Settings - Extra Fee Options PRO
2. [screenhot-2.png] Screenshot Frontend WooCommerce - Cart page
3. [screenhot-3.png] Screenshot Frontend WooCommerce - Checkout page

== Changelog ==

= 1.0.3 =
* Changed the way to get cart total and item count

= 1.0.2 =
* Upadted add to cart & checkout calculate total hook

= 1.0.1 =
* Updated payment option reload checkout function

= 1.0.0 =
* Initial Release that support WooCommerce 2.0.x
* Allow user to add multiple extra fee for WooCommerce site