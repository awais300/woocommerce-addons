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
include_once('TemplateLoader.php');

//Add-ons
include_once('powder-coat-product/init.php');
include_once('checkout-checkbox/init.php');
include_once('local-pickup-shipping/init.php');
include_once('auto-add-warranty-product/init.php');
include_once('auto-add-products/init.php');

