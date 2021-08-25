=== Minimum, Maximum, Fixed & Default Quantity for WooCommerce ===
Contributors: omardabbas
Tags: woocommerce, woo commerce, product, quantity, dropdown, min, max, default, step, decimal, unit, fixed quantity, allowed quantity, minimum quantity, maximum quantity, step quantity, fixed quantity, default quantity
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 3.9.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Define a minimum / maximum / step / default quantity for products, show a dropdown, quantities on archive/categories pages, use decimal quantities, and much more on WooCommerce stores.

== Description ==

**All in One Product Quantity for WooCommerce** plugin lets you take a full control of product order quantities in WooCommerce.

Whether you want to set a minimum quantity for all products in your WooCommerce store, a maximum one, or a quantity step so customers can only buy in multiplications of your defined step, you can accomplish that with this plugin.

And to make it even better, the plugin now has a **Default Quantity** section which will allow you to show a default quantity for your products when customers open the product page, or even on archives/categories pages.

Product Quantity for WooCommerce plugin is a full set of features grouped in just one location for your ease, it will give you a full control over your WooCommerce products quantities so you can set up orders the way your store works.

More to what is mentioned above, you can also define decimal values for products in your store, show total price based on quantity in the product page (before the cart), show a dropdown menu for quantities, and more.

= Main Features =

* Set **minimum** products order quantities (**cart total** minimum quantity or **per item** minimum quantity).
* Set **maximum** products order quantities (**cart total** maximum quantity or **per item** maximum quantity).
* Set product **quantity step** (**cart total** quantity step or **per item** quantity step).
* Set product **default** quantity for all your store products.
* Enable **decimal quantities** in WooCommerce.
* Replace standard WooCommerce quantity number input with **dropdown**.
* Set **exact (i.e. fixed) allowed or disallowed quantities** (as comma separated list).
* Show total price for the quantity in 5 locations: Before / instead / after the price, before / after add to cart
* Define a unit to show next to price (i.e. $10 per KG)
* Show quantity field on archive / categories pages so customers can add to cart directly the desired quantity

= More Features =

* **Customize messages** your customer sees.
* Enable/disable **cart notices**.
* Enable/disable **add to cart quantity validation and correction**.
* Optionally stop customer from **reaching the checkout page** on wrong quantities.
* Add product **quantity info** on single product and/or archive pages.
* **Price display by quantity** in real-time.
* **Force initial quantity on single product and/or archive pages** to either min or max quantity.
* Set quantity **input style**.
* Hide **"Update cart"** button.
* Display quantity **admin columns** in products list.

= Premium Version: Do More =

