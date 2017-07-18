=== WOO Stickers by Webline ===
Contributors: weblineindia
Tags: woocommerce stickers, woocommerce products stickers, product stickers, product badge, woocommerce product badge, custom product badge 
Requires at least: 3.5
Tested up to: 4.7.4
Stable tag: 1.1.0
License: GPLv2 or later

Product sticker plugin to improve customer experience while shopping by providing stickers for New, On Sale and Soldout Products.

== Description ==

Product sticker plugin to improve customer experience while shopping by providing stickers for New products, On Sale products, Soldout Products which is easily configure from admin panel without any extra efforts.

= Key Features =

- Sticker for New, On Sale, Soldout Products
- Admin can define number of days to define product as new.
- Admin can configure different style of stickers
- Admin can enable/disable this sticker feature
- Admin can configure stickers for Product List as well for Product Detail page
- Upload Custom Sticker functionality


== Installation ==

1. Upload 'woo-stickers-by-webline' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Done!

== Screenshots ==

1. Stickers on listing page.
2. The Round stickers on listing page.
3. The Round stickers of New product on detail page.
4. The Ribbon stickers of New product on detail page.
5. The Ribbon stickers of Sale product on Listing page.
6. Custom stickers with Ribbon Sticker on New and Sale product detail page.
7. The Custom stickers for Sold product on detail page.
8. WOO Stickers menu in admin side under Settings.
9. General Configuration of WooStickers Plugin.
10. New Product Configuration of WooStickers Plugin.
11. Sale Configuration of WooStickers Plugin.
12. Sold Configuration of WooStickers Plugin.

== Frequently Asked Questions ==

= Sticker image is not display in product detail page. =

Once check the General configuration -> Enable Sticker On Product Details Page as "YES"

Also check the appropriate settings for e.g problem cause for "NEW Sticker" than go to New Sticker configuration and setEnable Product Sticker as "YES"

If still problem arise override the CSS class to your Theme style which and increase the "z-index" amount.

= Sticker image is not display in product detail page. =

We have tested plugin in standard themes of WordPress. We found some themes which design is not proper or structured, in that case you have to override specific class to your Theme style and set margin according to that.

= Custom sticker image is repeating. =

We have a standard image dimension is 54 X 54 for custom sticker image. if dimension is below this size problem may occur, in that case you have to override the class "custom_sticker_image" to your own theme style or you can create image according to that size.


== Changelog ==

= 1.1.0 =

Release Date: May 06, 2017

* Fix: WooCommerce 3.x compitability fixes.
* Fix: Resolved Sticker shows above title issue.
* Fix: Minor other bug fixes.

= 1.0.4 =

Release Date: March 05, 2015

* Fix: Soldout product sticker not display on category page or listing while product is on sale and out of stock. 

= 1.0.3 =

Release Date: March 04, 2015

* Enhancement: Setting link on Plugins page.
* Enhancement: Added field for Product Sticker Position and Custom sticker upload for new, sale and soldout product.
* Enhancement: Shorten tab names.
* Fix: Override the default behavior of woocommerce badge.
* Fix: Setting options updated.
* Fix: Uninstall hook option delete.
* Fix: Field description updated.
* Fix: New product default value. 
 
= 1.0.2 =

Release Date: November 29, 2014

* Enhancement: Added field to consider product as new.

= 1.0.1 =
Release Date: November 26, 2014

* Initial release
