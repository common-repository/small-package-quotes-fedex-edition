=== Small Package Quotes - For Customers of FedEx ===
Contributors: enituretechnology
Tags: eniture. Fedex,parcel rates, parcel quotes, shipping estimates
Requires at least: 6.4
Tested up to: 6.6.2
Stable tag: 4.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Real-time small package (parcel) shipping rates from Fedex. Fifteen day free trial.



== Description ==
<p><b>ENITURE TECHNOLOGY AND THIS APPLICATION ARE NOT AFFILIATED WITH, ENDORSED, OR SUPPORTED BY FEDEX OR ANY RELATED FEDEX SERVICE.</b></p>

This version of Eniture Technology's Small Package Quotes plugin retrieves your negotiated parcel rates, takes action on them according to the plugin settings, and displays the results as shipping options on the WooCommerce Cart and Checkout pages.

**Key Features**

* Includes negotiated shipping rates in the shopping cart and on the checkout page.
* Select which shipping services to display.
* Support for variable products.
* Define multiple warehouses and drop ship locations
* Option to include residential delivery surcharge
* Option to mark up shipping rates by a set dollar amount or by a percentage.

**Requirements**

* WooCommerce 6.4 or newer.
* A carrier provided account number.
* A carrier provided API password.
* A carrier provided Meter Number.
* A carrier provided web services authentication key.
* A license from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* A carrier provided account number.
* Your username and password to the carrier's website.

If you need assistance obtaining any of the above information, conact the carrier's customer support department.

A more extensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-fedex-small-package-plugin/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "eniture small package quotes", and click Install Now on Small Package Quotes - For FedEx Customers.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get a license from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-fedex-small-package-plugin/) and pick a
subscription package. When you complete the registration process you will receive an email containing your license key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your licenses and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the license. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings and select the tab for the carrier. Use the *Connection* link to connect the plugin to your account; and the *Setting* link to configure the plugin according to your preferences.

**4. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the link for carrier name and enable the plugin.

== Frequently Asked Questions ==

= How do I get an account number? =

Visit the carrier's website or contact the carrier's customer support.

= Where do I find my username and password to the carrier's website? =

Your username and password will have been issued when you established your account. If you have your username and not your password, you can try to recover the password on the login page to the carrier's website. Otherwise contact the carrier's customer support department.



= How do I get a license key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or 
purchased a license outright. At the conclusion of the registration process an email will be sent to you that will include 
the license key. You can also login to eniture.com using the username and password you created during the registration process 
and retrieve the license key from the My Licenses tab.

= How do I change my plugin license from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My Licenses tab. There you will be able to manage the licensing of all of your Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site license. To use it on another website you will need to purchase an additional license. If you want 
to change the website with which the plugin is registered, login to eniture.com and navigate to the My Licenses tab. There you will 
be able to change the domain name that is associated with the license key.

= Do I have to purchase a second license for my staging or development site? =

No. Each license allows you to identify one domain for your production environment and one domain for your staging or 
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings, select the tab for the carrier and than connections)
then you have one or more of the following licensing issue(s): 1) You are using the license key on more than one domain. 
The licenses are for single sites. You will need to purchase an additional license. 2) Your trial period has expired. 
3) Your current license has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and 
go to the My Licenses tab to resolve any of these issues.

= Why were the shipment charges I received on the invoice from carrier different than what was quoted by the plugin? =

Common reasons include one of the shipment parameters (weight, dimensions) is different, or additional services (such as residential 
delivery) were required. Compare the details of the invoice to the shipping settings on the products included in the shipment. 
Consider making changes as needed. Remember that the weight of the packing materials is included in the billable weight for the shipment. 
If you are unable to reconcile the differences call your local Worldwide Express office for assistance.

= Why do I sometimes get a message that a shipping rate estimate couldn’t be provided? =

There are several possibilities:

* Carrier has restrictions on a shipment’s maximum weight, length and girth which your shipment may have exceeded.
* There wasn’t enough information about the weight or dimensions for the products in the shopping cart to retrieve a shipping rate estimate.
* The carrier's website isn’t operational.
* Your carrier's account has been suspended or cancelled.
* Your Eniture Technology license key for this plugin has expired.

