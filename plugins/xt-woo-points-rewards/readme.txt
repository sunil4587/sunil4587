=== XT WooCommerce Points & Rewards ===

Plugin Name: XT WooCommerce Points & Rewards
Contributors: XplodedThemes
Author: XplodedThemes
Author URI: https://www.xplodedthemes.com
Tags: points rewards, woocommerce points, woocommerce rewards, woocommerce loyalty, woocommerce coupons, woocommerce points an rewards, woocommerce discounts, points, rewards, customer loyalty, coupons, discounts
Requires at least: 4.6
Tested up to: 5.6
Stable tag: trunk
Requires PHP: 5.4+
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Points and Rewards for WooCommerce that lets you reward your customers for purchases and other actions with points that can be redeemed for discounts.

== Description ==

A WooCommerce extension that lets you reward your customers for purchases and other actions with points that can be redeemed for discounts. Easily set how many points customers should earn for each dollar spent and how many points can be redeemed for a specific discount amount. Points can be awarded by product, category, or global level, and you can also control the maximum discount available when redeeming points.

**Demo**

[https://demos.xplodedthemes.com/woo-points-rewards/](https://demos.xplodedthemes.com/woo-points-rewards/)

**Free Version**

- Set the conversion rate (spend/points) to set the number of points customers can collect for each purchase
- Admin can view a list of users / points collected with purchases
- Admin can update the number of points earned by users
- Users can view points earned so far in "My account" page
- Users can redeem their points on the cart & checkout page
- Assign points only when the order is completed
- Automatically removes points assigned to orders that are later cancelled or refunded
- Option to reset points history for all or specific customers
- Apply points for existing orders before the plugin was installed
- Insert "My points" link in customers' account page

**Premium Features**

- All Free Features
- Admin can BULK update the number of points earned by users
- Ability to filter Points Log by event type and by month
- Partially redeem points on the cart & checkout page
- Set a maximum amount for discounts (customisable globally, per category or single product)
- Assign a specific number of points that can be earned for each simple or variable product to the users who purchase on your store.
- Override points awarding rules on category and product level
- Assign extra points when the following actions occur:
    - Store registration
    - First order placed
    - Product review
    - Specific spend threshold reached
    - Specific number of points collected
    - User's birthday

- Show how many points can be earned when buying a product on the product page
- Show points in order details and in the Order confirmation email
- Edit all labels and messages shown to users
- Shortcode that allows showing the points history to users
- Possibility to set a percent discount based on the product price
- Possibility to set a minimum amount of discount under which users can’t redeem their points
- When creating a coupon, assign a percentage which modifies how points are earned when using the coupon.
- Allow the shop manager to edit user points
- Automated Updates & Security Patches
- Priority Email & Help Center Support

**Compatible With <a target="_blank" href="https://xplodedthemes.com/products/woo-floating-cart/">Woo Floating Cart</a>**
**Compatible With <a target="_blank" href="https://xplodedthemes.com/products/woo-quick-view/">Woo Quick View</a>**

**Translations**

- English - default

*Note:* All our plugins are localized / translatable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful.

== Installation ==

Installing "Woo Points & Rewards" can be done by following these steps:

1. Download the plugin from the customer area at "XplodedThemes.com" 
2. Upload the plugin ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

#### V.1.2.2 - 03.03.2021
- **update**: XT Framework update

#### V.1.2.0 - 02.03.2021
- **new**: Added new Points Badge option. Insert a points badge on your shop products to highlight how many points can a customer earn on purchase.
- **update**: XT Framework update

#### V.1.1.9 - 08.02.2021
- **fix**: Minor Fixes
- **update**: XT Framework update

#### V.1.1.8 - 28.01.2021
- **enhance**: **pro** Partial redeem input added within the notice instead of using an alert box
- **fix**: **pro** Make shortcode message always visible by ignoring visibility settings
- **fix**: Minor CSS Fixes

#### V.1.1.7 - 21.12.2020
- **fix**: Minor CSS Fixes

#### V.1.1.6 - 10.12.2020
- **support**: Added support for Loco Translate by adding a loco.xml bundle config file.
- **update**: Updated translation file

#### V.1.1.5 - 30.11.2020
- **fix**: CSS Fixes
- **fix**: Fixed issue with the earn & redeem messages page visibility option not being applied correctly
- **update**: The correct shortcode to display points related messages has been changed to [xt_woopr_earn_messages]. The old [xt_woopr_earn_message] will continue to work for now.
- **update**: XT Framework update
- **support**: Better support for XT Woo Floating Cart

#### V.1.1.4 - 29.10.2020
- **new**: Added an option to select on which pages among (shop, cart & checkout) to display the earn & redeem messages.
- **new**: Added {points_value} variable (monetary value) to be used in messages.
- **enhance**: Cache duplicated queries.
- **update**: XT Framework update

#### V.1.1.3 - 27.10.2020
- **fix**: Fixed issue with the "Earn Message" not displaying on product page when a variation (loaded via ajax) is selected.
- **update**: XT Framework update

#### V.1.1.2 - 26.10.2020
- **new**: Replaced long coupon label with "Points Redemption". Also added an option to modify the label.
- **fix**: Fix display issue for the redeem message on the checkout page.
- **update**: XT Framework update

#### V.1.1.1 - 15.10.2020
- **fix**: Redeem points action using Ajax without reloading the page

#### V.1.1.0 - 14.10.2020
- **fix**: Minor CSS fixes
- **support**: Better theme support
- **new**: Display earning message on Shop page as well.
- **new**: **pro** Added [xt_woopr_earn_message] shortcode for displaying the Earn X Points Message
- **enhance**: Messages on product page will now be styles as a woocommerce info notification that should inherit theme styles
- **update**: XT Framework update

#### V.1.0.9 - 10.10.2020
- **fix**: Fix issue with points messages not being displayed on cart / checkout on some themes
- **fix**: Minor CSS fixes
- **new**: Messages can now also be modified within the free version.

#### V.1.0.8 - 07.10.2020
- **new**: XT Framework System Status will now show info about the active theme as well as XT Plugin templates that are overridden by the theme. Similar to woocommerce, it will now be easier to know which plugin templates are outdated.

#### V.1.0.7 - 23.09.2020
- **fix**: Replaced deprecated function WC()->cart->coupons_enabled() with wc_coupons_enabled()
- **fix**: Minor fixes

#### V.1.0.6 - 15.07.2020
- **new**: Added a "How to earn points?" table on the "My Points" page
- **new**: **pro** Added 3 different shortcodes. [xt_woopr_my_points], [xt_woopr_my_points_log], [xt_woopr_points_legend]
- **fix**: Minor fixes

#### V.1.0.5 - 29.01.2020
- **fix**: Fixed error message with free version

#### V.1.0.4 - 29.01.2020
- **fix**: Fixed issue with plugin TextDomain not being loaded properly
- **update**: Updated translation files

#### V.1.0.3 - 16.01.2019
- **fix**: **pro** Fix issue with birthday field not showing on checkout page when it should
- **fix**: Minor fixes
- **fix**: Fix conflict with myCred and YITH WooCommerce Points and Rewards

#### V.1.0.2 - 10.01.2019
- **fix**: Minor fixes

#### V.1.0.1 - 10.01.2019
- **update**: XT Framework update

#### V.1.0.0 - 09.01.2019
- **Initial**: Initial Version