The plugin [Pro version](https://wpfactory.com/item/product-quantity-for-woocommerce/?utm_source=wporg&utm_medium=desc&utm_campaign=freeversion) allows you to set minimum quantity, maximum quantity, quantity step, default quantity, and exact (fixed) allowed & disallowed quantities options on **Per Product Basis** (i.e. different for each product).

So for example, Product A can have a minimum quantity of 5, maximum of 100, and step of 5, while product B can have no minimum quantity, maximum of 51 and step of 10 (Why 51? Simply because it has minimum of 1, then based on step 10, allowed quantities will be 1, 11, 21, 31, 41, and 51, but of course you can just leave it at 50, then the real maximum will be 41).

The Pro version also allows setting all these values on category level, so you can save yourself hours of input by just specifying the needed value on category base.

= Demo Store =

If you want to try the plugin features, play around with its settings before installing it on your live website, feel free to do so on this demo store:
URL: https://wpwhale.com/demo/wp-admin/
User: demo
Password: G6_32e!r@

== Screenshots ==

1. Plugin Main Page
2. Minimum Quantity Settings Page
3. Maximum Quantity Settings Page
4. Step Quantity Settings Page
5. Fixed Quantity Settings Page
6. Quantity Dropdown Settings Page
7. Price Display Settings Page
8. Quantity Info Control Settings Page
9. Advanced Settings Page

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Visit the [Product Quantity for WooCommerce plugin page](https://wpfactory.com/item/product-quantity-for-woocommerce/) for more details about the premium version and its features.

== Installation ==

You can either download the plugin from WordPress.org plugins and then upload it (as .zip file) to the `/wp-content/plugins/` directory.
Or search for it in the plugins directory from inside your WordPress website (Plugins >> Add New) and install it.
Then activate the plugin through the "Plugins" menu in WordPress.
Finally, you can start using it directly at "WooCommerce > Settings > Product Quantity".

== Changelog ==

= 3.9.1 - 14/08/2021 =
* Fixed an issue prevented showing product level setting (min, max, step) in free version after the last update 3.9
* Compatibility issue fixed on cart step for some themes

= 3.9 - 30/07/2021 =
* MAJOR UPDATE: Now you can allow Min/Max/Step quantities on product level in FREE version (was for Pro users only)
* Allowed showing Price Unit (rather than Total Price Unit) for variations in variable products

= 3.8.5 - 30/07/2021 =
* New Feature: Added an option to step quantity allowing going to remaining stock if not matching next step.
* Enhanced Price Unit on variable products page for single variation prices
* Verified compatibility with WordPress 5.8 and WooCommerce 5.5

= 3.8.4 - 28/06/2021 =
* Added an option on page load (product & archive) to force quantity on lowest fixed quantity
* Fixed an error message on parent_id call
* Added a Step Quantity option per attribute
* Category-defined values are now applied on variations of a variable product
* Renamed category fields for more clarity on functionality

= 3.8.3 - 16/06/2021 =
* Enhanced resetting quantity to min/max on variation change
* Added compatibility for Total Price by Quantity for dynamic pricing plugin "WooCommerce Tiered Price Table"
* Fixed a bug in showing Total Price by Quantity on archive pages for Elementor-built pages
* Verified compatibility with WooCommerce 5.4

= 3.8.2 - 29/05/2021 =
* Enhanced handling AJAX requests for Total Price by Quantity on archive pages.
* Enhanced compatibility with multiple themes archive pages.
* Added compatibility with FiboSearch (Quantity appearing on search results & Total Price by Quantity).
* Enhanced dropdown behavior on variable products if different fixed quantities are used.
* Fixed a bug in Disable plugin by category (not applying to Quantity info & Total Price by Quantity).
* Fixed a bug in Disable plugin by category when new products are added to that category.

= 3.8.1 - 22/05/2021 =
* Allowed a new option to disbale/enable bypassing minimum quantity in all products if stock is not available.
* Verified compatibility with WooCommerce 5.3

= 3.8 - 03/05/2021 =
* Added 2 attributes to Quantity Info tab to show/hide price unit & total price by quantity singular label.
* Added an option to allow bypassing minimum quantity if stock is lower than minimum.
* Total Price by Quantity will not appear if %unit% is used in template but not defined.
* Fixed a bug in Total Price by Quantity when product is out of stock (show pre-selected quantity total price).

= 3.7.4 - 23/04/2021 =
* Added Price Unit shortcode to Quantity Info tab
* Fixed a bug that prevented showing the featured image
* Enhanced Price Unit appearing on page load for variable products (when switching between variations)
* Tested compatibility with WC 5.2

= 3.7.3 - 20/04/2021 =
* Fixed compatability issue with Cart All in One plugin

= 3.7.2 - 11/04/2021 =
* Enhanced plugin loading speed for variable products
* Added compatibility with Dynamic Pricing plugin in Total Price by Quantity section
* Added a side section for Pro version (in free version)
* Added a side section for another plugin (in Pro version)
* Moved sections from Pro to free and vice versa

= 3.7.1 - 24/03/2021 =
* Added more compatibility to Woo Discounts plugin in Total Price feature
* Fixed a bug in Total price by Quantity in Dynamic Pricing
* Fixed a bug in duplicate product IDs that prevented updating values

= 3.7 - 13/03/2021 =
* Bug fixed: Unit price on archive if set by category
* Changed dropdown async and allowed options for store admins to change how it loads (async can be set to true or false)
* Tested compatibilty with WordPress 5.7 and WooCommerce 5.1

= 3.6.2 - 28/02/2021 =
* Tested compatibilty with WC 5.0

= 3.6.1 - 07/02/2021 =
* Fixed a bug in dynamic pricing on archive pages
* Enhanced decimal handling for admin orders
* Fixed translation issues for decimal quantities in "Add to Cart" messages
* Added shortcode %price% in dropdown labels

= 3.6 - 27/01/2021 =
* Tested compatibiltiy with WooCommerce 4.9
* Fixed Total Price by Quantity bug in archive page
* Enhanced dropdown functionality for variations in cart
* Fixed issues in admin permissions to edit orders (overwriting plugin settings)
* Fixed a notice message appearing on debug mode

= 3.5.4 - 01/01/2021 =
* Fixed an error message in compatibility with Product Configurator plugin

= 3.5.3 - 18/12/2020 =
* Exclude plugin by category feature is added
* More compatibility with multi-vendor plugins
* Labels on Total Price by Quantity can be set on attribute
* Tested compatibility with WooCommerce 4.8 & WordPress 5.6

= 3.5.2 - 09/12/2020 =
* Fixed an issue on archive pages created with shortcodes
* Compatibility with WC Coming Soon plugin

= 3.5.1 - 27/11/2020 =
* Fixed a bug in Price Unit on archive pages for some themes

= 3.5 - 27/11/2020 =
* Added compatibiltity with Barn2 custom table plugin
* Added a feature "Add to All" for exact quantities on variable products (to apply to all variations)
* Fixed a bug showing quantity info wrong if minimum quantity is defined on category level
* Added description to every section in the plugin to make it easier to understand

= 3.4.6 - 16/11/2020 =
* Verified compatibility with WC 4.7
* Fixed a bug in messages appearing when adding decimal quantities to cart

= 3.4.5 - 27/10/2020 =
* Checked compatibility with WC 4.6
* Allowed showing "Total Price by Quantity" feature based on category
* Bug fix: Quantities in dropdown (shown by fixed quantities) weren't properly appearing on cart for variable products
* Enhanced compatibility with a dynamic pricing plugin

= 3.4.4 - 11/10/2020 =
* Bug fix: Excluding dropdown for a category wasn't working on cart
* New option: Allow showing quantity info based on category
* Fixed a Price Unit issue for WooCommerce-Variation-Price if Price Unit field is empty

= 3.4.3 - 02/10/2020 =
* Enhancement for Total Price by Quantity feature in archive pages for some themes

= 3.4.2 - 02/10/2020 =
* Bug fixes: Two typos caused conflict in 3.4, fixed

= 3.4 - 01/10/2020 =
* New feature: Show quantity on archive/categories pages for simple products
* New feature: Total Price by Quantity can be shown now on archive/categories pages
* New feature: Dropdown is now enabled on archive/categories pages
* Bug fix: Reset to min/max for variations wasn't working on some themes when changing from a variation to another
* Tested compatibility with WC 4.5

= 3.3.1 - 12/09/2020 =
* Bug fix: Disable plugin by URL wasn't properly working on variables
* Code enhancement for Total Price by Quantity feature
* Added more compatibilty for Tiered Pricing plugins that was preventing reading correct values

= 3.3 - 04/09/2020 =
* New feature: Dropdown can be enabled/disbaled per category
* New feature: "Add to All" is now available on variable (parent) product that can be used to apply default values to all variations
* New feature: Minimum & Maximum quantities can be defined per attribute (either to apply the value to all products with that attribute, or total of products quantities with the attribute)
* Bug fixed in default quantity for variables
* Bug fixed that was showing total price by quantity wrong in variable products
* Tested compatibility with WP 5.5 & WC 4.4


= 3.2 - 24/08/2020 =
* Fixed a bug in WC 4.4 that wasn't showing Variable Product price range on pageload if "Total Price by Quantity" is enabled

= 3.1 - 25/07/2020 =
* Allowed decimal amounts for orders created by admin (manual orders)
* Added new field on product level to define Price Unit feature
* Added new button that allows showing Price Unit on category / archive pages
* Fixed a bug that adds the minimum quantity instead of default while using dropdown in some themes
* Fixed a bug that hides all variations if one is out of stock in particular themes hooks
* WPML currency enhancements
* Tested compatibility with WC 4.3

= 3.0 - 03/07/2020 =
* Introduced new feature that allows disabling / enabling all plugin settings based on WP user role
* Allowed setting fixed quantities on attributes (values) level
* Fixed a bug that prevented Price Unit from appearing if global field was empty (and defined only in category level)

= 2.9 - 17/06/2020 =
* Allowed defining decimal values for Default Quantity values
* Enhanced notification messages on checkout UI
* New Feature: Allowed defining a unit next to main price (Show $10 per KG instead of regular $10)

= 2.8 - 04/06/2020 =
* Display Price by Quantity: %unit% variable can now be set on global level (all store), category, and product-based
* Bug: Currency location on Display Price by Quantity feature wasn't reading on variable product if store currency is to the right
* Enhanced Exclude Plugin by URL to make it 100% compatibile with all plugin features to be excluded
* Changed $_product->id to $_product->get_id() to prevent warning message if product id is accessed directly

= 2.7 - 13/05/2020 =
* Introduced new 3 fields on category level: Min, Max, and Step, where you can apply a value to all products in that category with a single input
* Dropdown now appears if you define Fixed Quantity on variations without the need to define maximum quantity or max fall back quantity
* Dropdown on variations appears without also defining max or fixed value on main variable product
* Allowed thousand separator on dropdown in free version
* Added two new values on Price Display by Quantity: Before & After Add to Cart button
* Bug fix: Decimal inputs weren't working if defined as step value in category, now works fine
* Bug fix: Dropdown wasn't working properly on some themes, fixed using JS


= 2.6 - 04/05/2020 =
* WPML & Polylang compatibility introduced, new short code [alg_wc_pq_translate lang=" "] added to allow proper translation for all plugin messages for users
* Dropdown quantity will now work if you define fixed (allowed) quantities for product, i.e. without the need to define maximum quantity or max. fall back value
* Enhanced dropdown to work on extreme quantites (tens of millions)
* Introduced a thousand separtor for large quantities in dropdown (with an option to select separator)
* Default quantity & Step quantity are now available on category level (all plugin features now are available on category level)

= 2.5 - 23/04/2020 =
* Bug: Dropdown for variations wasn't showing the stock inventory as maximum if it's below defined maximum, fixed
* Enhancement: Default quantity works on variable products now
* Bug: Warning message on cart (Invalid argument), fixed

= 2.4 - 22/04/2020 =
* New feature: You can now define Default Quantity for products, and show on on product or archive page load
* New feature: Define min & max quantities for a mix of all variations in a variable product (useful if customers can mix variations)
* Bug: Decimal was showing extra zeros on some themes, fixed with a JS overwrite
* Bug: Fix going with quantity below 1 if "Do not force" option is enabled & min is set below 1

= 2.3 - 16/04/2020 =
* Bug: Forcing minimum & maximum weren't appearing on dropdown on archive pages
* Feature: Introduced %unit% label in Price Display by Quantity (you can define your own measurement to be shown on product level)
* Feature: Add to cart on product & archive pages are now using AJAX for seamless experience for shoppers

= 2.2 - 11/04/2020 =
* Bug: Variations were passing minimum quantity if stock is below minimum, fixed
* Enhancement: Prevented activating Pro Version if free version is active to avoid conflicts.
* Tested compatibility with WordPress 5.4

= 2.1 - 28/03/2020 =
* Bug Fixed: PayPal wasn't taking decimal amounts in quantities and rounds to the lower integer, fixed

= 2.0 - 27/03/2020 =
= New Feature: Allowed setting minimum / maximum / fixed quantities on category level

= 1.9.2 - 20/03/2020 =
* Enabled "Add to all variations" feature
* Tested plugin compatibility with WooCommerce 4.0
* Updated quantity-input.php template to WC 4.0 (dropdown quantity)

= 1.9.1 - 19/03/2020 =
* Removed a feature (apply min/max/step to all variations) that's causing fatal erros with WC 4 (re-adding it in progress)

= 1.9 - 24/02/2020 =
* Feature Update: Added total cart quantity option (allows admin to specify total number of items on cart level)

= 1.8.4 - 12/02/2020 =
* Dev. Fixed a bug in "Price Display by Quantity" in variable products.

= 1.8.3 - 01/02/2020 =
* Dev. Fixed the "Fixed Quantities" feature on variation products.

= 1.8.2 - 22/12/2019 =
* Dev- Added "Display price by quantity" for variable products as well.

= 1.8.1 - 22/11/2019 =
* Fix - Quantity Dropdown - Dropdown Labels - Labels per product - Fixed for variable products in cart.
* Dev - Quantity Dropdown - "Template" options added ("Before" and "After").
* Dev - Advanced - "Validate on checkout" option added (defaults to `yes`).
* Plugin author changed.

= 1.8.0 - 13/11/2019 =
* Dev - General Options - "Sold individually" (all products at once) option added.
* Dev - Shortcodes - Additional check for product object to exist added.
* Dev - Code refactoring.
* WC tested up to: 3.8.
* Tested up to: 5.3.

= 1.7.3 - 17/09/2019 =
* Dev - Price Display by Quantity - "Position" option added.
* Dev - Advanced - "Disable plugin by URL" option added.
* WC tested up to: 3.7.

= 1.7.2 - 11/07/2019 =
* Dev - General - Cart notices - "Cart notice type" option added.

= 1.7.1 - 10/07/2019 =
* Fix - Price Display by Quantity - JavaScript error fixed.

= 1.7.0 - 05/07/2019 =
* Dev - General - "Add to cart" validation - "Step auto-correct" options added.
* Dev - General - Variable Products - "Load all variations" option added.
* Dev - General - Variable Products - "Sum variations" option added.
* Dev - Quantity Step - "Cart Total Quantity" options added.
* Fix - Fixed Quantity - Now counting product's quantity already in cart when validating or correcting on "add to cart".
* Dev - Fixed Quantity - Dropdown compatibility added.
* Dev - Fixed Quantity - Settings now accept ranges (e.g. `[10-500|5]`).
* Dev - Quantity Dropdown - Dropdown label template - "Labels per product" options added.
* Dev - Price Display by Quantity - Renamed from "Price by Quantity".
* Dev - Price Display by Quantity - Link to pricing plugin added.
* Dev - Advanced - "Order Item Meta" section (and "Save quantity in order item meta" options) added.
* Dev - Advanced - "Hide 'Update cart' button" option added.
* Dev - "Main variable product" options added to meta box.
* Dev - Quantity input template updated to the template from WooCommerce v3.6.0.
* Dev - "General" settings section split into separate sections ("Quantity Dropdown", "Price Display by Quantity", "Quantity Info", "Styling", "Admin", "Advanced").
* Dev - Admin settings restyled and descriptions updated.
* Dev - `alg_wc_pq_cart_total_quantity` filter added for cart total quantity.
* Dev - Code refactoring (`qty-info`, `scripts`, `messenger` classes added etc.).

= 1.6.3 - 15/05/2019 =
* Dev - Fallback method added for checking if WooCommerce plugin is active (fixes the issue in case if WooCommerce is installed not in the default `woocommerce` directory).

= 1.6.2 - 14/05/2019 =
* Dev - "Rounding Options" options added.
* Dev - Quantity Dropdown - "Max value fallback" option added (and dropdown can now also be enabled for *variable* products).
* Dev - Advanced Options - Force JS check (periodically) - "Period (ms)" option added.
* Dev - Price by Quantity - `change` event added (e.g. fixes the issue with plus/minus quantity buttons in "OceanWP" theme).
* Dev - Code refactoring (`alg-wc-pq-force-step-check.js` and `alg-wc-pq-force-min-max-check.js` files added).
* Tested up to: 5.2.

= 1.6.1 - 04/05/2019 =
* Fix - Returning default min/max quantity for products with "Sold individually" option enabled.
* Dev - "Price by Quantity" options added.
* Dev - Admin Options - "Admin columns" options added.
* Dev - `alg_wc_pq_get_product_qty_step`, `alg_wc_pq_get_product_qty_min`, `alg_wc_pq_get_product_qty_max` filters added.
* WC tested up to: 3.6.

= 1.6.0 - 12/04/2019 =
* Fix - Variable products - Reset step on variation change fixed.
* Dev - "Quantity Info" options added.
* Dev - "Quantity Dropdown" options added.
* Dev - General Options - "Force initial quantity on archives" option added.
* Dev - Code refactoring.
* Dev - "Exact Quantity" renamed to "Exact (i.e. Fixed) Quantity".
* Dev - Settings split into sections ("General", "Minimum Quantity", "Maximum Quantity", "Quantity Step", "Fixed Quantity").

= 1.5.0 - 31/01/2019 =
* Fix - Stop customer from reaching the checkout page - "WC_Cart::get_cart_url is deprecated..." message fixed.
* Dev - "Exact Quantity" section added.
* Dev - General Options - "On variation change (variable products)" option added.
* Dev - Code refactoring (`alg-wc-pq-variable.js` etc.).

= 1.4.1 - 17/01/2019 =
* Fix - Step check - Min quantity default value changed to `0` (was `1`).
* Fix - Admin settings - Per product meta boxes - Step option fixed; checking if max/min sections are enabled.
* Fix - Force minimum quantity - Description fixed.

= 1.4.0 - 14/01/2019 =
* Dev - "Force JS check" options enabled for decimal quantities.
* Dev - "Add to cart validation" option added.
* Dev - "Quantity step message" option added.
* Dev - "Force cart items minimum quantity" option added.
* Dev - Force JS check - Quantity step - Now value is changed to *nearest* correct value (instead of always *higher* correct value).
* Dev - Code refactoring.
* Dev - Admin settings restyled and descriptions updated.

= 1.3.0 - 28/12/2018 =
* Dev - "Decimal quantities" option added.
* Dev - "Force initial quantity on single product page" option added.
* Dev - "Quantity input style" option added.
* Dev - Minor admin settings restyling.
* Dev - Code refactoring.

= 1.2.1 - 23/10/2018 =
* Dev - Min/max "Per item quantity" (for all products) moved to free version.
* Dev - Admin settings descriptions updated.

= 1.2.0 - 18/10/2018 =
* Fix - Cart min/max quantities fixed.
* Dev - Advanced Options - "Force JS check" options added.
* Dev - Raw input is now allowed in all "Message" admin options.
* Dev - Code refactoring.
* Dev - Minor admin settings restyling.
* Dev - Plugin URI updated.

= 1.1.0 - 09/11/2017 =
* Fix - Core - Checking if max/min section is enabled, when calculating product's max/min quantity.
* Fix - Admin settings - Per product meta boxes - Checking if max/min section is enabled (not only "Per item quantity on per product basis" checkbox).
* Fix - Core - Maximum order quantity - Upper limit bug fixed (when `get_max_purchase_quantity()` equals `-1`).
* Dev - Core - Minimum order quantity - Upper limit (`get_max_purchase_quantity()`) added.
* Dev - "Quantity Step" section added.

= 1.0.0 - 08/11/2017 =
* Initial Release.

== Frequently Asked Questions ==
= How to specify minimum/maximum/default/step quantity for all products at once? =
Enable Minimum Quantity tab, and define your minimum quantity in "All Products" field, that simple.

= I want to specify different minimum/maximum/default/step for every product, how? =
Recently introduced to free users, this is now available in Free version as well as the [Pro version](https://wpfactory.com/item/product-quantity-for-woocommerce/?utm_source=wporg&utm_medium=desc&utm_campaign=freeversion) with a lot more features.

= Can I change the default quantity from 1 to anything else with this plugin? =
Indeed, enable that tab and define whatever value you want there, then select the page load option to load on Default Quantity (from plugin General tab)

= Is the plugin compatible with my theme?" =
The plugin is tested on hundreds of themes, but we can't guarantee working on all of them, any well-coded theme will work fine with this plugin, and even if you encounter 3rd party compatibility issue, our support team will be glad to fix it.

= Does the plugin allow specifying minimum/maximum/default/step on category level? =
Yes, using the pro version you will be able to specify any quantity you want based on category or even attribute level. You can specify quantity for all products in that category at once, or total quantities for products from that category to match your needs.

= I want to enable decimal quantities in my store, can this plugin help? =
Yes, once you enable "Decimal Quantities" option, you can specify a minimum/step/fixed quantity in decimals, so if you want to sell in 0.5, just specify 0.5 in step quantities and your products will be sold in 0.5 increments.

= How to show a dropdown instead of quantity input field? =
The plugin has a tab "Quantity Dropdown", once enabled it will allow you to show dropdown instead of manual quantity input, the feature has to read a max value (so the dropdown doesn't go infinite), it can be defined as max-fallback value, or maximum quantity defined for a product, or if you're using fixed quantities.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