== Screenshots ==

1. Plugin options page
2. Connection settings page
3. Quotes returned to cart

== Changelog ==

= 4.3.1 =
* Fix: Fixed domain parsing issue for PHP versions below 8.0.

= 4.3.0 =
* Update: Introduced new FedEx API. 
* Update: Updated connection tab according to WordPress requirements
* Fix: Corrected the order of the plugin tabs.
* Fix: Resolved issues with the calculation of live shipping rates in draft orders.

= 4.2.3 =
* Fix: Fixed issue with slow loading of variant products

= 4.2.2 =
* Update: Compatibility with WordPress version 6.5.2
* Update: Compatibility with PHP version 8.2.0
* Update: Introduced an additional option to packaging method when standard boxes is not in use

= 4.2.1 =
* Update: Changed required plan from standard to basic for delivery estimate options.

= 4.2.0 =
* Update: Display "Free Shipping" at checkout when handling fee in the quote settings is  -100% .
* Update:  Introduced “product level markup” and “origin level markup”.
* Update: Compatibility with WooCommerce HPOS(High-Performance Order Storage)

= 4.1.4 =
* Update: Updated logs end point URLS. 

= 4.1.3 =
* Update: Fixed grammatical mistakes in "Ground transit time restrictions" admin settings.

= 4.1.2 =
* Update: Fixed description and position of Source Rate settings. 

= 4.1.1 =
* Update: Plugin name changed from "Small Package Quotes - For FedEx Customers" to "Small Package Quotes - For Customers of FedEx".

= 4.1.0 =
* Update: Introduced optimizing space utilization.
* Update: Modified expected delivery message at front-end from “Estimated number of days until delivery” to “Expected delivery by”.
* Fix: Inherent Flat Rate value of parent to variations

= 4.0.20 =
* Update: * Update: Introduced a settings on product page to Exempt ground Transit Time restrictions.

= 4.0.19 =
* Update: Added compatibility with "Address Type Disclosure" in Residential address detection 

= 4.0.18 =
* Update: Compatibility Shipping Discount addon plugin.

= 4.0.17 =
* Update: Compatibility with WordPress version 6.1
* Update: Compatibility with WooCommerce version 7.0.1

= 4.0.16 =
* Update: Updated of carrier name everywhere in the plugin
* Update: Updated shipping method name in the shipping zone

= 4.0.15 =
* Update: Updated the plugin name

= 4.0.14 =
* Fix: Fixed issue of free shipping option in case of error from carrier's API

= 4.0.13 =
* Fix: Fixed issue in release 4.0.12. 

= 4.0.12 =
* Update: Error handling in carrier's API response. 

= 4.0.11 =
* Update: Shipping methods will show as free on -100% handling free.

= 4.0.10 =
* Update: Included product parent id along with variant ID required by freightdesk.online

= 4.0.9 =
* Update: Introduced connectivity from the plugin to FreightDesk.Online using Company ID

= 4.0.8 =
* Update: Compatibility with WordPress version 6.0.
* Update: Included tabs for freightdesk.online and validate-addresses.com

= 4.0.7 =
* Update: Compatibility with WordPress multisite network
* Fix: Fixed support link.
* Fix: Fixed Cron scheduling.

= 4.0.6 =
* Update: Adds support to get rates for United Arab Emirates.

= 4.0.5 =
* Update: Compatibility with PHP version 8.1.
* Update: Compatibility with WordPress version 5.9.

= 4.0.4 =
* Update: Isolate Flat Rate from carrier's api request.

= 4.0.3 =
* Update: Show WooCommerce Shipping Options dropdown functionality.

= 4.0.2 =
* Update: Introduced debug logs tab.
* Fix: In case of multiply shipment, wil show rates if all shipments will return rates. 

= 4.0.1 =
* Update: Added carrier's SmartPost service.

= 4.0.0 =
* Update: Compatibility with PHP version 8.0
* Update: Compatibility with WordPress version 5.8
* Fix: Corrected product page URL in connection settings tab

= 3.10.3 =
* Update: Added carrier id to order meta data.

= 3.10.2 =
* Update: Changed in terminal origin address content.

