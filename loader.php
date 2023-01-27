<?php
/*
 Author: Awais
 Email: awais@effectwebagency.com | awais300@gmail.com
*/


/**
The idea of this file to include the folder of your custom work. That's it.
This would allow to keep all WooCommerce custom function under one place.
This is specifically created for WooCommerce custom work.
Note: This file should be included via theme's functions.php
 */

//common
include_once('Singleton.php');
include_once('Helper.php');
include_once('TemplateLoader.php');


//Add-ons
include_once('powder-coat-product/init.php');
include_once('checkout-checkbox/init.php');
include_once('local-pickup-shipping/init.php');
include_once('auto-add-warranty-product/init.php');
include_once('auto-add-products/init.php');
include_once('custom-attributes-fields/init.php');
//include_once('bogo-popup/init.php');
include_once('customer-free-shipping/init.php');
include_once('checkout-clear-cart-button/init.php');
include_once('geny-customizations/init.php');
include_once('sale-price/init.php'); 