= 3.10.0 =
* Update: Added carrier id to order meta data.
* Update: Changed in terminal origin address content.
* Update: Update to plugin information.

= 3.9.0 =
* Update: Update to plugin information. This update doesn't include any programming changes.

= 3.8.0 =
* Update: Added feature "Weight threshold limit".
* Update: Added feature In-store pickup with terminal information.

= 3.7.0 =
* Update: Added images URL for freightdesk.online portal.
* Update: CSV columns updated.
* Update: Virtual product details added in order meta data.
* Update: Compatibility with shippable addon.

= 3.6.0 =
* Update: Introduced new features, Order detail widget for draft orders, improved order detail widget for Freightdesk.online, compatibly with Shippable add-on, compatibly with Account Details(ET) add-don(Capturing account number on checkout page).

= 3.5.1 =
* Update: Sync orders to freightdesk.online

= 3.5.0 =
* Update: Compatibility with WordPress 5.6

= 3.4.9 =
* Fix: Fixed In Store and Local delivery as an default selection.

= 3.4.8 =
* Fix: Fixed customer issue ticket # 214635019

= 3.4.7 =
* Update: Compatibility a custom work micro warehouse

= 3.4.6 =
* Update: Compatibility with WordPress 5.5, Compatibility with shipping solution freightdesk.online, Added nested feature and added a link in plugin to get updated plans from eniture.com.

= 3.4.6 =
* Fix: Fixed selected shipping option reverted to default shipping.

= 3.4.5 =
* Fix: Compatibility with Eniture Technology Freight plugins 

= 3.4.4 =
* Update: Ignore items with given Shipping Class(es).

= 3.4.3 =
* Update: Improved session handling and calling on appropriate place

= 3.4.2 =
* Fix: Fixed UI issues and warning.

= 3.4.1 =
* Fix: Fixed UI of quote settings tab.

= 3.4.0 =
* Update: Introduced an option to control shipment days of the week in setting tab.

= 3.3.0 =
* Update: This update introduces: 1) Plugin description in the Shipping Methods page. 2) Product title with packaging steps. 3) Box Fee in Packaging and Multi Packaging of a product. 4) Code Rewrite for Order Detailed Widget. 5) New updated UI changes in quote settings page.

= 3.2.3 =
* Fix: Fix compatibility issue with Standard Box Sizes Addon.  

= 3.2.2 =
* Fix: Removed extra garbage code.  

= 3.2.1 =
* Fix: Fixed compatibility issue with Eniture Technology LTL Freight Quotes plugins.

= 3.2.0 =
* Update: This update introduces: 1) The ability to mark up individual services; 2) Customizable error message in the event the plugin is unable to retrieve rates from the carrier; 3) Ship single product in multiple packages;  4) The update also includes compatibility with the Standard Box Size pluign that creates multiple packages of single product.

= 3.1.1 =
* Fix: Fixed issue with carrier's SmartPost service

= 3.1.0 =
* Update: Introduced new feature, Cut Off Time & Ship Date Offset.

= 3.0.2 =
* Update: Compatibility with WordPress 5.1

= 3.0.1 =
* Fix: Identify one warehouse and multiple drop ship locations in basic plan.

= 3.0.0 =
* Update: Introduced new features and Basic, Standard and Advanced plans.

= 2.3.1 =
* Update: Compatibility with WordPress 5.0

= 2.3.0 =
* Update: Added support for the carrier's SmartPost service.

= 2.2.0 =
* Update: Added support for the carrier's One Rate program

= 2.1.1 =
* Update: Compatibility with WooCommerce 3.4.2 and PHP 7.1.

= 2.1.0 =
* Update: Added new subscription options for Residential Address Detection plug-in and Standard Box Sizes plug-in 

= 2.0.0 =
* Update: Introduction of Standard Box Sizes and Residential Address Detection features which are enabled though the installation of plugin add ons.

= 1.1.1 =
* Fix: Fixed issue with new reserved word in PHP 7.1

= 1.1.0 =
* Update: Compatibility with WooCommerce 4.9.

= 1.0.2 =
* Update: Compatibility with WooCommerce 4.8.
 
= 1.0.1 =
* Initial release.

== Upgrade Notice ==
